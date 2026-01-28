<?php

namespace App\Controllers;

use App\Models\MaterialRequestModel;
use App\Models\PurchaseOrderModel;
use App\Models\GoodsReceiptNoteModel;
use App\Models\QualityInspectionModel;
use App\Models\SupplierModel;
use App\Models\ProjectModel;
use App\Libraries\ExcelExport;
use App\Libraries\DomPDFWrapper;

class ProcurementReportsController extends BaseController
{
    protected $materialRequestModel;
    protected $purchaseOrderModel;
    protected $grnModel;
    protected $qualityInspectionModel;
    protected $supplierModel;
    protected $projectModel;

    public function __construct()
    {
        $this->materialRequestModel = new MaterialRequestModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->grnModel = new GoodsReceiptNoteModel();
        $this->qualityInspectionModel = new QualityInspectionModel();
        $this->supplierModel = new SupplierModel();
        $this->projectModel = new ProjectModel();
    }

    /**
     * Display procurement reports dashboard
     */
    public function index()
    {
        // Get counts for dashboard stats
        $materialRequestsCount = $this->materialRequestModel->countAllResults();
        $purchaseOrdersCount = $this->purchaseOrderModel->countAllResults();
        $goodsReceiptsCount = $this->grnModel->countAllResults();
        $inspectionsCount = $this->qualityInspectionModel->countAllResults();

        $data = [
            'title' => 'Procurement Reports',
            'suppliers' => $this->supplierModel->findAll(),
            'projects' => $this->projectModel->findAll(),
            'materialRequestsCount' => $materialRequestsCount,
            'purchaseOrdersCount' => $purchaseOrdersCount,
            'goodsReceiptsCount' => $goodsReceiptsCount,
            'inspectionsCount' => $inspectionsCount
        ];

        return view('procurement/reports/index', $data);
    }

    /**
     * Generate procurement reports
     */
    public function generate()
    {
        $reportType = $this->request->getPost('report_type');
        $dateFrom = $this->request->getPost('date_from');
        $dateTo = $this->request->getPost('date_to');
        $supplierId = $this->request->getPost('supplier_id');
        $projectId = $this->request->getPost('project_id');

        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'supplier_id' => $supplierId,
            'project_id' => $projectId
        ];

        $filters = array_filter($filters);
        $data = [];

        switch ($reportType) {
            case 'material_requests':
                $data['reportData'] = $this->materialRequestModel->getMaterialRequestsWithDetails($filters);
                $data['reportTitle'] = 'Material Requests Report';
                break;

            case 'purchase_orders':
                $data['reportData'] = $this->purchaseOrderModel->getPurchaseOrdersWithDetails($filters);
                $data['reportTitle'] = 'Purchase Orders Report';
                break;

            case 'goods_receipt':
                $data['reportData'] = $this->grnModel->getGRNsWithDetails($filters);
                $data['reportTitle'] = 'Goods Receipt Report';
                break;

            case 'quality_inspections':
                $data['reportData'] = $this->qualityInspectionModel->getInspectionsWithDetails($filters);
                $data['reportTitle'] = 'Quality Inspections Report';
                break;

            case 'procurement_summary':
                $data['reportData'] = [
                    'material_requests' => $this->materialRequestModel->getSummaryStats($filters),
                    'purchase_orders' => $this->purchaseOrderModel->getSummaryStats($filters),
                    'goods_receipt' => $this->grnModel->getSummaryStats($filters),
                    'quality_inspections' => $this->qualityInspectionModel->getSummaryStats($filters)
                ];
                $data['reportTitle'] = 'Procurement Summary Report';
                break;

            default:
                return redirect()->back()->with('error', 'Invalid report type selected');
        }

        $data['title'] = $data['reportTitle'];

