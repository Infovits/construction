<?php

namespace App\Controllers;

use App\Models\MaterialCategoryModel;

class MaterialCategories extends BaseController
{
    protected $categoryModel;
    
    public function __construct()
    {
        $this->categoryModel = new MaterialCategoryModel();
    }
    
    public function index()
    {
        $companyId = session()->get('company_id');
        
        $data = [
            'title' => 'Material Categories',
            'categories' => $this->categoryModel->getCategoriesWithSubcategories($companyId),
        ];
        
        return view('inventory/categories/index', $data);
    }
    
    public function new()
    {
        $companyId = session()->get('company_id');
        
        $data = [
            'title' => 'Add New Category',
            'parentCategories' => $this->categoryModel->where('company_id', $companyId)
                ->where('parent_id', null)
                ->where('is_active', 1)
                ->findAll(),
        ];
        
        return view('inventory/categories/create', $data);
    }
    
    public function create()
    {
        $companyId = session()->get('company_id');
        
        $data = [
            'title' => 'Add New Category',
            'parentCategories' => $this->categoryModel->where('company_id', $companyId)
                ->where('parent_id', null)
                ->where('is_active', 1)
                ->findAll(),
        ];
        
        return view('inventory/categories/create', $data);
    }
    
    public function store()
    {
        helper(['form', 'url']);
        
        // Debug: Log that the method was called
        log_message('debug', 'MaterialCategories::store() called');
        
        $companyId = session()->get('company_id');
        
        // Debug: Log company_id
        log_message('debug', 'Company ID: ' . ($companyId ?: 'NULL'));
        
        // Debug: Log request data
        log_message('debug', 'Request data: ' . json_encode($this->request->getPost()));
        
        // Debug: Check if company_id exists
        if (!$companyId) {
            return redirect()->back()->withInput()->with('error', 'Company ID not found in session. Please log in again.');
        }
        
        // Get form data
        $name = $this->request->getVar('name');
        $code = $this->request->getVar('code');
        $description = $this->request->getVar('description');
        $parentId = $this->request->getVar('parent_id') ?: null;
        $isActive = $this->request->getVar('is_active') ? 1 : 0;
        
        // Generate code if not provided
        if (empty($code)) {
            $code = $this->generateCategoryCode($name, $companyId);
        }
        
        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'code' => 'permit_empty|is_unique[material_categories.code,company_id,'.$companyId.']',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'company_id' => $companyId,
            'name' => $name,
            'code' => $code,
            'description' => $description,
            'parent_id' => $parentId,
            'is_active' => $isActive,
        ];
        
        if (!$this->categoryModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Failed to add category.');
        }
        
        return redirect()->to('/admin/material-categories')->with('success', 'Category added successfully');
    }
    
    public function edit($id)
    {
        $companyId = session()->get('company_id');
        $category = $this->categoryModel->find($id);
        
        if (!$category || $category['company_id'] != $companyId) {
            return redirect()->to('/admin/material-categories')->with('error', 'Category not found');
        }
        
        $data = [
            'title' => 'Edit Category',
            'categories' => $this->categoryModel->getCategoriesWithSubcategories($companyId),
            'category' => $category,
            'parentCategories' => $this->categoryModel->where('company_id', $companyId)
                ->where('parent_id', null)
                ->where('is_active', 1)
                ->where('id !=', $id)
                ->findAll(),
        ];
        
        return view('inventory/categories/index', $data);
    }
    
    public function update($id)
    {
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $category = $this->categoryModel->find($id);
        
        if (!$category || $category['company_id'] != $companyId) {
            return redirect()->to('/admin/material-categories')->with('error', 'Category not found');
        }
        
        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'code' => 'permit_empty|is_unique[material_categories.code,id,'.$id.',company_id,'.$companyId.']',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Prevent category from becoming its own parent
        $parentId = $this->request->getVar('parent_id') ?: null;
        if ($parentId == $id) {
            return redirect()->back()->withInput()->with('error', 'A category cannot be its own parent');
        }
        
        // Prevent circular references in category hierarchy
        if ($parentId && $this->categoryModel->isChildCategory($parentId, $id)) {
            return redirect()->back()->withInput()->with('error', 'Cannot set a child category as parent (circular reference)');
        }
        
        $data = [
            'name' => $this->request->getVar('name'),
            'code' => $this->request->getVar('code'),
            'description' => $this->request->getVar('description'),
            'parent_id' => $parentId,
            'is_active' => $this->request->getVar('is_active') ? 1 : 0,
        ];
        
        if (!$this->categoryModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Failed to update category.');
        }
        
        return redirect()->to('/admin/material-categories')->with('success', 'Category updated successfully');
    }
    
    public function delete($id)
    {
        $companyId = session()->get('company_id');
        $category = $this->categoryModel->find($id);
        
        if (!$category || $category['company_id'] != $companyId) {
            return redirect()->to('/admin/material-categories')->with('error', 'Category not found');
        }
        
        // Check if category has subcategories
        if ($this->categoryModel->where('parent_id', $id)->countAllResults() > 0) {
            return redirect()->to('/admin/material-categories')->with('error', 'Cannot delete category with subcategories');
        }
        
        // Check if category has materials
        $materialModel = new \App\Models\MaterialModel();
        if ($materialModel->where('category_id', $id)->countAllResults() > 0) {
            return redirect()->to('/admin/material-categories')->with('error', 'Cannot delete category with assigned materials');
        }
        
        if (!$this->categoryModel->delete($id)) {
            return redirect()->to('/admin/material-categories')->with('error', 'Failed to delete category');
        }
        
        return redirect()->to('/admin/material-categories')->with('success', 'Category deleted successfully');
    }
    
    /**
     * Generate a category code from the category name
     * 
     * @param string $name The category name
     * @param int $companyId The company ID
     * @return string Generated category code
     */
    private function generateCategoryCode($name, $companyId)
    {
        // Convert to uppercase and remove special characters
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $name));
        
        // Limit to 10 characters
        $code = substr($code, 0, 10);
        
        // If empty or too short, use a default
        if (empty($code) || strlen($code) < 2) {
            $code = 'CAT';
        }
        
        // Check if code already exists for this company and make it unique
        $originalCode = $code;
        $counter = 1;
        
        while ($this->categoryModel->where('company_id', $companyId)
            ->where('code', $code)
            ->countAllResults() > 0) {
            
            $code = $originalCode . $counter;
            $counter++;
            
            // Safety check to prevent infinite loop
            if ($counter > 100) {
                $code = $originalCode . '_' . time();
                break;
            }
        }
        
        return $code;
    }
}