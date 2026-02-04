<?php

namespace App\Models;

use CodeIgniter\Model;

class FileCategoryModel extends Model
{
    protected $table = 'file_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'company_id', 'name', 'description', 'color_code', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'company_id' => 'required|integer',
        'name' => 'required|string|max_length[100]|is_unique[file_categories.name,company_id,{company_id}]',
        'color_code' => 'permit_empty|regex_match[/^#[A-Fa-f0-9]{6}$/]',
    ];

    public function getCategoriesByCompany($companyId)
    {
        return $this->where('company_id', $companyId)
                    ->where('is_active', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    public function getAllCategories($companyId)
    {
        return $this->where('company_id', $companyId)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }
}
