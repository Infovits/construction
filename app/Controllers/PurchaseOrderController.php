<?php

namespace App\Controllers;

use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderItemModel;
use App\Models\MaterialRequestModel;
use App\Models\MaterialRequestItemModel;
use App\Models\SupplierModel;
use App\Models\MaterialModel;
use App\Models\ProjectModel;

class PurchaseOrderController extends BaseController
{
    protected $purchaseOrderModel;
    protected $purchaseOrderItemModel;
    protected $materialRequestModel;
    protected $materialRequestItemModel;
    protected $supplierModel;
    protected $materialModel;
    protected $projectModel;

    public function __construct()
    {
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->purchaseOrderItemModel = new PurchaseOrderItemModel();
        $this->materialRequestModel = new MaterialRequestModel();
        $this->materialRequestItemModel = new MaterialRequestItemModel();
        $this->supplierModel = new SupplierModel();
        $this->materialModel = new MaterialModel();
        $this->projectModel = new ProjectModel();
    }

    /**
     * Display list of purchase orders
     */
    public function index()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'supplier_id' => $this->request->getGet('supplier_id'),
            'project_id' => $this->request->getGet('project_id'),
            'created_by' => $this->request->getGet('created_by')
        ];

        // Remove empty filters
        $filters = array_filter($filters);

        $purchaseOrders = $this->purchaseOrderModel->getPurchaseOrdersWithDetails($filters);
        $suppliers = $this->supplierModel->findAll();
        $projects = $this->projectModel->findAll();

        $data = [
            'title' => 'Purchase Orders',
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => $suppliers,
            'projects' => $projects,
            'filters' => $filters
        ];

        return view('procurement/purchase_orders/index', $data);
    }

    /**
     * Show create purchase order form
     */
    public function create()
    {
        $materialRequestId = $this->request->getGet('material_request_id');
        $materialRequest = null;
        $materialRequestItems = [];

        if ($materialRequestId) {
            $materialRequest = $this->materialRequestModel->getMaterialRequestWithItems($materialRequestId);
            if ($materialRequest && $materialRequest['status'] === MaterialRequestModel::STATUS_APPROVED) {
                $materialRequestItems = $this->materialRequestItemModel->getItemsToBePurchased($materialRequestId);
            }
        }

        // Generate PO number
        $poNumber = $this->generatePONumber();

        // Get approved material requests
        $materialRequests = $this->materialRequestModel->getApprovedRequests();

        $data = [
            'title' => 'Create Purchase Order',
            'po_number' => $poNumber,
            'suppliers' => $this->supplierModel->findAll(),
            'projects' => $this->projectModel->findAll(),
            'materials' => $this->materialModel->findAll(),
            'materialRequests' => $materialRequests,
            'materialRequest' => $materialRequest,
            'materialRequestItems' => $materialRequestItems
        ];

        return view('procurement/purchase_orders/create', $data);
    }

    /**
     * Store new purchase order
     */
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'supplier_id' => 'required|integer',
            'material_request_id' => 'permit_empty|integer',
            'project_id' => 'permit_empty|integer',
            'expected_delivery_date' => 'permit_empty|valid_date',
            'payment_terms' => 'permit_empty|string',
            'delivery_terms' => 'permit_empty|string',
            'tax_amount' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'freight_cost' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'currency' => 'permit_empty|string|max_length[3]',
            'notes' => 'permit_empty|string',
            'terms_conditions' => 'permit_empty|string'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Basic validation of items
        $items = $this->request->getPost('items') ?: [];
        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'At least one item is required');
        }

        try {
            // Debug: Log the incoming data
            log_message('debug', 'PO Store - POST data: ' . json_encode($this->request->getPost()));
            log_message('debug', 'PO Store - Items: ' . json_encode($items));
            log_message('debug', 'PO Store - User ID: ' . session('user_id'));

            $materialRequestId = $this->request->getPost('material_request_id') ?: null;
            $supplierId = $this->request->getPost('supplier_id');
            
            // Validate supplier exists
            if (!$this->supplierModel->find($supplierId)) {
                return redirect()->back()->withInput()->with('error', 'Selected supplier does not exist');
            }
            
            // Validate items data
            foreach ($items as $index => $item) {
                if (empty($item['material_id'])) {
                    return redirect()->back()->withInput()->with('error', "Item #" . ($index + 1) . " is missing material selection");
                }
                if (empty($item['quantity_ordered']) || $item['quantity_ordered'] <= 0) {
                    return redirect()->back()->withInput()->with('error', "Item #" . ($index + 1) . " has invalid quantity");
                }
                if (empty($item['unit_cost']) || $item['unit_cost'] < 0) {
                    return redirect()->back()->withInput()->with('error', "Item #" . ($index + 1) . " has invalid unit cost");
                }
                
                // Verify material exists
                if (!$this->materialModel->find($item['material_id'])) {
                    return redirect()->back()->withInput()->with('error', "Material for item #" . ($index + 1) . " does not exist");
                }
            }
            
            // Calculate totals
            $subtotal = 0;
            foreach ($items as $item) {
                $quantity = (float)($item['quantity_ordered'] ?? 0);
                $unitCost = (float)($item['unit_cost'] ?? 0);
                $subtotal += $quantity * $unitCost;
            }
            
            $taxAmount = (float)($this->request->getPost('tax_amount') ?: 0);
            $freightCost = (float)($this->request->getPost('freight_cost') ?: 0);
            $totalAmount = $subtotal + $taxAmount + $freightCost;

            // Prepare order data
            $orderData = [
                'po_number' => $this->generatePONumber(),
                'supplier_id' => (int)$supplierId,
                'material_request_id' => $materialRequestId ? (int)$materialRequestId : null,
                'project_id' => $this->request->getPost('project_id') ? (int)$this->request->getPost('project_id') : null,
                'po_date' => date('Y-m-d'),
                'expected_delivery_date' => $this->request->getPost('expected_delivery_date') ?: null,
                'status' => 'draft',
                'payment_terms' => $this->request->getPost('payment_terms') ?: null,
                'delivery_terms' => $this->request->getPost('delivery_terms') ?: null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'freight_cost' => $freightCost,
                'total_amount' => $totalAmount,
                'currency' => $this->request->getPost('currency') ?: 'MWK',
                'notes' => $this->request->getPost('notes') ?: null,
                'terms_conditions' => $this->request->getPost('terms_conditions') ?: null,
                'created_by' => (int)session('user_id')
            ];

            log_message('debug', 'PO Store - Order data: ' . json_encode($orderData));

            // Create purchase order with items
            $poId = $this->purchaseOrderModel->createPurchaseOrderWithItems($orderData, $items);

            if ($poId) {
                log_message('info', "Purchase order created successfully with ID: $poId");
                return redirect()->to('/admin/purchase-orders')->with('success', "Purchase order {$orderData['po_number']} created successfully");
            } else {
                log_message('error', 'Purchase order creation returned false/null');
                
                // Get detailed error information
                $errors = $this->purchaseOrderModel->errors();
                if ($errors) {
                    $errorMsg = 'Validation errors: ' . implode(', ', $errors);
                } else {
                    $errorMsg = 'Failed to create purchase order. Please check your data and try again.';
                }
                
                return redirect()->back()->withInput()->with('error', $errorMsg);
            }

        } catch (\Exception $e) {
            log_message('error', 'Purchase order creation exception: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->withInput()->with('error', 'Error creating purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Show purchase order details
     */
    public function view($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->getPurchaseOrderWithItems($id);

        if (!$purchaseOrder) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Purchase order not found');
        }

        // Get delivery status summary
        $deliveryStatus = $this->purchaseOrderItemModel->getDeliveryStatusSummary($id);

        // Ensure we have items array and add additional fields needed by view
        $purchaseOrderItems = $purchaseOrder['items'] ?? [];
        
        // Add created_by_name for display
        $purchaseOrder['created_by_name'] = trim(($purchaseOrder['creator_first_name'] ?? '') . ' ' . ($purchaseOrder['creator_last_name'] ?? ''));

        $data = [
            'title' => 'Purchase Order Details',
            'purchaseOrder' => $purchaseOrder,
            'purchaseOrderItems' => $purchaseOrderItems,
            'deliveryStatus' => $deliveryStatus
        ];

        return view('procurement/purchase_orders/view', $data);
    }

    /**
     * Show edit purchase order form
     */
    public function edit($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->getPurchaseOrderWithItems($id);

        if (!$purchaseOrder) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Purchase order not found');
        }

        // Only allow editing of draft purchase orders
        if ($purchaseOrder['status'] !== PurchaseOrderModel::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Only draft purchase orders can be edited');
        }

        $data = [
            'title' => 'Edit Purchase Order',
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => $this->supplierModel->findAll(),
            'projects' => $this->projectModel->findAll(),
            'materials' => $this->materialModel->findAll()
        ];

        return view('procurement/purchase_orders/edit', $data);
    }

    /**
     * Update purchase order
     */
    public function update($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);

        if (!$purchaseOrder) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Purchase order not found');
        }

        // Only allow updating of draft purchase orders
        if ($purchaseOrder['status'] !== PurchaseOrderModel::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Only draft purchase orders can be updated');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'supplier_id' => 'required|integer',
            'project_id' => 'permit_empty|integer',
            'expected_delivery_date' => 'permit_empty|valid_date',
            'payment_terms' => 'permit_empty|string',
            'delivery_terms' => 'permit_empty|string',
            'tax_amount' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'freight_cost' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'currency' => 'permit_empty|string|max_length[3]',
            'notes' => 'permit_empty|string',
            'terms_conditions' => 'permit_empty|string',
            'items' => 'required|array',
            'items.*.material_id' => 'required|integer',
            'items.*.quantity_ordered' => 'required|decimal|greater_than[0]',
            'items.*.unit_cost' => 'required|decimal|greater_than[0]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calculate totals
            $items = $this->request->getPost('items');
            $subtotal = 0;
            
            foreach ($items as $item) {
                $subtotal += $item['quantity_ordered'] * $item['unit_cost'];
            }

            $taxAmount = $this->request->getPost('tax_amount') ?: 0;
            $freightCost = $this->request->getPost('freight_cost') ?: 0;
            $totalAmount = $subtotal + $taxAmount + $freightCost;

            // Update purchase order
            $orderData = [
                'supplier_id' => $this->request->getPost('supplier_id'),
                'project_id' => $this->request->getPost('project_id') ?: null,
                'expected_delivery_date' => $this->request->getPost('expected_delivery_date') ?: null,
                'payment_terms' => $this->request->getPost('payment_terms'),
                'delivery_terms' => $this->request->getPost('delivery_terms'),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'freight_cost' => $freightCost,
                'total_amount' => $totalAmount,
                'currency' => $this->request->getPost('currency') ?: 'USD',
                'notes' => $this->request->getPost('notes'),
                'terms_conditions' => $this->request->getPost('terms_conditions')
            ];

            $this->purchaseOrderModel->update($id, $orderData);

            // Delete existing items and recreate
            $db->table('purchase_order_items')->where('purchase_order_id', $id)->delete();

            // Create new purchase order items
            foreach ($items as $item) {
                $itemTotal = $item['quantity_ordered'] * $item['unit_cost'];

                $itemData = [
                    'purchase_order_id' => $id,
                    'material_id' => $item['material_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $itemTotal,
                    'quantity_pending' => $item['quantity_ordered'],
                    'specification_notes' => $item['specification_notes'] ?? null,
                    'delivery_date' => $item['delivery_date'] ?? null
                ];

                $this->purchaseOrderItemModel->insert($itemData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Failed to update purchase order');
            }

            return redirect()->to('/admin/purchase-orders')->with('success', 'Purchase order updated successfully');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to update purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Approve and send purchase order
     */
    public function approve($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);

        if (!$purchaseOrder) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Purchase order not found');
        }

        if ($purchaseOrder['status'] !== PurchaseOrderModel::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Only draft purchase orders can be approved');
        }

        $this->purchaseOrderModel->approvePurchaseOrder($id, session('user_id'));

        return redirect()->back()->with('success', 'Purchase order approved and sent to supplier');
    }

    /**
     * Acknowledge purchase order (supplier confirmed receipt)
     */
    public function acknowledge($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);

        if (!$purchaseOrder) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Purchase order not found');
        }

        if ($purchaseOrder['status'] !== PurchaseOrderModel::STATUS_SENT) {
            return redirect()->back()->with('error', 'Only sent purchase orders can be acknowledged');
        }

        $this->purchaseOrderModel->update($id, [
            'status' => PurchaseOrderModel::STATUS_ACKNOWLEDGED
        ]);

        return redirect()->back()->with('success', 'Purchase order marked as acknowledged by supplier');
    }

    /**
     * Cancel purchase order
     */
    public function cancel($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);

        if (!$purchaseOrder) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Purchase order not found');
        }

        if (in_array($purchaseOrder['status'], [PurchaseOrderModel::STATUS_COMPLETED, PurchaseOrderModel::STATUS_CANCELLED])) {
            return redirect()->back()->with('error', 'Cannot cancel completed or already cancelled purchase orders');
        }

        $this->purchaseOrderModel->update($id, [
            'status' => PurchaseOrderModel::STATUS_CANCELLED
        ]);

        return redirect()->back()->with('success', 'Purchase order cancelled successfully');
    }

    /**
     * Delete purchase order
     */
    public function delete($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->find($id);

        if (!$purchaseOrder) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Purchase order not found');
        }

        // Only allow deletion of draft purchase orders
        if ($purchaseOrder['status'] !== PurchaseOrderModel::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Only draft purchase orders can be deleted');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Delete items first
        $db->table('purchase_order_items')->where('purchase_order_id', $id)->delete();
        
        // Delete purchase order
        $this->purchaseOrderModel->delete($id);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to delete purchase order');
        }

        return redirect()->to('/admin/purchase-orders')->with('success', 'Purchase order deleted successfully');
    }

    /**
     * Generate PDF for purchase order
     */
    public function generatePDF($id)
    {
        $purchaseOrder = $this->purchaseOrderModel->getPurchaseOrderWithItems($id);

        if (!$purchaseOrder) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Purchase order not found');
        }

        // Generate PDF logic here
        // This would typically use a PDF library like TCPDF or mPDF
        
        $data = [
            'purchaseOrder' => $purchaseOrder
        ];

        return view('procurement/purchase_orders/pdf', $data);
    }

    /**
     * Generate PO number
     */
    private function generatePONumber()
    {
        $lastPO = $this->purchaseOrderModel
            ->select('po_number')
            ->like('po_number', 'PO-' . date('Y') . '-', 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        $sequence = 1;
        if ($lastPO) {
            $parts = explode('-', $lastPO['po_number']);
            if (count($parts) >= 3) {
                $sequence = intval($parts[2]) + 1;
            }
        }
        
        return 'PO-' . date('Y') . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get Purchase Order items via AJAX for goods receipt
     */
    public function getPurchaseOrderItems($purchaseOrderId)
    {
        $this->response->setContentType('application/json');

        try {
            // Get purchase order with items
            $purchaseOrder = $this->purchaseOrderModel->getPurchaseOrderWithItems($purchaseOrderId);
            
            if (!$purchaseOrder) {
                return $this->response->setJSON(['success' => false, 'error' => 'Purchase order not found']);
            }

            // Check if PO is in a state that allows goods receipt
            if (!in_array($purchaseOrder['status'], ['sent', 'acknowledged', 'partially_received'])) {
                return $this->response->setJSON([
                    'success' => false, 
                    'error' => 'Purchase order status does not allow goods receipt'
                ]);
            }

            // Get items that still have pending quantities
            $items = [];
            foreach ($purchaseOrder['items'] as $item) {
                if ($item['quantity_pending'] > 0) {
                    $items[] = [
                        'id' => $item['id'],
                        'material_id' => $item['material_id'],
                        'material_name' => $item['material_name'],
                        'material_code' => $item['item_code'],
                        'unit' => $item['unit'],
                        'quantity_ordered' => $item['quantity_ordered'],
                        'quantity_received' => $item['quantity_received'],
                        'quantity_pending' => $item['quantity_pending'],
                        'unit_cost' => $item['unit_cost'],
                        'specification_notes' => $item['specification_notes'] ?? ''
                    ];
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'items' => $items,
                'supplier' => [
                    'id' => $purchaseOrder['supplier_id'],
                    'name' => $purchaseOrder['supplier_name']
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting purchase order items: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Failed to retrieve purchase order items']);
        }
    }

    /**
     * Get Material Request items via AJAX
     */
    public function getMaterialRequestItems($materialRequestId)
    {
        // Accept both AJAX and direct requests for flexibility
        // Check for AJAX or XMLHttpRequest header
        $isAjax = $this->request->isAJAX() || 
                  $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' ||
                  $this->request->getHeaderLine('Content-Type') === 'application/json' ||
                  $this->request->getGet('ajax') === '1';
        
        // Always return JSON response
        $this->response->setContentType('application/json');

        try {
            // Get the material request with items
            $materialRequest = $this->materialRequestModel->getMaterialRequestWithItems($materialRequestId);
            
            if (!$materialRequest) {
                return $this->response->setJSON(['error' => 'Material request not found']);
            }

            // Check if the request is approved
            if ($materialRequest['status'] !== MaterialRequestModel::STATUS_APPROVED) {
                return $this->response->setJSON(['error' => 'Material request is not approved']);
            }

            // Get items that can be purchased
            $items = $this->materialRequestItemModel->getItemsToBePurchased($materialRequestId);
            
            // Format items for frontend consumption
            $formattedItems = [];
            foreach ($items as $item) {
                $formattedItems[] = [
                    'id' => $item['id'],
                    'material_id' => $item['material_id'],
                    'material_name' => $item['material_name'],
                    'item_code' => $item['item_code'],
                    'unit' => $item['unit'],
                    'quantity_requested' => $item['quantity_requested'],
                    'quantity_approved' => $item['quantity_approved'],
                    'estimated_unit_cost' => $item['estimated_unit_cost'],
                    'current_unit_cost' => $item['current_unit_cost'],
                    'specification_notes' => $item['specification_notes'],
                    'preferred_supplier_name' => $item['preferred_supplier_name'] ?? null,
                    'preferred_supplier_id' => $item['preferred_supplier_id'] ?? null
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'materialRequest' => [
                    'id' => $materialRequest['id'],
                    'request_number' => $materialRequest['request_number'],
                    'project_id' => $materialRequest['project_id'],
                    'project_name' => $materialRequest['project_name'] ?? null,
                    'total_estimated_cost' => $materialRequest['total_estimated_cost']
                ],
                'items' => $formattedItems
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error getting material request items: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Failed to retrieve material request items']);
        }
    }
}

