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
        'purchase_order_id', 'material_id', 'quantity', 
        'unit_price', 'total_price', 'received_quantity',
        'notes'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get items for a purchase order
     * 
     * @param int $purchaseOrderId The purchase order ID
     * @return array List of purchase order items with material info
     */
    public function getItemsWithMaterialInfo($purchaseOrderId)
    {
        return $this->select('purchase_order_items.*, materials.name as material_name, materials.item_code, materials.unit')
            ->where('purchase_order_id', $purchaseOrderId)
            ->join('materials', 'materials.id = purchase_order_items.material_id', 'left')
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
        
        $data = [
            'received_quantity' => $receivedQuantity
        ];
        
        if ($notes !== null) {
            $data['notes'] = $notes;
        }
        
        return $this->update($id, $data);
    }
}
