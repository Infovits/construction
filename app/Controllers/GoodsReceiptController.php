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
            'items' => 'required|array',
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

        return view('procurement/goods_receipt/edit', $data);
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
            'items' => 'required|array',
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

            $this->grnModel->update($id, $grnData);

            // Update GRN items
            foreach ($items as $itemId => $item) {
                $itemData = [
                    'quantity_delivered' => $item['quantity_delivered'],
                    'unit_cost' => $item['unit_cost'],
                    'batch_number' => $item['batch_number'],
                    'expiry_date' => $item['expiry_date'] ?: null,
                    'notes' => $item['notes']
                ];

                $this->grnItemModel->update($itemId, $itemData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Failed to update goods receipt note');
            }

            return redirect()->to('/admin/goods-receipt')->with('success', 'Goods receipt note updated successfully');

        } catch (\Exception $e) {
            $db->transRollback();
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
}
