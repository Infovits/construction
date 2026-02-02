<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\CompanyModel;
use App\Models\DepartmentModel;
use App\Models\JobPositionModel;
use App\Models\EmployeeDetailModel;

class Users extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $companyModel;
    protected $departmentModel;
    protected $jobPositionModel;
    protected $employeeDetailModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->companyModel = new CompanyModel();
        $this->departmentModel = new DepartmentModel();
        $this->jobPositionModel = new JobPositionModel();
        $this->employeeDetailModel = new EmployeeDetailModel();
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        helper(['form', 'url', 'utility']);
    }

    public function create()
    {
        // Ensure session has company_id
        if (!session('company_id')) {
            // Set default company_id for testing - remove in production
            $this->session->set('company_id', 1);
            $this->session->set('user_id', 1); // Set default user_id for testing
        }

        $data = [
            'title' => 'Add New User',
            'pageTitle' => 'Add New User',
            'roles' => $this->roleModel->getActiveRoles(),
            'departments' => $this->departmentModel->getActiveDepartments(),
            'positions' => $this->jobPositionModel->getActivePositions(),
            'validation' => \Config\Services::validation(),
            'employmentTypes' => $this->getEmploymentTypes(),
            'genderOptions' => $this->getGenderOptions()
        ];

        return view('admin/users/create', $data);
    }

    public function store()
    {
        // Ensure this is a POST request
        if (!$this->request->getMethod() === 'post') {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        // Debug logging
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'Users::store method called');
            log_message('debug', 'Request method: ' . $this->request->getMethod());
            log_message('debug', 'Company ID: ' . (session('company_id') ?: 'NOT SET'));
        }

        // Ensure session has company_id
        if (!session('company_id')) {
            $this->session->set('company_id', 1); // Default for testing
            $this->session->set('user_id', 1); // Default for testing
        }

        // Validation rules
        $rules = [
            'username' => [
                'rules' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
                'errors' => [
                    'required' => 'Username is required',
                    'min_length' => 'Username must be at least 3 characters',
                    'max_length' => 'Username cannot exceed 100 characters',
                    'is_unique' => 'Username already exists'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'Please enter a valid email address',
                    'is_unique' => 'Email already exists'
                ]
            ],
            'first_name' => [
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'First name is required',
                    'min_length' => 'First name must be at least 2 characters'
                ]
            ],
            'last_name' => [
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'Last name is required',
                    'min_length' => 'Last name must be at least 2 characters'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'Password is required',
                    'min_length' => 'Password must be at least 8 characters'
                ]
            ],
            'password_confirm' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Password confirmation is required',
                    'matches' => 'Passwords do not match'
                ]
            ],
            'phone' => [
                'rules' => 'permit_empty|min_length[10]|max_length[20]',
                'errors' => [
                    'min_length' => 'Phone number must be at least 10 characters'
                ]
            ],
            'role_id' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Role is required',
                    'numeric' => 'Invalid role selected'
                ]
            ],
            'department_id' => 'permit_empty|numeric',
            'position_id' => 'permit_empty|numeric',
            'hire_date' => 'permit_empty|valid_date',
            'basic_salary' => 'permit_empty|numeric|greater_than_equal_to[0]'
        ];

        // Perform validation
        if (!$this->validate($rules)) {
            if (ENVIRONMENT === 'development') {
                $errors = $this->validator->getErrors();
                log_message('debug', 'Validation failed. Errors: ' . print_r($errors, true));
            }
            
            // Store validation errors in session
            $this->session->setFlashdata('validation', \Config\Services::validation());
            
            // Return to create form with validation errors
            return redirect()->back()->withInput();
        }

        // Start database transaction
        $this->db->transStart();

        try {
            // Generate employee ID
            $employeeId = $this->generateEmployeeId();

            // Prepare user data
            $userData = [
                'company_id' => session('company_id'),
                'employee_id' => $employeeId,
                'username' => trim($this->request->getPost('username')),
                'email' => trim($this->request->getPost('email')),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'first_name' => trim($this->request->getPost('first_name')),
                'last_name' => trim($this->request->getPost('last_name')),
                'middle_name' => trim($this->request->getPost('middle_name')) ?: null,
                'phone' => trim($this->request->getPost('phone')) ?: null,
                'mobile' => trim($this->request->getPost('mobile')) ?: null,
                'date_of_birth' => $this->request->getPost('date_of_birth') ?: null,
                'gender' => $this->request->getPost('gender') ?: null,
                'national_id' => trim($this->request->getPost('national_id')) ?: null,
                'address' => trim($this->request->getPost('address')) ?: null,
                'city' => trim($this->request->getPost('city')) ?: null,
                'emergency_contact_name' => trim($this->request->getPost('emergency_contact_name')) ?: null,
                'emergency_contact_phone' => trim($this->request->getPost('emergency_contact_phone')) ?: null,
                'status' => 'active',
                'is_verified' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Insert user
            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                throw new \Exception('Failed to create user: ' . implode(', ', $this->userModel->errors()));
            }

            // Assign role to user
            $roleData = [
                'user_id' => $userId,
                'role_id' => (int)$this->request->getPost('role_id'),
                'assigned_by' => session('user_id'),
                'assigned_at' => date('Y-m-d H:i:s')
            ];

            $roleInserted = $this->db->table('user_roles')->insert($roleData);
            
            if (!$roleInserted) {
                throw new \Exception('Failed to assign role to user');
            }

            // Create employee details if any employment data provided
            $departmentId = $this->request->getPost('department_id');
            $positionId = $this->request->getPost('position_id');
            $hireDate = $this->request->getPost('hire_date');
            $basicSalary = $this->request->getPost('basic_salary');

            if ($departmentId || $positionId || $hireDate || $basicSalary) {
                $employeeData = [
                    'user_id' => $userId,
                    'department_id' => $departmentId ?: null,
                    'position_id' => $positionId ?: null,
                    'hire_date' => $hireDate ?: date('Y-m-d'),
                    'employment_type' => $this->request->getPost('employment_type') ?: 'full_time',
                    'employment_status' => 'active',
                    'basic_salary' => (float)($basicSalary ?: 0.00),
                    'currency' => 'MWK',
                    'pay_frequency' => 'monthly',
                    'bank_name' => trim($this->request->getPost('bank_name')) ?: null,
                    'bank_account_number' => trim($this->request->getPost('bank_account_number')) ?: null,
                    'bank_branch' => trim($this->request->getPost('bank_branch')) ?: null,
                    'tax_number' => trim($this->request->getPost('tax_number')) ?: null,
                    'supervisor_id' => $this->request->getPost('supervisor_id') ?: null,
                    'annual_leave_balance' => 21.00, // Default annual leave
                    'sick_leave_balance' => 14.00, // Default sick leave
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $employeeInserted = $this->employeeDetailModel->insert($employeeData);
                
                if (!$employeeInserted) {
                    log_message('warning', 'Failed to create employee details for user: ' . $userId);
                    // Don't throw exception - employee details are optional
                }
            }

            // Complete transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            // Log activity
            $this->logActivity('user_created', 'users', $userId, [
                'username' => $userData['username'],
                'email' => $userData['email'],
                'employee_id' => $employeeId
            ]);

            // Success message
            $this->session->setFlashdata('success', 'User "' . $userData['first_name'] . ' ' . $userData['last_name'] . '" created successfully!');
            
            return redirect()->to(base_url('admin/users'));

        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->transRollback();
            
            // Log error
            log_message('error', 'User creation failed: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Return with error
            $this->session->setFlashdata('error', 'Failed to create user: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        $role = $this->request->getGet('role');
        $department = $this->request->getGet('department');

        // Build query with filters
        $users = $this->userModel->getUsersWithFilters($search, $status, $role, $department);
        
        $data = [
            'title' => 'User Management',
            'pageTitle' => 'User Management',
            'users' => $users,
            'roles' => $this->roleModel->getActiveRoles(),
            'departments' => $this->departmentModel->getActiveDepartments(),
            'userStats' => $this->userModel->getUserStats(),
            'filters' => [
                'search' => $search,
                'status' => $status,
                'role' => $role,
                'department' => $department
            ]
        ];

        return view('admin/users/index', $data);
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        // Debug logging
        log_message('debug', 'Users::edit called with ID: ' . $id);
        log_message('debug', 'Company ID: ' . (session('company_id') ?: 'NOT SET'));

        try {
            $user = $this->userModel->getUserWithDetails($id);

            log_message('debug', 'User data: ' . json_encode($user));

            if (!$user) {
                log_message('error', 'User not found with ID: ' . $id);
                throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
            }

            // Check if user belongs to current company
            if ($user['company_id'] != session('company_id')) {
                log_message('error', 'User company mismatch. User company: ' . $user['company_id'] . ', Session company: ' . session('company_id'));
                throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
            }

            $data = [
                'title' => 'Edit User',
                'pageTitle' => 'Edit User',
                'user' => $user,
                'roles' => $this->roleModel->getActiveRoles(),
                'departments' => $this->departmentModel->getActiveDepartments(),
                'positions' => $this->jobPositionModel->getActivePositions(),
                'validation' => \Config\Services::validation(),
                'employmentTypes' => $this->getEmploymentTypes(),
                'genderOptions' => $this->getGenderOptions()
            ];

            log_message('debug', 'Data prepared for view: ' . json_encode(array_keys($data)));

            return view('admin/users/edit', $data);

        } catch (\Exception $e) {
            log_message('error', 'Error in Users::edit: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Update user
     */
    public function update($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }

        // Check if user belongs to current company
        if ($user['company_id'] != session('company_id')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }

        // Validation rules (excluding current user for unique checks)
        $rules = [
            'username' => [
                'rules' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,'.$id.']',
                'errors' => [
                    'required' => 'Username is required',
                    'min_length' => 'Username must be at least 3 characters',
                    'max_length' => 'Username cannot exceed 100 characters',
                    'is_unique' => 'Username already exists'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email,id,'.$id.']',
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'Please enter a valid email address',
                    'is_unique' => 'Email already exists'
                ]
            ],
            'first_name' => [
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'First name is required',
                    'min_length' => 'First name must be at least 2 characters'
                ]
            ],
            'last_name' => [
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'Last name is required',
                    'min_length' => 'Last name must be at least 2 characters'
                ]
            ],
            'phone' => [
                'rules' => 'permit_empty|min_length[10]|max_length[20]',
                'errors' => [
                    'min_length' => 'Phone number must be at least 10 characters'
                ]
            ],
            'role_id' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Role is required',
                    'numeric' => 'Invalid role selected'
                ]
            ],
            'department_id' => 'permit_empty|numeric',
            'position_id' => 'permit_empty|numeric',
            'hire_date' => 'permit_empty|valid_date',
            'basic_salary' => 'permit_empty|numeric|greater_than_equal_to[0]'
        ];

        // Add password validation only if password is provided
        if ($this->request->getPost('password')) {
            $rules['password'] = [
                'rules' => 'min_length[8]',
                'errors' => [
                    'min_length' => 'Password must be at least 8 characters'
                ]
            ];
            $rules['password_confirm'] = [
                'rules' => 'matches[password]',
                'errors' => [
                    'matches' => 'Passwords do not match'
                ]
            ];
        }

        // Perform validation
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput();
        }

        // Start database transaction
        $this->db->transStart();

        try {
            // Prepare user data
            $userData = [
                'username' => trim($this->request->getPost('username')),
                'email' => trim($this->request->getPost('email')),
                'first_name' => trim($this->request->getPost('first_name')),
                'last_name' => trim($this->request->getPost('last_name')),
                'middle_name' => trim($this->request->getPost('middle_name')) ?: null,
                'phone' => trim($this->request->getPost('phone')) ?: null,
                'mobile' => trim($this->request->getPost('mobile')) ?: null,
                'date_of_birth' => $this->request->getPost('date_of_birth') ?: null,
                'gender' => $this->request->getPost('gender') ?: null,
                'national_id' => trim($this->request->getPost('national_id')) ?: null,
                'address' => trim($this->request->getPost('address')) ?: null,
                'city' => trim($this->request->getPost('city')) ?: null,
                'emergency_contact_name' => trim($this->request->getPost('emergency_contact_name')) ?: null,
                'emergency_contact_phone' => trim($this->request->getPost('emergency_contact_phone')) ?: null,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update password if provided
            if ($this->request->getPost('password')) {
                $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }

            // Update user
            if (!$this->userModel->update($id, $userData)) {
                throw new \Exception('Failed to update user');
            }

            // Update user role
            $roleId = (int)$this->request->getPost('role_id');

            // Remove existing role assignment
            $this->db->table('user_roles')->where('user_id', $id)->delete();

            // Assign new role
            $roleData = [
                'user_id' => $id,
                'role_id' => $roleId,
                'assigned_by' => session('user_id'),
                'assigned_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->db->table('user_roles')->insert($roleData)) {
                throw new \Exception('Failed to update user role');
            }

            // Update or create employee details
            $departmentId = $this->request->getPost('department_id');
            $positionId = $this->request->getPost('position_id');
            $hireDate = $this->request->getPost('hire_date');
            $basicSalary = $this->request->getPost('basic_salary');

            $employeeDetail = $this->employeeDetailModel->where('user_id', $id)->first();

            if ($employeeDetail) {
                // Update existing employee details
                $employeeData = [
                    'department_id' => $departmentId ?: null,
                    'position_id' => $positionId ?: null,
                    'hire_date' => $hireDate ?: $employeeDetail['hire_date'],
                    'employment_type' => $this->request->getPost('employment_type') ?: $employeeDetail['employment_type'],
                    'basic_salary' => (float)($basicSalary ?: $employeeDetail['basic_salary']),
                    'bank_name' => trim($this->request->getPost('bank_name')) ?: $employeeDetail['bank_name'],
                    'bank_account_number' => trim($this->request->getPost('bank_account_number')) ?: $employeeDetail['bank_account_number'],
                    'bank_branch' => trim($this->request->getPost('bank_branch')) ?: $employeeDetail['bank_branch'],
                    'tax_number' => trim($this->request->getPost('tax_number')) ?: $employeeDetail['tax_number'],
                    'supervisor_id' => $this->request->getPost('supervisor_id') ?: $employeeDetail['supervisor_id'],
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if (!$this->employeeDetailModel->update($employeeDetail['id'], $employeeData)) {
                    throw new \Exception('Failed to update employee details');
                }
            } elseif ($departmentId || $positionId || $hireDate || $basicSalary) {
                // Create new employee details
                $employeeData = [
                    'user_id' => $id,
                    'department_id' => $departmentId ?: null,
                    'position_id' => $positionId ?: null,
                    'hire_date' => $hireDate ?: date('Y-m-d'),
                    'employment_type' => $this->request->getPost('employment_type') ?: 'full_time',
                    'employment_status' => 'active',
                    'basic_salary' => (float)($basicSalary ?: 0.00),
                    'currency' => 'MWK',
                    'pay_frequency' => 'monthly',
                    'bank_name' => trim($this->request->getPost('bank_name')) ?: null,
                    'bank_account_number' => trim($this->request->getPost('bank_account_number')) ?: null,
                    'bank_branch' => trim($this->request->getPost('bank_branch')) ?: null,
                    'tax_number' => trim($this->request->getPost('tax_number')) ?: null,
                    'supervisor_id' => $this->request->getPost('supervisor_id') ?: null,
                    'annual_leave_balance' => 21.00,
                    'sick_leave_balance' => 14.00,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if (!$this->employeeDetailModel->insert($employeeData)) {
                    throw new \Exception('Failed to create employee details');
                }
            }

            // Complete transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            // Log activity
            $this->logActivity('user_updated', 'users', $id, $userData);

            return redirect()->to(base_url('admin/users'))->with('success', 'User updated successfully');

        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->transRollback();

            // Log error
            log_message('error', 'User update failed: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Get positions by department ID - AJAX endpoint
     */
    public function getPositionsByDepartment()
    {
        $departmentId = $this->request->getGet('department_id');

        if (!$departmentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No department ID provided',
                'positions' => []
            ]);
        }

        $positions = $this->jobPositionModel->where('department_id', $departmentId)
                                           ->where('is_active', true)
                                           ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Found ' . count($positions) . ' positions',
            'positions' => $positions
        ]);
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        $this->checkPermission('users.delete');

        $user = $this->userModel->find($id);

        if (!$user || $user['company_id'] != session('company_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        // Prevent deleting yourself
        if ($user['id'] == session('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'You cannot delete your own user account']);
        }

        // Start transaction
        $this->db->transStart();

        try {
            // Delete user roles
            $this->db->table('user_roles')->where('user_id', $id)->delete();

            // Delete employee details
            $this->employeeDetailModel->where('user_id', $id)->delete();

            // Delete user
            if ($this->userModel->delete($id)) {
                $this->db->transComplete();
                
                if ($this->db->transStatus() !== false) {
                    return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully']);
                }
            }
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Failed to delete user: ' . $e->getMessage());
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user']);
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggle($id)
    {
        $this->checkPermission('users.edit');

        $user = $this->userModel->find($id);

        if (!$user || $user['company_id'] != session('company_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        // Prevent toggling yourself
        if ($user['id'] == session('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'You cannot change your own user status']);
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';

        if ($this->userModel->update($id, ['status' => $newStatus])) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'User status updated successfully',
                'status' => $newStatus
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update user status']);
    }

    private function checkPermission($permission)
    {
        if (!hasPermission($permission)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }
    }

    // Helper Methods
    private function generateEmployeeId()
    {
        $year = date('Y');
        $prefix = 'EMP';
        $companyId = session('company_id');
        
        // Get last employee for this company
        $lastEmployee = $this->userModel->where('company_id', $companyId)
            ->where('employee_id IS NOT NULL')
            ->where('employee_id !=', '')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastEmployee && !empty($lastEmployee['employee_id'])) {
            // Extract number from last employee ID
            preg_match('/(\d+)$/', $lastEmployee['employee_id'], $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function getEmploymentTypes()
    {
        return [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'temporary' => 'Temporary',
            'intern' => 'Intern'
        ];
    }

    private function getGenderOptions()
    {
        return [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other'
        ];
    }

    private function logActivity($action, $table, $recordId, $data = [])
    {
        try {
            $this->db->table('audit_logs')->insert([
                'user_id' => session('user_id'),
                'company_id' => session('company_id'),
                'action' => $action,
                'table_name' => $table,
                'record_id' => $recordId,
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to log activity: ' . $e->getMessage());
        }
    }
}
