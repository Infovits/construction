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

            // Handle inspection criteria
            $criteria = $this->request->getPost('criteria');
            if (!empty($criteria)) {
                // Filter out empty values and N/A values
                $filteredCriteria = [];
                foreach ($criteria as $key => $value) {
                    if (!empty($value) && $value !== '') {
                        $filteredCriteria[$key] = $value;
                    }
                }
                
                if (!empty($filteredCriteria)) {
                    $results['criteria'] = json_encode($filteredCriteria);
                } else {
                    $results['criteria'] = null;
                }
            } else {
                $results['criteria'] = null;
            }

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
            // Debug: Log the form data
            log_message('debug', 'Quality Inspection Update - Form Data: ' . print_r($this->request->getPost(), true));
            
            $inspectionData = [
                'inspector_id' => $this->request->getPost('inspector_id'),
                'inspection_type' => $this->request->getPost('inspection_type'),
                'quantity_inspected' => $this->request->getPost('quantity_inspected')
            ];

            // Handle inspection criteria
            $criteria = $this->request->getPost('criteria');
            if (!empty($criteria)) {
                // Filter out empty values and N/A values
                $filteredCriteria = [];
                foreach ($criteria as $key => $value) {
                    if (!empty($value) && $value !== '') {
                        $filteredCriteria[$key] = $value;
                    }
                }
                
                if (!empty($filteredCriteria)) {
                    $inspectionData['criteria'] = json_encode($filteredCriteria);
                } else {
                    $inspectionData['criteria'] = null;
                }
            } else {
                $inspectionData['criteria'] = null;
            }

            // Debug: Log the inspection data
            log_message('debug', 'Quality Inspection Update - Inspection Data: ' . print_r($inspectionData, true));
            
            // Debug: Check if model update returns true
            $result = $this->qualityInspectionModel->update($id, $inspectionData);
            log_message('debug', 'Quality Inspection Update - Model Result: ' . ($result ? 'true' : 'false'));
            
            if ($result) {
                return redirect()->to('/admin/quality-inspections')->with('success', 'Quality inspection updated successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update quality inspection: No changes made or validation failed');
            }

        } catch (\Exception $e) {
            log_message('error', 'Quality Inspection Update - Exception: ' . $e->getMessage());
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

        // Check if view exists before trying to load it
        if (file_exists(APPPATH . 'Views/procurement/quality_inspections/my_inspections.php')) {
            return view('procurement/quality_inspections/my_inspections', $data);
        } else {
            // Return error if view doesn't exist
            return redirect()->back()->with('error', 'My inspections view not available');
        }
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

    /**
     * Export quality inspections report to PDF
     */
    public function exportPdf()
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

        // Get company information for PDF
        $companyModel = new \App\Models\CompanyModel();
        $companyInfo = $companyModel->getCompanyInfo();

        // Initialize DomPDFWrapper
        $pdf = new \App\Libraries\DomPDFWrapper($companyInfo);

        // Generate HTML content for the PDF
        $html = $this->generateInspectionReportPDFContent($inspections, $filters);

        // Generate and return PDF
        return $pdf->generatePdf($html, 'Quality_Inspections_Report_' . date('Y-m-d') . '.pdf', 'I');
    }

    /**
     * Generate HTML content for inspection report PDF
     */
    private function generateInspectionReportPDFContent($inspections, $filters)
    {
        // Get company info from the exportPdf method
        $companyModel = new \App\Models\CompanyModel();
        $companyInfo = $companyModel->getCompanyInfo();
        
        $companyName = $companyInfo['name'] ?? 'Construction Management System';
        $companyAddress = $companyInfo['address'] ?? 'Default Address';
        $date = date('Y-m-d H:i:s');

        $html = '<!DOCTYPE html>';
        $html .= '<html><head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>Quality Inspections Report</title>';
        $html .= '<style>';
        $html .= $this->getInspectionPDFStyles();
        $html .= '</style>';
        $html .= '</head><body>';

        // Header
        $html .= '<div class="header">';
        $html .= '<div class="company-info">';
        $html .= '<div class="company-details">';
        $html .= '<h1>' . $companyName . '</h1>';
        $html .= '<p>' . $companyAddress . '</p>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="report-title">';
        $html .= '<h2>Quality Inspections Report</h2>';
        $html .= '<p class="report-date">Generated on: ' . $date . '</p>';
        $html .= '</div>';
        $html .= '</div>';

        // Filters Summary
        $html .= $this->getFiltersSummary($filters);

        // Summary Section
        if (!empty($inspections)) {
            $totalInspections = count($inspections);
            $passedInspections = count(array_filter($inspections, function($i) { return $i['status'] === 'passed'; }));
            $failedInspections = count(array_filter($inspections, function($i) { return $i['status'] === 'failed'; }));
            $pendingInspections = count(array_filter($inspections, function($i) { return $i['status'] === 'pending'; }));
            $conditionalInspections = count(array_filter($inspections, function($i) { return $i['status'] === 'conditional'; }));

            $html .= '<div class="summary-section">';
            $html .= '<h3>Inspection Summary</h3>';
            $html .= '<div class="summary-grid">';
            $html .= '<div class="summary-item"><strong>Total Inspections:</strong> ' . $totalInspections . '</div>';
            $html .= '<div class="summary-item"><strong>Passed:</strong> ' . $passedInspections . '</div>';
            $html .= '<div class="summary-item"><strong>Failed:</strong> ' . $failedInspections . '</div>';
            $html .= '<div class="summary-item"><strong>Pending:</strong> ' . $pendingInspections . '</div>';
            $html .= '<div class="summary-item"><strong>Conditional:</strong> ' . $conditionalInspections . '</div>';
            if ($totalInspections > 0) {
                $passRate = round(($passedInspections / $totalInspections) * 100, 1);
                $html .= '<div class="summary-item"><strong>Pass Rate:</strong> ' . $passRate . '%</div>';
            }
            $html .= '</div>';
            $html .= '</div>';
        }

        // Inspections Table
        $html .= '<div class="content">';
        $html .= '<h3>Quality Inspections</h3>';
        
        if (!empty($inspections)) {
            $html .= '<table class="data-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>#</th>';
            $html .= '<th>Inspection No.</th>';
            $html .= '<th>Material</th>';
            $html .= '<th>GRN</th>';
            $html .= '<th>Type</th>';
            $html .= '<th>Inspector</th>';
            $html .= '<th>Status</th>';
            $html .= '<th>Date</th>';
            $html .= '<th>Grade</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            $counter = 1;
            foreach ($inspections as $inspection) {
                $statusClass = 'status-normal';
                $statusText = ucfirst($inspection['status']);
                
                if ($inspection['status'] === 'failed') {
                    $statusClass = 'status-critical';
                } elseif ($inspection['status'] === 'pending') {
                    $statusClass = 'status-low';
                } elseif ($inspection['status'] === 'conditional') {
                    $statusClass = 'status-warning';
                }

                $html .= '<tr>';
                $html .= '<td>' . $counter . '</td>';
                $html .= '<td>' . $inspection['inspection_number'] . '</td>';
                $html .= '<td>' . ($inspection['material_name'] ?? 'N/A') . ' (' . ($inspection['material_code'] ?? '') . ')</td>';
                $html .= '<td>' . ($inspection['grn_number'] ?? 'N/A') . '</td>';
                $html .= '<td>' . ucfirst($inspection['inspection_type']) . '</td>';
                $html .= '<td>' . ($inspection['inspector_name'] ?? 'Unassigned') . '</td>';
                $html .= '<td class="' . $statusClass . '">' . $statusText . '</td>';
                $html .= '<td>' . date('M j, Y', strtotime($inspection['inspection_date'])) . '</td>';
                $html .= '<td>' . ($inspection['overall_grade'] ?? 'N/A') . '</td>';
                $html .= '</tr>';

                $counter++;
            }

            $html .= '</tbody>';
            $html .= '</table>';
        } else {
            $html .= '<div style="text-align: center; padding: 20px; color: #777; font-size: 16px;">No quality inspections found matching your criteria.</div>';
        }

        $html .= $this->getPDFFooter();

        return $html;
    }

    /**
     * Get CSS styles for inspection PDF
     */
    private function getInspectionPDFStyles()
    {
        return '
            body {
                font-family: "DejaVu Sans", sans-serif;
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .header {
                border-bottom: 2px solid #333;
                padding-bottom: 20px;
                margin-bottom: 20px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }
            
            .company-info {
                display: flex;
                align-items: center;
            }
            
            .company-details h1 {
                margin: 0 0 5px 0;
                font-size: 20px;
                font-weight: bold;
            }
            
            .company-details p {
                margin: 0;
                font-size: 12px;
                color: #666;
            }
            
            .report-title {
                text-align: right;
            }
            
            .report-title h2 {
                margin: 0 0 5px 0;
                font-size: 18px;
                color: #333;
            }
            
            .report-date {
                margin: 0;
                font-size: 11px;
                color: #666;
            }
            
            .content {
                margin-bottom: 30px;
            }
            
            .summary-section {
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 20px;
            }
            
            .summary-section h3 {
                margin: 0 0 15px 0;
                font-size: 14px;
                color: #495057;
            }
            
            .summary-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 10px;
            }
            
            .summary-item {
                background-color: #fff;
                padding: 10px;
                border-radius: 4px;
                border: 1px solid #dee2e6;
                font-size: 12px;
            }
            
            .summary-item strong {
                color: #495057;
                display: block;
                margin-bottom: 2px;
            }
            
            .filters-summary {
                background-color: #f5f5f5;
                padding: 10px;
                margin-bottom: 20px;
                border-radius: 4px;
                font-size: 12px;
                border-left: 4px solid #007bff;
            }
            
            .data-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 11px;
                margin-bottom: 20px;
            }
            
            .data-table th {
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                padding: 8px;
                font-weight: bold;
                text-align: left;
                font-size: 12px;
            }
            
            .data-table td {
                border: 1px solid #dee2e6;
                padding: 8px;
                font-size: 11px;
            }
            
            .data-table tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            .status-critical {
                color: #dc3545;
                font-weight: bold;
                background-color: #f8d7da;
                text-align: center;
            }
            
            .status-low {
                color: #856404;
                font-weight: bold;
                background-color: #fff3cd;
                text-align: center;
            }
            
            .status-warning {
                color: #856404;
                font-weight: bold;
                background-color: #fff3cd;
                text-align: center;
            }
            
            .status-normal {
                color: #155724;
                font-weight: bold;
                background-color: #d4edda;
                text-align: center;
            }
            
            .footer {
                border-top: 1px solid #dee2e6;
                padding-top: 10px;
                margin-top: 30px;
                display: flex;
                justify-content: space-between;
                font-size: 10px;
                color: #666;
            }
        ';
    }

    /**
     * Generate filters summary for PDF
     */
    private function getFiltersSummary($filters)
    {
        if (empty($filters)) {
            return '';
        }

        $html = '<div class="filters-summary">';
        $html .= '<strong>Applied Filters:</strong> ';
        
        $filterTexts = [];
        if (!empty($filters['status'])) {
            $filterTexts[] = 'Status: ' . ucfirst($filters['status']);
        }
        if (!empty($filters['inspection_type'])) {
            $filterTexts[] = 'Type: ' . ucfirst($filters['inspection_type']);
        }
        if (!empty($filters['inspector_id'])) {
            $inspector = $this->userModel->find($filters['inspector_id']);
            if ($inspector) {
                $filterTexts[] = 'Inspector: ' . $inspector['first_name'] . ' ' . $inspector['last_name'];
            }
        }
        if (!empty($filters['material_id'])) {
            $material = $this->materialModel->find($filters['material_id']);
            if ($material) {
                $filterTexts[] = 'Material: ' . $material['name'];
            }
        }

        $html .= implode(' | ', $filterTexts);
        $html .= '</div>';

        return $html;
    }

    /**
     * Generate PDF footer
     */
    private function getPDFFooter()
    {
        return '
            <div class="footer">
                <div>Generated by Construction Management System</div>
                <div>Page <script type="text/php">if (isset($pdf)) { $font = Font_Metrics::get_font("helvetica", "normal"); $pdf->page_text(520, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0)); }</script></div>
            </div>
        ';
    }
}
