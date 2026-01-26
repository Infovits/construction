<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'company_id', 'category_id', 'item_code', 'barcode', 'name', 
        'description', 'brand', 'model', 'specifications', 'unit', 
        'unit_cost', 'selling_price', 'current_stock', 'minimum_stock', 
        'maximum_stock', 'reorder_level', 'weight', 'dimensions', 
        'color', 'material_type', 'is_tracked', 'is_serialized', 
        'requires_inspection', 'shelf_life_days', 'status', 'created_by',
        'primary_supplier_id'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getMaterialsWithCategories($companyId)
    {
        return $this->select('materials.*, material_categories.name as category_name')
            ->where('materials.company_id', $companyId)
            ->join('material_categories', 'material_categories.id = materials.category_id', 'left')
            ->findAll();
    }
    
    public function getMaterial($id)
    {
        return $this->select('materials.*, material_categories.name as category_name, users.first_name, users.last_name')
            ->join('material_categories', 'material_categories.id = materials.category_id', 'left')
            ->join('users', 'users.id = materials.created_by', 'left')
            ->find($id);
    }
    
    public function getLowStockItems($companyId, $warehouseId = null, $categoryId = null)
    {
        if ($warehouseId) {
            return $this->db->table('warehouse_stock')
                ->select('materials.*, warehouse_stock.current_quantity, warehouse_stock.minimum_quantity, warehouses.name as warehouse_name, material_categories.name as category_name')
                ->join('materials', 'materials.id = warehouse_stock.material_id')
                ->join('warehouses', 'warehouses.id = warehouse_stock.warehouse_id')
                ->join('material_categories', 'material_categories.id = materials.category_id', 'left')
                ->where('materials.company_id', $companyId)
                ->where('warehouse_stock.warehouse_id', $warehouseId)
                ->where('warehouse_stock.current_quantity <= warehouse_stock.minimum_quantity')
                ->where('materials.status', 'active')
                ->where($categoryId ? ['materials.category_id' => $categoryId] : [])
                ->get()
                ->getResultArray();
        } else {
            return $this->select('materials.*, material_categories.name as category_name')
                ->join('material_categories', 'material_categories.id = materials.category_id', 'left')
                ->where('materials.company_id', $companyId)
                ->where('materials.current_stock <= materials.minimum_stock')
                ->where('materials.status', 'active')
                ->where($categoryId ? ['materials.category_id' => $categoryId] : [])
                ->findAll();
        }
    }
    
    public function getStockLevelsReport($companyId, $warehouseId = null, $categoryId = null)
    {
        $builder = $this->db->table('materials');
        $builder->select('materials.id, materials.item_code, materials.name, materials.unit, materials.unit_cost, materials.current_stock, materials.minimum_stock, material_categories.name as category_name');
        $builder->join('material_categories', 'material_categories.id = materials.category_id', 'left');
        $builder->where('materials.company_id', $companyId);
        $builder->where('materials.status', 'active');
        
        if ($categoryId) {
            $builder->where('materials.category_id', $categoryId);
        }
        
        // If warehouse specified, get stock by warehouse
        if ($warehouseId) {
            $builder->select('warehouses.name as warehouse_name, IFNULL(warehouse_stock.current_quantity, 0) as quantity');
            $builder->join('warehouse_stock', 'warehouse_stock.material_id = materials.id AND warehouse_stock.warehouse_id = ' . $warehouseId, 'left');
            $builder->join('warehouses', 'warehouses.id = ' . $warehouseId);
        } else {
            // Get total stock across all warehouses
            $subquery = $this->db->table('warehouse_stock')
                ->select('SUM(current_quantity) as total_quantity, material_id')
                ->groupBy('material_id');
                
            $builder->select('IFNULL(warehouse_totals.total_quantity, 0) as quantity');
            $builder->join('(' . $subquery->getCompiledSelect() . ') as warehouse_totals', 'warehouse_totals.material_id = materials.id', 'left');
        }
        
        return $builder->get()->getResultArray();
    }
    
    public function updateStockLevels($materialId, $quantity, $operation = 'add')
    {
        $material = $this->find($materialId);
        
        if (!$material) {
            return false;
        }
        
        if ($operation == 'add') {
            $newStock = $material['current_stock'] + $quantity;
        } else {
            $newStock = $material['current_stock'] - $quantity;
            
            // Prevent negative stock
            if ($newStock < 0) {
                $newStock = 0;
            }
        }
        
        return $this->update($materialId, ['current_stock' => $newStock]);
    }
    
    /**
     * Get all materials that are at or below their reorder level
     *
     * @param int $companyId
     * @return array
     */
    public function getMaterialsNeedingReorder($companyId)
    {
        return $this->select('materials.*, material_categories.name as category_name')
            ->join('material_categories', 'material_categories.id = materials.category_id', 'left')
            ->where('materials.company_id', $companyId)
            ->where('materials.status', 'active')
            ->where('materials.current_stock <= materials.reorder_level')
            ->findAll();
    }
    
    /**
     * Get materials with critically low stock (below minimum stock level)
     *
     * @param int $companyId
     * @return array
     */
    public function getCriticalLowStockMaterials($companyId)
    {
        return $this->select('materials.*, material_categories.name as category_name')
            ->join('material_categories', 'material_categories.id = materials.category_id', 'left')
            ->where('materials.company_id', $companyId)
            ->where('materials.status', 'active')
            ->where('materials.current_stock <= materials.minimum_stock')
            ->findAll();
    }
    
    /**
     * Check if a material has low stock status
     * 
     * @param int $materialId
     * @return string|null 'critical', 'low', or null
     */
    public function checkLowStockStatus($materialId)
    {
        $material = $this->find($materialId);
        
        if (!$material) {
            return null;
        }
        
        if ($material['current_stock'] <= $material['minimum_stock']) {
            return 'critical';
        } elseif ($material['current_stock'] <= $material['reorder_level']) {
            return 'low';
        }
        
        return null;
    }
    
    /**
     * Get all materials with their stock status
     * 
     * @param int $companyId
     * @return array
     */
    public function getMaterialsWithStockStatus($companyId)
    {
        $materials = $this->select('materials.*, material_categories.name as category_name')
            ->join('material_categories', 'material_categories.id = materials.category_id', 'left')
            ->where('materials.company_id', $companyId)
            ->where('materials.status', 'active')
            ->findAll();
            
        foreach ($materials as &$material) {
            if ($material['current_stock'] <= $material['minimum_stock']) {
                $material['stock_status'] = 'critical';
            } elseif ($material['current_stock'] <= $material['reorder_level']) {
                $material['stock_status'] = 'low';
            } else {
                $material['stock_status'] = 'normal';
            }
            
            // Calculate stock percentage
            if ($material['maximum_stock'] > 0) {
                $material['stock_percentage'] = min(100, round(($material['current_stock'] / $material['maximum_stock']) * 100));
            } else {
                $material['stock_percentage'] = 0;
            }
        }
        
        return $materials;
    }
    
    /**
     * Get all materials that need attention with detailed low stock information
     *
     * @param int $companyId
     * @return array
     */
    public function getLowStockNotifications($companyId)
    {
        $materials = $this->select('
            materials.id, materials.name, materials.item_code, materials.barcode,
            materials.unit, materials.current_stock, materials.minimum_stock,
            materials.reorder_level, materials.maximum_stock,
            material_categories.name as category_name
        ')
        ->join('material_categories', 'material_categories.id = materials.category_id', 'left')
        ->where('materials.company_id', $companyId)
        ->where('materials.status', 'active')
        ->where('(materials.current_stock <= materials.reorder_level OR materials.current_stock <= materials.minimum_stock)')
        ->findAll();

        // Add stock status in PHP
        foreach ($materials as &$material) {
            if ($material['current_stock'] <= $material['minimum_stock']) {
                $material['stock_status'] = 'critical';
            } elseif ($material['current_stock'] <= $material['reorder_level']) {
                $material['stock_status'] = 'low';
            } else {
                $material['stock_status'] = 'normal';
            }
        }

        // Sort by stock status priority (critical first, then low, then normal)
        // Within same status, sort by lowest stock first
        usort($materials, function($a, $b) {
            $statusOrder = ['critical' => 1, 'low' => 2, 'normal' => 3];
            $statusA = $statusOrder[$a['stock_status']] ?? 3;
            $statusB = $statusOrder[$b['stock_status']] ?? 3;

            if ($statusA === $statusB) {
                return $a['current_stock'] <=> $b['current_stock'];
            }

            return $statusA <=> $statusB;
        });

        return $materials;
    }
}
