<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
//use App\Models\CompanyModel;

class Auth extends BaseController
{
    protected $userModel;
   // protected $companyModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        //$this->companyModel = new CompanyModel();
        $this->session = \Config\Services::session();
        helper(['form', 'url']);
    }

    public function login()
    {
        // Redirect if already logged in
        if (session('user_id')) {
            return redirect()->to('/admin/dashboard');
        }

        $data = [
            'title' => 'Login - Construction Management System',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/login', $data);
    }

    public function authenticate()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'login' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $login = $this->request->getPost('login');
        $password = $this->request->getPost('password');

        // Try to find user by email or username
        $user = $this->userModel->getUserByEmailOrUsername($login);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Invalid login credentials.');
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid login credentials.');
        }

        // Check if user is active
        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Your account is not active. Please contact administrator.');
        }

        // Get user role
        $userRole = $this->userModel->getUserRole($user['id']);

        // Set session data
        $sessionData = [
            'user_id' => $user['id'],
            'company_id' => $user['company_id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'full_name' => $user['first_name'] . ' ' . $user['last_name'],
            'role_id' => $userRole['role_id'] ?? null,
            'role_name' => $userRole['role_name'] ?? 'User',
            'is_logged_in' => true
        ];

        $this->session->set($sessionData);

        // Update last login
        $this->userModel->updateLastLogin($user['id']);

        // Redirect to intended page or dashboard
        $redirectTo = session('redirect_to') ?: '/admin/dashboard';
        $this->session->remove('redirect_to');

        return redirect()->to($redirectTo)->with('success', 'Welcome back!');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/auth/login')->with('success', 'You have been logged out successfully.');
    }

    public function register()
    {
        // For demo purposes - you might want to disable this in production
        $data = [
            'title' => 'Register - Construction Management System',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/register', $data);
    }

    public function createAccount()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'company_name' => 'required|min_length[3]|max_length[255]',
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'terms' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        // Create company first
        $companyData = [
            'name' => $this->request->getPost('company_name'),
            'status' => 'active',
            'subscription_plan' => 'basic',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $companyId = $this->companyModel->insert($companyData);

        if (!$companyId) {
            return redirect()->back()->with('error', 'Failed to create company.');
        }

        // Create user
        $userData = [
            'company_id' => $companyId,
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'status' => 'active',
            'is_verified' => true,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $userId = $this->userModel->insert($userData);

        if (!$userId) {
            return redirect()->back()->with('error', 'Failed to create user account.');
        }

        // Create default admin role for the company
        $roleModel = new \App\Models\RoleModel();
        $adminRoleData = [
            'company_id' => $companyId,
            'name' => 'Super Admin',
            'slug' => 'super_admin',
            'description' => 'Full system access',
            'is_system_role' => true,
            'permissions' => json_encode(['*'])
        ];

        $roleId = $roleModel->insert($adminRoleData);

        if ($roleId) {
            // Assign admin role to user
            $this->userModel->assignRole([
                'user_id' => $userId,
                'role_id' => $roleId,
                'assigned_by' => $userId,
                'assigned_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to('/auth/login')->with('success', 'Account created successfully! Please login to continue.');
    }

    public function forgotPassword()
    {
        $data = [
            'title' => 'Forgot Password - Construction Management System',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/forgot_password', $data);
    }

    public function sendResetLink()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $email = $this->request->getPost('email');
        $user = $this->userModel->getUserByEmail($email);

        if (!$user) {
            return redirect()->back()->with('error', 'No account found with that email address.');
        }

        // Generate reset token (you'll need to implement this)
        $token = bin2hex(random_bytes(32));

        // Store token in database (you'll need to create password_resets table)
        // Send email (you'll need to implement email sending)

        return redirect()->back()->with('success', 'Password reset link has been sent to your email.');
    }
}