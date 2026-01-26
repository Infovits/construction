<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\ChartOfAccountsModel;
use App\Models\AccountCategoryModel;

class ChartOfAccountsController extends BaseController
{
    protected $chartOfAccountsModel;
    protected $accountCategoryModel;

    public function __construct()
    {
        $this->chartOfAccountsModel = new ChartOfAccountsModel();
        $this->accountCategoryModel = new AccountCategoryModel();
    }

    public function index()
    {
        $filters = [
            'account_type' => $this->request->getGet('account_type'),
            'category_id' => $this->request->getGet('category_id'),
            'is_active' => $this->request->getGet('is_active'),
            'search' => $this->request->getGet('search')
        ];

        $accounts = $this->chartOfAccountsModel->getAccountsWithDetails($filters);
        
        // Build proper hierarchical tree structure
        $hierarchicalAccounts = $this->buildAccountHierarchy($accounts);
        $flattenedAccounts = $this->flattenAccountsForDisplay($hierarchicalAccounts);
        
        $data = [
            'title' => 'Chart of Accounts',
            'accounts' => $flattenedAccounts,
            'categories' => $this->accountCategoryModel->getActiveCategories(),
            'stats' => $this->chartOfAccountsModel->getAccountStats(),
            'filters' => $filters
        ];

        return view('accounting/chart_of_accounts/index', $data);
    }

    /**
     * Build hierarchical structure from flat accounts array
     */
    private function buildAccountHierarchy($accounts, $parentId = null, $depth = 0)
    {
        $hierarchy = [];
        
        foreach ($accounts as $account) {
            if ($account['parent_account_id'] == $parentId) {
                $account['depth'] = $depth;
                $account['children'] = $this->buildAccountHierarchy($accounts, $account['id'], $depth + 1);
                $hierarchy[] = $account;
            }
        }
        
        return $hierarchy;
    }
    
    /**
     * Flatten hierarchical structure for display while preserving depth
     */
    private function flattenAccountsForDisplay($hierarchy)
    {
        $flattened = [];
        
        foreach ($hierarchy as $account) {
            $flattened[] = $account;
            if (!empty($account['children'])) {
                $flattened = array_merge($flattened, $this->flattenAccountsForDisplay($account['children']));
            }
        }
        
        return $flattened;
    }

    public function create()
    {
        $data = [
            'title' => 'Create Account',
            'categories' => $this->accountCategoryModel->getActiveCategories(),
            'parentAccounts' => $this->chartOfAccountsModel->getActiveAccounts(),
            'accountTypes' => ChartOfAccountsModel::getAccountTypes(),
            'validation' => \Config\Services::validation()
        ];

        return view('accounting/chart_of_accounts/create', $data);
    }

    public function store()
    {
        $rules = [
            'account_code' => 'required|max_length[50]',
            'account_name' => 'required|max_length[255]',
            'account_type' => 'required|in_list[asset,liability,equity,revenue,expense]',
            'category_id' => 'permit_empty|integer',
            'parent_account_id' => 'permit_empty|integer',
            'account_subtype' => 'permit_empty|max_length[100]',
            'description' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Check if account code is unique
        if (!$this->chartOfAccountsModel->isAccountCodeUnique($this->request->getPost('account_code'))) {
            return redirect()->back()->withInput()->with('error', 'Account code already exists.');
        }

        $data = [
            'company_id' => session('company_id') ?? 1,
            'account_code' => $this->request->getPost('account_code'),
            'account_name' => $this->request->getPost('account_name'),
            'account_type' => $this->request->getPost('account_type'),
            'category_id' => $this->request->getPost('category_id') ?: null,
            'parent_account_id' => $this->request->getPost('parent_account_id') ?: null,
            'account_subtype' => $this->request->getPost('account_subtype'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'balance' => 0.00
        ];

        if ($this->chartOfAccountsModel->insert($data)) {
            return redirect()->to('/admin/accounting/chart-of-accounts')->with('success', 'Account created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create account.');
        }
    }

    public function show($id)
    {
        // Get account with detailed information
        $accountsWithDetails = $this->chartOfAccountsModel->getAccountsWithDetails(['account_id' => $id]);
        $account = !empty($accountsWithDetails) ? $accountsWithDetails[0] : null;
        
        if (!$account) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Account not found');
        }

        // Get child accounts (sub-accounts)
        $childAccounts = $this->chartOfAccountsModel->where('parent_account_id', $id)
                                                   ->where('is_active', 1)
                                                   ->findAll();

        $data = [
            'title' => 'Account Details - ' . $account['account_name'],
            'account' => $account,
            'childAccounts' => $childAccounts
        ];

        return view('accounting/chart_of_accounts/show', $data);
    }

    public function edit($id)
    {
        $account = $this->chartOfAccountsModel->find($id);
        
        if (!$account) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Account not found');
        }

        $data = [
            'title' => 'Edit Account',
            'account' => $account,
            'categories' => $this->accountCategoryModel->getActiveCategories(),
            'parentAccounts' => $this->chartOfAccountsModel->getActiveAccounts(),
            'accountTypes' => ChartOfAccountsModel::getAccountTypes(),
            'validation' => \Config\Services::validation()
        ];

        return view('accounting/chart_of_accounts/edit', $data);
    }

    public function update($id)
    {
        $account = $this->chartOfAccountsModel->find($id);
        
        if (!$account) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Account not found');
        }

        $rules = [
            'account_code' => 'required|max_length[50]',
            'account_name' => 'required|max_length[255]',
            'account_type' => 'required|in_list[asset,liability,equity,revenue,expense]',
            'category_id' => 'permit_empty|integer',
            'parent_account_id' => 'permit_empty|integer',
            'account_subtype' => 'permit_empty|max_length[100]',
            'description' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Check if account code is unique (excluding current account)
        if (!$this->chartOfAccountsModel->isAccountCodeUnique($this->request->getPost('account_code'), $id)) {
            return redirect()->back()->withInput()->with('error', 'Account code already exists.');
        }

        $data = [
            'account_code' => $this->request->getPost('account_code'),
            'account_name' => $this->request->getPost('account_name'),
            'account_type' => $this->request->getPost('account_type'),
            'category_id' => $this->request->getPost('category_id') ?: null,
            'parent_account_id' => $this->request->getPost('parent_account_id') ?: null,
            'account_subtype' => $this->request->getPost('account_subtype'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->chartOfAccountsModel->update($id, $data)) {
            return redirect()->to('/admin/accounting/chart-of-accounts')->with('success', 'Account updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update account.');
        }
    }

    public function delete($id)
    {
        $account = $this->chartOfAccountsModel->find($id);
        
        if (!$account) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Account not found');
        }

        // Check if account is a system account
        if ($account['is_system_account']) {
            return redirect()->back()->with('error', 'Cannot delete system account.');
        }

        if ($this->chartOfAccountsModel->delete($id)) {
            return redirect()->to('/admin/accounting/chart-of-accounts')->with('success', 'Account deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete account.');
        }
    }

    public function toggle($id)
    {
        $account = $this->chartOfAccountsModel->find($id);
        
        if (!$account) {
            return $this->response->setJSON(['success' => false, 'message' => 'Account not found']);
        }

        $newStatus = $account['is_active'] ? 0 : 1;
        
        if ($this->chartOfAccountsModel->update($id, ['is_active' => $newStatus])) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Status updated successfully',
                'new_status' => $newStatus
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
        }
    }
}
