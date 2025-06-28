<?php

namespace App\Controllers;

use App\Models\WarehouseModel;
use App\Models\WarehouseStockModel;
use App\Models\MaterialModel;
use App\Models\ProjectModel;
use App\Models\UserModel;

class Warehouses extends BaseController
{
    protected $warehouseModel;
    protected $warehouseStockModel;
    protected $materialModel;
    protected $projectModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->warehouseModel = new WarehouseModel();
        $this->warehouseStockModel = new WarehouseStockModel();
        $this->materialModel = new MaterialModel();
        $this->projectModel = new ProjectModel();
        $this->userModel = new UserModel();
    }
    
    public function index()
    {
        $companyId = session()->get('company_id');
        
        $data = [
            'title' => 'Warehouses & Stock Locations',
            'warehouses' => $this->warehouseModel->getWarehouses($companyId),
        ];
        
        return view('inventory/warehouses/index', $data);
    }
    
    public function new()
    {
        $companyId = session()->get('company_id');
        
        $data = [
            'title' => 'Add New Warehouse',
            'projects' => $this->projectModel->where('company_id', $companyId)->where('status', 'active')->findAll(),
            'users' => $this->userModel->where('company_id', $companyId)->where('status', 'active')->findAll(),
        ];
        
        return view('inventory/warehouses/create', $data);
    }
    
    public function create()
    {
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $userId = session()->get('user_id');
        
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'code' => 'required|is_unique[warehouses.code,company_id,'.$companyId.']',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'company_id' => $companyId,
            'name' => $this->request->getVar('name'),
            'code' => $this->request->getVar('code'),
            'address' => $this->request->getVar('address'),
            'city' => $this->request->getVar('city'),
            'state' => $this->request->getVar('state'),
            'country' => $this->request->getVar('country'),
            'manager_id' => $this->request->getVar('manager_id') ?: null,
            'phone' => $this->request->getVar('phone'),
            'email' => $this->request->getVar('email'),
            'warehouse_type' => $this->request->getVar('warehouse_type') ?? 'main',
            'capacity' => $this->request->getVar('capacity'),
            'is_project_site' => $this->request->getVar('is_project_site') ? 1 : 0,
            'project_id' => $this->request->getVar('project_id') ?: null,
            'status' => $this->request->getVar('status') ?? 'active',
            'notes' => $this->request->getVar('notes'),
            'created_by' => $userId,
        ];
        
        $warehouseId = $this->warehouseModel->insert($data);
        
        if (!$warehouseId) {
            return redirect()->back()->withInput()->with('error', 'Failed to add warehouse.');
        }
        
        return redirect()->to('/admin/warehouses')->with('success', 'Warehouse added successfully');
    }
    
    public function view($id)
    {
        $companyId = session()->get('company_id');
        $warehouse = $this->warehouseModel->getWarehouse($id);
        
        if (!$warehouse || $warehouse['company_id'] != $companyId) {
            return redirect()->to('/admin/warehouses')->with('error', 'Warehouse not found');
        }
        
        $data = [
            'title' => 'Warehouse Details - ' . $warehouse['name'],
            'warehouse' => $warehouse,
            'stock' => $this->warehouseStockModel->getWarehouseStock($id),
            'lowStockItems' => $this->warehouseStockModel->getLowStockItems($id),
        ];
        
        return view('inventory/warehouses/view', $data);
    }
    
    public function edit($id)
    {
        $companyId = session()->get('company_id');
        $warehouse = $this->warehouseModel->find($id);
        
        if (!$warehouse || $warehouse['company_id'] != $companyId) {
            return redirect()->to('/admin/warehouses')->with('error', 'Warehouse not found');
        }
        
        $data = [
            'title' => 'Edit Warehouse',
            'warehouse' => $warehouse,
            'projects' => $this->projectModel->where('company_id', $companyId)->where('status', 'active')->findAll(),
            'users' => $this->userModel->where('company_id', $companyId)->where('status', 'active')->findAll(),
        ];
        
        return view('inventory/warehouses/edit', $data);
    }
    
    public function update($id)
    {
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $warehouse = $this->warehouseModel->find($id);
        
        if (!$warehouse || $warehouse['company_id'] != $companyId) {
            return redirect()->to('/admin/warehouses')->with('error', 'Warehouse not found');
        }
        
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'code' => 'required|is_unique[warehouses.code,id,'.$id.',company_id,'.$companyId.']',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'name' => $this->request->getVar('name'),
            'code' => $this->request->getVar('code'),
            'address' => $this->request->getVar('address'),
            'city' => $this->request->getVar('city'),
            'state' => $this->request->getVar('state'),
            'country' => $this->request->getVar('country'),
            'manager_id' => $this->request->getVar('manager_id') ?: null,
            'phone' => $this->request->getVar('phone'),
            'email' => $this->request->getVar('email'),
            'warehouse_type' => $this->request->getVar('warehouse_type'),
            'capacity' => $this->request->getVar('capacity'),
            'is_project_site' => $this->request->getVar('is_project_site') ? 1 : 0,
            'project_id' => $this->request->getVar('project_id') ?: null,
            'status' => $this->request->getVar('status'),
            'notes' => $this->request->getVar('notes'),
        ];
        
        if (!$this->warehouseModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Failed to update warehouse.');
        }
        
        return redirect()->to('/admin/warehouses')->with('success', 'Warehouse updated successfully');
    }
    
    public function delete($id)
    {
        $companyId = session()->get('company_id');
        $warehouse = $this->warehouseModel->find($id);
        
        if (!$warehouse || $warehouse['company_id'] != $companyId) {
            return redirect()->to('/admin/warehouses')->with('error', 'Warehouse not found');
        }
        
        // Check if warehouse has stock
        if ($this->warehouseStockModel->where('warehouse_id', $id)->countAllResults() > 0) {
            return redirect()->to('/admin/warehouses')->with('error', 'Cannot delete warehouse as it has stock items');
        }
        
        if (!$this->warehouseModel->delete($id)) {
            return redirect()->to('/admin/warehouses')->with('error', 'Failed to delete warehouse');
        }
        
        return redirect()->to('/admin/warehouses')->with('success', 'Warehouse deleted successfully');
    }
    
    public function stock($id)
    {
        $companyId = session()->get('company_id');
        $warehouse = $this->warehouseModel->find($id);
        
        if (!$warehouse || $warehouse['company_id'] != $companyId) {
            return redirect()->to('/admin/warehouses')->with('error', 'Warehouse not found');
        }
        
        $data = [
            'title' => 'Warehouse Stock - ' . $warehouse['name'],
            'warehouse' => $warehouse,
            'stock' => $this->warehouseStockModel->getWarehouseStock($id),
            'materials' => $this->materialModel->where('company_id', $companyId)
                ->where('status', 'active')
                ->findAll(),
        ];
        
        return view('inventory/warehouses/stock', $data);
    }
    
    public function updateStock($warehouseId, $materialId)
    {
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $warehouse = $this->warehouseModel->find($warehouseId);
        $material = $this->materialModel->find($materialId);
        
        if (!$warehouse || $warehouse['company_id'] != $companyId || !$material || $material['company_id'] != $companyId) {
            return redirect()->to('/admin/warehouses')->with('error', 'Invalid warehouse or material');
        }
        
        $rules = [
            'current_quantity' => 'required|numeric|greater_than_equal_to[0]',
            'minimum_quantity' => 'required|numeric|greater_than_equal_to[0]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'current_quantity' => $this->request->getVar('current_quantity'),
            'minimum_quantity' => $this->request->getVar('minimum_quantity'),
            'shelf_location' => $this->request->getVar('shelf_location'),
            'last_stock_update' => date('Y-m-d H:i:s'),
        ];
        
        $stockEntry = $this->warehouseStockModel
            ->where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->first();
        
        if ($stockEntry) {
            // Update existing stock entry
            $this->warehouseStockModel->update($stockEntry['id'], $data);
        } else {
            // Create new stock entry
            $data['company_id'] = $companyId;
            $data['warehouse_id'] = $warehouseId;
            $data['material_id'] = $materialId;
            
            $this->warehouseStockModel->insert($data);
        }
        
        return redirect()->to('/admin/warehouses/stock/' . $warehouseId)->with('success', 'Stock updated successfully');
    }
    
    public function addStockItem($warehouseId)
    {
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $warehouse = $this->warehouseModel->find($warehouseId);
        
        if (!$warehouse || $warehouse['company_id'] != $companyId) {
            return redirect()->to('/admin/warehouses')->with('error', 'Warehouse not found');
        }
        
        $rules = [
            'material_id' => 'required|numeric',
            'current_quantity' => 'required|numeric|greater_than_equal_to[0]',
            'minimum_quantity' => 'required|numeric|greater_than_equal_to[0]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $materialId = $this->request->getVar('material_id');
        $material = $this->materialModel->find($materialId);
        
        if (!$material || $material['company_id'] != $companyId) {
            return redirect()->back()->withInput()->with('error', 'Material not found');
        }
        
        // Check if material already exists in this warehouse
        $existingStock = $this->warehouseStockModel
            ->where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->first();
        
        if ($existingStock) {
            return redirect()->back()->withInput()->with('error', 'This material already exists in the warehouse stock');
        }
        
        $data = [
            'company_id' => $companyId,
            'warehouse_id' => $warehouseId,
            'material_id' => $materialId,
            'current_quantity' => $this->request->getVar('current_quantity'),
            'minimum_quantity' => $this->request->getVar('minimum_quantity'),
            'shelf_location' => $this->request->getVar('shelf_location'),
            'last_stock_update' => date('Y-m-d H:i:s'),
        ];
        
        if (!$this->warehouseStockModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Failed to add stock item');
        }
        
        return redirect()->to('/admin/warehouses/stock/' . $warehouseId)->with('success', 'Stock item added successfully');
    }
    
    public function removeStockItem($warehouseId, $materialId)
    {
        $companyId = session()->get('company_id');
        $warehouse = $this->warehouseModel->find($warehouseId);
        
        if (!$warehouse || $warehouse['company_id'] != $companyId) {
            return redirect()->to('/admin/warehouses')->with('error', 'Warehouse not found');
        }
        
        $stockItem = $this->warehouseStockModel
            ->where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->first();
        
        if (!$stockItem) {
            return redirect()->to('/admin/warehouses/stock/' . $warehouseId)->with('error', 'Stock item not found');
        }
        
        if ($stockItem['current_quantity'] > 0) {
            return redirect()->to('/admin/warehouses/stock/' . $warehouseId)
                ->with('error', 'Cannot remove stock item with quantity greater than zero. Please transfer or use the stock first.');
        }
        
        if (!$this->warehouseStockModel->delete($stockItem['id'])) {
            return redirect()->to('/admin/warehouses/stock/' . $warehouseId)->with('error', 'Failed to remove stock item');
        }
        
        return redirect()->to('/admin/warehouses/stock/' . $warehouseId)->with('success', 'Stock item removed successfully');
    }
    
    public function stockMovement($id)
    {
        $companyId = session()->get('company_id');
        $warehouse = $this->warehouseModel->find($id);
        
        if (!$warehouse || $warehouse['company_id'] != $companyId) {
            return redirect()->to('/admin/warehouses')->with('error', 'Warehouse not found');
        }
        
        $data = [
            'title' => 'Stock Movement History - ' . $warehouse['name'],
            'warehouse' => $warehouse,
            'incomingMovements' => $this->stockMovementModel->getWarehouseIncomingMovements($id),
            'outgoingMovements' => $this->stockMovementModel->getWarehouseOutgoingMovements($id),
        ];
        
        return view('inventory/warehouses/movements', $data);
    }
}
