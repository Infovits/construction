<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentTypeModel extends Model
{
    protected $table = 'incident_types';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'company_id', 'name', 'description', 'icon', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'company_id' => 'required|integer',
        'name' => 'required|string|max_length[100]',
    ];

    public function getActiveTypes($companyId)
    {
        return $this->where('company_id', $companyId)
                    ->where('is_active', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    public function getAllTypes($companyId)
    {
        return $this->where('company_id', $companyId)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }
}
