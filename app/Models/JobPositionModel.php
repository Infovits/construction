<?php

namespace App\Models;

use CodeIgniter\Model;

class JobPositionModel extends Model
{
    protected $table = 'job_positions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id', 'department_id', 'title', 'code', 'description',
        'requirements', 'employment_type', 'min_salary', 'max_salary', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getActivePositions($departmentId = null)
    {
        $companyId = session('company_id');

        $builder = $this->select('job_positions.*, departments.name as department_name,
                                COUNT(employee_details.user_id) as employee_count')
            ->join('departments', 'job_positions.department_id = departments.id', 'left')
            ->join('employee_details', 'job_positions.id = employee_details.position_id', 'left')
            ->where('job_positions.company_id', $companyId)
            ->where('job_positions.is_active', 1)
            ->groupBy('job_positions.id')
            ->orderBy('job_positions.title', 'ASC');

        if ($departmentId) {
            $builder->where('job_positions.department_id', $departmentId);
        }

        return $builder->findAll();
    }

    public function getPositionsWithFilters($search = null, $department = null, $employment_type = null)
    {
        $builder = $this->select('job_positions.*, departments.name as department_name,
                                COUNT(employee_details.user_id) as employee_count')
            ->join('departments', 'job_positions.department_id = departments.id', 'left')
            ->join('employee_details', 'job_positions.id = employee_details.position_id', 'left')
            ->where('job_positions.company_id', session('company_id'))
            ->groupBy('job_positions.id');

        if ($search) {
            $builder->groupStart()
                ->like('job_positions.title', $search)
                ->orLike('job_positions.code', $search)
                ->orLike('job_positions.description', $search)
                ->groupEnd();
        }

        if ($department) {
            $builder->where('job_positions.department_id', $department);
        }

        if ($employment_type) {
            $builder->where('job_positions.employment_type', $employment_type);
        }

        return $builder->orderBy('departments.name', 'ASC')
                      ->orderBy('job_positions.title', 'ASC')
                      ->findAll();
    }

    public function getEmploymentTypes()
    {
        return [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'temporary' => 'Temporary',
            'intern' => 'Intern'
        ];
    }

    public function getPositionsWithDepartments()
    {
        return $this->select('job_positions.id, job_positions.title, departments.name as department_name')
            ->join('departments', 'job_positions.department_id = departments.id', 'left')
            ->where('job_positions.company_id', session('company_id'))
            ->where('job_positions.is_active', 1)
            ->orderBy('departments.name', 'ASC')
            ->orderBy('job_positions.title', 'ASC')
            ->findAll();
    }
}
