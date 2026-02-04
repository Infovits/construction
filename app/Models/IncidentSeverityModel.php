<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentSeverityModel extends Model
{
    protected $table = 'incident_severity_levels';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'company_id', 'name', 'description', 'color_code',
        'numeric_level', 'requires_immediate_action',
        'requires_reporting', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'company_id' => 'required|integer',
        'name' => 'required|string|max_length[100]',
        'numeric_level' => 'required|integer',
    ];

    public function getActiveSeverities($companyId)
    {
        return $this->where('company_id', $companyId)
                    ->where('is_active', 1)
                    ->orderBy('numeric_level', 'DESC')
                    ->findAll();
    }

    public function getAllSeverities($companyId)
    {
        return $this->where('company_id', $companyId)
                    ->orderBy('numeric_level', 'DESC')
                    ->findAll();
    }

    public function getSeverityById($severityId, $companyId)
    {
        return $this->where('id', $severityId)
                    ->where('company_id', $companyId)
                    ->first();
    }
}
