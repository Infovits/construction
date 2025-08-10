<?php

namespace App\Controllers;

use App\Models\MaterialRequestModel;
use App\Models\PurchaseOrderModel;
use App\Models\GoodsReceiptNoteModel;
use App\Models\QualityInspectionModel;
use App\Models\SupplierModel;
use App\Models\ProjectModel;

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
        $data = [
            'title' => 'Procurement Reports',
            'suppliers' => $this->supplierModel->findAll(),
            'projects' => $this->projectModel->findAll()
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
        $data['filters'] = $filters;
        $data['reportType'] = $reportType;

        return view('procurement/reports/generate', $data);
    }
}