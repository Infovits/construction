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
        'company_id', 'supplier_id', 'po_number', 'order_date', 
        'expected_delivery_date', 'status', 'total_amount', 
        'shipping_address', 'shipping_method', 'payment_terms', 
        'notes', 'created_by', 'approved_by', 'approved_date'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ORDERED = 'ordered';
    const STATUS_RECEIVED = 'received';
    const STATUS_PARTIALLY_RECEIVED = 'partially_received';
    const STATUS_CANCELLED = 'cancelled';
    
    /**
     * Get purchase orders with supplier info
     * 
     * @param int $companyId The company ID
     * @return array List of purchase orders with supplier info
     */
    public function getPurchaseOrdersWithSuppliers($companyId)
    {
        return $this->select('purchase_orders.*, suppliers.name as supplier_name, users.first_name, users.last_name')
            ->where('purchase_orders.company_id', $companyId)
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
            ->join('users', 'users.id = purchase_orders.created_by', 'left')
            ->orderBy('purchase_orders.created_at', 'DESC')
            ->findAll();
    }
    
    /**
     * Get purchase order details with items
     * 
     * @param int $id The purchase order ID
     * @return array Purchase order details with items
     */
    public function getPurchaseOrderWithItems($id)
    {
        $db = \Config\Database::connect();
        
        // Get purchase order
        $order = $this->select('purchase_orders.*, suppliers.name as supplier_name, suppliers.contact_person, suppliers.phone, suppliers.email, users.first_name, users.last_name')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
            ->join('users', 'users.id = purchase_orders.created_by', 'left')
            ->find($id);
            
        if (!$order) {
            return null;
        }
        
        // Get purchase order items
        $itemsBuilder = $db->table('purchase_order_items');
        $items = $itemsBuilder->select('purchase_order_items.*, materials.name as material_name, materials.item_code, materials.unit')
            ->where('purchase_order_id', $id)
            ->join('materials', 'materials.id = purchase_order_items.material_id', 'left')
            ->get()
            ->getResultArray();
            
        $order['items'] = $items;
        
        return $order;
    }
    
    /**
     * Generate next PO number
     * 
     * @param int $companyId The company ID
     * @return string The next PO number
     */
    public function generatePoNumber($companyId)
    {
        $lastPo = $this->where('company_id', $companyId)
            ->orderBy('id', 'DESC')
            ->first();
            
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
     * Create a purchase order from low stock items
     * 
     * @param int $companyId The company ID
     * @param int $supplierId The supplier ID
     * @param array $items List of items to order
     * @param array $orderData Additional order data
     * @param int $userId The user ID creating the order
     * @return int|bool The new purchase order ID or false on failure
     */
    public function createFromLowStock($companyId, $supplierId, array $items, array $orderData, $userId)
    {
        if (empty($items)) {
            return false;
        }
        
        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Generate PO number
        $poNumber = $this->generatePoNumber($companyId);
        
        // Create purchase order
        $poData = [
            'company_id' => $companyId,
            'supplier_id' => $supplierId,
            'po_number' => $poNumber,
            'order_date' => date('Y-m-d'),
            'expected_delivery_date' => $orderData['expected_delivery_date'] ?? null,
            'status' => self::STATUS_DRAFT,
            'shipping_address' => $orderData['shipping_address'] ?? null,
            'shipping_method' => $orderData['shipping_method'] ?? null,
            'payment_terms' => $orderData['payment_terms'] ?? null,
            'notes' => $orderData['notes'] ?? null,
            'created_by' => $userId
        ];
        
        // Insert purchase order
        $this->insert($poData);
        $poId = $db->insertID();
        
        if (!$poId) {
            $db->transRollback();
            return false;
        }
        
        // Insert purchase order items
        $poItemsModel = new PurchaseOrderItemModel();
        $totalAmount = 0;
        
        foreach ($items as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $totalAmount += $itemTotal;
            
            $poItemData = [
                'purchase_order_id' => $poId,
                'material_id' => $item['material_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $itemTotal
            ];
            
            if (!$poItemsModel->insert($poItemData)) {
                $db->transRollback();
                return false;
            }
        }
        
        // Update purchase order with total amount
        $this->update($poId, ['total_amount' => $totalAmount]);
        
        // Complete transaction
        $db->transComplete();
        
        return ($db->transStatus() === false) ? false : $poId;
    }
}
