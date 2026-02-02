<?php

namespace App\Controllers;

use App\Models\JobPositionModel;
use App\Models\DepartmentModel;

class Positions extends BaseController
{
    protected $jobPositionModel;
    protected $departmentModel;

    public function __construct()
    {
        $this->jobPositionModel = new JobPositionModel();
        $this->departmentModel = new DepartmentModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $department = $this->request->getGet('department');
        $level = $this->request->getGet('level');

        $positions = $this->jobPositionModel->getPositionsWithFilters($search, $department, $level);
        $departments = $this->departmentModel->getActiveDepartments();
        
        $data = [
            'title' => 'Job Positions',
            'pageTitle' => 'Job Positions Management',
            'positions' => $positions,
            'departments' => $departments,
            'filters' => [
                'search' => $search,
                'department' => $department,
                'level' => $level
            ]
        ];

        return view('admin/positions/index', $data);
    }

    public function create()
    {
        $this->checkPermission('positions.create');

        $data = [
            'title' => 'Add Position',
            'pageTitle' => 'Add New Job Position',
            'departments' => $this->departmentModel->getActiveDepartments(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/positions/create', $data);
    }

    public function store()
    {
        $this->checkPermission('positions.create');

        $validation = \Config\Services::validation();

        $rules = [
            'title' => 'required|min_length[2]|max_length[100]',
            'code' => 'required|min_length[2]|max_length[20]|is_unique[job_positions.code]',
            'department_id' => 'required|numeric',
            'employment_type' => 'required|in_list[full_time,part_time,contract,temporary,intern]',
            'description' => 'permit_empty|max_length[1000]',
            'requirements' => 'permit_empty|max_length[1000]',
            'min_salary' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'max_salary' => 'permit_empty|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $data = [
            'company_id' => session('company_id'),
            'department_id' => $this->request->getPost('department_id'),
            'title' => $this->request->getPost('title'),
            'code' => strtoupper($this->request->getPost('code')),
            'employment_type' => $this->request->getPost('employment_type'),
            'description' => $this->request->getPost('description'),
            'requirements' => $this->request->getPost('requirements'),
            'min_salary' => $this->request->getPost('min_salary') ?: 0.00,
            'max_salary' => $this->request->getPost('max_salary') ?: 0.00,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->jobPositionModel->insert($data)) {
            return redirect()->to('/admin/positions')->with('success', 'Job position created successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create job position.');
    }

    public function edit($id)
    {
        $this->checkPermission('positions.edit');

        $position = $this->jobPositionModel->find($id);

        if (!$position || $position['company_id'] != session('company_id')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Position not found');
        }

        $data = [
            'title' => 'Edit Position',
            'pageTitle' => 'Edit Job Position - ' . $position['title'],
            'position' => $position,
            'departments' => $this->departmentModel->getActiveDepartments(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/positions/edit', $data);
    }

    public function update($id)
    {
        $this->checkPermission('positions.edit');

        $position = $this->jobPositionModel->find($id);

        if (!$position || $position['company_id'] != session('company_id')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Position not found');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'title' => 'required|min_length[2]|max_length[100]',
            'code' => "required|min_length[2]|max_length[20]|is_unique[job_positions.code,id,$id]",
            'department_id' => 'required|numeric',
            'employment_type' => 'required|in_list[full_time,part_time,contract,temporary,intern]',
            'description' => 'permit_empty|max_length[1000]',
            'requirements' => 'permit_empty|max_length[1000]',
            'min_salary' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'max_salary' => 'permit_empty|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $data = [
            'department_id' => $this->request->getPost('department_id'),
            'title' => $this->request->getPost('title'),
            'code' => strtoupper($this->request->getPost('code')),
            'employment_type' => $this->request->getPost('employment_type'),
            'description' => $this->request->getPost('description'),
            'requirements' => $this->request->getPost('requirements'),
            'min_salary' => $this->request->getPost('min_salary') ?: 0.00,
            'max_salary' => $this->request->getPost('max_salary') ?: 0.00,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->jobPositionModel->update($id, $data)) {
            return redirect()->to('/admin/positions')->with('success', 'Job position updated successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update job position.');
    }

    public function delete($id)
    {
        $this->checkPermission('positions.delete');

        $position = $this->jobPositionModel->find($id);

        if (!$position || $position['company_id'] != session('company_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Position not found']);
        }

        // Check if position is assigned to employees
        $employeeCount = $this->db->table('employee_details')
            ->where('position_id', $id)
            ->countAllResults();

        if ($employeeCount > 0) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => "Cannot delete position. It is assigned to $employeeCount employees."
            ]);
        }

        if ($this->jobPositionModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Position deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete position']);
    }

    public function toggle($id)
    {
        $this->checkPermission('positions.edit');

        $position = $this->jobPositionModel->find($id);

        if (!$position || $position['company_id'] != session('company_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Position not found']);
        }

        $newStatus = $position['is_active'] ? 0 : 1;

        if ($this->jobPositionModel->update($id, ['is_active' => $newStatus])) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Position status updated successfully',
                'status' => $newStatus
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update position status']);
    }

    public function byDepartment()
    {
        $departmentId = $this->request->getGet('department');
        
        if (!$departmentId) {
            return $this->response->setJSON([]);
        }

        $positions = $this->jobPositionModel->getActivePositions($departmentId);
        
        return $this->response->setJSON($positions);
    }

    private function checkPermission($permission)
    {
        if (!hasPermission($permission)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }
    }
}
