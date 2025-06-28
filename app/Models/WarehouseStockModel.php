<?php

namespace App\Models;

use CodeIgniter\Model;

class WarehouseStockModel extends Model
{
    protected $table = 'warehouse_stock';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'company_id', 'warehouse_id', 'material_id', 'current_quantity',
        'minimum_quantity', 'shelf_location', 'last_stock_update'
    ];
    
    public function getWarehouseStock($warehouseId)
    {
        return $this->select('warehouse_stock.*, materials.name, materials.item_code, materials.unit, materials.unit_cost, materials.material_type, material_categories.name as category_name')
            ->join('materials', 'materials.id = warehouse_stock.material_id')
            ->join('material_categories', 'material_categories.id = materials.category_id', 'left')
            ->where('warehouse_stock.warehouse_id', $warehouseId)
            ->orderBy('materials.name', 'ASC')
            ->findAll();
    }
    
    public function getMaterialStockLevels($materialId)
    {
        return $this->select('warehouse_stock.*, warehouses.name as warehouse_name, warehouses.code as warehouse_code')
            ->join('warehouses', 'warehouses.id = warehouse_stock.warehouse_id')
            ->where('warehouse_stock.material_id', $materialId)
            ->orderBy('warehouses.name', 'ASC')
            ->findAll();
    }
    
    public function getLowStockItems($warehouseId)
    {
        return $this->select('warehouse_stock.*, materials.name, materials.item_code, materials.unit, materials.material_type')
            ->join('materials', 'materials.id = warehouse_stock.material_id')
            ->where('warehouse_stock.warehouse_id', $warehouseId)
            ->where('warehouse_stock.current_quantity <= warehouse_stock.minimum_quantity')
            ->orderBy('warehouse_stock.current_quantity / warehouse_stock.minimum_quantity', 'ASC')
            ->findAll();
    }
    
    public function updateStockQuantity($warehouseId, $materialId, $quantity, $operation = 'add')
    {
        $stockItem = $this->where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->first();
            
        if (!$stockItem) {
            return false;
        }
        
        if ($operation == 'add') {
            $newQuantity = $stockItem['current_quantity'] + $quantity;
        } else {
            $newQuantity = $stockItem['current_quantity'] - $quantity;
            
            // Prevent negative quantities
            if ($newQuantity < 0) {
                return false;
            }
        }
        
        return $this->update($stockItem['id'], [
            'current_quantity' => $newQuantity,
            'last_stock_update' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function addInitialStock($companyId, $warehouseId, $materialId, $quantity, $minimumQuantity = 0, $shelfLocation = null)
    {
        $stockItem = $this->where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->first();
        
        if ($stockItem) {
            // Update existing stock entry
            return $this->update($stockItem['id'], [
                'current_quantity' => $stockItem['current_quantity'] + $quantity,
                'minimum_quantity' => $minimumQuantity > 0 ? $minimumQuantity : $stockItem['minimum_quantity'],
                'shelf_location' => $shelfLocation ?: $stockItem['shelf_location'],
                'last_stock_update' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Create new stock entry
            return $this->insert([
                'company_id' => $companyId,
                'warehouse_id' => $warehouseId,
                'material_id' => $materialId,
                'current_quantity' => $quantity,
                'minimum_quantity' => $minimumQuantity,
                'shelf_location' => $shelfLocation,
                'last_stock_update' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    public function hasStock($warehouseId, $materialId, $requiredQuantity)
    {
        $stockItem = $this->where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->first();
            
        if (!$stockItem || $stockItem['current_quantity'] < $requiredQuantity) {
            return false;
        }
        
        return true;
    }
}