        // Render the report results view
        return view('procurement/reports/report_results', $data);
    }

    /**
     * Export report to Excel
     */
    public function exportExcel($reportTitle)
    {
        // URL decode the report title
        $reportTitle = urldecode($reportTitle);
        
        // Get the same filters that would be used for generating the report
        $filters = $this->getReportFilters();
        $data = $this->getReportData($reportTitle, $filters);

        if (empty($data['reportData'])) {
            return redirect()->back()->with('error', 'No data available for export');
        }

        $excelExport = new ExcelExport();
        $excelExport->setTitle($reportTitle);
        $excelExport->setGeneratedDate(date('Y-m-d H:i:s'));

        // Prepare data based on report type
        switch ($reportTitle) {
            case 'Material Requests Report':
                $excelExport->setHeaders(['ID', 'Date', 'Project', 'Requested By', 'Status', 'Items', 'Priority']);
                $rows = [];
                foreach ($data['reportData'] as $item) {
                    $rows[] = [
                        $item['id'],
                        date('Y-m-d', strtotime($item['request_date'])),
                        $item['project_name'] ?? 'N/A',
                        trim(($item['requester_first_name'] ?? '') . ' ' . ($item['requester_last_name'] ?? '')) ?: 'N/A',
                        $item['status'],
                        $item['item_count'] ?? 0,
                        $item['priority']
                    ];
                }
                $excelExport->setData($rows);
                break;

            case 'Purchase Orders Report':
                $excelExport->setHeaders(['PO #', 'Date', 'Supplier', 'Project', 'Status', 'Total Value', 'Items']);
                $rows = [];
                foreach ($data['reportData'] as $item) {
                    $rows[] = [
                        $item['po_number'],
                        date('Y-m-d', strtotime($item['po_date'])),
                        $item['supplier_name'],
                        $item['project_name'] ?? 'N/A',
                        $item['status'],
                        $item['total_amount'],
                        $item['item_count'] ?? 0
                    ];
                }
                $excelExport->setData($rows);
                break;

            case 'Goods Receipt Report':
                $excelExport->setHeaders(['GRN #', 'Date', 'PO Reference', 'Supplier', 'Received By', 'Items', 'Status']);
                $rows = [];
                foreach ($data['reportData'] as $item) {
                    $rows[] = [
                        $item['grn_number'],
                        date('Y-m-d', strtotime($item['delivery_date'])),
                        $item['po_number'],
                        $item['supplier_name'],
                        $item['received_by_name'],
                        $item['item_count'] ?? 0,
                        $item['status']
                    ];
                }
                $excelExport->setData($rows);
                break;

            case 'Quality Inspections Report':
                $excelExport->setHeaders(['Inspection #', 'Date', 'GRN Reference', 'Inspector', 'Status', 'Items Passed', 'Items Failed']);
                $rows = [];
                foreach ($data['reportData'] as $item) {
                    $rows[] = [
                        $item['inspection_number'],
                        date('Y-m-d', strtotime($item['inspection_date'])),
                        $item['grn_number'],
                        $item['inspector_name'],
                        $item['status'],
                        $item['items_passed'] ?? 0,
                        $item['items_failed'] ?? 0
                    ];
                }
                $excelExport->setData($rows);
                break;

            case 'Procurement Summary Report':
                // For summary reports, create a different format
                $excelExport->setHeaders(['Category', 'Total', 'Pending', 'Completed/Approved', 'Rejected/Failed', 'Total Value']);
                $rows = [];
                
                // Material Requests Summary
                $mr = $data['reportData']['material_requests'];
                $rows[] = [
                    'Material Requests',
                    $mr['total'] ?? 0,
                    $mr['pending'] ?? 0,
                    $mr['approved'] ?? 0,
                    $mr['rejected'] ?? 0,
                    ''
                ];

                // Purchase Orders Summary
                $po = $data['reportData']['purchase_orders'];
                $rows[] = [
                    'Purchase Orders',
                    $po['total'] ?? 0,
                    $po['pending'] ?? 0,
                    $po['completed'] ?? 0,
                    '',
                    $po['total_value'] ?? 0
                ];

                // Goods Receipt Summary
                $gr = $data['reportData']['goods_receipt'];
                $rows[] = [
                    'Goods Receipt',
                    $gr['total'] ?? 0,
                    $gr['pending'] ?? 0,
                    $gr['completed'] ?? 0,
                    '',
                    ''
                ];

                // Quality Inspections Summary
                $qi = $data['reportData']['quality_inspections'];
                $rows[] = [
                    'Quality Inspections',
                    $qi['total'] ?? 0,
                    $qi['pending'] ?? 0,
                    $qi['passed'] ?? 0,
                    $qi['failed'] ?? 0,
                    ''
                ];

                $excelExport->setData($rows);
                break;

            default:
                return redirect()->back()->with('error', 'Invalid report type for export');
        }

        // Generate and download the Excel file
        $filename = $excelExport->generateExcel($reportTitle);
        $filepath = WRITEPATH . 'uploads/' . $filename;

        if (file_exists($filepath)) {
            return $this->response->download($filepath, null)->setFileName($filename);
        } else {
            return redirect()->back()->with('error', 'Failed to generate Excel file');
        }
    }

    /**
     * Export report to PDF
     */
    public function exportPdf($reportTitle)
    {
        // URL decode the report title
        $reportTitle = urldecode($reportTitle);
        
        // Get the same filters that would be used for generating the report
        $filters = $this->getReportFilters();
        $data = $this->getReportData($reportTitle, $filters);

        if (empty($data['reportData'])) {
            return redirect()->back()->with('error', 'No data available for export');
        }

        $data['title'] = $reportTitle;
        $data['generated_date'] = date('F d, Y');

        // Load the appropriate PDF template based on report type
        $view = 'procurement/reports/pdf/' . strtolower(str_replace(' ', '_', $reportTitle));
        
        // Check if the specific template exists, otherwise use a generic one
        if (!file_exists(APPPATH . 'Views/' . $view . '.php')) {
            $view = 'procurement/reports/pdf/generic_report';
        }

        $html = view($view, $data);

        $pdf = new DomPDFWrapper();
        $pdf->generatePdf($html, $reportTitle . '_' . date('Y-m-d_H-i-s') . '.pdf', 'D');
    }

    /**
     * Helper method to get report filters from request or session
     */
    private function getReportFilters()
    {
        $dateFrom = $this->request->getGet('date_from') ?? $this->request->getPost('date_from');
        $dateTo = $this->request->getGet('date_to') ?? $this->request->getPost('date_to');
        $supplierId = $this->request->getGet('supplier_id') ?? $this->request->getPost('supplier_id');
        $projectId = $this->request->getGet('project_id') ?? $this->request->getPost('project_id');

        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'supplier_id' => $supplierId,
            'project_id' => $projectId
        ];

        return array_filter($filters);
    }

    /**
     * Helper method to get report data based on title and filters
     */
    private function getReportData($reportTitle, $filters)
    {
        $data = [];

        switch ($reportTitle) {
            case 'Material Requests Report':
                $data['reportData'] = $this->materialRequestModel->getMaterialRequestsWithDetails($filters);
                break;

            case 'Purchase Orders Report':
                $data['reportData'] = $this->purchaseOrderModel->getPurchaseOrdersWithDetails($filters);
                break;

            case 'Goods Receipt Report':
                $data['reportData'] = $this->grnModel->getGRNsWithDetails($filters);
                break;

            case 'Quality Inspections Report':
                $data['reportData'] = $this->qualityInspectionModel->getInspectionsWithDetails($filters);
                break;

            case 'Procurement Summary Report':
                $data['reportData'] = [
                    'material_requests' => $this->materialRequestModel->getSummaryStats($filters),
                    'purchase_orders' => $this->purchaseOrderModel->getSummaryStats($filters),
                    'goods_receipt' => $this->grnModel->getSummaryStats($filters),
                    'quality_inspections' => $this->qualityInspectionModel->getSummaryStats($filters)
                ];
                break;

            default:
                $data['reportData'] = [];
        }

        return $data;
    }
}
