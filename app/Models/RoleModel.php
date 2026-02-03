<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id', 'name', 'slug', 'description', 'is_system_role', 'permissions'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getRolesWithUserCount($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->select('roles.*, COUNT(user_roles.user_id) as user_count')
            ->join('user_roles', 'roles.id = user_roles.role_id', 'left')
            ->where('roles.company_id', $companyId)
            ->groupBy('roles.id')
            ->orderBy('roles.created_at', 'DESC')
            ->findAll();
    }

    public function getActiveRoles($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->where('company_id', $companyId)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getUserPermissions($userId)
    {
        $userRole = $this->db->table('user_roles')
            ->select('roles.permissions')
            ->join('roles', 'user_roles.role_id = roles.id')
            ->where('user_roles.user_id', $userId)
            ->get()
            ->getRowArray();

        if ($userRole) {
            return json_decode($userRole['permissions'], true) ?: [];
        }

        return [];
    }

    public function getDefaultPermissions()
    {
        return [
            'Dashboard' => [
                'dashboard.view' => 'View Dashboard',
                'dashboard.analytics' => 'View Analytics'
            ],
            'Users & Roles' => [
                'users.view' => 'View Users',
                'users.create' => 'Create Users',
                'users.edit' => 'Edit Users',
                'users.delete' => 'Delete Users',
                'roles.view' => 'View Roles',
                'roles.create' => 'Create Roles',
                'roles.edit' => 'Edit Roles',
                'roles.delete' => 'Delete Roles'
            ],
            'Projects' => [
                'projects.view' => 'View Projects',
                'projects.create' => 'Create Projects',
                'projects.edit' => 'Edit Projects',
                'projects.delete' => 'Delete Projects',
                'projects.manage_team' => 'Manage Project Teams',
                'projects.view_financials' => 'View Project Financials'
            ],
            'Tasks' => [
                'tasks.view' => 'View Tasks',
                'tasks.create' => 'Create Tasks',
                'tasks.edit' => 'Edit Tasks',
                'tasks.delete' => 'Delete Tasks',
                'tasks.assign' => 'Assign Tasks',
                'tasks.approve' => 'Approve Tasks'
            ],
            'Inventory' => [
                'inventory.view' => 'View Inventory',
                'inventory.create' => 'Add Inventory Items',
                'inventory.edit' => 'Edit Inventory',
                'inventory.delete' => 'Delete Inventory',
                'inventory.stock_movement' => 'Manage Stock Movements',
                'inventory.reports' => 'View Inventory Reports'
            ],
            'Accounting' => [
                'accounting.view' => 'View Accounting',
                'accounting.create' => 'Create Entries',
                'accounting.edit' => 'Edit Entries',
                'accounting.delete' => 'Delete Entries',
                'accounting.invoices' => 'Manage Invoices',
                'accounting.payments' => 'Manage Payments',
                'accounting.reports' => 'View Financial Reports'
            ],
            'HR & Payroll' => [
                'hr.view' => 'View HR Data',
                'hr.create' => 'Create HR Records',
                'hr.edit' => 'Edit HR Records',
                'hr.attendance' => 'Manage Attendance',
                'hr.leave' => 'Manage Leave',
                'hr.payroll' => 'Manage Payroll',
                'hr.reports' => 'View HR Reports'
            ],
            'Equipment & Assets' => [
                'assets.view' => 'View Assets',
                'assets.create' => 'Add Assets',
                'assets.edit' => 'Edit Assets',
                'assets.delete' => 'Delete Assets',
                'assets.assign' => 'Assign Assets',
                'assets.maintenance' => 'Manage Maintenance'
            ],
            'Safety & Incidents' => [
                'safety.view' => 'View Safety Reports',
                'safety.create' => 'Create Safety Reports',
                'safety.edit' => 'Edit Safety Reports',
                'safety.inspections' => 'Manage Inspections',
                'safety.incidents' => 'Manage Incidents'
            ],
            'Files & Documents' => [
                'files.view' => 'View Files',
                'files.upload' => 'Upload Files',
                'files.edit' => 'Edit Files',
                'files.delete' => 'Delete Files',
                'files.share' => 'Share Files'
            ],
            'Reports' => [
                'reports.view' => 'View Reports',
                'reports.create' => 'Create Custom Reports',
                'reports.export' => 'Export Reports'
            ],
            'Messaging & Notifications' => [
                'messages.view' => 'View Messages',
                'messages.create' => 'Send Messages',
                'notifications.view' => 'View Notifications'
            ],
            'Settings' => [
                'settings.view' => 'View Settings',
                'settings.edit' => 'Edit Settings',
                'settings.system' => 'System Settings',
                'settings.company' => 'Company Settings'
            ]
        ];
    }

    public function createDefaultRoles($companyId)
    {
        $defaultRoles = [
            [
                'company_id' => $companyId,
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Full system access with all permissions',
                'is_system_role' => true,
                'permissions' => json_encode(['*'])
            ],
            [
                'company_id' => $companyId,
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrative access to most modules',
                'is_system_role' => true,
                'permissions' => json_encode([
                    'dashboard.*', 'users.*', 'projects.*', 'tasks.*', 'inventory.*',
                    'accounting.*', 'hr.*', 'assets.*', 'safety.*', 'files.*', 'reports.*',
                    'messages.*', 'notifications.view'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'Project Manager',
                'slug' => 'project_manager',
                'description' => 'Manage projects, tasks, and team members',
                'is_system_role' => true,
                'permissions' => json_encode([
                    'dashboard.view', 'projects.*', 'tasks.*', 'files.view', 'files.upload',
                    'reports.view', 'users.view', 'inventory.view', 'safety.view',
                    'messages.view', 'messages.create', 'notifications.view'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'Site Supervisor',
                'slug' => 'site_supervisor',
                'description' => 'On-site management and safety oversight',
                'is_system_role' => true,
                'permissions' => json_encode([
                    'dashboard.view', 'projects.view', 'tasks.view', 'tasks.edit',
                    'attendance.*', 'safety.*', 'assets.view', 'files.view',
                    'messages.view', 'messages.create', 'notifications.view'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'Accountant',
                'slug' => 'accountant',
                'description' => 'Financial management and accounting',
                'is_system_role' => true,
                'permissions' => json_encode([
                    'dashboard.view', 'accounting.*', 'hr.payroll', 'projects.view_financials',
                    'reports.view', 'reports.export'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'HR Manager',
                'slug' => 'hr_manager',
                'description' => 'Human resources and payroll management',
                'is_system_role' => true,
                'permissions' => json_encode([
                    'dashboard.view', 'hr.*', 'users.view', 'users.create', 'users.edit',
                    'reports.view', 'reports.export'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'Inventory Manager',
                'slug' => 'inventory_manager',
                'description' => 'Manage inventory, stock, and suppliers',
                'is_system_role' => true,
                'permissions' => json_encode([
                    'dashboard.view', 'inventory.*', 'projects.view', 'reports.view'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'Employee',
                'slug' => 'employee',
                'description' => 'Basic employee access',
                'is_system_role' => true,
                'permissions' => json_encode([
                    'dashboard.view', 'tasks.view', 'projects.view', 'files.view',
                    'profile.edit', 'attendance.own', 'leave.own'
                ])
            ]
        ];

        foreach ($defaultRoles as $role) {
            $this->insert($role);
        }

        return true;
    }

    public function getRolesWithFilters($search = null, $type = null)
    {
        $builder = $this->select('roles.*, COUNT(user_roles.user_id) as user_count')
            ->join('user_roles', 'roles.id = user_roles.role_id', 'left')
            ->where('roles.company_id', session('company_id'))
            ->groupBy('roles.id');

        if ($search) {
            $builder->groupStart()
                ->like('roles.name', $search)
                ->orLike('roles.slug', $search)
                ->orLike('roles.description', $search)
                ->groupEnd();
        }

        if ($type) {
            if ($type === 'system') {
                $builder->where('roles.is_system_role', true);
            } elseif ($type === 'custom') {
                $builder->where('roles.is_system_role', false);
            }
        }

        return $builder->orderBy('roles.is_system_role', 'DESC')
                      ->orderBy('roles.name', 'ASC')
                      ->findAll();
    }
}