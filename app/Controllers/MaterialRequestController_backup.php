<?php

namespace App\Controllers;

use App\Models\MaterialRequestModel;
use App\Models\MaterialRequestItemModel;
use App\Models\MaterialModel;
use App\Models\ProjectModel;
use App\Models\DepartmentModel;
use App\Models\UserModel;

class MaterialRequestController extends BaseController
{
    protected $materialRequestModel;
    protected $materialRequestItemModel;
    protected $materialModel;
    protected $projectModel;
    protected $departmentModel;
    protected $userModel;

    public function __construct()
    {
        $this->materialRequestModel = new MaterialRequestModel();
        $this->materialRequestItemModel = new MaterialRequestItemModel();
        $this->materialModel = new MaterialModel();
        $this->projectModel = new ProjectModel();
        $this->departmentModel = new DepartmentModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display list of material requests
     */
    public function index()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'priority' => $this->request->getGet('priority'),
            'project_id' => $this->request->getGet('project_id'),
            'requested_by' => $this->request->getGet('requested_by')
        ];

        // Remove empty filters
        $filters = array_filter($filters);

        $materialRequests = $this->materialRequestModel->getMaterialRequestsWithDetails($filters);
        $projects = $this->projectModel->findAll();
        $users = $this->userModel->findAll();

        $data = [
            'title' => 'Material Requests',
            'materialRequests' => $materialRequests,
            'projects' => $projects,
            'users' => $users,
            'filters' => $filters
        ];

