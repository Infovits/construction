<?php

namespace App\Models;

use CodeIgniter\Model;

class WarehouseModel extends Model
{
    protected $table = 'warehouses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'company_id', 'code', 'name', 'address', 'city', 'state', 'country',
        'manager_id', 'phone', 'email', 'warehouse_type', 'capacity',
        'is_project_site', 'project_id', 'status', 'notes', 'created_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getWarehouses($companyId)
    {
        return $this->select('warehouses.*, users.first_name, users.last_name, projects.name as project_name')
            ->join('users', 'users.id = warehouses.manager_id', 'left')
            ->join('projects', 'projects.id = warehouses.project_id', 'left')
            ->where('warehouses.company_id', $companyId)
            ->orderBy('warehouses.name', 'ASC')
            ->findAll();
    }
    
    public function getWarehouse($id)
    {
        return $this->select('warehouses.*, users.first_name, users.last_name, projects.name as project_name')
            ->join('users', 'users.id = warehouses.manager_id', 'left')
            ->join('projects', 'projects.id = warehouses.project_id', 'left')
            ->find($id);
    }
    
    public function getProjectWarehouses($projectId)
    {
        return $this->where('project_id', $projectId)
            ->where('is_project_site', 1)
            ->where('status', 'active')
            ->findAll();
    }
    
    public function countWarehouseStock($warehouseId)
    {
        $warehouseStockModel = new WarehouseStockModel();
        return $warehouseStockModel->where('warehouse_id', $warehouseId)
            ->countAllResults();
    }
    
    public function getWarehouseStockValue($warehouseId)
    {
        return $this->db->table('warehouse_stock')
            ->select('SUM(warehouse_stock.current_quantity * materials.unit_cost) as total_value')
            ->join('materials', 'materials.id = warehouse_stock.material_id')
            ->where('warehouse_stock.warehouse_id', $warehouseId)
            ->get()
            ->getRow()
            ->total_value ?? 0;
    }
}
