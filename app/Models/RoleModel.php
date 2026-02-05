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
                'users.reset_password' => 'Reset User Passwords',
                'users.manage_roles' => 'Assign Roles to Users',
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
                'projects.view_financials' => 'View Project Financials',
                'projects.clone' => 'Clone Projects'
            ],
            'Project Categories' => [
                'project_categories.view' => 'View Project Categories',
                'project_categories.create' => 'Create Project Categories',
                'project_categories.edit' => 'Edit Project Categories',
                'project_categories.delete' => 'Delete Project Categories',
                'project_categories.toggle' => 'Toggle Project Categories'
            ],
            'Clients' => [
                'clients.view' => 'View Clients',
                'clients.create' => 'Create Clients',
                'clients.edit' => 'Edit Clients',
                'clients.delete' => 'Delete Clients',
                'clients.toggle' => 'Toggle Client Status',
                'clients.export' => 'Export Clients'
            ],
            'Milestones' => [
                'milestones.view' => 'View Milestones',
                'milestones.create' => 'Create Milestones',
                'milestones.edit' => 'Edit Milestones',
                'milestones.delete' => 'Delete Milestones',
                'milestones.update_progress' => 'Update Progress',
                'milestones.complete' => 'Complete Milestones',
                'milestones.reports' => 'View Milestone Reports',
                'milestones.export' => 'Export Milestones'
            ],
            'Tasks' => [
                'tasks.view' => 'View Tasks',
                'tasks.create' => 'Create Tasks',
                'tasks.edit' => 'Edit Tasks',
                'tasks.delete' => 'Delete Tasks',
                'tasks.assign' => 'Assign Tasks',
                'tasks.approve' => 'Approve Tasks',
                'tasks.update_status' => 'Update Task Status',
                'tasks.comments' => 'Add Task Comments',
                'tasks.log_time' => 'Log Time',
                'tasks.attachments' => 'Manage Attachments',
                'tasks.reports' => 'View Task Reports',
                'tasks.export' => 'Export Tasks'
            ],
            'Materials & Inventory' => [
                'materials.view' => 'View Materials',
                'materials.create' => 'Add Materials',
                'materials.edit' => 'Edit Materials',
                'materials.delete' => 'Delete Materials',
                'materials.stock_movement' => 'Record Stock Movements',
                'materials.reports' => 'View Material Reports',
                'materials.barcode' => 'Use Barcode Scanner',
                'materials.low_stock' => 'Manage Low Stock Alerts',
                'materials.optimize' => 'Optimize Stock Levels'
            ],
            'Material Categories' => [
                'material_categories.view' => 'View Material Categories',
                'material_categories.create' => 'Create Material Categories',
                'material_categories.edit' => 'Edit Material Categories',
                'material_categories.delete' => 'Delete Material Categories'
            ],
            'Warehouses' => [
                'warehouses.view' => 'View Warehouses',
                'warehouses.create' => 'Create Warehouses',
                'warehouses.edit' => 'Edit Warehouses',
                'warehouses.delete' => 'Delete Warehouses',
                'warehouses.view_stock' => 'View Warehouse Stock',
                'warehouses.add_stock' => 'Add Stock',
                'warehouses.reports' => 'View Warehouse Reports'
            ],
            'Suppliers' => [
                'suppliers.view' => 'View Suppliers',
                'suppliers.create' => 'Create Suppliers',
                'suppliers.edit' => 'Edit Suppliers',
                'suppliers.delete' => 'Delete Suppliers',
                'suppliers.rate' => 'Rate Suppliers',
                'suppliers.manage_materials' => 'Manage Supplier Materials',
                'suppliers.deliveries' => 'Manage Deliveries'
            ],
            'Procurement' => [
                'procurement.view' => 'View Procurement',
                'procurement.reports' => 'View Procurement Reports',
                'procurement.export' => 'Export Procurement Data'
            ],
            'Material Requests' => [
                'material_requests.view' => 'View Material Requests',
                'material_requests.create' => 'Create Material Requests',
                'material_requests.edit' => 'Edit Material Requests',
                'material_requests.delete' => 'Delete Material Requests',
                'material_requests.submit' => 'Submit Requests',
                'material_requests.approve' => 'Approve Requests',
                'material_requests.reject' => 'Reject Requests'
            ],
            'Purchase Orders' => [
                'purchase_orders.view' => 'View Purchase Orders',
                'purchase_orders.create' => 'Create Purchase Orders',
                'purchase_orders.edit' => 'Edit Purchase Orders',
                'purchase_orders.delete' => 'Delete Purchase Orders',
                'purchase_orders.approve' => 'Approve Purchase Orders',
                'purchase_orders.acknowledge' => 'Acknowledge Orders',
                'purchase_orders.cancel' => 'Cancel Purchase Orders'
            ],
            'Goods Receipt' => [
                'goods_receipt.view' => 'View Goods Receipts',
                'goods_receipt.create' => 'Create Goods Receipts',
                'goods_receipt.edit' => 'Edit Goods Receipts',
                'goods_receipt.accept' => 'Accept Goods',
                'goods_receipt.reject' => 'Reject Goods',
                'goods_receipt.pdf' => 'Generate PDF'
            ],
            'Quality Inspections' => [
                'quality_inspections.view' => 'View Quality Inspections',
                'quality_inspections.create' => 'Create Inspections',
                'quality_inspections.edit' => 'Edit Inspections',
                'quality_inspections.delete' => 'Delete Inspections',
                'quality_inspections.inspect' => 'Perform Inspections',
                'quality_inspections.complete' => 'Complete Inspections',
                'quality_inspections.export' => 'Export Inspection Data'
            ],
            'Accounting' => [
                'accounting.view' => 'View Accounting',
                'accounting.reports' => 'View Financial Reports'
            ],
            'Account Categories' => [
                'account_categories.view' => 'View Account Categories',
                'account_categories.create' => 'Create Account Categories',
                'account_categories.edit' => 'Edit Account Categories',
                'account_categories.delete' => 'Delete Account Categories',
                'account_categories.toggle' => 'Toggle Categories'
            ],
            'Chart of Accounts' => [
                'chart_of_accounts.view' => 'View Chart of Accounts',
                'chart_of_accounts.create' => 'Create Accounts',
                'chart_of_accounts.edit' => 'Edit Accounts',
                'chart_of_accounts.delete' => 'Delete Accounts',
                'chart_of_accounts.toggle' => 'Toggle Accounts'
            ],
            'Journal Entries' => [
                'journal_entries.view' => 'View Journal Entries',
                'journal_entries.create' => 'Create Entries',
                'journal_entries.edit' => 'Edit Entries',
                'journal_entries.delete' => 'Delete Entries',
                'journal_entries.post' => 'Post Entries',
                'journal_entries.reverse' => 'Reverse Entries'
            ],
            'General Ledger' => [
                'general_ledger.view' => 'View General Ledger',
                'general_ledger.account' => 'View Account Details',
                'general_ledger.trial_balance' => 'View Trial Balance'
            ],
            'Cost Codes' => [
                'cost_codes.view' => 'View Cost Codes',
                'cost_codes.create' => 'Create Cost Codes',
                'cost_codes.edit' => 'Edit Cost Codes',
                'cost_codes.delete' => 'Delete Cost Codes',
                'cost_codes.toggle' => 'Toggle Cost Codes'
            ],
            'Job Budgets' => [
                'job_budgets.view' => 'View Job Budgets',
                'job_budgets.create' => 'Create Budgets',
                'job_budgets.edit' => 'Edit Budgets',
                'job_budgets.delete' => 'Delete Budgets'
            ],
            'Job Cost Tracking' => [
                'job_cost_tracking.view' => 'View Job Costs',
                'job_cost_tracking.create' => 'Create Cost Entries',
                'job_cost_tracking.edit' => 'Edit Cost Entries',
                'job_cost_tracking.delete' => 'Delete Cost Entries',
                'job_cost_tracking.project_summary' => 'View Project Summary'
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
            'Departments' => [
                'departments.view' => 'View Departments',
                'departments.create' => 'Create Departments',
                'departments.edit' => 'Edit Departments',
                'departments.delete' => 'Delete Departments',
                'departments.toggle' => 'Toggle Departments'
            ],
            'Positions' => [
                'positions.view' => 'View Positions',
                'positions.create' => 'Create Positions',
                'positions.edit' => 'Edit Positions',
                'positions.delete' => 'Delete Positions',
                'positions.toggle' => 'Toggle Positions'
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
            'Overview & Analytics' => [
                'overview.view' => 'View Overview',
                'analytics.view' => 'View Analytics',
                'analytics.reports' => 'Generate Analytics Reports'
            ],
            'Reports' => [
                'reports.view' => 'View Reports',
                'reports.create' => 'Create Custom Reports',
                'reports.generate' => 'Generate Reports',
                'reports.export' => 'Export Reports'
            ],
            'Messaging' => [
                'messages.view' => 'View Messages',
                'messages.create' => 'Send Messages',
                'messages.delete' => 'Delete Messages'
            ],
            'Notifications' => [
                'notifications.view' => 'View Notifications',
                'notifications.manage' => 'Manage Notifications'
            ],
            'Settings' => [
                'settings.view' => 'View Settings',
                'settings.edit' => 'Edit Settings',
                'settings.general' => 'General Settings',
                'settings.security' => 'Security Settings',
                'settings.preferences' => 'Preferences',
                'settings.integrations' => 'Integrations',
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