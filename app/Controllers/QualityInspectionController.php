<?php

namespace App\Controllers;

use App\Models\QualityInspectionModel;
use App\Models\GoodsReceiptNoteModel;
use App\Models\GoodsReceiptItemModel;
use App\Models\MaterialModel;
use App\Models\UserModel;

class QualityInspectionController extends BaseController
{
    protected $qualityInspectionModel;
    protected $grnModel;
    protected $grnItemModel;
    protected $materialModel;
    protected $userModel;

    public function __construct()
    {
        $this->qualityInspectionModel = new QualityInspectionModel();
        $this->grnModel = new GoodsReceiptNoteModel();
        $this->grnItemModel = new GoodsReceiptItemModel();
        $this->materialModel = new MaterialModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display list of quality inspections
     */
    public function index()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'inspection_type' => $this->request->getGet('inspection_type'),
            'inspector_id' => $this->request->getGet('inspector_id'),
            'material_id' => $this->request->getGet('material_id')
        ];

        // Remove empty filters
        $filters = array_filter($filters);

        $inspections = $this->qualityInspectionModel->getInspectionsWithDetails($filters);
        $inspectors = $this->userModel->findAll(); // In practice, you'd filter for users with inspector role
        $materials = $this->materialModel->findAll();

        $data = [
            'title' => 'Quality Inspections',
            'inspections' => $inspections,
            'inspectors' => $inspectors,
            'materials' => $materials,
            'filters' => $filters
        ];

