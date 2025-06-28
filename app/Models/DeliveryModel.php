<?php

namespace App\Models;

use CodeIgniter\Model;

class DeliveryModel extends Model
{
    protected $table = 'deliveries';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'company_id', 'supplier_id', 'material_id', 'warehouse_id', 
        'delivery_date', 'reference_number', 'quantity', 'unit_price', 
        'total_amount', 'status', 'notes', 'created_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'company_id' => 'required|numeric',
        'supplier_id' => 'required|numeric',
        'material_id' => 'required|numeric',
        'warehouse_id' => 'required|numeric',
        'delivery_date' => 'required|valid_date',
        'reference_number' => 'required',
        'quantity' => 'required|numeric|greater_than[0]',
        'unit_price' => 'required|numeric|greater_than[0]'
    ];
    
    /**
     * Get recent deliveries for a company
     *
     * @param int $companyId
     * @param int $limit
     * @return array
     */
    public function getRecentDeliveries($companyId, $limit = 10)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('deliveries d');
        
        $builder->select('d.*, s.name as supplier_name, m.name as material_name, m.unit_of_measure, w.name as warehouse_name');
        $builder->join('suppliers s', 's.id = d.supplier_id');
        $builder->join('materials m', 'm.id = d.material_id');
        $builder->join('warehouses w', 'w.id = d.warehouse_id');
        $builder->where('d.company_id', $companyId);
        $builder->orderBy('d.delivery_date', 'DESC');
        $builder->limit($limit);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get deliveries within a date range
     *
     * @param int $companyId
     * @param string $startDate
     * @param string $endDate
     * @param array $filters
     * @return array
     */
    public function getDeliveriesInDateRange($companyId, $startDate, $endDate, $filters = [])
    {
        $db = \Config\Database::connect();
        $builder = $db->table('deliveries d');
        
        $builder->select('d.*, s.name as supplier_name, m.name as material_name, m.unit_of_measure, w.name as warehouse_name');
        $builder->join('suppliers s', 's.id = d.supplier_id');
        $builder->join('materials m', 'm.id = d.material_id');
        $builder->join('warehouses w', 'w.id = d.warehouse_id');
        $builder->where('d.company_id', $companyId);
        $builder->where('d.delivery_date >=', $startDate);
        $builder->where('d.delivery_date <=', $endDate);
        
        // Apply supplier filter if exists
        if (!empty($filters['supplier_id'])) {
            $builder->where('d.supplier_id', $filters['supplier_id']);
        }
        
        // Apply material filter if exists
        if (!empty($filters['material_id'])) {
            $builder->where('d.material_id', $filters['material_id']);
        }
        
        // Apply warehouse filter if exists
        if (!empty($filters['warehouse_id'])) {
            $builder->where('d.warehouse_id', $filters['warehouse_id']);
        }
        
        // Apply status filter if exists
        if (!empty($filters['status'])) {
            $builder->where('d.status', $filters['status']);
        }
        
        $builder->orderBy('d.delivery_date', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Update delivery status and update stock if necessary
     *
     * @param int $deliveryId
     * @param string $status
     * @return bool
     */
    public function updateStatus($deliveryId, $status)
    {
        $db = \Config\Database::connect();
        $stockMovementModel = model(StockMovementModel::class);
        $warehouseStockModel = model(WarehouseStockModel::class);
        
        // Get the delivery details
        $delivery = $this->find($deliveryId);
        if (!$delivery) {
            return false;
        }
        
        $oldStatus = $delivery['status'];
        
        // Begin transaction
        $db->transBegin();
        
        try {
            // Update the delivery status
            $this->update($deliveryId, ['status' => $status]);
            
            // If status changed from non-received to received, add to stock
            if ($oldStatus !== 'received' && $status === 'received') {
                // Add stock to warehouse
                $warehouseStockModel->addStock(
                    $delivery['warehouse_id'], 
                    $delivery['material_id'], 
                    $delivery['quantity']
                );
                
                // Record stock movement
                $stockMovementModel->insert([
                    'company_id' => $delivery['company_id'],
                    'material_id' => $delivery['material_id'],
                    'warehouse_id' => $delivery['warehouse_id'],
                    'delivery_id' => $deliveryId,
                    'quantity' => $delivery['quantity'],
                    'movement_type' => 'in',
                    'movement_date' => date('Y-m-d'),
                    'notes' => "Delivery status updated to received - ID: {$deliveryId}",
                    'unit_price' => $delivery['unit_price'],
                    'created_by' => session()->get('user_id')
                ]);
            }
            
            // If status changed from received to non-received, remove from stock
            elseif ($oldStatus === 'received' && $status !== 'received') {
                // Remove stock from warehouse
                $warehouseStockModel->removeStock(
                    $delivery['warehouse_id'], 
                    $delivery['material_id'], 
                    $delivery['quantity']
                );
                
                // Record stock movement
                $stockMovementModel->insert([
                    'company_id' => $delivery['company_id'],
                    'material_id' => $delivery['material_id'],
                    'warehouse_id' => $delivery['warehouse_id'],
                    'delivery_id' => $deliveryId,
                    'quantity' => $delivery['quantity'],
                    'movement_type' => 'out',
                    'movement_date' => date('Y-m-d'),
                    'notes' => "Delivery status updated from received - ID: {$deliveryId}",
                    'unit_price' => $delivery['unit_price'],
                    'created_by' => session()->get('user_id')
                ]);
            }
            
            // Commit transaction
            $db->transCommit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction
            $db->transRollback();
            log_message('error', 'Failed to update delivery status: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get total amount of deliveries for a company in a date range
     *
     * @param int $companyId
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public function getTotalDeliveryAmount($companyId, $startDate, $endDate)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('deliveries');
        
        $builder->selectSum('total_amount');
        $builder->where('company_id', $companyId);
        $builder->where('delivery_date >=', $startDate);
        $builder->where('delivery_date <=', $endDate);
        $builder->where('status', 'received');
        
        $result = $builder->get()->getRow();
        return $result->total_amount ?? 0;
    }
    
    /**
     * Get delivery counts by status for a company
     *
     * @param int $companyId
     * @return array
     */
    public function getDeliveryCountsByStatus($companyId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('deliveries');
        
        $builder->select('status, COUNT(*) as count');
        $builder->where('company_id', $companyId);
        $builder->groupBy('status');
        
        $results = $builder->get()->getResultArray();
        
        $counts = [
            'received' => 0,
            'partial' => 0,
            'pending' => 0,
            'cancelled' => 0
        ];
        
        foreach ($results as $row) {
            $counts[$row['status']] = (int)$row['count'];
        }
        
        return $counts;
    }
}
