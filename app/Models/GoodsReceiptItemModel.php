<?php

namespace App\Models;

use CodeIgniter\Model;

class GoodsReceiptItemModel extends Model
{
    protected $table = 'goods_receipt_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'grn_id', 'purchase_order_item_id', 'material_id', 'quantity_delivered',
        'quantity_accepted', 'quantity_rejected', 'unit_cost', 'batch_number',
        'expiry_date', 'quality_status', 'rejection_reason', 'notes', 'criteria'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Quality status constants
    const QUALITY_PENDING = 'pending';
    const QUALITY_PASSED = 'passed';
    const QUALITY_FAILED = 'failed';
    const QUALITY_CONDITIONAL = 'conditional';

    /**
     * Get items for a GRN
     *
     * @param int $grnId GRN ID
     * @return array List of GRN items with material info
     */
    public function getItemsWithMaterialInfo($grnId)
    {
        return $this->select('goods_receipt_items.*, 
                materials.name as material_name,
                materials.item_code as material_code,
                materials.unit as material_unit,
                purchase_order_items.quantity_ordered as quantity_ordered,
                purchase_order_items.unit_cost as ordered_unit_cost,
                purchase_order_items.quantity_received as previously_received')
            ->join('materials', 'materials.id = goods_receipt_items.material_id', 'left')
            ->join('purchase_order_items', 'purchase_order_items.id = goods_receipt_items.purchase_order_item_id', 'left')
            ->where('grn_id', $grnId)
            ->findAll();
    }

    /**
     * Update quality inspection results
     *
     * @param int $id GRN item ID
     * @param string $qualityStatus Quality status
     * @param float $quantityAccepted Accepted quantity
     * @param float $quantityRejected Rejected quantity
     * @param string $rejectionReason Rejection reason if any
     * @param string $notes Additional notes
     * @return bool Success status
     */
    public function updateQualityInspection($id, $qualityStatus, $quantityAccepted, $quantityRejected = 0, $rejectionReason = null, $notes = null)
    {
        $data = [
            'quality_status' => $qualityStatus,
            'quantity_accepted' => $quantityAccepted,
            'quantity_rejected' => $quantityRejected
        ];

        if ($rejectionReason) {
            $data['rejection_reason'] = $rejectionReason;
        }

        if ($notes) {
            $data['notes'] = $notes;
        }

        return $this->update($id, $data);
    }

    /**
     * Get items pending quality inspection
     *
     * @param int $grnId Optional GRN ID filter
     * @return array List of items pending inspection
     */
    public function getItemsPendingInspection($grnId = null)
    {
        $builder = $this->select('goods_receipt_items.*, 
                materials.name as material_name,
                materials.item_code,
                materials.unit,
                goods_receipt_notes.grn_number,
                suppliers.name as supplier_name')
            ->join('materials', 'materials.id = goods_receipt_items.material_id', 'left')
            ->join('goods_receipt_notes', 'goods_receipt_notes.id = goods_receipt_items.grn_id', 'left')
            ->join('suppliers', 'suppliers.id = goods_receipt_notes.supplier_id', 'left')
            ->where('goods_receipt_items.quality_status', self::QUALITY_PENDING);

        if ($grnId) {
            $builder->where('goods_receipt_items.grn_id', $grnId);
        }

        return $builder->findAll();
    }

    /**
     * Get items ready for stock movement (passed inspection)
     *
     * @param int $grnId GRN ID
     * @return array List of items ready for stock movement
     */
    public function getItemsReadyForStock($grnId)
    {
        return $this->select('goods_receipt_items.*, 
                materials.name as material_name,
                materials.item_code,
                materials.unit,
                goods_receipt_notes.warehouse_id')
            ->join('materials', 'materials.id = goods_receipt_items.material_id', 'left')
            ->join('goods_receipt_notes', 'goods_receipt_notes.id = goods_receipt_items.grn_id', 'left')
            ->where('goods_receipt_items.grn_id', $grnId)
            ->where('goods_receipt_items.quality_status', self::QUALITY_PASSED)
            ->where('goods_receipt_items.quantity_accepted >', 0)
            ->findAll();
    }

    /**
     * Create stock movements for accepted items
     *
     * @param int $grnId GRN ID
     * @param int $performedBy User ID who performed the stock movement
     * @return bool Success status
     */
    public function createStockMovements($grnId, $performedBy)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Get items ready for stock movement
        $items = $this->getItemsReadyForStock($grnId);

        if (empty($items)) {
            $db->transRollback();
            return false;
        }

        $stockMovementModel = new StockMovementModel();
        $warehouseStockModel = new WarehouseStockModel();

        foreach ($items as $item) {
            // Create stock movement
            $stockMovementData = [
                'material_id' => $item['material_id'],
                'destination_warehouse_id' => $item['warehouse_id'],
                'reference_type' => 'goods_receipt',
                'reference_id' => $grnId,
                'movement_type' => 'stock_in',
                'quantity' => $item['quantity_accepted'],
                'unit_cost' => $item['unit_cost'],
                'total_cost' => $item['quantity_accepted'] * $item['unit_cost'],
                'batch_number' => $item['batch_number'],
                'expiry_date' => $item['expiry_date'],
                'notes' => 'Stock in from GRN: ' . $item['grn_number'],
                'performed_by' => $performedBy,
                'grn_id' => $grnId
            ];

            if (!$stockMovementModel->insert($stockMovementData)) {
                $db->transRollback();
                return false;
            }

            // Update warehouse stock
            $warehouseStockModel->updateStock(
                $item['material_id'],
                $item['warehouse_id'],
                $item['quantity_accepted'],
                'add'
            );
        }

        $db->transComplete();
        return $db->transStatus() !== false;
    }
}