        return view('procurement/quality_inspections/index', $data);
    }

    /**
     * Show create quality inspection form
     */
    public function create()
    {
        $grnItemId = $this->request->getGet('grn_item_id');
        $grnItem = null;

        if ($grnItemId) {
            $grnItem = $this->grnItemModel->select('goods_receipt_items.*, 
                    materials.name as material_name,
                    materials.item_code,
                    materials.unit,
                    goods_receipt_notes.grn_number,
                    suppliers.name as supplier_name')
                ->join('materials', 'materials.id = goods_receipt_items.material_id', 'left')
                ->join('goods_receipt_notes', 'goods_receipt_notes.id = goods_receipt_items.grn_id', 'left')
                ->join('suppliers', 'suppliers.id = goods_receipt_notes.supplier_id', 'left')
                ->find($grnItemId);
        }

        $data = [
            'title' => 'Create Quality Inspection',
            'grnItem' => $grnItem,
            'pendingItems' => $this->grnItemModel->getItemsPendingInspection(),
            'inspectors' => $this->userModel->findAll()
        ];

        return view('procurement/quality_inspections/create', $data);
    }

    /**
     * Store new quality inspection
     */
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'grn_item_id' => 'required|integer',
            'inspector_id' => 'required|integer',
            'inspection_type' => 'required|in_list[incoming,random,complaint,audit]',
            'quantity_inspected' => 'required|decimal|greater_than[0]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        try {
            $grnItemId = $this->request->getPost('grn_item_id');
            $inspectorId = $this->request->getPost('inspector_id');
            $inspectionType = $this->request->getPost('inspection_type');

            $inspectionId = $this->qualityInspectionModel->createFromGRNItem(
                $grnItemId,
                $inspectorId,
                $inspectionType
            );

            if ($inspectionId) {
                return redirect()->to('/admin/quality-inspections')->with('success', 'Quality inspection created successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to create quality inspection');
            }

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create quality inspection: ' . $e->getMessage());
        }
    }

    /**
     * Show quality inspection details
     */
    public function view($id)
    {
        $inspection = $this->qualityInspectionModel->getInspectionDetails($id);

        if (!$inspection) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Quality inspection not found');
        }

        $data = [
            'title' => 'Quality Inspection Details',
            'inspection' => $inspection
        ];

        return view('procurement/quality_inspections/view', $data);
    }

    /**
     * Show inspection form for conducting inspection
     */
    public function inspect($id)
    {
        $inspection = $this->qualityInspectionModel->getInspectionDetails($id);

        if (!$inspection) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Quality inspection not found');
        }

        if ($inspection['status'] !== QualityInspectionModel::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending inspections can be conducted');
        }

        // Check if current user is the assigned inspector
        if ($inspection['inspector_id'] != session('user_id')) {
            return redirect()->back()->with('error', 'You are not authorized to conduct this inspection');
        }

        $data = [
            'title' => 'Conduct Quality Inspection',
            'inspection' => $inspection
        ];

        return view('procurement/quality_inspections/inspect', $data);
    }

    /**
     * Complete quality inspection
     */
    public function complete($id)
    {
        $inspection = $this->qualityInspectionModel->find($id);

        if (!$inspection) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Quality inspection not found');
        }

        if ($inspection['status'] !== QualityInspectionModel::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending inspections can be completed');
        }

        // Check if current user is the assigned inspector
        if ($inspection['inspector_id'] != session('user_id')) {
            return redirect()->back()->with('error', 'You are not authorized to complete this inspection');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'status' => 'required|in_list[passed,failed,conditional]',
            'overall_grade' => 'permit_empty|in_list[A,B,C,D,F]',
            'quantity_passed' => 'required|decimal|greater_than_equal_to[0]',
            'quantity_failed' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'defect_description' => 'permit_empty|string',
            'corrective_action' => 'permit_empty|string',
            'inspector_notes' => 'permit_empty|string'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        try {
            $results = [
                'status' => $this->request->getPost('status'),
                'overall_grade' => $this->request->getPost('overall_grade'),
                'quantity_passed' => $this->request->getPost('quantity_passed'),
                'quantity_failed' => $this->request->getPost('quantity_failed') ?: 0,
                'defect_description' => $this->request->getPost('defect_description'),
                'corrective_action' => $this->request->getPost('corrective_action'),
                'inspector_notes' => $this->request->getPost('inspector_notes')
            ];

            // Validate quantities
            $totalInspected = $results['quantity_passed'] + $results['quantity_failed'];
            if ($totalInspected != $inspection['quantity_inspected']) {
                return redirect()->back()->withInput()->with('error', 'Total passed and failed quantities must equal inspected quantity');
            }

            $success = $this->qualityInspectionModel->completeInspection($id, $results);

            if ($success) {
                return redirect()->to('/admin/quality-inspections')->with('success', 'Quality inspection completed successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to complete quality inspection');
            }

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to complete quality inspection: ' . $e->getMessage());
        }
    }

    /**
     * Show edit quality inspection form
     */
    public function edit($id)
    {
        $inspection = $this->qualityInspectionModel->getInspectionDetails($id);

        if (!$inspection) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Quality inspection not found');
        }

        // Only allow editing of pending inspections
        if ($inspection['status'] !== QualityInspectionModel::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending inspections can be edited');
        }

        $data = [
            'title' => 'Edit Quality Inspection',
            'inspection' => $inspection,
            'inspectors' => $this->userModel->findAll()
        ];

        return view('procurement/quality_inspections/edit', $data);
    }

    /**
     * Update quality inspection
     */
    public function update($id)
    {
        $inspection = $this->qualityInspectionModel->find($id);

        if (!$inspection) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Quality inspection not found');
        }

        // Only allow updating of pending inspections
        if ($inspection['status'] !== QualityInspectionModel::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending inspections can be updated');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'inspector_id' => 'required|integer',
            'inspection_type' => 'required|in_list[incoming,random,complaint,audit]',
            'quantity_inspected' => 'required|decimal|greater_than[0]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        try {
            $inspectionData = [
                'inspector_id' => $this->request->getPost('inspector_id'),
                'inspection_type' => $this->request->getPost('inspection_type'),
                'quantity_inspected' => $this->request->getPost('quantity_inspected')
            ];

            $this->qualityInspectionModel->update($id, $inspectionData);

            return redirect()->to('/admin/quality-inspections')->with('success', 'Quality inspection updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update quality inspection: ' . $e->getMessage());
        }
    }

    /**
     * Delete quality inspection
     */
    public function delete($id)
    {
        $inspection = $this->qualityInspectionModel->find($id);

        if (!$inspection) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Quality inspection not found');
        }

        // Only allow deletion of pending inspections
        if ($inspection['status'] !== QualityInspectionModel::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending inspections can be deleted');
        }

        $this->qualityInspectionModel->delete($id);

        return redirect()->to('/admin/quality-inspections')->with('success', 'Quality inspection deleted successfully');
    }

    /**
     * Get pending inspections for current user
     */
    public function myInspections()
    {
        $inspections = $this->qualityInspectionModel->getInspectionsByInspector(session('user_id'));

        $data = [
            'title' => 'My Quality Inspections',
            'inspections' => $inspections
        ];

        return view('procurement/quality_inspections/my_inspections', $data);
    }

    /**
     * Get GRN items pending inspection for AJAX
     */
    public function getPendingItems()
    {
        $items = $this->grnItemModel->getItemsPendingInspection();
        
        return $this->response->setJSON([
            'success' => true,
            'items' => $items
        ]);
    }
}
