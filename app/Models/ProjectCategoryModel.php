<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectCategoryModel extends Model
{
    protected $table = 'project_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id', 'name', 'description', 'color_code', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'company_id' => 'required|numeric',
        'name' => 'required|min_length[3]|max_length[100]'
    ];

    public function getActiveCategories($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getCategoriesWithCompany($companyId = null)
    {
        $builder = $this->select('project_categories.*, companies.name as company_name')
                        ->join('companies', 'companies.id = project_categories.company_id', 'left');
        
        if ($companyId) {
            $builder->where('project_categories.company_id', $companyId);
        }
        
        return $builder->where('project_categories.is_active', 1)
                      ->orderBy('project_categories.name', 'ASC')
                      ->findAll();
    }
}