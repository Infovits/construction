<?php

namespace App\Models;

use CodeIgniter\Model;

class GoodsReceiptNoteModel extends Model
{
    protected $table = 'goods_receipt_notes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'grn_number', 'purchase_order_id', 'supplier_id', 'warehouse_id',
        'delivery_date', 'received_by', 'delivery_note_number', 'vehicle_number',
        'driver_name', 'status', 'total_received_value', 'freight_cost', 'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Status constants
    const STATUS_PENDING_INSPECTION = 'pending_inspection';
    const STATUS_PARTIALLY_ACCEPTED = 'partially_accepted';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get goods receipt notes with related information
     *
     * @param array $filters Optional filters
     * @return array List of GRNs
     */
    public function getGRNsWithDetails($filters = [])
    {
        $builder = $this->select('goods_receipt_notes.*, 
                purchase_orders.po_number,
                suppliers.name as supplier_name,
                warehouses.name as warehouse_name,
                receiver.first_name as receiver_first_name,
                receiver.last_name as receiver_last_name,
                CONCAT(receiver.first_name, " ", receiver.last_name) as received_by_name')
            ->join('purchase_orders', 'purchase_orders.id = goods_receipt_notes.purchase_order_id', 'left')
            ->join('suppliers', 'suppliers.id = goods_receipt_notes.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = goods_receipt_notes.warehouse_id', 'left')
            ->join('users as receiver', 'receiver.id = goods_receipt_notes.received_by', 'left');

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('goods_receipt_notes.status', $filters['status']);
        }

        if (!empty($filters['supplier_id'])) {
            $builder->where('goods_receipt_notes.supplier_id', $filters['supplier_id']);
        }

        if (!empty($filters['warehouse_id'])) {
            $builder->where('goods_receipt_notes.warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['purchase_order_id'])) {
            $builder->where('goods_receipt_notes.purchase_order_id', $filters['purchase_order_id']);
        }

        return $builder->orderBy('goods_receipt_notes.created_at', 'DESC')->findAll();
    }

    /**
     * Get GRN with items
     *
     * @param int $id GRN ID
     * @return array|null GRN with items
     */
    public function getGRNWithItems($id)
    {
        $grn = $this->select('goods_receipt_notes.*, 
                purchase_orders.po_number,
                suppliers.name as supplier_name,
                suppliers.contact_person,
                suppliers.phone,
                suppliers.email,
                warehouses.name as warehouse_name,
                warehouses.address as warehouse_address,
                receiver.first_name as receiver_first_name,
                receiver.last_name as receiver_last_name,
                CONCAT(receiver.first_name, " ", receiver.last_name) as received_by_name')
            ->join('purchase_orders', 'purchase_orders.id = goods_receipt_notes.purchase_order_id', 'left')
            ->join('suppliers', 'suppliers.id = goods_receipt_notes.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = goods_receipt_notes.warehouse_id', 'left')
            ->join('users as receiver', 'receiver.id = goods_receipt_notes.received_by', 'left')
            ->find($id);

        if (!$grn) {
            return null;
        }

        // Get GRN items
        $db = \Config\Database::connect();
        $items = $db->table('goods_receipt_items')
            ->select('goods_receipt_items.*, 
                materials.name as material_name,
                materials.item_code as material_code,
                materials.unit as material_unit,
                purchase_order_items.quantity_ordered as quantity_ordered,
                purchase_order_items.unit_cost as ordered_unit_cost')
            ->join('materials', 'materials.id = goods_receipt_items.material_id', 'left')
            ->join('purchase_order_items', 'purchase_order_items.id = goods_receipt_items.purchase_order_item_id', 'left')
            ->where('grn_id', $id)
            ->get()
            ->getResultArray();

        $grn['items'] = $items;

        return $grn;
    }

    /**
     * Generate next GRN number
     *
     * @return string Next GRN number
     */
    public function generateGRNNumber()
    {
        $lastGRN = $this->orderBy('id', 'DESC')->first();
        $prefix = 'GRN-' . date('Ymd') . '-';

        if (!$lastGRN) {
            return $prefix . '0001';
        }

        // Extract sequence number from last GRN
        $lastGRNNumber = $lastGRN['grn_number'];
        $lastSequence = 0;

        if (preg_match('/GRN-\d{8}-(\d+)/', $lastGRNNumber, $matches)) {
            $lastSequence = (int) $matches[1];
        }

        // Increment sequence
        $nextSequence = $lastSequence + 1;

        // Format with leading zeros
        return $prefix . sprintf('%04d', $nextSequence);
    }

    /**
     * Create GRN from purchase order
     *
     * @param int $purchaseOrderId Purchase order ID
     * @param array $grnData GRN data
     * @param array $items Items received
     * @param int $receivedBy User ID who received
     * @return int|bool GRN ID or false on failure
     */
    public function createFromPurchaseOrder($purchaseOrderId, $grnData, $items, $receivedBy)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            log_message('debug', 'GRN Model - Starting creation for PO: ' . $purchaseOrderId);
            log_message('debug', 'GRN Model - Items count: ' . count($items));
            log_message('debug', 'GRN Model - Received by: ' . $receivedBy);

            // Get purchase order details
            $poModel = new PurchaseOrderModel();
            $purchaseOrder = $poModel->find($purchaseOrderId);

            if (!$purchaseOrder) {
                log_message('error', 'GRN Model - Purchase order not found: ' . $purchaseOrderId);
                $db->transRollback();
                return false;
            }

            log_message('debug', 'GRN Model - PO found: ' . $purchaseOrder['po_number']);

            // Generate GRN number
            $grnNumber = $this->generateGRNNumber();
            log_message('debug', 'GRN Model - Generated GRN number: ' . $grnNumber);

            // Create GRN
            $finalGrnData = array_merge($grnData, [
                'grn_number' => $grnNumber,
                'purchase_order_id' => $purchaseOrderId,
                'supplier_id' => $purchaseOrder['supplier_id'],
                'received_by' => $receivedBy,
                'status' => self::STATUS_PENDING_INSPECTION
            ]);

            log_message('debug', 'GRN Model - Final GRN data: ' . json_encode($finalGrnData));

            // Use direct database insert instead of model insert to avoid validation issues
            $result = $db->table('goods_receipt_notes')->insert($finalGrnData);
            if (!$result) {
                log_message('error', 'GRN Model - Direct insert failed');
                $db->transRollback();
                return false;
            }

            $grnId = $db->insertID();
            log_message('debug', 'GRN Model - GRN inserted with ID: ' . $grnId);

            if (!$grnId) {
                log_message('error', 'GRN Model - No insert ID returned');
                $db->transRollback();
                return false;
            }

            // Create GRN items
            $grnItemModel = new GoodsReceiptItemModel();
            $totalValue = 0;

            log_message('debug', 'GRN Model - Creating ' . count($items) . ' items');

            foreach ($items as $index => $item) {
                log_message('debug', 'GRN Model - Processing item ' . ($index + 1) . ': ' . json_encode($item));
                
                $itemValue = $item['quantity_delivered'] * $item['unit_cost'];
                $totalValue += $itemValue;

                $grnItemData = [
                    'grn_id' => $grnId,
                    'purchase_order_item_id' => $item['purchase_order_item_id'],
                    'material_id' => $item['material_id'],
                    'quantity_delivered' => $item['quantity_delivered'],
                    'unit_cost' => $item['unit_cost'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'quality_status' => 'pending',
                    'notes' => $item['notes'] ?? null
                ];

                log_message('debug', 'GRN Model - Item data: ' . json_encode($grnItemData));

                // Use direct database insert for items too
                $itemResult = $db->table('goods_receipt_items')->insert($grnItemData);
                if (!$itemResult) {
                    log_message('error', 'GRN Model - Item direct insert failed');
                    log_message('error', 'GRN Model - Failed item data: ' . json_encode($grnItemData));
                    $db->transRollback();
                    return false;
                }

                log_message('debug', 'GRN Model - Item ' . ($index + 1) . ' created successfully');
            }

            // Update GRN with total value using direct database operation
            log_message('debug', 'GRN Model - Total value: ' . $totalValue);
            $db->table('goods_receipt_notes')->where('id', $grnId)->update(['total_received_value' => $totalValue]);

            $db->transComplete();
            
            if ($db->transStatus() === false) {
                log_message('error', 'GRN Model - Transaction failed during commit');
                return false;
            }

            log_message('info', 'GRN Model - Successfully created GRN ' . $grnId . ' with ' . count($items) . ' items');
            return $grnId;
            
        } catch (\Exception $e) {
            log_message('error', 'GRN Model - Exception: ' . $e->getMessage());
            log_message('error', 'GRN Model - Exception trace: ' . $e->getTraceAsString());
            $db->transRollback();
            return false;
        }
    }

    /**
     * Get pending inspection GRNs
     *
     * @return array List of GRNs pending inspection
     */
    public function getPendingInspectionGRNs()
    {
        return $this->getGRNsWithDetails(['status' => self::STATUS_PENDING_INSPECTION]);
    }

    /**
     * Get summary statistics for goods receipt notes based on filters
     *
     * @param array $filters Optional filters for date range, supplier, project
     * @return array Summary statistics
     */
    public function getSummaryStats($filters = [])
    {
        $builder = $this->db->table($this->table);

        // Apply date filters if provided
        if (!empty($filters['date_from'])) {
            $builder->where('delivery_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('delivery_date <=', $filters['date_to']);
        }

        // Apply supplier filter if provided
        if (!empty($filters['supplier_id'])) {
            $builder->where('supplier_id', $filters['supplier_id']);
        }

        // Apply project filter if provided (join with PO to get project)
        if (!empty($filters['project_id'])) {
            $builder->join('purchase_orders', 'purchase_orders.id = goods_receipt_notes.purchase_order_id', 'left')
                   ->where('purchase_orders.project_id', $filters['project_id']);
        }

        // Get total count
        $total = $builder->countAllResults(false);

        // Get completed (accepted) count
        $completed = (clone $builder)->where('status', self::STATUS_ACCEPTED)->countAllResults();

        // Get pending count
        $pending = (clone $builder)->where('status', self::STATUS_PENDING_INSPECTION)->countAllResults();

        // Get total items count
        $db = \Config\Database::connect();
        $itemsQuery = $db->table('goods_receipt_items')
                         ->select('COUNT(*) as total_items');

        // Apply the same date filters to items through join
        if (!empty($filters['date_from']) || !empty($filters['date_to']) || !empty($filters['supplier_id']) || !empty($filters['project_id'])) {
            $itemsQuery->join('goods_receipt_notes', 'goods_receipt_notes.id = goods_receipt_items.goods_receipt_id', 'left');

            if (!empty($filters['date_from'])) {
                $itemsQuery->where('goods_receipt_notes.delivery_date >=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $itemsQuery->where('goods_receipt_notes.delivery_date <=', $filters['date_to']);
            }

            if (!empty($filters['supplier_id'])) {
                $itemsQuery->where('goods_receipt_notes.supplier_id', $filters['supplier_id']);
            }

            if (!empty($filters['project_id'])) {
                $itemsQuery->join('purchase_orders', 'purchase_orders.id = goods_receipt_notes.purchase_order_id', 'left')
                           ->where('purchase_orders.project_id', $filters['project_id']);
            }
        }

        $totalItems = $itemsQuery->get()->getRow()->total_items ?? 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'total_items' => $totalItems
        ];
    }
}
