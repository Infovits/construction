<?php

namespace App\Models;
use CodeIgniter\Model;

class EmployeeDetailModel extends Model
{
    protected $table = 'employee_details';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'department_id', 'position_id', 'hire_date', 'contract_start_date',
        'contract_end_date', 'employment_status', 'employment_type', 'basic_salary',
        'currency', 'pay_frequency', 'bank_name', 'bank_account_number', 'bank_branch',
        'tax_number', 'tax_exempt', 'annual_leave_balance', 'sick_leave_balance',
        'supervisor_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    public function getEmployeesWithDetails($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->select('employee_details.*, users.first_name, users.last_name, users.email,
                            departments.name as department_name, job_positions.title as position_title,
                            CONCAT(supervisor.first_name, " ", supervisor.last_name) as supervisor_name')
            ->join('users', 'employee_details.user_id = users.id')
            ->join('departments', 'employee_details.department_id = departments.id', 'left')
            ->join('job_positions', 'employee_details.position_id = job_positions.id', 'left')
            ->join('users supervisor', 'employee_details.supervisor_id = supervisor.id', 'left')
            ->where('users.company_id', $companyId)
            ->orderBy('users.first_name', 'ASC')
            ->findAll();
    }
}