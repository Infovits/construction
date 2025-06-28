<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'company_id', 'supplier_code', 'name', 'contact_person', 'email', 
        'phone', 'mobile', 'address', 'city', 'state', 'country', 
        'tax_number', 'payment_terms', 'credit_limit', 'supplier_type', 
        'rating', 'status', 'notes', 'website', 'last_order_date', 'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'phone' => 'required',
    ];
    
    /**
     * Check if supplier has any delivery records
     *
     * @param int $supplierId
     * @return bool
     */
    public function hasDeliveries($supplierId)
    {
        $db = \Config\Database::connect();
        $count = $db->table('deliveries')
            ->where('supplier_id', $supplierId)
            ->countAllResults();
        return $count > 0;
    }
    
    /**
     * Get all deliveries for a supplier
     *
     * @param int $supplierId
     * @return array
     */
    public function getSupplierDeliveries($supplierId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('deliveries d');
        $builder->select('d.*, m.name as material_name, m.sku, m.unit_of_measure, w.name as warehouse_name');
        $builder->join('materials m', 'm.id = d.material_id');
        $builder->join('warehouses w', 'w.id = d.warehouse_id');
        $builder->where('d.supplier_id', $supplierId);
        $builder->orderBy('d.delivery_date', 'DESC');
        $builder->limit(10); // Get the 10 most recent deliveries
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get all materials supplied by a supplier
     *
     * @param int $supplierId
     * @return array
     */
    public function getSupplierMaterials($supplierId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('supplier_materials sm');
        $builder->select('sm.*, m.name, m.sku, m.description, m.unit_of_measure, m.barcode');
        $builder->join('materials m', 'm.id = sm.material_id');
        $builder->where('sm.supplier_id', $supplierId);
        $builder->orderBy('m.name', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Add a material to a supplier
     *
     * @param array $data
     * @return bool
     */
    public function addMaterialToSupplier($data)
    {
        $db = \Config\Database::connect();
        
        // Check if the material is already linked to this supplier
        $existing = $db->table('supplier_materials')
            ->where('supplier_id', $data['supplier_id'])
            ->where('material_id', $data['material_id'])
            ->get()
            ->getRow();
            
        if ($existing) {
            // Update the existing relationship
            return $db->table('supplier_materials')
                ->where('id', $existing->id)
                ->update([
                    'unit_price' => $data['unit_price'],
                    'min_order_qty' => $data['min_order_qty'] ?? null,
                    'lead_time' => $data['lead_time'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        } else {
            // Create a new relationship
            return $db->table('supplier_materials')->insert([
                'supplier_id' => $data['supplier_id'],
                'material_id' => $data['material_id'],
                'unit_price' => $data['unit_price'],
                'min_order_qty' => $data['min_order_qty'] ?? null,
                'lead_time' => $data['lead_time'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    /**
     * Remove a material from a supplier
     *
     * @param int $supplierId
     * @param int $materialId
     * @return bool
     */
    public function removeMaterialFromSupplier($supplierId, $materialId)
    {
        $db = \Config\Database::connect();
        return $db->table('supplier_materials')
            ->where('supplier_id', $supplierId)
            ->where('material_id', $materialId)
            ->delete();
    }
    
    /**
     * Record a delivery from a supplier
     *
     * @param array $data
     * @return int|bool
     */
    public function recordDelivery($data)
    {
        // Calculate total amount if not provided
        if (!isset($data['total_amount']) && isset($data['quantity']) && isset($data['unit_price'])) {
            $data['total_amount'] = $data['quantity'] * $data['unit_price'];
        }
        
        $db = \Config\Database::connect();
        $deliveryId = $db->table('deliveries')->insert($data);
        
        if ($deliveryId && $data['status'] == 'received') {
            // Update warehouse stock
            $warehouseStockModel = model(WarehouseStockModel::class);
            $warehouseStockModel->addStock(
                $data['warehouse_id'], 
                $data['material_id'], 
                $data['quantity']
            );
            
            // Record stock movement
            $stockMovementModel = model(StockMovementModel::class);
            $stockMovementModel->insert([
                'company_id' => $data['company_id'],
                'material_id' => $data['material_id'],
                'warehouse_id' => $data['warehouse_id'],
                'delivery_id' => $deliveryId,
                'quantity' => $data['quantity'],
                'movement_type' => 'in',
                'movement_date' => $data['delivery_date'],
                'notes' => "Delivery from supplier ID {$data['supplier_id']} - {$data['reference_number']}",
                'unit_price' => $data['unit_price'],
                'created_by' => $data['created_by']
            ]);
            
            // Update last order date for the supplier
            $this->update($data['supplier_id'], [
                'last_order_date' => $data['delivery_date']
            ]);
        }
        
        return $deliveryId;
    }
    
    /**
     * Get all active suppliers for a company
     *
     * @param int $companyId
     * @return array
     */
    public function getActiveSuppliers($companyId)
    {
        return $this->where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get supplier details with material count and last order date
     * 
     * @param int $companyId
     * @param array $filters
     * @return array
     */
    public function getSuppliersList($companyId, $filters = [])
    {
        $db = \Config\Database::connect();
        $builder = $db->table('suppliers s');
        
        $builder->select('s.*, 
            (SELECT COUNT(*) FROM supplier_materials WHERE supplier_id = s.id) as material_count,
            (SELECT MAX(delivery_date) FROM deliveries WHERE supplier_id = s.id) as last_order_date');
        
        $builder->where('s.company_id', $companyId);
        
        // Apply search filter if exists
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('s.name', $filters['search'])
                ->orLike('s.contact_person', $filters['search'])
                ->orLike('s.supplier_code', $filters['search'])
                ->orLike('s.phone', $filters['search'])
                ->orLike('s.email', $filters['search'])
                ->groupEnd();
        }
        
        // Apply status filter if exists
        if (!empty($filters['status'])) {
            $builder->where('s.status', $filters['status']);
        }
        
        // Apply type filter if exists
        if (!empty($filters['type'])) {
            $builder->where('s.supplier_type', $filters['type']);
        }
        
        $builder->orderBy('s.name', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get supplier material by ID
     * 
     * @param int $supplierId
     * @param int $materialId
     * @return array|null
     */
    public function getSupplierMaterial($supplierId, $materialId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('supplier_materials sm');
        $builder->select('sm.*, m.name, m.sku, m.unit_of_measure');
        $builder->join('materials m', 'm.id = sm.material_id');
        $builder->where('sm.supplier_id', $supplierId);
        $builder->where('sm.material_id', $materialId);
        
        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }
    
    /**
     * Get preferred supplier for a material
     * 
     * @param int $materialId
     * @return array|null
     */
    public function getPreferredSupplierForMaterial($materialId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('supplier_materials sm');
        $builder->select('s.*, sm.unit_price, sm.lead_time, sm.min_order_qty');
        $builder->join('suppliers s', 's.id = sm.supplier_id');
        $builder->where('sm.material_id', $materialId);
        $builder->where('s.status', 'active');
        $builder->orderBy('sm.is_preferred', 'DESC');
        $builder->orderBy('sm.unit_price', 'ASC');
        
        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }
}
