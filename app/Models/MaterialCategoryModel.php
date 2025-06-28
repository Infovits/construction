<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialCategoryModel extends Model
{
    protected $table = 'material_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'company_id', 'parent_id', 'code', 'name', 'description', 'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getCategoriesWithSubcategories($companyId)
    {
        // Get all top-level categories (no parent)
        $topCategories = $this->where('company_id', $companyId)
            ->where('parent_id IS NULL')
            ->orderBy('name', 'ASC')
            ->findAll();
            
        foreach ($topCategories as &$category) {
            // Get subcategories for each top-level category
            $category['subcategories'] = $this->where('parent_id', $category['id'])
                ->orderBy('name', 'ASC')
                ->findAll();
                
            // Get material count for this category
            $materialModel = new MaterialModel();
            $category['material_count'] = $materialModel->where('category_id', $category['id'])->countAllResults();
            
            // Get material count for each subcategory
            foreach ($category['subcategories'] as &$subcategory) {
                $subcategory['material_count'] = $materialModel->where('category_id', $subcategory['id'])->countAllResults();
            }
        }
        
        return $topCategories;
    }
    
    public function getCategoryPath($categoryId)
    {
        $category = $this->find($categoryId);
        if (!$category) {
            return 'Uncategorized';
        }
        
        if (empty($category['parent_id'])) {
            return $category['name'];
        }
        
        $parentPath = $this->getCategoryPath($category['parent_id']);
        return $parentPath . ' > ' . $category['name'];
    }
    
    public function isChildCategory($parentId, $childId)
    {
        // Check if childId is parent of parentId (circular reference)
        $category = $this->find($parentId);
        
        if (!$category) {
            return false;
        }
        
        if ($category['parent_id'] == $childId) {
            return true;
        }
        
        if ($category['parent_id']) {
            return $this->isChildCategory($category['parent_id'], $childId);
        }
        
        return false;
    }
}
