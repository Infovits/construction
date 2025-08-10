<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialRequestItemModel extends Model
{
    protected $table = 'material_request_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'material_request_id', 'material_id', 'quantity_requested',
        'quantity_approved', 'estimated_unit_cost', 'estimated_total_cost',
        'specification_notes', 'urgency_notes'
    ];

    protected $useTimestamps = false;
    protected $skipValidation = true;

    /**
     * Get items for a material request
     *
     * @param int $materialRequestId Material request ID
     * @return array List of request items with material info
     */
    public function getItemsWithMaterialInfo($materialRequestId)
    {
        return $this->select('material_request_items.*, 
                materials.name as material_name,
                materials.item_code,
                materials.unit,
                materials.unit_cost as current_unit_cost,
                materials.current_stock,
                materials.minimum_stock')
            ->join('materials', 'materials.id = material_request_items.material_id', 'left')
            ->where('material_request_id', $materialRequestId)
            ->findAll();
    }

    /**
     * Update approved quantities for request items
     *
     * @param int $materialRequestId Material request ID
     * @param array $approvedQuantities Array of item_id => approved_quantity
     * @return bool Success status
     */
    public function updateApprovedQuantities($materialRequestId, $approvedQuantities)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($approvedQuantities as $itemId => $approvedQuantity) {
            $item = $this->find($itemId);
            
            if ($item && $item['material_request_id'] == $materialRequestId) {
                $this->update($itemId, [
                    'quantity_approved' => $approvedQuantity
                ]);
            }
        }

        $db->transComplete();
        return $db->transStatus() !== false;
    }

    /**
     * Get items that need to be purchased (approved but not yet ordered)
     *
     * @param int $materialRequestId Material request ID
     * @return array List of items to be purchased
     */
    public function getItemsToBePurchased($materialRequestId)
    {
        return $this->select('material_request_items.*, 
                materials.name as material_name,
                materials.item_code,
                materials.unit,
                materials.unit_cost as current_unit_cost,
                suppliers.name as preferred_supplier_name,
                suppliers.id as preferred_supplier_id')
            ->join('materials', 'materials.id = material_request_items.material_id', 'left')
            ->join('suppliers', 'suppliers.id = materials.primary_supplier_id', 'left')
            ->where('material_request_id', $materialRequestId)
            ->where('quantity_approved >', 0)
            ->findAll();
    }

    /**
     * Calculate total estimated cost for a material request
     *
     * @param int $materialRequestId Material request ID
     * @return float Total estimated cost
     */
    public function calculateTotalEstimatedCost($materialRequestId)
    {
        $result = $this->select('SUM(estimated_total_cost) as total')
            ->where('material_request_id', $materialRequestId)
            ->first();

        return $result ? (float)$result['total'] : 0.0;
    }

    /**
     * Get consolidated material requirements across multiple requests
     *
     * @param array $materialRequestIds Array of material request IDs
     * @return array Consolidated material requirements
     */
    public function getConsolidatedRequirements($materialRequestIds)
    {
        if (empty($materialRequestIds)) {
            return [];
        }

        return $this->select('material_id,
                materials.name as material_name,
                materials.item_code,
                materials.unit,
                materials.unit_cost as current_unit_cost,
                SUM(quantity_approved) as total_quantity_needed,
                AVG(estimated_unit_cost) as avg_estimated_cost,
                suppliers.name as preferred_supplier_name,
                suppliers.id as preferred_supplier_id')
            ->join('materials', 'materials.id = material_request_items.material_id', 'left')
            ->join('suppliers', 'suppliers.id = materials.primary_supplier_id', 'left')
            ->whereIn('material_request_id', $materialRequestIds)
            ->where('quantity_approved >', 0)
            ->groupBy('material_id')
            ->findAll();
    }
}
