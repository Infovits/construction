<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\MaterialCategoryModel;
use App\Models\WarehouseModel;
use App\Models\WarehouseStockModel;
use App\Models\StockMovementModel;
use App\Models\SupplierModel;

class Materials extends BaseController
{
    protected $materialModel;
    protected $categoryModel;
    protected $warehouseModel;
    protected $warehouseStockModel;
    protected $stockMovementModel;
    protected $supplierModel;
    
    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->categoryModel = new MaterialCategoryModel();
        $this->warehouseModel = new WarehouseModel();
        $this->warehouseStockModel = new WarehouseStockModel();
        $this->stockMovementModel = new StockMovementModel();
        $this->supplierModel = new SupplierModel();
    }
    
    public function index()
    {
        $companyId = session()->get('company_id');
        
        $data = [
            'title' => 'Materials Management',
            'materials' => $this->materialModel->where('company_id', $companyId)->findAll(),
            'categories' => $this->categoryModel->where('company_id', $companyId)->findAll(),
            'lowStockItems' => $this->materialModel->getLowStockItems($companyId),
        ];
        
        return view('inventory/materials/index', $data);
    }
    
    public function new()
    {
        $companyId = session()->get('company_id');
        
        $data = [
            'title' => 'Add New Material',
            'categories' => $this->categoryModel->where('company_id', $companyId)->findAll(),
            'suppliers' => $this->supplierModel->where('company_id', $companyId)->findAll(),
        ];
        
        return view('inventory/materials/create', $data);
    }
    
    public function create()
    {
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $userId = session()->get('user_id');

        // Auto-generate item code if not provided
        $itemCode = $this->request->getVar('item_code');
        if (empty($itemCode)) {
            $itemCode = $this->generateItemCode($companyId);
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'item_code' => 'required|is_unique[materials.item_code,company_id,'.$companyId.']',
            'unit' => 'required',
            'category_id' => 'permit_empty|numeric',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'company_id' => $companyId,
            'name' => $this->request->getVar('name'),
            'item_code' => $itemCode,
            'barcode' => $this->request->getVar('barcode') ?? null,
            'description' => $this->request->getVar('description') ?? null,
            'brand' => $this->request->getVar('brand') ?? null,
            'model' => $this->request->getVar('model') ?? null,
            'specifications' => $this->request->getVar('specifications') ?? null,
            'unit' => $this->request->getVar('unit'),
            'unit_cost' => $this->request->getVar('unit_cost') ?? 0,
            'selling_price' => $this->request->getVar('selling_price') ?? 0,
            'current_stock' => $this->request->getVar('current_stock') ?? 0,
            'minimum_stock' => $this->request->getVar('minimum_stock') ?? 0,
            'maximum_stock' => $this->request->getVar('maximum_stock') ?? 0,
            'reorder_level' => $this->request->getVar('reorder_level') ?? 0,
            'weight' => $this->request->getVar('weight') ?? null,
            'dimensions' => $this->request->getVar('dimensions') ?? null,
            'color' => $this->request->getVar('color') ?? null,
            'material_type' => $this->request->getVar('material_type') ?? 'consumable',
            'is_tracked' => $this->request->getVar('is_tracked') ? 1 : 0,
            'is_serialized' => $this->request->getVar('is_serialized') ? 1 : 0,
            'requires_inspection' => $this->request->getVar('requires_inspection') ? 1 : 0,
            'shelf_life_days' => $this->request->getVar('shelf_life_days') ?? null,
            'status' => $this->request->getVar('status') ?? 'active',
            'category_id' => $this->request->getVar('category_id') ?: null,
            'primary_supplier_id' => $this->request->getVar('primary_supplier_id') ?: null,
            'created_by' => $userId,
        ];
        
        $materialId = $this->materialModel->insert($data);
        
        if (!$materialId) {
            return redirect()->back()->withInput()->with('error', 'Failed to add material.');
        }
        
        // Add initial stock if provided
        $initialStock = $this->request->getVar('initial_stock');
        $warehouseId = $this->request->getVar('warehouse_id');
        
        if ($initialStock > 0 && $warehouseId) {
            $this->warehouseStockModel->addInitialStock($companyId, $warehouseId, $materialId, $initialStock);
            
            // Record stock movement
            $this->stockMovementModel->recordMovement(
                $companyId,
                $materialId,
                null,
                $warehouseId,
                'purchase',
                $initialStock,
                $data['unit'],
                $data['unit_cost'],
                null,
                null,
                'Initial stock entry',
                $userId
            );
        }
        
        return redirect()->to('/admin/materials')->with('success', 'Material added successfully');
    }
    
    public function edit($id)
    {
        $companyId = session()->get('company_id');
        $material = $this->materialModel->find($id);
        
        if (!$material || $material['company_id'] != $companyId) {
            return redirect()->to('/admin/materials')->with('error', 'Material not found');
        }
        
        $data = [
            'title' => 'Edit Material',
            'material' => $material,
            'categories' => $this->categoryModel->where('company_id', $companyId)->findAll(),
            'suppliers' => $this->supplierModel->where('company_id', $companyId)->findAll(),
        ];
        
        return view('inventory/materials/edit', $data);
    }
    
    public function update($id)
    {
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $material = $this->materialModel->find($id);
        
        if (!$material || $material['company_id'] != $companyId) {
            return redirect()->to('/admin/materials')->with('error', 'Material not found');
        }
        
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'item_code' => 'required|is_unique[materials.item_code,id,'.$id.',company_id,'.$companyId.']',
            'unit' => 'required',
            'category_id' => 'permit_empty|numeric',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'name' => $this->request->getVar('name'),
            'item_code' => $this->request->getVar('item_code'),
            'barcode' => $this->request->getVar('barcode'),
            'description' => $this->request->getVar('description'),
            'brand' => $this->request->getVar('brand'),
            'model' => $this->request->getVar('model'),
            'specifications' => $this->request->getVar('specifications'),
            'unit' => $this->request->getVar('unit'),
            'unit_cost' => $this->request->getVar('unit_cost'),
            'selling_price' => $this->request->getVar('selling_price'),
            'minimum_stock' => $this->request->getVar('minimum_stock'),
            'maximum_stock' => $this->request->getVar('maximum_stock'),
            'reorder_level' => $this->request->getVar('reorder_level'),
            'weight' => $this->request->getVar('weight'),
            'dimensions' => $this->request->getVar('dimensions'),
            'color' => $this->request->getVar('color'),
            'material_type' => $this->request->getVar('material_type'),
            'is_tracked' => $this->request->getVar('is_tracked') ? 1 : 0,
            'is_serialized' => $this->request->getVar('is_serialized') ? 1 : 0,
            'requires_inspection' => $this->request->getVar('requires_inspection') ? 1 : 0,
            'shelf_life_days' => $this->request->getVar('shelf_life_days'),
            'status' => $this->request->getVar('status'),
            'category_id' => $this->request->getVar('category_id') ?: null,
            'primary_supplier_id' => $this->request->getVar('primary_supplier_id') ?: null,
        ];
        
        if (!$this->materialModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Failed to update material.');
        }
        
        return redirect()->to('/admin/materials')->with('success', 'Material updated successfully');
    }
    
    public function delete($id)
    {
        $companyId = session()->get('company_id');
        $material = $this->materialModel->find($id);
        
        if (!$material || $material['company_id'] != $companyId) {
            return redirect()->to('/admin/materials')->with('error', 'Material not found');
        }
        
        // Check if material is used in any stock movements
        if ($this->stockMovementModel->where('material_id', $id)->countAllResults() > 0) {
            return redirect()->to('/admin/materials')->with('error', 'Cannot delete material as it has stock movement history');
        }
        
        if (!$this->materialModel->delete($id)) {
            return redirect()->to('/admin/materials')->with('error', 'Failed to delete material');
        }
        
        return redirect()->to('/admin/materials')->with('success', 'Material deleted successfully');
    }
    
    public function stockMovement($id)
    {
        $companyId = session()->get('company_id');
        $material = $this->materialModel->find($id);
        
        if (!$material || $material['company_id'] != $companyId) {
            return redirect()->to('/admin/materials')->with('error', 'Material not found');
        }
        
        $data = [
            'title' => 'Stock Movement History - ' . $material['name'],
            'material' => $material,
            'movements' => $this->stockMovementModel->getMaterialMovements($id),
            'warehouses' => $this->warehouseModel->where('company_id', $companyId)->findAll(),
        ];
        
        return view('inventory/materials/stock_movement', $data);
    }
    
    public function recordStockMovement($id)
    {
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $userId = session()->get('user_id');
        $material = $this->materialModel->find($id);
        
        if (!$material || $material['company_id'] != $companyId) {
            return redirect()->to('/admin/materials')->with('error', 'Material not found');
        }
        
        $rules = [
            'movement_type' => 'required',
            'quantity' => 'required|numeric|greater_than[0]',
            'unit_cost' => 'required|numeric|greater_than_equal_to[0]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $movementType = $this->request->getVar('movement_type');
        $quantity = $this->request->getVar('quantity');
        $sourceWarehouseId = $this->request->getVar('source_warehouse_id');
        $destinationWarehouseId = $this->request->getVar('destination_warehouse_id');
        $projectId = $this->request->getVar('project_id');
        $taskId = $this->request->getVar('task_id');
        
        if ($movementType == 'stock_transfer' && (!$sourceWarehouseId || !$destinationWarehouseId || $sourceWarehouseId == $destinationWarehouseId)) {
            return redirect()->back()->withInput()->with('error', 'For transfers, you must select different source and destination warehouses');
        }
        
        if ($movementType == 'project_usage' && !$projectId) {
            return redirect()->back()->withInput()->with('error', 'For project usage, you must select a project');
        }

        // Convert movement type and adjust warehouse parameters for compatibility with the processMovement method
        $convertedMovementType = $movementType;
        $adjustedSourceWarehouseId = $sourceWarehouseId;
        $adjustedDestinationWarehouseId = $destinationWarehouseId;

        switch ($movementType) {
            case 'in':
                $convertedMovementType = 'purchase';
                // For stock in, the selected warehouse becomes the destination
                $adjustedDestinationWarehouseId = $sourceWarehouseId;
                $adjustedSourceWarehouseId = null;
                break;
            case 'out':
                $convertedMovementType = 'project_usage';
                // For stock out, the selected warehouse is the source
                $adjustedSourceWarehouseId = $sourceWarehouseId;
                $adjustedDestinationWarehouseId = null;
                break;
            case 'transfer':
                $convertedMovementType = 'stock_transfer';
                // For transfer, both warehouses are needed as provided
                break;
            case 'adjustment':
                $convertedMovementType = 'adjustment';
                // For adjustment, we'll need to determine if it's increase or decrease
                // For now, treat as increase (destination warehouse)
                $adjustedDestinationWarehouseId = $sourceWarehouseId;
                $adjustedSourceWarehouseId = null;
                break;
            case 'project_usage':
                $convertedMovementType = 'project_usage';
                // For project usage, the selected warehouse is the source
                $adjustedSourceWarehouseId = $sourceWarehouseId;
                $adjustedDestinationWarehouseId = null;
                break;
        }

        // Process the stock movement
        $result = $this->stockMovementModel->processMovement(
            $companyId,
            $id,
            $convertedMovementType,
            $quantity,
            $material['unit'],
            $this->request->getVar('unit_cost'),
            $adjustedSourceWarehouseId,
            $adjustedDestinationWarehouseId,
            $projectId,
            $taskId,
            $this->request->getVar('notes'),
            $userId,
            $this->request->getVar('reference_number'),
            $this->request->getVar('batch_number')
        );
        
        if (!$result['success']) {
            // Log the detailed error for debugging
            log_message('error', 'Stock movement failed: ' . $result['message']);
            log_message('error', 'Movement data: ' . json_encode([
                'movement_type' => $convertedMovementType,
                'quantity' => $quantity,
                'source_warehouse' => $adjustedSourceWarehouseId,
                'destination_warehouse' => $adjustedDestinationWarehouseId,
                'material_id' => $id
            ]));
            return redirect()->back()->withInput()->with('error', 'Failed to record stock movement: ' . $result['message']);
        }
        
        return redirect()->to('/admin/materials/stock-movement/' . $id)->with('success', 'Stock movement recorded successfully');
    }
    
    public function barcodeScanner()
    {
        $companyId = session()->get('company_id');
        
        // Get warehouses for the dropdown
        $warehouses = $this->warehouseModel->where('company_id', $companyId)->findAll();
        
        // Get projects for the dropdown
        $projectModel = model('App\Models\ProjectModel');
        $projects = $projectModel->where('company_id', $companyId)
            ->where('status', 'active')
            ->findAll();
        
        $data = [
            'title' => 'Barcode Scanner',
            'warehouses' => $warehouses,
            'projects' => $projects
        ];
        
        return view('inventory/materials/barcode_scanner', $data);
    }
    
    public function recordStockMovementAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        helper(['form', 'url']);
        
        $companyId = session()->get('company_id');
        $userId = session()->get('user_id');
        $materialId = $this->request->getVar('material_id');
        
        $material = $this->materialModel->find($materialId);
        
        if (!$material || $material['company_id'] != $companyId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Material not found']);
        }
        
        $rules = [
            'movement_type' => 'required',
            'warehouse_id' => 'required',
            'quantity' => 'required|numeric|greater_than[0]',
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Validation error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        $movementType = $this->request->getVar('movement_type');
        $quantity = $this->request->getVar('quantity');
        $warehouseId = $this->request->getVar('warehouse_id');
        $destinationWarehouseId = $this->request->getVar('destination_warehouse_id');
        $projectId = $this->request->getVar('project_id');
        
        if ($movementType === 'transfer' && (!$destinationWarehouseId || $warehouseId == $destinationWarehouseId)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'For transfers, you must select different source and destination warehouses'
            ]);
        }
        
        // Convert movement type for compatibility with the existing method
        $convertedMovementType = $movementType;
        switch ($movementType) {
            case 'stock_in':
                $convertedMovementType = 'stock_in';
                break;
            case 'stock_out':
                $convertedMovementType = 'stock_out';
                break;
            case 'transfer':
                $convertedMovementType = 'stock_transfer';
                break;
            case 'adjustment':
                $convertedMovementType = 'inventory_adjustment';
                break;
        }
        
        // Get current stock price for unit cost if not provided
        $unitCost = $material['unit_cost'] ?? 0;
        
        // Process the stock movement
        $result = $this->stockMovementModel->processMovement(
            $companyId,
            $materialId,
            $convertedMovementType,
            $quantity,
            $material['unit'],
            $unitCost,
            $warehouseId,
            $destinationWarehouseId,
            $projectId,
            null, // taskId
            $this->request->getVar('notes'),
            $userId,
            null, // reference_number
            null  // batch_number
        );
        
        if (!$result['success']) {
            return $this->response->setJSON(['success' => false, 'message' => $result['message']]);
        }
        
        return $this->response->setJSON([
            'success' => true, 
            'message' => 'Stock movement recorded successfully',
            'movementId' => $result['movement_id'] ?? null
        ]);
    }
    
    public function getMaterialByBarcode()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request']);
        }
        
        $barcode = $this->request->getVar('barcode');
        $companyId = session()->get('company_id');
        
        $material = $this->materialModel->where('company_id', $companyId)
            ->where('barcode', $barcode)
            ->first();
        
        if (!$material) {
            return $this->response->setJSON(['error' => 'Material not found']);
        }
        
        // Get stock levels in all warehouses
        $stockLevels = $this->warehouseStockModel->getMaterialStockLevels($material['id']);
        
        return $this->response->setJSON([
            'success' => true,
            'material' => $material,
            'stockLevels' => $stockLevels
        ]);
    }
    
    public function generateReport()
    {
        $companyId = session()->get('company_id');
        $reportType = $this->request->getVar('report_type') ?? 'stock_levels';
        $projectId = $this->request->getVar('project_id');
        $warehouseId = $this->request->getVar('warehouse_id');
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');
        $categoryId = $this->request->getVar('category_id');
        $supplierId = $this->request->getVar('supplier_id');
        $materialId = $this->request->getVar('material_id');
        $format = $this->request->getVar('format') ?? 'html';
        
        // Get projects for the form
        $projectModel = model('App\Models\ProjectModel');
        $projects = $projectModel->where('company_id', $companyId)->findAll();
        
        // Get suppliers for the form
        $suppliers = $this->supplierModel->where('company_id', $companyId)->findAll();
        
        // Get materials for the form
        $materials = $this->materialModel->where('company_id', $companyId)->findAll();
        
        $data = [
            'title' => 'Material Reports',
            'reportType' => $reportType,
            'warehouses' => $this->warehouseModel->where('company_id', $companyId)->findAll(),
            'categories' => $this->categoryModel->where('company_id', $companyId)->findAll(),
            'projects' => $projects,
            'suppliers' => $suppliers,
            'materials' => $materials
        ];
        
        switch ($reportType) {
            case 'stock_levels':
                $data['report'] = $this->materialModel->getStockLevelsReport($companyId, $warehouseId, $categoryId);
                break;
                
            case 'stock_movement':
                $data['report'] = $this->stockMovementModel->getStockMovementReport(
                    $companyId, 
                    $startDate, 
                    $endDate, 
                    $warehouseId,
                    $categoryId
                );
                break;
                
            case 'project_usage':
                $data['report'] = $this->stockMovementModel->getProjectUsageReport(
                    $companyId,
                    $projectId,
                    $startDate,
                    $endDate
                );
                break;
                
            case 'low_stock':
                $data['report'] = $this->materialModel->getLowStockItems($companyId, $warehouseId, $categoryId);
                break;
        }
        
        // Handle report generation based on format
        if ($this->request->getMethod() === 'post' && $reportType) {
            switch ($format) {
                case 'pdf':
                    return $this->generatePDF($data, $reportType);
                    
                case 'excel':
                    // Always ensure report data is available for Excel generation
                    switch ($reportType) {
                        case 'stock_levels':
                            $data['report'] = $this->materialModel->getStockLevelsReport($companyId, $warehouseId, $categoryId);
                            break;

                        case 'stock_movement':
                            $data['report'] = $this->stockMovementModel->getStockMovementReport(
                                $companyId,
                                $startDate,
                                $endDate,
                                $warehouseId,
                                $categoryId
                            );
                            break;

                        case 'project_usage':
                            $data['report'] = $this->stockMovementModel->getProjectUsageReport(
                                $companyId,
                                $projectId,
                                $startDate,
                                $endDate
                            );
                            break;

                        case 'low_stock':
                            $data['report'] = $this->materialModel->getLowStockItems($companyId, $warehouseId, $categoryId);
                            break;

                        case 'supplier':
                            $data['report'] = $this->supplierModel->getSupplierReport(
                                $companyId,
                                $supplierId,
                                $startDate,
                                $endDate
                            );
                            break;

                        case 'cost_trend':
                            $dateRange = $this->request->getVar('date_range');
                            if ($dateRange === 'custom') {
                                // Use provided custom range
                            } else {
                                // Calculate date based on range (30, 90, 180, 365 days)
                                $endDate = date('Y-m-d');
                                $startDate = date('Y-m-d', strtotime("-{$dateRange} days"));
                            }

                            $data['report'] = $this->materialModel->getCostTrendReport(
                                $companyId,
                                $materialId,
                                $startDate,
                                $endDate
                            );
                            break;

                        default:
                            // For unsupported report types, return error
                            return redirect()->back()->with('error', 'Excel generation not supported for this report type');
                    }
                    return $this->generateExcel($data, $reportType);
                    
                case 'html':
                    // For HTML format, prepare and add the report to data array
                    switch ($reportType) {
                        // case 'valuation':
                        //     $data['report'] = $this->materialModel->getStockValuationReport($companyId, $warehouseId, $categoryId);
                        //     return view('inventory/materials/reports/valuation', $data);
                            
                        case 'movement':
                            $data['report'] = $this->stockMovementModel->getStockMovementReport(
                                $companyId,
                                $startDate,
                                $endDate,
                                $materialId,
                                $warehouseId
                            );
                            $data['startDate'] = $startDate;
                            $data['endDate'] = $endDate;
                            return view('inventory/materials/reports/movement', $data);
                            
                        case 'project_usage':
                            $data['report'] = $this->stockMovementModel->getProjectUsageReport(
                                $companyId,
                                $projectId,
                                $startDate,
                                $endDate,
                                $categoryId
                            );
                            return view('inventory/materials/reports/project_usage', $data);
                            
                        case 'low_stock':
                            $threshold = $this->request->getVar('threshold') ?? 0;
                            $data['report'] = $this->materialModel->getLowStockItems($companyId, $warehouseId, $categoryId, (int)$threshold);
                            // Check if view exists before trying to load it
                            if (file_exists(APPPATH . 'Views/inventory/materials/reports/low_stock.php')) {
                                return view('inventory/materials/reports/low_stock', $data);
                            } else {
                                // Return error if view doesn't exist
                                return redirect()->back()->with('error', 'Low stock report view not available');
                            }
                            
                        case 'supplier':
                            $data['report'] = $this->supplierModel->getSupplierReport(
                                $companyId,
                                $supplierId,
                                $startDate,
                                $endDate
                            );
                            return view('inventory/materials/reports/supplier', $data);
                            
                        case 'cost_trend':
                            $dateRange = $this->request->getVar('date_range');
                            if ($dateRange === 'custom') {
                                // Use provided custom range
                            } else {
                                // Calculate date based on range (30, 90, 180, 365 days)
                                $endDate = date('Y-m-d');
                                $startDate = date('Y-m-d', strtotime("-{$dateRange} days"));
                            }
                            
                            $data['report'] = $this->materialModel->getCostTrendReport(
                                $companyId,
                                $materialId,
                                $startDate,
                                $endDate
                            );
                            return view('inventory/materials/reports/cost_trend', $data);
                    }
            }
        }
        
        // If no report type or method is GET, just show the report options view
        return view('inventory/materials/report', $data);
    }
    
    /**
     * Generate PDF report
     * 
     * @param array $data Report data
     * @param string $reportType Type of report
     * @return mixed PDF output
     */
    private function generatePDF($data, $reportType)
    {
        // Get company info for the PDF header
        $companyId = session()->get('company_id');
        $companyModel = new \App\Models\CompanyModel();
        $company = $companyModel->find($companyId);
        
        // Initialize PDF wrapper
        $pdf = new \App\Libraries\MpdfWrapper($company);
        
        // Get filters for the PDF
        $filters = [
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'warehouse_name' => $this->request->getVar('warehouse_id') ? 
                $this->warehouseModel->find($this->request->getVar('warehouse_id'))['name'] : null,
            'category_id' => $this->request->getVar('category_id'),
            'category_name' => $this->request->getVar('category_id') ? 
                $this->categoryModel->find($this->request->getVar('category_id'))['name'] : null,
            'project_id' => $this->request->getVar('project_id'),
            'project_name' => $this->request->getVar('project_id') ? 
                model('App\Models\ProjectModel')->find($this->request->getVar('project_id'))['name'] : null,
            'material_id' => $this->request->getVar('material_id'),
            'material_name' => $this->request->getVar('material_id') ? 
                $this->materialModel->find($this->request->getVar('material_id'))['name'] : null,
            'start_date' => $this->request->getVar('start_date'),
            'end_date' => $this->request->getVar('end_date'),
            'movement_type' => $this->request->getVar('movement_type')
        ];
        
        // Add date variables to data for PDF generation
        $data['startDate'] = $this->request->getVar('start_date');
        $data['endDate'] = $this->request->getVar('end_date');

        // Generate the appropriate report based on type
        switch ($reportType) {
            case 'stock_movement':
                return $pdf->generateStockMovementReport($data['report'], $filters);

            case 'project_usage':
                return $pdf->generateProjectUsageReport($data['report'], $filters);

            case 'low_stock':
                return $pdf->generateLowStockReport($data['report'], $filters);

            default:
                // For other report types, check if PDF view exists, otherwise use HTML view
                $pdfViewPath = 'inventory/materials/reports/' . $reportType . '_pdf';
                if (file_exists(APPPATH . 'Views/' . str_replace('/', DIRECTORY_SEPARATOR, $pdfViewPath) . '.php')) {
                    $html = view($pdfViewPath, $data, ['debug' => false]);
                    return $pdf->generatePdf($html, $reportType . '_report.pdf');
                } else {
                    // Fall back to HTML view if PDF view doesn't exist
                    $htmlViewPath = 'inventory/materials/reports/' . $reportType;
                    if (file_exists(APPPATH . 'Views/' . str_replace('/', DIRECTORY_SEPARATOR, $htmlViewPath) . '.php')) {
                        $html = view($htmlViewPath, $data, ['debug' => false]);
                        return $pdf->generatePdf($html, $reportType . '_report.pdf');
                    } else {
                        // Return error if neither view exists
                        return redirect()->back()->with('error', 'PDF generation not supported for this report type');
                    }
                }
        }
    }
    
    /**
     * Generate Excel report
     * 
     * @param array $data Report data
     * @param string $reportType Type of report
     * @return mixed Excel output
     */
    private function generateExcel($data, $reportType)
    {
        // Set up response headers for Excel download
        $response = service('response');
        $response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->setHeader('Content-Disposition', 'attachment;filename="' . $reportType . '_report.xlsx"');
        $response->noCache();
        
        // Initialize Excel export with report title
        $excel = new \App\Libraries\ExcelExport($data['report'], ucfirst(str_replace('_', ' ', $reportType)) . ' Report');
        
        // Generate the appropriate report based on type
        switch ($reportType) {
            case 'stock_movement':
                $excelContent = $excel->exportStockMovement($data['report']);
                break;
                
            case 'project_usage':
                $excelContent = $excel->exportProjectUsage($data['report']);
                break;
                
            case 'low_stock':
                $excelContent = $excel->exportLowStock($data['report']);
                break;
                
            case 'cost_trend':
                $excelContent = $excel->exportCostTrend($data['report']);
                break;
                
            default:
                // For other report types, use default Excel generation
                $excelContent = $excel->export();
        }
        
        $response->setBody($excelContent);
        return $response->send();
    }
    
    /**
     * Display low stock notifications
     */
    public function lowStockNotifications()
    {
        $companyId = session()->get('company_id');
        $materialModel = new \App\Models\MaterialModel();
        $warehouseModel = new \App\Models\WarehouseModel();
        
        // Get notification settings
        $notificationSettings = model('App\Models\SettingModel')->getNotificationSettings($companyId, 'inventory_low_stock');
        
        $data = [
            'title' => 'Low Stock Notifications',
            'lowStockItems' => $materialModel->getLowStockNotifications($companyId),
            'warehouses' => $warehouseModel->where('company_id', $companyId)->findAll(),
            'settings' => $notificationSettings,
            'page' => 'notifications'
        ];
        
        return view('inventory/materials/low_stock_notifications', $data);
    }
    
    /**
     * API endpoint for checking low stock materials
     */
    public function checkLowStockApi()
    {
        $companyId = session()->get('company_id');
        $materialModel = new \App\Models\MaterialModel();
        
        $criticalItems = $materialModel->getCriticalLowStockMaterials($companyId);
        $lowItems = $materialModel->getMaterialsNeedingReorder($companyId);
        
        $response = [
            'critical_count' => count($criticalItems),
            'low_count' => count($lowItems) - count($criticalItems), // Avoid double counting critical items
            'total' => count($lowItems),
            'has_notifications' => count($lowItems) > 0
        ];
        
        return $this->response->setJSON($response);
    }
    
    /**
     * Save notification settings
     */
    public function saveNotificationSettings()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $companyId = session()->get('company_id');
        $userId = session()->get('user_id');
        
        // Get form data
        $emailEnabled = (bool) $this->request->getVar('email_notifications');
        $emailRecipients = $this->request->getVar('email_recipients');
        $pushEnabled = (bool) $this->request->getVar('push_notifications');
        $frequency = $this->request->getVar('notification_frequency');
        $threshold = $this->request->getVar('notification_threshold');
        $customThresholdValue = (int) $this->request->getVar('custom_threshold_value');
        
        // Validate custom threshold
        if ($threshold === 'custom' && ($customThresholdValue < 1 || $customThresholdValue > 100)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Custom threshold percentage must be between 1 and 100'
            ]);
        }
        
        // Prepare settings array
        $settings = [
            'email_enabled' => $emailEnabled,
            'email_recipients' => $emailRecipients,
            'push_enabled' => $pushEnabled,
            'frequency' => $frequency,
            'threshold' => $threshold,
            'custom_threshold_value' => $customThresholdValue,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $userId
        ];
        
        // Save settings
        $settingModel = model('App\Models\SettingModel');
        $result = $settingModel->saveNotificationSettings($companyId, 'inventory_low_stock', $settings);
        
        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification settings saved successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to save notification settings'
            ]);
        }
    }
    
    /**
     * Send test notification
     */
    public function sendTestNotification()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $companyId = session()->get('company_id');
        $userId = session()->get('user_id');
        
        // Get user info
        $userModel = model('App\Models\UserModel');
        $user = $userModel->find($userId);
        
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }
        
        $settingModel = model('App\Models\SettingModel');
        $notificationSettings = $settingModel->getNotificationSettings($companyId, 'inventory_low_stock');
        
        // Initialize notification service
        $notificationService = new \App\Libraries\NotificationService();
        
        // Send test notification
        $result = $notificationService->sendTestNotification($notificationSettings, $user);
        
        if ($result['emailSent'] || $result['pushSent']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Test notification sent successfully',
                'emailSent' => $result['emailSent'],
                'pushSent' => $result['pushSent']
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No notification methods are enabled'
            ]);
        }
    }
    
    /**
     * Optimize stock levels based on historical usage
     */
    public function optimizeStockLevels()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $companyId = session()->get('company_id');
        
        // This would normally be a complex algorithm analyzing historical usage patterns
        // For this implementation, we'll simulate the process
        
        // Get all materials
        $materials = $this->materialModel->where('company_id', $companyId)->findAll();
        
        // Get stock movement history to analyze usage patterns
        $stockMovementModel = new \App\Models\StockMovementModel();
        $updatedCount = 0;
        
        // Process each material
        foreach ($materials as $material) {
            // Get historical usage (last 90 days)
            $endDate = date('Y-m-d');
            $startDate = date('Y-m-d', strtotime('-90 days'));
            
            $usage = $stockMovementModel->getHistoricalUsage($material['id'], $startDate, $endDate);
            
            if (!empty($usage) && isset($usage['avg_daily_usage']) && $usage['avg_daily_usage'] > 0) {
                // Calculate new minimum stock (30-day supply + lead time buffer)
                $leadTimeBuffer = !empty($material['avg_lead_time']) ? $material['avg_lead_time'] : 7;
                $newMinStock = ceil($usage['avg_daily_usage'] * ($leadTimeBuffer + 30));
                
                // Calculate new reorder level (14-day supply + lead time buffer)
                $newReorderLevel = ceil($usage['avg_daily_usage'] * ($leadTimeBuffer + 14));
                
                // Only update if significantly different from current values
                if (abs($newMinStock - $material['minimum_stock']) / $material['minimum_stock'] > 0.1 ||
                    abs($newReorderLevel - $material['reorder_level']) / $material['reorder_level'] > 0.1) {
                    
                    $this->materialModel->update($material['id'], [
                        'minimum_stock' => $newMinStock,
                        'reorder_level' => $newReorderLevel,
                        'last_optimized' => date('Y-m-d H:i:s')
                    ]);
                    
                    $updatedCount++;
                }
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Stock levels optimized successfully',
            'updated' => $updatedCount
        ]);
    }
    
    /**
     * Cron job to check for low stock items and send notifications
     * This method is designed to be called via a scheduled task/cron job
     */
    public function sendAutoLowStockNotifications($apiKey = null)
    {
        // Validate API key if provided (basic security for cron jobs)
        if ($apiKey !== null && $apiKey !== getenv('CRON_API_KEY')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid API key'
            ]);
        }
        
        $settingModel = model('App\Models\SettingModel');
        $userModel = model('App\Models\UserModel');
        
        // Process for each company
        $companyModel = new \App\Models\CompanyModel();
        $companies = $companyModel->findAll();
        
        $results = [];
        
        foreach ($companies as $company) {
            $companyId = $company['id'];
            
            // Get notification settings for this company
            $notificationSettings = $settingModel->getNotificationSettings($companyId, 'inventory_low_stock');
            
            // Skip if notifications are disabled (both email and push are off)
            if (empty($notificationSettings['email_enabled']) && empty($notificationSettings['push_enabled'])) {
                $results[$company['name']] = 'Notifications disabled';
                continue;
            }
            
            // Check notification frequency
            $frequency = $notificationSettings['frequency'] ?? 'daily';
            $sendNotification = false;
            
            switch ($frequency) {
                case 'daily':
                    $sendNotification = true; // Assuming this runs once daily
                    break;
                    
                case 'weekly':
                    // Check if today is the first day of week (Sunday = 0)
                    $sendNotification = (date('w') == 0);
                    break;
                    
                case 'monthly':
                    // Check if today is the first day of month
                    $sendNotification = (date('j') == 1);
                    break;
                    
                case 'critical_only':
                    // Will check for critical items below
                    $sendNotification = true;
                    break;
            }
            
            if (!$sendNotification) {
                $results[$company['name']] = "Skipped based on frequency setting: {$frequency}";
                continue;
            }
            
            // Get low stock items based on settings
            $materialModel = new \App\Models\MaterialModel();
            $lowStockItems = [];
            
            if ($frequency === 'critical_only') {
                $lowStockItems = $materialModel->getCriticalLowStockMaterials($companyId);
                
                if (empty($lowStockItems)) {
                    $results[$company['name']] = 'No critical items found';
                    continue;
                }
            } else {
                $lowStockItems = $materialModel->getLowStockNotifications($companyId);
                
                if (empty($lowStockItems)) {
                    $results[$company['name']] = 'No low stock items found';
                    continue;
                }
            }
            
            // Get admin user for this company
            $admin = $userModel->where('company_id', $companyId)
                ->where('role_id', 1) // Assuming role_id 1 is admin
                ->first();
                
            if (!$admin) {
                $results[$company['name']] = 'No admin user found';
                continue;
            }
            
            // Initialize notification service
            $notificationService = new \App\Libraries\NotificationService();
            
            // Send notifications
            $notificationResult = $notificationService->sendLowStockNotification(
                $notificationSettings, 
                $lowStockItems, 
                $admin
            );
            
            $results[$company['name']] = $notificationResult;
            
            // Log the notification
            log_message('info', "Auto low stock notification for company {$company['name']} ({$companyId}): " . 
                json_encode($notificationResult));
        }
        
        if (is_cli()) {
            // Output for CLI
            foreach ($results as $company => $result) {
                echo "{$company}: " . (is_array($result) ? json_encode($result) : $result) . PHP_EOL;
            }
            return;
        } else {
            // Output JSON for web requests
            return $this->response->setJSON([
                'success' => true,
                'results' => $results,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    /**
     * Create purchase order from low stock items
     */
    public function createPurchaseOrder()
    {
        $companyId = session()->get('company_id');
        $userId = session()->get('user_id');
        
        // Get material IDs from request
        $materialIds = $this->request->getVar('materials');
        
        if (empty($materialIds)) {
            return redirect()->to('materials/low-stock-notifications')->with('error', 'No materials selected for purchase order');
        }
        
        // Convert to array if string
        if (!is_array($materialIds)) {
            $materialIds = [$materialIds];
        }
        
        // Get selected materials
        $materialModel = new \App\Models\MaterialModel();
        $materials = $materialModel->whereIn('id', $materialIds)->findAll();
        
        if (empty($materials)) {
            return redirect()->to('materials/low-stock-notifications')->with('error', 'Selected materials not found');
        }
        
        // Group materials by supplier (if assigned)
        $groupedMaterials = [];
        $unassignedMaterials = [];
        $supplierIds = [];
        
        foreach ($materials as $material) {
            if (!empty($material['preferred_supplier_id'])) {
                if (!isset($groupedMaterials[$material['preferred_supplier_id']])) {
                    $groupedMaterials[$material['preferred_supplier_id']] = [];
                    $supplierIds[] = $material['preferred_supplier_id'];
                }
                $groupedMaterials[$material['preferred_supplier_id']][] = $material;
            } else {
                $unassignedMaterials[] = $material;
            }
        }
        
        // Get suppliers
        $supplierModel = new \App\Models\SupplierModel();
        $suppliers = $supplierModel->where('company_id', $companyId)->findAll();
        
        // Get default supplier info for grouped materials
        $materialSuppliers = [];
        if (!empty($supplierIds)) {
            $materialSuppliers = $supplierModel->whereIn('id', $supplierIds)->findAll();
        }
        
        // Get warehouses
        $warehouseModel = new \App\Models\WarehouseModel();
        $warehouses = $warehouseModel->where('company_id', $companyId)->findAll();
        
        $data = [
            'title' => 'Create Purchase Order',
            'materials' => $materials,
            'groupedMaterials' => $groupedMaterials,
            'unassignedMaterials' => $unassignedMaterials,
            'suppliers' => $suppliers,
            'materialSuppliers' => $materialSuppliers,
            'warehouses' => $warehouses,
            'page' => 'purchase_order'
        ];
        
        return view('inventory/materials/create_purchase_order', $data);
    }
    
    /**
     * Save purchase order
     */
    public function savePurchaseOrder()
    {
        if (!$this->request->getMethod() === 'post') {
            return redirect()->to('materials/low-stock-notifications')->with('error', 'Invalid request method');
        }
        
        $companyId = session()->get('company_id');
        $userId = session()->get('user_id');
        
        // Get form data
        $supplierId = $this->request->getVar('supplier_id');
        $materialIds = $this->request->getVar('material_id');
        $quantities = $this->request->getVar('quantity');
        $unitPrices = $this->request->getVar('unit_price');
        $expectedDeliveryDate = $this->request->getVar('expected_delivery_date');
        $shippingAddress = $this->request->getVar('shipping_address');
        $shippingMethod = $this->request->getVar('shipping_method');
        $paymentTerms = $this->request->getVar('payment_terms');
        $notes = $this->request->getVar('notes');
        
        // Validate required fields
        if (empty($supplierId) || empty($materialIds) || empty($quantities) || empty($unitPrices)) {
            return redirect()->to('materials/create-purchase-order')->with('error', 'Please fill in all required fields');
        }
        
        // Prepare items for purchase order
        $items = [];
        foreach ($materialIds as $index => $materialId) {
            if (empty($quantities[$index]) || empty($unitPrices[$index])) {
                continue;
            }
            
            $items[] = [
                'material_id' => $materialId,
                'quantity' => $quantities[$index],
                'unit_price' => $unitPrices[$index]
            ];
        }
        
        if (empty($items)) {
            return redirect()->to('materials/create-purchase-order')->with('error', 'No valid items to order');
        }
        
        // Prepare order data
        $orderData = [
            'expected_delivery_date' => $expectedDeliveryDate,
            'shipping_address' => $shippingAddress,
            'shipping_method' => $shippingMethod,
            'payment_terms' => $paymentTerms,
            'notes' => $notes
        ];
        
        // Create purchase order
        $purchaseOrderModel = new \App\Models\PurchaseOrderModel();
        $poId = $purchaseOrderModel->createFromLowStock($companyId, $supplierId, $items, $orderData, $userId);
        
        if (!$poId) {
            return redirect()->to('materials/create-purchase-order')->with('error', 'Failed to create purchase order');
        }
        
        return redirect()->to('purchase-orders/view/' . $poId)->with('success', 'Purchase order created successfully');
    }

    /**
     * Get materials in JSON format for AJAX requests
     */
    public function getJson()
    {
        $companyId = session()->get('company_id');
        $materials = $this->materialModel->where('company_id', $companyId)->findAll();

        return $this->response->setJSON($materials);
    }

    /**
     * Generate a unique item code for the company
     *
     * @param int $companyId
     * @return string
     */
    private function generateItemCode($companyId)
    {
        // Get the count of existing materials for this company
        $count = $this->materialModel->where('company_id', $companyId)->countAllResults();

        // Generate code in format MAT001, MAT002, etc.
        do {
            $count++;
            $code = 'MAT' . str_pad($count, 3, '0', STR_PAD_LEFT);

            // Check if this code already exists
            $existing = $this->materialModel->where('company_id', $companyId)
                                          ->where('item_code', $code)
                                          ->first();
        } while ($existing);

        return $code;
    }
}
