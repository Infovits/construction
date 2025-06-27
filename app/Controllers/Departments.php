<?php

namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\UserModel;

class Departments extends BaseController
{
    protected $departmentModel;
    protected $userModel;

    public function __construct()
    {
        $this->departmentModel = new DepartmentModel();
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $departments = $this->departmentModel->getDepartmentsWithFilters($search, $status);
        
        $data = [
            'title' => 'Departments',
            'pageTitle' => 'Department Management',
            'departments' => $departments,
            'filters' => [
                'search' => $search,
                'status' => $status
            ]
        ];

        return view('admin/departments/index', $data);
    }

    public function create()
    {
        $this->checkPermission('departments.create');

        $data = [
            'title' => 'Add Department',
            'pageTitle' => 'Add New Department',
            'departments' => $this->departmentModel->getActiveDepartments(), // For parent department
            'managers' => $this->userModel->getPotentialSupervisors(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/departments/create', $data);
    }

    public function store()
    {
        $this->checkPermission('departments.create');

        $validation = \Config\Services::validation();

        $rules = [
            'code' => 'required|min_length[2]|max_length[20]|is_unique[departments.code]',
            'name' => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|max_length[500]',
            'manager_id' => 'permit_empty|numeric',
            'parent_department_id' => 'permit_empty|numeric',
            'budget' => 'permit_empty|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $data = [
            'company_id' => session('company_id'),
            'code' => strtoupper($this->request->getPost('code')),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'manager_id' => $this->request->getPost('manager_id') ?: null,
            'parent_department_id' => $this->request->getPost('parent_department_id') ?: null,
            'budget' => $this->request->getPost('budget') ?: 0.00,
            'status' => 'active'
        ];

        if ($this->departmentModel->insert($data)) {
            return redirect()->to('/admin/departments')->with('success', 'Department created successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create department.');
    }

    public function edit($id)
    {
        $this->checkPermission('departments.edit');

        $department = $this->departmentModel->find($id);

        if (!$department || $department['company_id'] != session('company_id')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Department not found');
        }

        $data = [
            'title' => 'Edit Department',
            'pageTitle' => 'Edit Department - ' . $department['name'],
            'department' => $department,
            'departments' => $this->departmentModel->getActiveDepartments(), // For parent department
            'managers' => $this->userModel->getPotentialSupervisors(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/departments/edit', $data);
    }

    public function update($id)
    {
        $this->checkPermission('departments.edit');

        $department = $this->departmentModel->find($id);

        if (!$department || $department['company_id'] != session('company_id')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Department not found');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'code' => "required|min_length[2]|max_length[20]|is_unique[departments.code,id,$id]",
            'name' => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|max_length[500]',
            'manager_id' => 'permit_empty|numeric',
            'parent_department_id' => 'permit_empty|numeric',
            'budget' => 'permit_empty|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $data = [
            'code' => strtoupper($this->request->getPost('code')),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'manager_id' => $this->request->getPost('manager_id') ?: null,
            'parent_department_id' => $this->request->getPost('parent_department_id') ?: null,
            'budget' => $this->request->getPost('budget') ?: 0.00,
            'status' => $this->request->getPost('status')
        ];

        if ($this->departmentModel->update($id, $data)) {
            return redirect()->to('/admin/departments')->with('success', 'Department updated successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update department.');
    }

    public function delete($id)
    {
        $this->checkPermission('departments.delete');

        $department = $this->departmentModel->find($id);

        if (!$department || $department['company_id'] != session('company_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Department not found']);
        }

        // Check if department has employees
        $employeeCount = $this->db->table('employee_details')
            ->where('department_id', $id)
            ->countAllResults();

        if ($employeeCount > 0) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => "Cannot delete department. It has $employeeCount employees assigned."
            ]);
        }

        // Check if department has sub-departments
        $subDeptCount = $this->departmentModel
            ->where('parent_department_id', $id)
            ->countAllResults();

        if ($subDeptCount > 0) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => "Cannot delete department. It has $subDeptCount sub-departments."
            ]);
        }

        if ($this->departmentModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Department deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete department']);
    }

    public function toggle($id)
    {
        $this->checkPermission('departments.edit');

        $department = $this->departmentModel->find($id);

        if (!$department || $department['company_id'] != session('company_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Department not found']);
        }

        $newStatus = $department['status'] === 'active' ? 'inactive' : 'active';

        if ($this->departmentModel->update($id, ['status' => $newStatus])) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Department status updated successfully',
                'status' => $newStatus
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update department status']);
    }

    private function checkPermission($permission)
    {
        if (!hasPermission($permission)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }
    }
}
