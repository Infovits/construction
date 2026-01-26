<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\BudgetCategoryModel;

class BudgetCategoriesController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new BudgetCategoryModel();
    }

    public function index()
    {
        $filters = [
            'budget_type' => $this->request->getGet('budget_type'),
            'is_active' => $this->request->getGet('is_active'),
            'search' => $this->request->getGet('search')
        ];

        $data = [
            'title' => 'Budget Categories',
            'categories' => $this->categoryModel->getCategoriesForCompany($filters),
            'budgetTypes' => BudgetCategoryModel::getBudgetTypes(),
            'stats' => $this->categoryModel->getCategoryStats(),
            'filters' => $filters
        ];

        return view('accounting/budget_categories/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Budget Category',
            'budgetTypes' => BudgetCategoryModel::getBudgetTypes()
        ];

        return view('accounting/budget_categories/create', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|max_length[255]',
            'budget_type' => 'required|in_list[labor,materials,equipment,subcontractor,overhead,other]',
            'description' => 'permit_empty|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'company_id' => session('company_id') ?? 1,
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'budget_type' => $this->request->getPost('budget_type'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        try {
            if ($this->categoryModel->insert($data)) {
                return redirect()->to('/admin/accounting/budget-categories')
                               ->with('success', 'Budget category created successfully!');
            } else {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Failed to create budget category.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Budget Category Store Error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error creating budget category: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $category = $this->categoryModel->find($id);

            if (!$category || $category['company_id'] != (session('company_id') ?? 1)) {
                return redirect()->to('/admin/accounting/budget-categories')
                               ->with('error', 'Budget category not found.');
            }

            $data = [
                'title' => 'Edit Budget Category',
                'category' => $category,
                'budgetTypes' => BudgetCategoryModel::getBudgetTypes()
            ];

            return view('accounting/budget_categories/edit', $data);

        } catch (\Exception $e) {
            log_message('error', 'Budget Category Edit Error: ' . $e->getMessage());
            return redirect()->to('/admin/accounting/budget-categories')
                           ->with('error', 'Error loading budget category for editing.');
        }
    }

    public function update($id)
    {
        try {
            $category = $this->categoryModel->find($id);

            if (!$category || $category['company_id'] != (session('company_id') ?? 1)) {
                return redirect()->to('/admin/accounting/budget-categories')
                               ->with('error', 'Budget category not found.');
            }

            $rules = [
                'name' => 'required|max_length[255]',
                'budget_type' => 'required|in_list[labor,materials,equipment,subcontractor,overhead,other]',
                'description' => 'permit_empty|max_length[1000]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                               ->withInput()
                               ->with('errors', $this->validator->getErrors());
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'budget_type' => $this->request->getPost('budget_type'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            if ($this->categoryModel->update($id, $data)) {
                return redirect()->to('/admin/accounting/budget-categories')
                               ->with('success', 'Budget category updated successfully!');
            } else {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Failed to update budget category.');
            }

        } catch (\Exception $e) {
            log_message('error', 'Budget Category Update Error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error updating budget category: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $category = $this->categoryModel->find($id);

            if (!$category || $category['company_id'] != (session('company_id') ?? 1)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Budget category not found.'
                ]);
            }

            // Check if category is being used in any budgets
            $db = \Config\Database::connect();
            $builder = $db->table('budget_line_items');
            $count = $builder->where('category_id', $id)->countAllResults();

            if ($count > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot delete category. It is being used in ' . $count . ' budget line item(s).'
                ]);
            }

            if ($this->categoryModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Budget category deleted successfully!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete budget category.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Budget Category Delete Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error deleting budget category: ' . $e->getMessage()
            ]);
        }
    }
}
