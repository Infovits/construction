<?php

namespace App\Controllers;

use App\Models\ProjectCategoryModel;
use App\Models\CompanyModel;

class ProjectCategories extends BaseController
{
    protected $categoryModel;
    protected $companyModel;

    public function __construct()
    {
        helper('project');
        $this->categoryModel = new ProjectCategoryModel();
        $this->companyModel = new CompanyModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Project Categories',
            'categories' => $this->categoryModel->getCategoriesWithCompany(),
            'stats' => [
                'total_categories' => $this->categoryModel->countAll(),
                'active_categories' => $this->categoryModel->where('is_active', 1)->countAllResults(),
                'categories_this_month' => $this->categoryModel->where('created_at >=', date('Y-m-01'))->countAllResults()
            ]
        ];

        return view('project_categories/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Project Category',
            'companies' => $this->companyModel->findAll()
        ];

        return view('project_categories/create', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'company_id' => 'required|numeric',
            'color_code' => 'max_length[7]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $data = [
            'company_id' => $this->request->getPost('company_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'color_code' => $this->request->getPost('color_code') ?: '#6366f1',
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        try {
            if ($this->categoryModel->save($data)) {
                return redirect()->to(base_url('admin/project-categories'))->with('success', 'Project category created successfully');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error creating category: ' . $e->getMessage());
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create project category');
    }

    public function show($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Project category not found');
        }

        // Get projects using this category
        $projectModel = new \App\Models\ProjectModel();
        $projects = $projectModel->where('category_id', $id)->findAll();

        $data = [
            'title' => 'Category Details - ' . $category['name'],
            'category' => $category,
            'projects' => $projects,
            'project_count' => count($projects)
        ];

        return view('project_categories/view', $data);
    }

    public function edit($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Project category not found');
        }

        $data = [
            'title' => 'Edit Project Category',
            'category' => $category,
            'companies' => $this->companyModel->findAll()
        ];

        return view('project_categories/edit', $data);
    }

    public function update($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Project category not found');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'company_id' => 'required|numeric',
            'color_code' => 'max_length[7]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $data = [
            'company_id' => $this->request->getPost('company_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'color_code' => $this->request->getPost('color_code') ?: '#6366f1',
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        try {
            if ($this->categoryModel->update($id, $data)) {
                return redirect()->to(base_url('admin/project-categories'))->with('success', 'Project category updated successfully');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error updating category: ' . $e->getMessage());
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update project category');
    }

    public function delete($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return $this->response->setJSON(['success' => false, 'message' => 'Project category not found']);
        }

        // Check if category is being used by any projects
        $projectModel = new \App\Models\ProjectModel();
        $projectCount = $projectModel->where('category_id', $id)->countAllResults();

        if ($projectCount > 0) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => "Cannot delete category. It is being used by {$projectCount} project(s)."
            ]);
        }

        try {
            if ($this->categoryModel->delete($id)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Project category deleted successfully']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error deleting category: ' . $e->getMessage()]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete project category']);
    }

    public function toggle($id)
    {
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return $this->response->setJSON(['success' => false, 'message' => 'Project category not found']);
        }

        $newStatus = $category['is_active'] ? 0 : 1;

        try {
            if ($this->categoryModel->update($id, ['is_active' => $newStatus])) {
                $message = $newStatus ? 'Project category activated' : 'Project category deactivated';
                return $this->response->setJSON(['success' => true, 'message' => $message]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update category status']);
    }

    public function getByCompany($companyId)
    {
        $categories = $this->categoryModel->where('company_id', $companyId)
                                          ->where('is_active', 1)
                                          ->findAll();

        return $this->response->setJSON($categories);
    }
}
