<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\AccountCategoryModel;

class AccountCategoriesController extends BaseController
{
    protected $accountCategoryModel;

    public function __construct()
    {
        $this->accountCategoryModel = new AccountCategoryModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Account Categories',
            'categories' => $this->accountCategoryModel->getAccountCategoriesWithDetails()
        ];

        return view('accounting/account_categories/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Account Category',
            'validation' => \Config\Services::validation()
        ];

        return view('accounting/account_categories/create', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|max_length[255]',
            'code' => 'required|max_length[20]|is_unique[account_categories.code]',
            'account_type' => 'required|in_list[asset,liability,equity,revenue,expense]',
            'description' => 'permit_empty|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'company_id' => session('company_id') ?? 1,
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'account_type' => $this->request->getPost('account_type'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->accountCategoryModel->insert($data)) {
            return redirect()->to('/admin/accounting/account-categories')->with('success', 'Account category created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create account category.');
        }
    }

    public function show($id)
    {
        $category = $this->accountCategoryModel->find($id);
        
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Account category not found');
        }

        $data = [
            'title' => 'Account Category Details',
            'category' => $category
        ];

        return view('accounting/account_categories/show', $data);
    }

    public function edit($id)
    {
        $category = $this->accountCategoryModel->find($id);
        
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Account category not found');
        }

        $data = [
            'title' => 'Edit Account Category',
            'category' => $category,
            'validation' => \Config\Services::validation()
        ];

        return view('accounting/account_categories/edit', $data);
    }

    public function update($id)
    {
        $category = $this->accountCategoryModel->find($id);
        
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Account category not found');
        }

        $rules = [
            'name' => 'required|max_length[255]',
            'code' => "required|max_length[20]|is_unique[account_categories.code,id,{$id}]",
            'account_type' => 'required|in_list[asset,liability,equity,revenue,expense]',
            'description' => 'permit_empty|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'account_type' => $this->request->getPost('account_type'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->accountCategoryModel->update($id, $data)) {
            return redirect()->to('/admin/accounting/account-categories')->with('success', 'Account category updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update account category.');
        }
    }

    public function delete($id)
    {
        $category = $this->accountCategoryModel->find($id);
        
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Account category not found');
        }

        // Check if category is being used by any accounts
        $chartOfAccountsModel = new \App\Models\ChartOfAccountsModel();
        $accountsCount = $chartOfAccountsModel->where('category_id', $id)->countAllResults();
        
        if ($accountsCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete category. It is being used by ' . $accountsCount . ' account(s).');
        }

        if ($this->accountCategoryModel->delete($id)) {
            return redirect()->to('/admin/accounting/account-categories')->with('success', 'Account category deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete account category.');
        }
    }

    public function toggle($id)
    {
        $category = $this->accountCategoryModel->find($id);
        
        if (!$category) {
            return $this->response->setJSON(['success' => false, 'message' => 'Category not found']);
        }

        $newStatus = $category['is_active'] ? 0 : 1;
        
        if ($this->accountCategoryModel->update($id, ['is_active' => $newStatus])) {
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
