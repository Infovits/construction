<?php

namespace App\Controllers;

use App\Models\GoodsReceiptNoteModel;
use App\Models\GoodsReceiptItemModel;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderItemModel;
use App\Models\SupplierModel;
use App\Models\WarehouseModel;
use App\Models\MaterialModel;

class GoodsReceiptController extends BaseController
{
    protected $grnModel;
    protected $grnItemModel;
    protected $purchaseOrderModel;
    protected $purchaseOrderItemModel;
    protected $supplierModel;
    protected $warehouseModel;
    protected $materialModel;

    public function __construct()
    {
        $this->grnModel = new GoodsReceiptNoteModel();
        $this->grnItemModel = new GoodsReceiptItemModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->purchaseOrderItemModel = new PurchaseOrderItemModel();
        $this->supplierModel = new SupplierModel();
        $this->warehouseModel = new WarehouseModel();
        $this->materialModel = new MaterialModel();
    }

    /**
     * Display list of goods receipt notes
     */
    public function index()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'supplier_id' => $this->request->getGet('supplier_id'),
            'warehouse_id' => $this->request->getGet('warehouse_id'),
            'purchase_order_id' => $this->request->getGet('purchase_order_id')
        ];

        // Remove empty filters
        $filters = array_filter($filters);

        $grns = $this->grnModel->getGRNsWithDetails($filters);
        $suppliers = $this->supplierModel->findAll();
        $warehouses = $this->warehouseModel->findAll();
        $purchaseOrders = $this->purchaseOrderModel->getPOsReadyForDelivery();

        $data = [
            'title' => 'Goods Receipt Notes',
            'grns' => $grns,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'purchaseOrders' => $purchaseOrders,
            'filters' => $filters
        ];

        return view('procurement/goods_receipt/index', $data);
    }

    /**
     * Show create goods receipt form
     */
    public function create()
    {
        $purchaseOrderId = $this->request->getGet('purchase_order_id');
        $purchaseOrder = null;
        $purchaseOrderItems = [];

        if ($purchaseOrderId) {
            $purchaseOrder = $this->purchaseOrderModel->getPurchaseOrderWithItems($purchaseOrderId);
            if ($purchaseOrder && in_array($purchaseOrder['status'], [PurchaseOrderModel::STATUS_SENT, PurchaseOrderModel::STATUS_ACKNOWLEDGED])) {
                $purchaseOrderItems = $this->purchaseOrderItemModel->getItemsReadyForReceipt($purchaseOrderId);
            }
        }

        $data = [
            'title' => 'Create Goods Receipt Note',
            'purchaseOrders' => $this->purchaseOrderModel->getPOsReadyForDelivery(),
            'warehouses' => $this->warehouseModel->findAll(),
            'purchaseOrder' => $purchaseOrder,
            'purchaseOrderItems' => $purchaseOrderItems
        ];

        return view('procurement/goods_receipt/create', $data);
    }

    /**
     * Store new goods receipt note
     */
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'purchase_order_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'delivery_date' => 'required|valid_date',
            'delivery_note_number' => 'permit_empty|string',
            'vehicle_number' => 'permit_empty|string',
            'driver_name' => 'permit_empty|string',
            'freight_cost' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'notes' => 'permit_empty|string',
            'items' => 'required',
            'items.*.purchase_order_item_id' => 'required|integer',
            'items.*.material_id' => 'required|integer',
            'items.*.quantity_delivered' => 'required|decimal|greater_than[0]',
            'items.*.unit_cost' => 'required|decimal|greater_than[0]',
            'items.*.batch_number' => 'permit_empty|string',
            'items.*.expiry_date' => 'permit_empty|valid_date',
            'items.*.notes' => 'permit_empty|string'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        try {
            $purchaseOrderId = $this->request->getPost('purchase_order_id');
            $items = $this->request->getPost('items');

            $grnData = [
                'warehouse_id' => $this->request->getPost('warehouse_id'),
                'delivery_date' => $this->request->getPost('delivery_date'),
                'delivery_note_number' => $this->request->getPost('delivery_note_number'),
                'vehicle_number' => $this->request->getPost('vehicle_number'),
                'driver_name' => $this->request->getPost('driver_name'),
                'freight_cost' => $this->request->getPost('freight_cost') ?: 0,
                'notes' => $this->request->getPost('notes')
            ];

            $grnId = $this->grnModel->createFromPurchaseOrder(
                $purchaseOrderId,
                $grnData,
                $items,
                session('user_id')
            );

            if ($grnId) {
                // Update purchase order items received quantities
                foreach ($items as $item) {
                    $this->purchaseOrderItemModel->updateReceivedQuantity(
                        $item['purchase_order_item_id'],
                        $item['quantity_delivered']
                    );
                }

                // Update purchase order status
                if ($this->purchaseOrderItemModel->isPurchaseOrderFullyReceived($purchaseOrderId)) {
                    $this->purchaseOrderModel->update($purchaseOrderId, [
                        'status' => PurchaseOrderModel::STATUS_COMPLETED
                    ]);
                } else {
                    $this->purchaseOrderModel->update($purchaseOrderId, [
                        'status' => PurchaseOrderModel::STATUS_PARTIALLY_RECEIVED
                    ]);
                }

                return redirect()->to('/admin/goods-receipt')->with('success', 'Goods receipt note created successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to create goods receipt note');
            }

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create goods receipt note: ' . $e->getMessage());
        }
    }

    /**
     * Show goods receipt note details
     */
    public function view($id)
    {
        $grn = $this->grnModel->getGRNWithItems($id);

        if (!$grn) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Goods receipt note not found');
        }

        // Extract items from GRN data
        $grnItems = $grn['items'] ?? [];

        $data = [
            'title' => 'Goods Receipt Note Details',
            'grn' => $grn,
            'grnItems' => $grnItems
        ];

        return view('procurement/goods_receipt/view', $data);
    }

    /**
     * Show edit goods receipt form
     */
    public function edit($id)
    {
        $grn = $this->grnModel->getGRNWithItems($id);

        if (!$grn) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Goods receipt note not found');
        }

        // Only allow editing of pending inspection GRNs
        if ($grn['status'] !== GoodsReceiptNoteModel::STATUS_PENDING_INSPECTION) {
            return redirect()->back()->with('error', 'Only pending inspection GRNs can be edited');
        }

        $data = [
            'title' => 'Edit Goods Receipt Note',
            'grn' => $grn,
            'warehouses' => $this->warehouseModel->findAll()
        ];

        // Check if view exists before trying to load it
        if (file_exists(APPPATH . 'Views/procurement/goods_receipt/edit.php')) {
            return view('procurement/goods_receipt/edit', $data);
        } else {
            // Return error if view doesn't exist
            return redirect()->back()->with('error', 'Edit view not available for goods receipt');
        }
    }

    /**
     * Update goods receipt note
     */
    public function update($id)
    {
        $grn = $this->grnModel->find($id);

        if (!$grn) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Goods receipt note not found');
        }

        // Only allow updating of pending inspection GRNs
        if ($grn['status'] !== GoodsReceiptNoteModel::STATUS_PENDING_INSPECTION) {
            return redirect()->back()->with('error', 'Only pending inspection GRNs can be updated');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'warehouse_id' => 'required|integer',
            'delivery_date' => 'required|valid_date',
            'delivery_note_number' => 'permit_empty|string',
            'vehicle_number' => 'permit_empty|string',
            'driver_name' => 'permit_empty|string',
            'freight_cost' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'notes' => 'permit_empty|string',
            'items' => 'required',
            'items.*.quantity_delivered' => 'required|decimal|greater_than[0]',
            'items.*.unit_cost' => 'required|decimal|greater_than[0]',
            'items.*.batch_number' => 'permit_empty|string',
            'items.*.expiry_date' => 'permit_empty|valid_date',
            'items.*.notes' => 'permit_empty|string'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calculate total value
            $items = $this->request->getPost('items');
            $totalValue = 0;
            
            foreach ($items as $itemId => $item) {
                $totalValue += $item['quantity_delivered'] * $item['unit_cost'];
            }

            // Update GRN
            $grnData = [
                'warehouse_id' => $this->request->getPost('warehouse_id'),
                'delivery_date' => $this->request->getPost('delivery_date'),
                'delivery_note_number' => $this->request->getPost('delivery_note_number'),
                'vehicle_number' => $this->request->getPost('vehicle_number'),
                'driver_name' => $this->request->getPost('driver_name'),
                'freight_cost' => $this->request->getPost('freight_cost') ?: 0,
                'total_received_value' => $totalValue,
                'notes' => $this->request->getPost('notes')
            ];

            log_message('debug', 'Updating GRN ' . $id . ' with data: ' . json_encode($grnData));
            
            if (!$this->grnModel->update($id, $grnData)) {
                throw new \Exception('Failed to update GRN: ' . json_encode($this->grnModel->errors()));
            }

            log_message('debug', 'GRN updated successfully, updating items: ' . json_encode($items));

            // Update GRN items
            foreach ($items as $itemId => $item) {
                $itemData = [
                    'quantity_delivered' => $item['quantity_delivered'],
                    'unit_cost' => $item['unit_cost'],
                    'batch_number' => $item['batch_number'],
                    'expiry_date' => $item['expiry_date'] ?: null,
                    'notes' => $item['notes']
                ];

                log_message('debug', 'Updating GRN item ' . $itemId . ' with data: ' . json_encode($itemData));
                
                if (!$this->grnItemModel->update($itemId, $itemData)) {
                    throw new \Exception('Failed to update GRN item ' . $itemId . ': ' . json_encode($this->grnItemModel->errors()));
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return redirect()->to('/admin/goods-receipt')->with('success', 'Goods receipt note updated successfully');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'GRN update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update goods receipt note: ' . $e->getMessage());
        }
    }

    /**
     * Accept goods receipt (after quality inspection)
     */
    public function accept($id)
    {
        $grn = $this->grnModel->find($id);

        if (!$grn) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Goods receipt note not found');
        }

        if ($grn['status'] !== GoodsReceiptNoteModel::STATUS_PENDING_INSPECTION) {
            return redirect()->back()->with('error', 'Only pending inspection GRNs can be accepted');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update GRN status
            $this->grnModel->update($id, [
                'status' => GoodsReceiptNoteModel::STATUS_ACCEPTED
            ]);

            // Create stock movements for accepted items
            $this->grnItemModel->createStockMovements($id, session('user_id'));

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Failed to accept goods receipt');
            }

            return redirect()->back()->with('success', 'Goods receipt accepted and stock updated');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Failed to accept goods receipt: ' . $e->getMessage());
        }
    }

    /**
     * Reject goods receipt
     */
    public function reject($id)
    {
        $grn = $this->grnModel->find($id);

        if (!$grn) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Goods receipt note not found');
        }

        if ($grn['status'] !== GoodsReceiptNoteModel::STATUS_PENDING_INSPECTION) {
            return redirect()->back()->with('error', 'Only pending inspection GRNs can be rejected');
        }

        $this->grnModel->update($id, [
            'status' => GoodsReceiptNoteModel::STATUS_REJECTED
        ]);

        return redirect()->back()->with('success', 'Goods receipt rejected');
    }

    /**
     * Get purchase order items for AJAX
     */
    public function getPurchaseOrderItems($purchaseOrderId)
    {
        $items = $this->purchaseOrderItemModel->getItemsReadyForReceipt($purchaseOrderId);
        
        return $this->response->setJSON([
            'success' => true,
            'items' => $items
        ]);
    }

    /**
     * Generate PDF for goods receipt note
     */
    public function generatePDF($id)
    {
        $grn = $this->grnModel->getGRNWithItems($id);

        if (!$grn) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Goods receipt note not found');
        }

        // Extract items from GRN data
        $grnItems = $grn['items'] ?? [];

        // Get company information for PDF
        $companyModel = new \App\Models\CompanyModel();
        $companyInfo = $companyModel->getCompanyInfo();

        // Initialize DomPDFWrapper
        $pdf = new \App\Libraries\DomPDFWrapper($companyInfo);

        // Generate HTML content for the PDF
        $html = $this->generateGRNPDFContent($grn, $grnItems);

        // Generate and return PDF
        return $pdf->generatePdf($html, 'GRN-' . $grn['grn_number'] . '-' . date('Y-m-d') . '.pdf', 'I');
    }

    /**
     * Generate HTML content for GRN PDF
     */
    private function generateGRNPDFContent($grn, $grnItems)
    {
        $companyName = $grn['company_name'] ?? 'Construction Management System';
        $companyAddress = $grn['company_address'] ?? 'Default Address';
        $date = date('Y-m-d H:i:s');

        $html = '<!DOCTYPE html>';
        $html .= '<html><head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>Goods Receipt Note - ' . $grn['grn_number'] . '</title>';
        $html .= '<style>';
        $html .= $this->getGRNPDFStyles();
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
        $html .= '<h2>Goods Receipt Note</h2>';
        $html .= '<p class="report-date">GRN No: ' . $grn['grn_number'] . '</p>';
        $html .= '<p class="report-date">Generated on: ' . $date . '</p>';
        $html .= '</div>';
        $html .= '</div>';

        // GRN Details
        $html .= '<div class="content">';
        $html .= '<div class="grn-details">';
        $html .= '<h3>Goods Receipt Details</h3>';
        $html .= '<div class="details-grid">';
        $html .= '<div class="detail-item"><strong>GRN Number:</strong> ' . $grn['grn_number'] . '</div>';
        $html .= '<div class="detail-item"><strong>Purchase Order:</strong> PO #' . $grn['po_number'] . '</div>';
        $html .= '<div class="detail-item"><strong>Delivery Date:</strong> ' . date('M j, Y', strtotime($grn['delivery_date'])) . '</div>';
        $html .= '<div class="detail-item"><strong>Warehouse:</strong> ' . $grn['warehouse_name'] . '</div>';
        $html .= '<div class="detail-item"><strong>Received By:</strong> ' . $grn['received_by_name'] . '</div>';
        $html .= '<div class="detail-item"><strong>Status:</strong> ' . ucfirst(str_replace('_', ' ', $grn['status'])) . '</div>';
        
        if (!empty($grn['delivery_note_number'])) {
            $html .= '<div class="detail-item"><strong>Delivery Note:</strong> ' . $grn['delivery_note_number'] . '</div>';
        }
        if (!empty($grn['vehicle_number'])) {
            $html .= '<div class="detail-item"><strong>Vehicle Number:</strong> ' . $grn['vehicle_number'] . '</div>';
        }
        if (!empty($grn['driver_name'])) {
            $html .= '<div class="detail-item"><strong>Driver Name:</strong> ' . $grn['driver_name'] . '</div>';
        }
        if (!empty($grn['total_received_value'])) {
            $html .= '<div class="detail-item"><strong>Total Value:</strong> MWK ' . number_format($grn['total_received_value'], 2) . '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';

        // Supplier Information
        $html .= '<div class="supplier-info">';
        $html .= '<h3>Supplier Information</h3>';
        $html .= '<div class="details-grid">';
        $html .= '<div class="detail-item"><strong>Supplier:</strong> ' . $grn['supplier_name'] . '</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Items Table
        $html .= '<div class="items-section">';
        $html .= '<h3>Received Items</h3>';
        
        if (!empty($grnItems)) {
            $html .= '<table class="items-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>#</th>';
            $html .= '<th>Material</th>';
            $html .= '<th>Ordered</th>';
            $html .= '<th>Delivered</th>';
            $html .= '<th>Accepted</th>';
            $html .= '<th>Quality Status</th>';
            $html .= '<th>Unit Cost</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            $counter = 1;
            foreach ($grnItems as $item) {
                $statusClass = 'status-normal';
                $statusText = ucfirst($item['quality_status']);
                
                if ($item['quality_status'] === 'failed') {
                    $statusClass = 'status-critical';
                } elseif ($item['quality_status'] === 'pending') {
                    $statusClass = 'status-low';
                }

                $html .= '<tr>';
                $html .= '<td>' . $counter . '</td>';
                $html .= '<td>';
                $html .= '<strong>' . $item['material_name'] . '</strong><br>';
                $html .= '<small style="color: #666;">Code: ' . $item['material_code'] . '</small>';
                $html .= '</td>';
                $html .= '<td>' . number_format($item['quantity_ordered'], 2) . ' ' . $item['material_unit'] . '</td>';
                $html .= '<td>' . number_format($item['quantity_delivered'], 2) . ' ' . $item['material_unit'] . '</td>';
                $html .= '<td>' . number_format($item['quantity_accepted'], 2) . ' ' . $item['material_unit'] . '</td>';
                $html .= '<td class="' . $statusClass . '">' . $statusText . '</td>';
                $html .= '<td style="text-align: right;">MWK ' . number_format($item['unit_cost'], 2) . '</td>';
                $html .= '</tr>';

                $counter++;
            }

            $html .= '</tbody>';
            $html .= '</table>';
        } else {
            $html .= '<div style="text-align: center; padding: 20px; color: #777; font-size: 16px;">No items found</div>';
        }

        $html .= '</div>';

        // Notes
        if (!empty($grn['notes'])) {
            $html .= '<div class="notes-section">';
            $html .= '<h3>Notes</h3>';
            $html .= '<div class="notes-content">' . nl2br(htmlspecialchars($grn['notes'])) . '</div>';
            $html .= '</div>';
        }

        // Signatures
        $html .= '<div class="signatures-section">';
        $html .= '<h3>Authorizations</h3>';
        $html .= '<div class="signatures-grid">';
        $html .= '<div class="signature-card">';
        $html .= '<div class="signature-title">RECEIVER</div>';
        $html .= '<div class="signature-area"></div>';
        $html .= '<div class="signature-name">' . $grn['received_by_name'] . '</div>';
        $html .= '<div class="signature-label">Name & Signature</div>';
        $html .= '</div>';
        
        $html .= '<div class="signature-card">';
        $html .= '<div class="signature-title">QUALITY INSPECTOR</div>';
        $html .= '<div class="signature-area"></div>';
        $html .= '<div class="signature-name">_________________</div>';
        $html .= '<div class="signature-label">Name & Signature</div>';
        $html .= '</div>';
        
        $html .= '<div class="signature-card">';
        $html .= '<div class="signature-title">WAREHOUSE MANAGER</div>';
        $html .= '<div class="signature-area"></div>';
        $html .= '<div class="signature-name">_________________</div>';
        $html .= '<div class="signature-label">Name & Signature</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Footer
        $html .= '<div class="footer">';
        $html .= '<p>Goods Receipt Note - Generated by Construction Management System</p>';
        $html .= '<p>Document ID: ' . $grn['grn_number'] . ' | Date: ' . date('F j, Y \a\t g:i A') . '</p>';
        $html .= '</div>';

        $html .= '</div>';
        $html .= '</body></html>';

        return $html;
    }

    /**
     * Get CSS styles for GRN PDF
     */
    private function getGRNPDFStyles()
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
            
            .grn-details, .supplier-info, .items-section, .notes-section {
                margin-bottom: 20px;
            }
            
            .grn-details h3, .supplier-info h3, .items-section h3, .notes-section h3 {
                margin: 0 0 15px 0;
                font-size: 14px;
                color: #495057;
                border-bottom: 2px solid #007bff;
                padding-bottom: 5px;
            }
            
            .details-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 10px;
            }
            
            .detail-item {
                background-color: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
                border: 1px solid #dee2e6;
                font-size: 12px;
            }
            
            .detail-item strong {
                color: #495057;
                display: block;
                margin-bottom: 2px;
            }
            
            .items-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 11px;
                margin-bottom: 20px;
            }
            
            .items-table th {
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                padding: 8px;
                font-weight: bold;
                text-align: left;
                font-size: 12px;
            }
            
            .items-table td {
                border: 1px solid #dee2e6;
                padding: 8px;
                font-size: 11px;
            }
            
            .items-table tr:nth-child(even) {
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
            
            .status-normal {
                color: #155724;
                font-weight: bold;
                background-color: #d4edda;
                text-align: center;
            }
            
            .notes-section {
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 20px;
            }
            
            .notes-content {
                font-size: 12px;
                line-height: 1.6;
                color: #475569;
            }
            
            .signatures-section {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #dee2e6;
            }
            
            .signatures-section h3 {
                margin: 0 0 15px 0;
                font-size: 14px;
                color: #495057;
            }
            
            .signatures-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
            }
            
            .signature-card {
                background: white;
                border-radius: 12px;
                padding: 30px 20px;
                text-align: center;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                border: 2px solid #f1f5f9;
            }
            
            .signature-title {
                font-weight: 700;
                color: #1e293b;
                font-size: 12px;
                letter-spacing: 1px;
                margin-bottom: 20px;
            }
            
            .signature-area {
                height: 60px;
                border-bottom: 2px solid #667eea;
                margin: 20px 0;
                position: relative;
            }
            
            .signature-name {
                font-weight: 600;
                color: #475569;
                font-size: 14px;
                margin-bottom: 8px;
            }
            
            .signature-label {
                font-size: 11px;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.5px;
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
}
