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
        
        $data = [
            'title' => 'Material Requests',
            'materialRequests' => $materialRequests,
            'projects' => $this->projectModel->findAll(),
            'users' => $this->userModel->findAll(),
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
            'notes' => 'permit_empty|string'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Validate items
        $items = $this->request->getPost('items') ?: [];
        
        // Debug logging
        log_message('debug', 'Posted items: ' . json_encode($items));
        
        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'At least one material item is required');
        }

        try {
            $db = \Config\Database::connect();
            
            // Check if tables exist
            if (!$db->tableExists('material_requests')) {
                return redirect()->back()->withInput()->with('error', 'Material requests table does not exist. Please run database migrations.');
            }
            
            if (!$db->tableExists('material_request_items')) {
                return redirect()->back()->withInput()->with('error', 'Material request items table does not exist. Please run database migrations.');
            }
            
            $db->transStart();

            // Calculate total estimated cost
            $totalEstimatedCost = 0;
            foreach ($items as $item) {
                $quantity = (float)($item['quantity_requested'] ?? 0);
                $unitCost = (float)($item['estimated_unit_cost'] ?? 0);
                $totalEstimatedCost += $quantity * $unitCost;
            }

            // Create material request
            $requestData = [
                'request_number' => $this->materialRequestModel->generateRequestNumber(),
                'project_id' => $this->request->getPost('project_id') ?: null,
                'requested_by' => session('user_id'),
                'department_id' => $this->request->getPost('department_id') ?: null,
                'request_date' => date('Y-m-d'),
                'required_date' => $this->request->getPost('required_date') ?: null,
                'status' => MaterialRequestModel::STATUS_DRAFT,
                'priority' => $this->request->getPost('priority'),
                'total_estimated_cost' => $totalEstimatedCost,
                'notes' => $this->request->getPost('notes')
            ];

            $requestId = $this->materialRequestModel->insert($requestData);

            if (!$requestId) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Failed to create material request');
            }

            // Create material request items
            foreach ($items as $item) {
                $quantity = (float)($item['quantity_requested'] ?? 0);
                $unitCost = (float)($item['estimated_unit_cost'] ?? 0);
                
                $itemData = [
                    'material_request_id' => $requestId,
                    'material_id' => $item['material_id'],
                    'quantity_requested' => $quantity,
                    'quantity_approved' => null,
                    'estimated_unit_cost' => $unitCost,
                    'estimated_total_cost' => $quantity * $unitCost,
                    'specification_notes' => $item['specification_notes'] ?? null,
                    'urgency_notes' => $item['urgency_notes'] ?? null
                ];

                log_message('debug', 'Inserting item data: ' . json_encode($itemData));

                $itemInsertResult = $this->materialRequestItemModel->insert($itemData);
                
                if (!$itemInsertResult) {
                    $errors = $this->materialRequestItemModel->errors();
                    log_message('error', 'Failed to insert material request item: ' . json_encode($errors));
                    log_message('error', 'Item data that failed: ' . json_encode($itemData));
                    $db->transRollback();
                    return redirect()->back()->withInput()->with('error', 'Failed to create material request items: ' . json_encode($errors));
                }
                
                log_message('debug', 'Successfully inserted item with ID: ' . $itemInsertResult);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to save material request');
            }

            return redirect()->to('admin/material-requests')->with('success', 'Material request created successfully');

        } catch (\Exception $e) {
            log_message('error', 'Material request creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the material request');
        }
    }

    /**
     * Show material request details (alias for view)
     */
    public function show($id)
    {
        return $this->view($id);
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

        // Check if view exists before trying to load it
        if (file_exists(APPPATH . 'Views/procurement/material_requests/edit.php')) {
            return view('procurement/material_requests/edit', $data);
        } else {
            // Return error if view doesn't exist
            return redirect()->back()->with('error', 'Edit view not available for material requests');
        }
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
            'notes' => 'permit_empty|string'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Validate items
        $items = $this->request->getPost('items') ?: [];
        
        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'At least one material item is required');
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Calculate total estimated cost
            $totalEstimatedCost = 0;
            foreach ($items as $item) {
                $quantity = (float)($item['quantity_requested'] ?? 0);
                $unitCost = (float)($item['estimated_unit_cost'] ?? 0);
                $totalEstimatedCost += $quantity * $unitCost;
            }

            // Update material request
            $updateData = [
                'project_id' => $this->request->getPost('project_id') ?: null,
                'department_id' => $this->request->getPost('department_id') ?: null,
                'required_date' => $this->request->getPost('required_date') ?: null,
                'priority' => $this->request->getPost('priority'),
                'total_estimated_cost' => $totalEstimatedCost,
                'notes' => $this->request->getPost('notes')
            ];

            if (!$this->materialRequestModel->update($id, $updateData)) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Failed to update material request');
            }

            // Delete existing items and recreate them
            $this->materialRequestItemModel->where('material_request_id', $id)->delete();

            // Create new material request items
            foreach ($items as $item) {
                $quantity = (float)($item['quantity_requested'] ?? 0);
                $unitCost = (float)($item['estimated_unit_cost'] ?? 0);
                
                $itemData = [
                    'material_request_id' => $id,
                    'material_id' => $item['material_id'],
                    'quantity_requested' => $quantity,
                    'quantity_approved' => null,
                    'estimated_unit_cost' => $unitCost,
                    'estimated_total_cost' => $quantity * $unitCost,
                    'specification_notes' => $item['specification_notes'] ?? null,
                    'urgency_notes' => $item['urgency_notes'] ?? null
                ];

                if (!$this->materialRequestItemModel->insert($itemData)) {
                    $errors = $this->materialRequestItemModel->errors();
                    log_message('error', 'Failed to update material request item: ' . json_encode($errors));
                    $db->transRollback();
                    return redirect()->back()->withInput()->with('error', 'Failed to update material request items: ' . json_encode($errors));
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Failed to update material request');
            }

            return redirect()->to('admin/material-requests/' . $id)->with('success', 'Material request updated successfully');

        } catch (\Exception $e) {
            log_message('error', 'Material request update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the material request');
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

        if ($this->materialRequestModel->update($id, ['status' => MaterialRequestModel::STATUS_PENDING_APPROVAL])) {
            return redirect()->back()->with('success', 'Material request submitted for approval');
        } else {
            return redirect()->back()->with('error', 'Failed to submit material request');
        }
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
            return redirect()->back()->with('error', 'Only pending approval requests can be approved');
        }

        $approvalNotes = $this->request->getPost('approval_notes');
        
        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // First, approve the material request
            if (!$this->materialRequestModel->approveMaterialRequest($id, session('user_id'), $approvalNotes)) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Failed to approve material request');
            }

            // Then, approve all items by setting quantity_approved = quantity_requested
            $items = $this->materialRequestItemModel->where('material_request_id', $id)->findAll();
            
            foreach ($items as $item) {
                $this->materialRequestItemModel->update($item['id'], [
                    'quantity_approved' => $item['quantity_requested']
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Failed to approve material request items');
            }

            return redirect()->back()->with('success', 'Material request approved successfully');

        } catch (\Exception $e) {
            log_message('error', 'Material request approval failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while approving the material request');
        }
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
            return redirect()->back()->with('error', 'Only pending approval requests can be rejected');
        }

        $rejectionReason = $this->request->getPost('rejection_reason');
        
        if (empty($rejectionReason)) {
            return redirect()->back()->with('error', 'Rejection reason is required');
        }
        
        if ($this->materialRequestModel->rejectMaterialRequest($id, session('user_id'), $rejectionReason)) {
            return redirect()->back()->with('success', 'Material request rejected');
        } else {
            return redirect()->back()->with('error', 'Failed to reject material request');
        }
    }

    /**
     * Delete material request (only drafts)
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

        if ($this->materialRequestModel->delete($id)) {
            return redirect()->to('admin/material-requests')->with('success', 'Material request deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to delete material request');
        }
    }
}
