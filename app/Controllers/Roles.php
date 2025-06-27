<?php

namespace App\Controllers;

use App\Models\RoleModel;

class Roles extends BaseController
{
    protected $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $type = $this->request->getGet('type');

        $roles = $this->roleModel->getRolesWithFilters($search, $type);
        
        $data = [
            'title' => 'Roles & Permissions',
            'pageTitle' => 'Roles & Permissions Management',
            'roles' => $roles,
            'filters' => [
                'search' => $search,
                'type' => $type
            ]
        ];

        return view('admin/roles/index', $data);
    }

    public function create()
    {
        $this->checkPermission('roles.create');

        $data = [
            'title' => 'Add Role',
            'pageTitle' => 'Add New Role',
            'permissions' => $this->roleModel->getDefaultPermissions(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/roles/create', $data);
    }

    public function store()
    {
        $this->checkPermission('roles.create');

        $validation = \Config\Services::validation();

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'slug' => 'required|min_length[2]|max_length[100]|is_unique[roles.slug]',
            'description' => 'permit_empty|max_length[500]',
            'permissions' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $permissions = $this->request->getPost('permissions') ?: [];

        $data = [
            'company_id' => session('company_id'),
            'name' => $this->request->getPost('name'),
            'slug' => strtolower(str_replace(' ', '_', $this->request->getPost('slug'))),
            'description' => $this->request->getPost('description'),
            'is_system_role' => false,
            'permissions' => json_encode($permissions)
        ];

        if ($this->roleModel->insert($data)) {
            return redirect()->to('/admin/roles')->with('success', 'Role created successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create role.');
    }

    public function edit($id)
    {
        $this->checkPermission('roles.edit');

        $role = $this->roleModel->find($id);

        if (!$role || $role['company_id'] != session('company_id')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Role not found');
        }

        if ($role['is_system_role']) {
            return redirect()->to('/admin/roles')->with('error', 'System roles cannot be modified.');
        }

        $data = [
            'title' => 'Edit Role',
            'pageTitle' => 'Edit Role - ' . $role['name'],
            'role' => $role,
            'permissions' => $this->roleModel->getDefaultPermissions(),
            'selectedPermissions' => json_decode($role['permissions'], true) ?: [],
            'validation' => \Config\Services::validation()
        ];

        return view('admin/roles/edit', $data);
    }

    public function update($id)
    {
        $this->checkPermission('roles.edit');

        $role = $this->roleModel->find($id);

        if (!$role || $role['company_id'] != session('company_id')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Role not found');
        }

        if ($role['is_system_role']) {
            return redirect()->to('/admin/roles')->with('error', 'System roles cannot be modified.');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'slug' => "required|min_length[2]|max_length[100]|is_unique[roles.slug,id,$id]",
            'description' => 'permit_empty|max_length[500]',
            'permissions' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $permissions = $this->request->getPost('permissions') ?: [];

        $data = [
            'name' => $this->request->getPost('name'),
            'slug' => strtolower(str_replace(' ', '_', $this->request->getPost('slug'))),
            'description' => $this->request->getPost('description'),
            'permissions' => json_encode($permissions)
        ];

        if ($this->roleModel->update($id, $data)) {
            return redirect()->to('/admin/roles')->with('success', 'Role updated successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update role.');
    }

    public function delete($id)
    {
        $this->checkPermission('roles.delete');

        $role = $this->roleModel->find($id);

        if (!$role || $role['company_id'] != session('company_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Role not found']);
        }

        if ($role['is_system_role']) {
            return $this->response->setJSON(['success' => false, 'message' => 'System roles cannot be deleted']);
        }

        // Check if role is assigned to users
        $userCount = $this->db->table('user_roles')
            ->where('role_id', $id)
            ->countAllResults();

        if ($userCount > 0) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => "Cannot delete role. It is assigned to $userCount users."
            ]);
        }

        if ($this->roleModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Role deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete role']);
    }

    public function duplicate($id)
    {
        $this->checkPermission('roles.create');

        $role = $this->roleModel->find($id);

        if (!$role || $role['company_id'] != session('company_id')) {
            return redirect()->to('/admin/roles')->with('error', 'Role not found');
        }

        $data = [
            'title' => 'Duplicate Role',
            'pageTitle' => 'Duplicate Role - ' . $role['name'],
            'role' => $role,
            'permissions' => $this->roleModel->getDefaultPermissions(),
            'selectedPermissions' => json_decode($role['permissions'], true) ?: [],
            'validation' => \Config\Services::validation()
        ];

        return view('admin/roles/duplicate', $data);
    }

    private function checkPermission($permission)
    {
        if (!hasPermission($permission)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }
    }
}
