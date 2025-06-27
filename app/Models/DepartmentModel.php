<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id', 'code', 'name', 'description', 'manager_id',
        'parent_department_id', 'budget', 'status'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getActiveDepartments($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->select('departments.*, CONCAT(users.first_name, " ", users.last_name) as manager_name,
                            COUNT(employee_details.user_id) as employee_count')
            ->join('users', 'departments.manager_id = users.id', 'left')
            ->join('employee_details', 'departments.id = employee_details.department_id', 'left')
            ->where('departments.company_id', $companyId)
            ->where('departments.status', 'active')
            ->groupBy('departments.id')
            ->orderBy('departments.name', 'ASC')
            ->findAll();
    }

    public function getDepartmentHierarchy($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        $departments = $this->where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();

        return $this->buildTree($departments);
    }

    private function buildTree($departments, $parentId = null)
    {
        $tree = [];
        foreach ($departments as $department) {
            if ($department['parent_department_id'] == $parentId) {
                $department['children'] = $this->buildTree($departments, $department['id']);
                $tree[] = $department;
            }
        }
        return $tree;
    }

    public function getDepartmentsWithFilters($search = null, $status = null)
    {
        $builder = $this->select('departments.*, CONCAT(users.first_name, " ", users.last_name) as manager_name,
                                 parent_dept.name as parent_name,
                                 COUNT(employee_details.user_id) as employee_count')
            ->join('users', 'departments.manager_id = users.id', 'left')
            ->join('departments as parent_dept', 'departments.parent_department_id = parent_dept.id', 'left')
            ->join('employee_details', 'departments.id = employee_details.department_id', 'left')
            ->where('departments.company_id', session('company_id'))
            ->groupBy('departments.id');

        if ($search) {
            $builder->groupStart()
                ->like('departments.name', $search)
                ->orLike('departments.code', $search)
                ->orLike('departments.description', $search)
                ->groupEnd();
        }

        if ($status) {
            $builder->where('departments.status', $status);
        }

        return $builder->orderBy('departments.name', 'ASC')->findAll();
    }
}
