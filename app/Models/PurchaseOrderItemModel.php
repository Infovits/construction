<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderItemModel extends Model
{
    protected $table = 'purchase_order_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'purchase_order_id', 'material_id', 'material_request_item_id',
        'quantity_ordered', 'unit_cost', 'total_cost', 'quantity_received',
        'quantity_pending', 'specification_notes', 'delivery_date'
    ];

    protected $useTimestamps = false; // Table doesn't have updated_at column

    /**
     * Get items for a purchase order
     *
     * @param int $purchaseOrderId The purchase order ID
     * @return array List of purchase order items with material info
     */
    public function getItemsWithMaterialInfo($purchaseOrderId)
    {
        return $this->select('purchase_order_items.*, 
                materials.name as material_name,
                materials.item_code,
                materials.unit,
                materials.specifications,
                material_request_items.quantity_requested,
                material_request_items.specification_notes as request_notes')
            ->join('materials', 'materials.id = purchase_order_items.material_id', 'left')
            ->join('material_request_items', 'material_request_items.id = purchase_order_items.material_request_item_id', 'left')
            ->where('purchase_order_id', $purchaseOrderId)
            ->findAll();
    }

    /**
     * Update received quantity for purchase order item
     *
     * @param int $id The purchase order item ID
     * @param float $receivedQuantity The received quantity
     * @param string $notes Optional notes about the receipt
     * @return bool True if updated successfully
     */
    public function updateReceivedQuantity($id, $receivedQuantity, $notes = null)
    {
        $item = $this->find($id);

        if (!$item) {
            return false;
        }

        $newReceivedQuantity = $item['quantity_received'] + $receivedQuantity;
        $newPendingQuantity = $item['quantity_ordered'] - $newReceivedQuantity;

        $data = [
            'quantity_received' => $newReceivedQuantity,
            'quantity_pending' => max(0, $newPendingQuantity)
        ];

        return $this->update($id, $data);
    }

    /**
     * Get items pending delivery
     *
     * @param int $purchaseOrderId Optional PO ID filter
     * @return array List of items with pending quantities
     */
    public function getItemsPendingDelivery($purchaseOrderId = null)
    {
        $builder = $this->select('purchase_order_items.*, 
                materials.name as material_name,
                materials.item_code,
                materials.unit,
                purchase_orders.po_number,
                suppliers.name as supplier_name')
            ->join('materials', 'materials.id = purchase_order_items.material_id', 'left')
            ->join('purchase_orders', 'purchase_orders.id = purchase_order_items.purchase_order_id', 'left')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
            ->where('purchase_order_items.quantity_pending >', 0);

        if ($purchaseOrderId) {
            $builder->where('purchase_order_items.purchase_order_id', $purchaseOrderId);
        }

        return $builder->findAll();
    }

    /**
     * Get delivery status summary for a purchase order
     *
     * @param int $purchaseOrderId Purchase order ID
     * @return array Delivery status summary
     */
    public function getDeliveryStatusSummary($purchaseOrderId)
    {
        $items = $this->where('purchase_order_id', $purchaseOrderId)->findAll();
        
        $summary = [
            'total_items' => count($items),
            'fully_received_items' => 0,
            'partially_received_items' => 0,
            'pending_items' => 0,
            'total_ordered_quantity' => 0,
            'total_received_quantity' => 0,
            'total_pending_quantity' => 0
        ];

        foreach ($items as $item) {
            $summary['total_ordered_quantity'] += $item['quantity_ordered'];
            $summary['total_received_quantity'] += $item['quantity_received'];
            $summary['total_pending_quantity'] += $item['quantity_pending'];

            if ($item['quantity_pending'] == 0) {
                $summary['fully_received_items']++;
            } elseif ($item['quantity_received'] > 0) {
                $summary['partially_received_items']++;
            } else {
                $summary['pending_items']++;
            }
        }

        $summary['completion_percentage'] = $summary['total_ordered_quantity'] > 0 
            ? round(($summary['total_received_quantity'] / $summary['total_ordered_quantity']) * 100, 2)
            : 0;

        return $summary;
    }

    /**
     * Check if purchase order is fully received
     *
     * @param int $purchaseOrderId Purchase order ID
     * @return bool True if fully received
     */
    public function isPurchaseOrderFullyReceived($purchaseOrderId)
    {
        $pendingItems = $this->where('purchase_order_id', $purchaseOrderId)
            ->where('quantity_pending >', 0)
            ->countAllResults();

        return $pendingItems == 0;
    }

    /**
     * Get items ready for goods receipt
     *
     * @param int $purchaseOrderId Purchase order ID
     * @return array List of items ready for receipt
     */
    public function getItemsReadyForReceipt($purchaseOrderId)
    {
        return $this->select('purchase_order_items.*, 
                materials.name as material_name,
                materials.item_code,
                materials.unit,
                materials.requires_inspection')
            ->join('materials', 'materials.id = purchase_order_items.material_id', 'left')
            ->where('purchase_order_id', $purchaseOrderId)
            ->where('quantity_pending >', 0)
            ->findAll();
    }
}
