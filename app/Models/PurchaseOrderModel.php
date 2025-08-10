<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderModel extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'po_number', 'supplier_id', 'material_request_id', 'project_id',
        'po_date', 'expected_delivery_date', 'status', 'payment_terms',
        'delivery_terms', 'subtotal', 'tax_amount', 'freight_cost',
        'total_amount', 'currency', 'created_by', 'approved_by',
        'approved_date', 'sent_date', 'notes', 'terms_conditions'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_ACKNOWLEDGED = 'acknowledged';
    const STATUS_PARTIALLY_RECEIVED = 'partially_received';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get purchase orders with supplier info
     *
     * @param array $filters Optional filters
     * @return array List of purchase orders with supplier info
     */
    public function getPurchaseOrdersWithDetails($filters = [])
    {
        $builder = $this->select('purchase_orders.*, 
                suppliers.name as supplier_name,
                suppliers.contact_person,
                suppliers.phone,
                suppliers.email,
                creator.first_name as creator_first_name,
                creator.last_name as creator_last_name,
                approver.first_name as approver_first_name,
                approver.last_name as approver_last_name,
                projects.name as project_name,
                material_requests.request_number as material_request_number')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
            ->join('users as creator', 'creator.id = purchase_orders.created_by', 'left')
            ->join('users as approver', 'approver.id = purchase_orders.approved_by', 'left')
            ->join('projects', 'projects.id = purchase_orders.project_id', 'left')
            ->join('material_requests', 'material_requests.id = purchase_orders.material_request_id', 'left');

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('purchase_orders.status', $filters['status']);
        }

        if (!empty($filters['supplier_id'])) {
            $builder->where('purchase_orders.supplier_id', $filters['supplier_id']);
        }

        if (!empty($filters['project_id'])) {
            $builder->where('purchase_orders.project_id', $filters['project_id']);
        }

        if (!empty($filters['created_by'])) {
            $builder->where('purchase_orders.created_by', $filters['created_by']);
        }

        return $builder->orderBy('purchase_orders.created_at', 'DESC')->findAll();
    }

    /**
     * Get purchase order details with items
     *
     * @param int $id The purchase order ID
     * @return array Purchase order details with items
     */
    public function getPurchaseOrderWithItems($id)
    {
        $order = $this->select('purchase_orders.*, 
                suppliers.name as supplier_name,
                suppliers.contact_person,
                suppliers.phone,
                suppliers.email,
                suppliers.address as supplier_address,
                creator.first_name as creator_first_name,
                creator.last_name as creator_last_name,
                approver.first_name as approver_first_name,
                approver.last_name as approver_last_name,
                projects.name as project_name,
                material_requests.request_number,
                material_requests.request_number as material_request_number')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
            ->join('users as creator', 'creator.id = purchase_orders.created_by', 'left')
            ->join('users as approver', 'approver.id = purchase_orders.approved_by', 'left')
            ->join('projects', 'projects.id = purchase_orders.project_id', 'left')
            ->join('material_requests', 'material_requests.id = purchase_orders.material_request_id', 'left')
            ->find($id);

        if (!$order) {
            return null;
        }

        // Get purchase order items
        $db = \Config\Database::connect();
        $items = $db->table('purchase_order_items')
            ->select('purchase_order_items.*, 
                materials.name as material_name,
                materials.item_code,
                materials.item_code as material_code,
                materials.unit,
                materials.unit as material_unit,
                material_request_items.quantity_requested,
                material_request_items.specification_notes')
            ->join('materials', 'materials.id = purchase_order_items.material_id', 'left')
            ->join('material_request_items', 'material_request_items.id = purchase_order_items.material_request_item_id', 'left')
            ->where('purchase_order_id', $id)
            ->get()
            ->getResultArray();

        $order['items'] = $items;

        return $order;
    }

    /**
     * Generate next PO number
     *
     * @return string The next PO number
     */
    public function generatePoNumber()
    {
        $lastPo = $this->orderBy('id', 'DESC')->first();
        $prefix = 'PO-' . date('Ymd') . '-';

        if (!$lastPo) {
            return $prefix . '0001';
        }

        // Extract sequence number from last PO
        $lastPoNumber = $lastPo['po_number'];
        $lastSequence = 0;

        if (preg_match('/PO-\d{8}-(\d+)/', $lastPoNumber, $matches)) {
            $lastSequence = (int) $matches[1];
        }

        // Increment sequence
        $nextSequence = $lastSequence + 1;

        // Format with leading zeros
        return $prefix . sprintf('%04d', $nextSequence);
    }

    /**
     * Create purchase order from material request
     *
     * @param int $materialRequestId Material request ID
     * @param int $supplierId Supplier ID
     * @param array $items Items to order
     * @param array $orderData Additional order data
     * @param int $userId User ID creating the order
     * @return int|bool The new purchase order ID or false on failure
     */
    public function createFromMaterialRequest($materialRequestId, $supplierId, array $items, array $orderData, $userId)
    {
        if (empty($items)) {
            return false;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Get material request details
        $materialRequestModel = new MaterialRequestModel();
        $materialRequest = $materialRequestModel->find($materialRequestId);

        if (!$materialRequest) {
            $db->transRollback();
            return false;
        }

        // Generate PO number
        $poNumber = $this->generatePoNumber();

        // Calculate totals
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['quantity_ordered'] * $item['unit_cost'];
        }

        $taxAmount = $orderData['tax_amount'] ?? 0;
        $freightCost = $orderData['freight_cost'] ?? 0;
        $totalAmount = $subtotal + $taxAmount + $freightCost;

        // Create purchase order
        $poData = [
            'po_number' => $poNumber,
            'supplier_id' => $supplierId,
            'material_request_id' => $materialRequestId,
            'project_id' => $materialRequest['project_id'],
            'po_date' => date('Y-m-d'),
            'expected_delivery_date' => $orderData['expected_delivery_date'] ?? null,
            'status' => self::STATUS_DRAFT,
            'payment_terms' => $orderData['payment_terms'] ?? null,
            'delivery_terms' => $orderData['delivery_terms'] ?? null,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'freight_cost' => $freightCost,
            'total_amount' => $totalAmount,
            'currency' => $orderData['currency'] ?? 'MWK',
            'notes' => $orderData['notes'] ?? null,
            'terms_conditions' => $orderData['terms_conditions'] ?? null,
            'created_by' => $userId
        ];

        $this->insert($poData);
        $poId = $db->insertID();

        if (!$poId) {
            $db->transRollback();
            return false;
        }

        // Insert purchase order items
        $poItemsModel = new PurchaseOrderItemModel();

        foreach ($items as $item) {
            $itemTotal = $item['quantity_ordered'] * $item['unit_cost'];

            $poItemData = [
                'purchase_order_id' => $poId,
                'material_id' => $item['material_id'],
                'material_request_item_id' => $item['material_request_item_id'] ?? null,
                'quantity_ordered' => $item['quantity_ordered'],
                'unit_cost' => $item['unit_cost'],
                'total_cost' => $itemTotal,
                'quantity_pending' => $item['quantity_ordered'],
                'specification_notes' => $item['specification_notes'] ?? null,
                'delivery_date' => $item['delivery_date'] ?? null
            ];

            if (!$poItemsModel->insert($poItemData)) {
                $db->transRollback();
                return false;
            }
        }

        $db->transComplete();
        return $db->transStatus() === false ? false : $poId;
    }

    /**
     * Approve purchase order
     *
     * @param int $id Purchase order ID
     * @param int $approvedBy User ID who approved
     * @return bool Success status
     */
    public function approvePurchaseOrder($id, $approvedBy)
    {
        return $this->update($id, [
            'status' => self::STATUS_SENT,
            'approved_by' => $approvedBy,
            'approved_date' => date('Y-m-d H:i:s'),
            'sent_date' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get pending approval purchase orders
     *
     * @return array List of POs pending approval
     */
    public function getPendingApprovalPOs()
    {
        return $this->getPurchaseOrdersWithDetails(['status' => self::STATUS_DRAFT]);
    }

    /**
     * Get purchase orders ready for delivery
     *
     * @return array List of POs ready for delivery
     */
    public function getPOsReadyForDelivery()
    {
        $builder = $this->select('purchase_orders.*, 
                suppliers.name as supplier_name,
                suppliers.contact_person,
                suppliers.phone,
                suppliers.email,
                creator.first_name as creator_first_name,
                creator.last_name as creator_last_name,
                approver.first_name as approver_first_name,
                approver.last_name as approver_last_name,
                projects.name as project_name,
                material_requests.request_number as material_request_number')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
            ->join('users as creator', 'creator.id = purchase_orders.created_by', 'left')
            ->join('users as approver', 'approver.id = purchase_orders.approved_by', 'left')
            ->join('projects', 'projects.id = purchase_orders.project_id', 'left')
            ->join('material_requests', 'material_requests.id = purchase_orders.material_request_id', 'left')
            ->whereIn('purchase_orders.status', [self::STATUS_SENT, self::STATUS_ACKNOWLEDGED, self::STATUS_PARTIALLY_RECEIVED])
            ->orderBy('purchase_orders.created_at', 'DESC');

        return $builder->findAll();
    }

    /**
     * Create purchase order with items
     *
     * @param array $orderData Purchase order data
     * @param array $items Purchase order items
     * @return int|false Purchase order ID on success, false on failure
     */
    public function createPurchaseOrderWithItems($orderData, $items)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            log_message('debug', 'PO Model - Starting creation with data: ' . json_encode($orderData));
            log_message('debug', 'PO Model - Items to create: ' . json_encode($items));
            
            // Create purchase order
            $poId = $this->insert($orderData);
            
            if (!$poId) {
                $errors = $this->errors();
                log_message('error', 'PO Model - Failed to insert purchase order. Errors: ' . json_encode($errors));
                log_message('error', 'PO Model - Order data that failed: ' . json_encode($orderData));
                $db->transRollback();
                return false;
            }

            log_message('debug', "PO Model - Purchase order created with ID: $poId");

            // Create purchase order items
            $itemModel = new \App\Models\PurchaseOrderItemModel();
            foreach ($items as $index => $item) {
                $quantityOrdered = (float)$item['quantity_ordered'];
                $unitCost = (float)$item['unit_cost'];
                
                $itemData = [
                    'purchase_order_id' => $poId,
                    'material_id' => (int)$item['material_id'],
                    'material_request_item_id' => isset($item['material_request_item_id']) ? (int)$item['material_request_item_id'] : null,
                    'quantity_ordered' => $quantityOrdered,
                    'unit_cost' => $unitCost,
                    'total_cost' => $quantityOrdered * $unitCost,
                    'quantity_received' => 0.000,
                    'quantity_pending' => $quantityOrdered,
                    'specification_notes' => $item['specification_notes'] ?? null,
                    'delivery_date' => $item['delivery_date'] ?? null
                ];

                log_message('debug', "PO Model - Inserting item $index: " . json_encode($itemData));
                
                $itemId = $itemModel->insert($itemData);
                if (!$itemId) {
                    $itemErrors = $itemModel->errors();
                    log_message('error', "PO Model - Failed to insert item $index. Errors: " . json_encode($itemErrors));
                    log_message('error', "PO Model - Item data that failed: " . json_encode($itemData));
                    $db->transRollback();
                    return false;
                }
                
                log_message('debug', "PO Model - Item $index created with ID: $itemId");
            }

            $db->transComplete();
            
            if ($db->transStatus() === false) {
                log_message('error', 'PO Model - Transaction failed during commit');
                return false;
            }

            log_message('info', "PO Model - Successfully created PO $poId with " . count($items) . " items");
            return $poId;
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'PO Model - Exception during creation: ' . $e->getMessage());
            log_message('error', 'PO Model - Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }
}