        return view('procurement/material_requests/index', $data);
    }

    /**
     * Show create material request form
     */
    public function create()
    {
        $data = [
            'title' => 'Create Material Request',
            'projects' => $this->projectModel->findAll(),
            'departments' => $this->departmentModel->findAll(),
            'materials' => $this->materialModel->findAll()
        ];

        return view('procurement/material_requests/create', $data);
    }

    /**
     * Store new material request
     */
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'project_id' => 'permit_empty|integer',
            'department_id' => 'permit_empty|integer',
            'required_date' => 'permit_empty|valid_date',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'notes' => 'permit_empty|string',
            'items' => 'required|array',
            'items.*.material_id' => 'required|integer',
            'items.*.quantity_requested' => 'required|decimal|greater_than[0]',
            'items.*.estimated_unit_cost' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'items.*.specification_notes' => 'permit_empty|string',
            'items.*.urgency_notes' => 'permit_empty|string'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Generate request number
            $requestNumber = $this->materialRequestModel->generateRequestNumber();

            // Calculate total estimated cost
            $totalEstimatedCost = 0;
            $items = $this->request->getPost('items');
            
            foreach ($items as $item) {
                $estimatedCost = $item['estimated_unit_cost'] ?? 0;
                $totalEstimatedCost += $item['quantity_requested'] * $estimatedCost;
            }

            // Create material request
            $requestData = [
                'request_number' => $requestNumber,
                'project_id' => $this->request->getPost('project_id') ?: null,
                'department_id' => $this->request->getPost('department_id') ?: null,
                'requested_by' => session('user_id'),
                'request_date' => date('Y-m-d'),
                'required_date' => $this->request->getPost('required_date') ?: null,
                'status' => MaterialRequestModel::STATUS_DRAFT,
                'priority' => $this->request->getPost('priority'),
                'total_estimated_cost' => $totalEstimatedCost,
                'notes' => $this->request->getPost('notes')
            ];

            $this->materialRequestModel->insert($requestData);
            $requestId = $db->insertID();

            // Create material request items
            foreach ($items as $item) {
                $estimatedUnitCost = $item['estimated_unit_cost'] ?? 0;
                $estimatedTotalCost = $item['quantity_requested'] * $estimatedUnitCost;

                $itemData = [
                    'material_request_id' => $requestId,
                    'material_id' => $item['material_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'estimated_unit_cost' => $estimatedUnitCost,
                    'estimated_total_cost' => $estimatedTotalCost,
                    'specification_notes' => $item['specification_notes'] ?? null,
                    'urgency_notes' => $item['urgency_notes'] ?? null
                ];

                $this->materialRequestItemModel->insert($itemData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Failed to create material request');
            }

            return redirect()->to('/admin/material-requests')->with('success', 'Material request created successfully');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to create material request: ' . $e->getMessage());
        }
    }

    /**
     * Show material request details
     */
    public function view($id)
    {
        $materialRequest = $this->materialRequestModel->getMaterialRequestWithItems($id);

        if (!$materialRequest) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Material request not found');
        }

        $data = [
            'title' => 'Material Request Details',
            'materialRequest' => $materialRequest
        ];

        return view('procurement/material_requests/view', $data);
    }

    /**
     * Show edit material request form
     */
    public function edit($id)
    {
        $materialRequest = $this->materialRequestModel->getMaterialRequestWithItems($id);

        if (!$materialRequest) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Material request not found');
        }

        // Only allow editing of draft requests
        if ($materialRequest['status'] !== MaterialRequestModel::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Only draft material requests can be edited');
        }

        $data = [
            'title' => 'Edit Material Request',
            'materialRequest' => $materialRequest,
            'projects' => $this->projectModel->findAll(),
            'departments' => $this->departmentModel->findAll(),
            'materials' => $this->materialModel->findAll()
        ];

        return view('procurement/material_requests/edit', $data);
    }

    /**
     * Update material request
     */
    public function update($id)
    {
        $materialRequest = $this->materialRequestModel->find($id);

        if (!$materialRequest) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Material request not found');
        }

        // Only allow updating of draft requests
        if ($materialRequest['status'] !== MaterialRequestModel::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Only draft material requests can be updated');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'project_id' => 'permit_empty|integer',
            'department_id' => 'permit_empty|integer',
            'required_date' => 'permit_empty|valid_date',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'notes' => 'permit_empty|string',
            'items' => 'required|array',
            'items.*.material_id' => 'required|integer',
            'items.*.quantity_requested' => 'required|decimal|greater_than[0]',
            'items.*.estimated_unit_cost' => 'permit_empty|decimal|greater_than_equal_to[0]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calculate total estimated cost
            $totalEstimatedCost = 0;
            $items = $this->request->getPost('items');
            
            foreach ($items as $item) {
                $estimatedCost = $item['estimated_unit_cost'] ?? 0;
                $totalEstimatedCost += $item['quantity_requested'] * $estimatedCost;
            }

            // Update material request
            $requestData = [
                'project_id' => $this->request->getPost('project_id') ?: null,
                'department_id' => $this->request->getPost('department_id') ?: null,
                'required_date' => $this->request->getPost('required_date') ?: null,
                'priority' => $this->request->getPost('priority'),
                'total_estimated_cost' => $totalEstimatedCost,
                'notes' => $this->request->getPost('notes')
            ];

            $this->materialRequestModel->update($id, $requestData);

            // Delete existing items and recreate
            $db->table('material_request_items')->where('material_request_id', $id)->delete();

            // Create new material request items
            foreach ($items as $item) {
                $estimatedUnitCost = $item['estimated_unit_cost'] ?? 0;
                $estimatedTotalCost = $item['quantity_requested'] * $estimatedUnitCost;

                $itemData = [
                    'material_request_id' => $id,
                    'material_id' => $item['material_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'estimated_unit_cost' => $estimatedUnitCost,
                    'estimated_total_cost' => $estimatedTotalCost,
                    'specification_notes' => $item['specification_notes'] ?? null,
                    'urgency_notes' => $item['urgency_notes'] ?? null
                ];

                $this->materialRequestItemModel->insert($itemData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Failed to update material request');
            }

            return redirect()->to('/admin/material-requests')->with('success', 'Material request updated successfully');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to update material request: ' . $e->getMessage());
        }
    }

    /**
     * Submit material request for approval
     */
    public function submit($id)
    {
        $materialRequest = $this->materialRequestModel->find($id);

        if (!$materialRequest) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Material request not found');
        }

        if ($materialRequest['status'] !== MaterialRequestModel::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Only draft material requests can be submitted');
        }

        $this->materialRequestModel->update($id, [
            'status' => MaterialRequestModel::STATUS_PENDING_APPROVAL
        ]);

        return redirect()->back()->with('success', 'Material request submitted for approval');
    }

    /**
     * Approve material request
     */
    public function approve($id)
    {
        $materialRequest = $this->materialRequestModel->find($id);

        if (!$materialRequest) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Material request not found');
        }

        if ($materialRequest['status'] !== MaterialRequestModel::STATUS_PENDING_APPROVAL) {
            return redirect()->back()->with('error', 'Only pending material requests can be approved');
        }

        $approvalNotes = $this->request->getPost('approval_notes');
        
        $this->materialRequestModel->approveMaterialRequest($id, session('user_id'), $approvalNotes);

        return redirect()->back()->with('success', 'Material request approved successfully');
    }

    /**
     * Reject material request
     */
    public function reject($id)
    {
        $materialRequest = $this->materialRequestModel->find($id);

        if (!$materialRequest) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Material request not found');
        }

        if ($materialRequest['status'] !== MaterialRequestModel::STATUS_PENDING_APPROVAL) {
            return redirect()->back()->with('error', 'Only pending material requests can be rejected');
        }

        $rejectionReason = $this->request->getPost('rejection_reason');
        
        if (empty($rejectionReason)) {
            return redirect()->back()->with('error', 'Rejection reason is required');
        }

        $this->materialRequestModel->rejectMaterialRequest($id, session('user_id'), $rejectionReason);

        return redirect()->back()->with('success', 'Material request rejected');
    }

    /**
     * Delete material request
     */
    public function delete($id)
    {
        $materialRequest = $this->materialRequestModel->find($id);

        if (!$materialRequest) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Material request not found');
        }

        // Only allow deletion of draft requests
        if ($materialRequest['status'] !== MaterialRequestModel::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Only draft material requests can be deleted');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Delete items first
        $db->table('material_request_items')->where('material_request_id', $id)->delete();
        
        // Delete request
        $this->materialRequestModel->delete($id);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to delete material request');
        }

        return redirect()->to('/admin/material-requests')->with('success', 'Material request deleted successfully');
    }
}
