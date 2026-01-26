<?php

namespace App\Controllers;

use App\Models\ProjectCategoryModel;

class TestController extends BaseController
{
    public function testDelete()
    {
        $categoryModel = new ProjectCategoryModel();
        
        // Test with a specific category ID (change this to an existing ID)
        $testCategoryId = 4; // Using Residential Construction category
        
        log_message('debug', '=== TEST DELETE START ===');
        log_message('debug', 'Testing delete with category ID: ' . $testCategoryId);
        
        // Check if category exists
        $category = $categoryModel->find($testCategoryId);
        log_message('debug', 'Category found: ' . ($category ? 'YES' : 'NO'));
        
        if ($category) {
            log_message('debug', 'Category name: ' . $category['name']);
            
            // Check if used by projects
            $projectModel = new \App\Models\ProjectModel();
            $projectCount = $projectModel->where('category_id', $testCategoryId)->countAllResults();
            log_message('debug', 'Project count using category: ' . $projectCount);
            
            if ($projectCount == 0) {
                log_message('debug', 'Attempting delete...');
                $result = $categoryModel->delete($testCategoryId);
                log_message('debug', 'Delete result: ' . ($result ? 'SUCCESS' : 'FAILED'));
                
                if ($result) {
                    log_message('debug', 'Category deleted successfully!');
                } else {
                    log_message('debug', 'Delete failed - checking last query...');
                    $db = \Config\Database::connect();
                    log_message('debug', 'Last query: ' . $db->getLastQuery());
                }
            } else {
                log_message('debug', 'Cannot delete - category in use');
            }
        }
        
        log_message('debug', '=== TEST DELETE END ===');
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Test completed - check logs for details'
        ]);
    }
    
    public function listCategories()
    {
        $categoryModel = new ProjectCategoryModel();
        $categories = $categoryModel->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'categories' => $categories
        ]);
    }
}