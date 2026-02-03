<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id', 'employee_id', 'username', 'email', 'password',
        'first_name', 'last_name', 'middle_name', 'phone', 'mobile',
        'date_of_birth', 'gender', 'national_id', 'passport_number',
        'address', 'city', 'emergency_contact_name', 'emergency_contact_phone',
        'profile_photo_url', 'status', 'is_verified', 'last_login_at',
        'password_changed_at', 'two_factor_enabled', 'two_factor_secret'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name' => 'required|min_length[2]|max_length[100]'
    ];

    public function getUsersWithRoles($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->select('users.*, roles.name as role_name, roles.slug as role_slug,
                            departments.name as department_name, job_positions.title as position_title,
                            employee_details.employment_status, employee_details.hire_date')
            ->join('user_roles', 'users.id = user_roles.user_id', 'left')
            ->join('roles', 'user_roles.role_id = roles.id', 'left')
            ->join('employee_details', 'users.id = employee_details.user_id', 'left')
            ->join('departments', 'employee_details.department_id = departments.id', 'left')
            ->join('job_positions', 'employee_details.position_id = job_positions.id', 'left')
            ->where('users.company_id', $companyId)
            ->orderBy('users.created_at', 'DESC')
            ->findAll();
    }

    public function getUsersWithFilters($search = null, $status = null, $role = null, $department = null)
    {
        $builder = $this->select('users.*, roles.name as role_name, roles.slug as role_slug,
                                 departments.name as department_name, job_positions.title as position_title,
                                 employee_details.employment_status, employee_details.hire_date,
                                 employee_details.basic_salary')
            ->join('user_roles', 'users.id = user_roles.user_id', 'left')
            ->join('roles', 'user_roles.role_id = roles.id', 'left')
            ->join('employee_details', 'users.id = employee_details.user_id', 'left')
            ->join('departments', 'employee_details.department_id = departments.id', 'left')
            ->join('job_positions', 'employee_details.position_id = job_positions.id', 'left')
            ->where('users.company_id', session('company_id'));

        if ($search) {
            $builder->groupStart()
                ->like('users.first_name', $search)
                ->orLike('users.last_name', $search)
                ->orLike('users.username', $search)
                ->orLike('users.email', $search)
                ->orLike('users.employee_id', $search)
                ->groupEnd();
        }

        if ($status) {
            $builder->where('users.status', $status);
        }

        if ($role) {
            $builder->where('roles.id', $role);
        }

        if ($department) {
            $builder->where('departments.id', $department);
        }

        return $builder->orderBy('users.created_at', 'DESC')->findAll();
    }

    public function getUserWithDetails($userId)
    {
        return $this->select('users.*, roles.name as role_name, roles.id as role_id,
                            departments.name as department_name, departments.id as department_id,
                            job_positions.title as position_title, job_positions.id as position_id,
                            employee_details.*')
            ->join('user_roles', 'users.id = user_roles.user_id', 'left')
            ->join('roles', 'user_roles.role_id = roles.id', 'left')
            ->join('employee_details', 'users.id = employee_details.user_id', 'left')
            ->join('departments', 'employee_details.department_id = departments.id', 'left')
            ->join('job_positions', 'employee_details.position_id = job_positions.id', 'left')
            ->where('users.id', $userId)
            ->first();
    }

    public function getUserRole($userId)
    {
        return $this->db->table('user_roles')
            ->select('user_roles.*, roles.name as role_name, roles.permissions')
            ->join('roles', 'user_roles.role_id = roles.id')
            ->where('user_roles.user_id', $userId)
            ->get()
            ->getRowArray();
    }

    public function assignRole($data)
    {
        return $this->db->table('user_roles')->insert($data);
    }

    public function updateUserRole($userId, $roleId)
    {
        // Remove existing role
        $this->db->table('user_roles')->where('user_id', $userId)->delete();

        // Assign new role
        return $this->assignRole([
            'user_id' => $userId,
            'role_id' => $roleId,
            'assigned_by' => session('user_id'),
            'assigned_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getUserStats($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        $total = $this->where('company_id', $companyId)->countAllResults();
        $active = $this->where('company_id', $companyId)->where('status', 'active')->countAllResults();
        $inactive = $this->where('company_id', $companyId)->where('status', 'inactive')->countAllResults();
        $newThisMonth = $this->where('company_id', $companyId)
            ->where('created_at >=', date('Y-m-01'))
            ->countAllResults();

        // Get role distribution
        $roleDistribution = $this->select('roles.name, COUNT(users.id) as count')
            ->join('user_roles', 'users.id = user_roles.user_id', 'left')
            ->join('roles', 'user_roles.role_id = roles.id', 'left')
            ->where('users.company_id', $companyId)
            ->groupBy('roles.id')
            ->get()
            ->getResultArray();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'new_this_month' => $newThisMonth,
            'role_distribution' => $roleDistribution
        ];
    }

    public function getPotentialSupervisors($excludeUserId = null)
    {
        $builder = $this->select('users.id, users.first_name, users.last_name, users.email, roles.name as role_name')
            ->join('user_roles', 'users.id = user_roles.user_id')
            ->join('roles', 'user_roles.role_id = roles.id')
            ->where('users.company_id', session('company_id'))
            ->where('users.status', 'active')
            ->whereIn('roles.slug', ['admin', 'project_manager', 'site_supervisor']);

        if ($excludeUserId) {
            $builder->where('users.id !=', $excludeUserId);
        }

        return $builder->findAll();
    }

    public function getUsersForExport($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->select('users.employee_id, users.first_name, users.last_name, users.email, 
                            users.phone, users.status, roles.name as role_name, 
                            departments.name as department_name, job_positions.title as position_title,
                            employee_details.hire_date, employee_details.employment_status,
                            employee_details.basic_salary, users.created_at')
            ->join('user_roles', 'users.id = user_roles.user_id', 'left')
            ->join('roles', 'user_roles.role_id = roles.id', 'left')
            ->join('employee_details', 'users.id = employee_details.user_id', 'left')
            ->join('departments', 'employee_details.department_id = departments.id', 'left')
            ->join('job_positions', 'employee_details.position_id = job_positions.id', 'left')
            ->where('users.company_id', $companyId)
            ->orderBy('users.created_at', 'DESC')
            ->findAll();
    }

    public function hasPermission($userId, $permission)
    {
        $userRole = $this->getUserRole($userId);
        
        if (!$userRole) {
            return false;
        }

        $permissions = json_decode($userRole['permissions'], true) ?: [];

        // Check for wildcard permission
        if (in_array('*', $permissions)) {
            return true;
        }

        // Check for exact permission
        if (in_array($permission, $permissions)) {
            return true;
        }

        // Check for wildcard module permission (e.g., users.*)
        $parts = explode('.', $permission);
        if (count($parts) > 1) {
            $moduleWildcard = $parts[0] . '.*';
            if (in_array($moduleWildcard, $permissions)) {
                return true;
            }
        }

        return false;
    }

    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function getUserByEmailOrUsername($login)
    {
        return $this->groupStart()
            ->where('email', $login)
            ->orWhere('username', $login)
            ->groupEnd()
            ->first();
    }

    public function updateLastLogin($userId)
    {
        return $this->update($userId, [
            'last_login_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getActiveEmployees($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');
        
        return $this->select('users.id, users.first_name, users.last_name, users.email, users.employee_id, 
                            CONCAT(users.first_name, " ", users.last_name) as full_name,
                            departments.name as department_name, job_positions.title as position_title')
            ->join('employee_details', 'users.id = employee_details.user_id', 'left')
            ->join('departments', 'employee_details.department_id = departments.id', 'left')
            ->join('job_positions', 'employee_details.position_id = job_positions.id', 'left')
            ->where('users.company_id', $companyId)
            ->where('users.status', 'active')
            ->groupStart()
                ->where('employee_details.employment_status', 'active')
                ->orWhere('employee_details.employment_status IS NULL')
            ->groupEnd()
            ->orderBy('users.first_name', 'ASC')
            ->findAll();
    }

    public function getDailyActiveCount($companyId)
    {
        $today = date('Y-m-d');
        $startOfDay = $today . ' 00:00:00';
        $endOfDay = $today . ' 23:59:59';

        // Count users who sent messages or accessed conversations today
        $db = $this->db;
        $result = $db->query("
            SELECT COUNT(DISTINCT m.sender_id) as active_count
            FROM messages m
            JOIN users u ON u.id = m.sender_id
            WHERE u.company_id = ?
            AND DATE(m.created_at) = ?
        ", [$companyId, $today])->getRow();

        return $result ? intval($result->active_count) : 0;
    }
}
