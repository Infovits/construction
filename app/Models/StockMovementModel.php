<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table = 'stock_movements';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'company_id', 'material_id', 'source_warehouse_id', 'destination_warehouse_id',
        'reference_type', 'reference_id', 'movement_type', 'project_id', 'task_id',
        'quantity', 'unit_cost', 'total_cost', 'previous_balance', 'new_balance',
        'batch_number', 'serial_numbers', 'expiry_date', 'notes', 'moved_by',
        'approved_by', 'performed_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getMaterialMovements($materialId)
    {
        // Check if tasks table exists and if task_id column exists in stock_movements
        $db = \Config\Database::connect();
        $tasksTableExists = $db->tableExists('tasks');
        $taskIdColumnExists = $db->fieldExists('task_id', 'stock_movements');
        $projectsTableExists = $db->tableExists('projects');
        $projectIdColumnExists = $db->fieldExists('project_id', 'stock_movements');

        $canJoinTasks = $tasksTableExists && $taskIdColumnExists;
        $canJoinProjects = $projectsTableExists && $projectIdColumnExists;

        $builder = $this->select('stock_movements.*,
                source.name as source_warehouse_name,
                destination.name as destination_warehouse_name,
                materials.name as material_name,
                materials.item_code,
                performer.first_name as performer_first_name,
                performer.last_name as performer_last_name,
                approver.first_name as approver_first_name,
                approver.last_name as approver_last_name' .
                ($canJoinProjects ? ', projects.name as project_name' : ', NULL as project_name') .
                ($canJoinTasks ? ', tasks.title as task_name' : ', NULL as task_name'))
            ->join('warehouses as source', 'source.id = stock_movements.source_warehouse_id', 'left')
            ->join('warehouses as destination', 'destination.id = stock_movements.destination_warehouse_id', 'left')
            ->join('materials', 'materials.id = stock_movements.material_id')
            ->join('users as performer', 'performer.id = stock_movements.performed_by', 'left')
            ->join('users as approver', 'approver.id = stock_movements.approved_by', 'left');

        // Only join projects table if both table and column exist
        if ($canJoinProjects) {
            $builder->join('projects', 'projects.id = stock_movements.project_id', 'left');
        }

        // Only join tasks table if both table and column exist
        if ($canJoinTasks) {
            $builder->join('tasks', 'tasks.id = stock_movements.task_id', 'left');
        }

        return $builder->where('stock_movements.material_id', $materialId)
            ->orderBy('stock_movements.created_at', 'DESC')
            ->findAll();
    }
    
    public function getWarehouseIncomingMovements($warehouseId)
    {
        return $this->select('stock_movements.*, 
                source.name as source_warehouse_name, 
                materials.name as material_name,
                materials.item_code,
                performer.first_name as performer_first_name,
                performer.last_name as performer_last_name')
            ->join('warehouses as source', 'source.id = stock_movements.source_warehouse_id', 'left')
            ->join('materials', 'materials.id = stock_movements.material_id')
            ->join('users as performer', 'performer.id = stock_movements.performed_by', 'left')
            ->where('stock_movements.destination_warehouse_id', $warehouseId)
            ->orderBy('stock_movements.created_at', 'DESC')
            ->findAll();
    }
    
    public function getWarehouseOutgoingMovements($warehouseId)
    {
        return $this->select('stock_movements.*, 
                destination.name as destination_warehouse_name, 
                materials.name as material_name,
                materials.item_code,
                performer.first_name as performer_first_name,
                performer.last_name as performer_last_name,
                projects.name as project_name')
            ->join('warehouses as destination', 'destination.id = stock_movements.destination_warehouse_id', 'left')
            ->join('materials', 'materials.id = stock_movements.material_id')
            ->join('users as performer', 'performer.id = stock_movements.performed_by', 'left')
            ->join('projects', 'projects.id = stock_movements.project_id', 'left')
            ->where('stock_movements.source_warehouse_id', $warehouseId)
            ->orderBy('stock_movements.created_at', 'DESC')
            ->findAll();
    }
    
    public function recordMovement($companyId, $materialId, $sourceWarehouseId, $destinationWarehouseId, 
                                   $movementType, $quantity, $unit, $unitCost, $projectId = null, 
                                   $taskId = null, $notes = null, $performedBy = null, $referenceNumber = null, 
                                   $batchNumber = null, $serialNumbers = null, $expiryDate = null)
    {
        // Convert movement type to match database enum values
        $dbMovementType = $movementType;
        switch ($movementType) {
            case 'purchase':
                $dbMovementType = 'in';
                break;
            case 'project_usage':
                $dbMovementType = 'out';
                break;
            case 'stock_transfer':
                $dbMovementType = 'transfer';
                break;
        }

        $data = [
            'company_id' => $companyId,
            'material_id' => $materialId,
            'source_warehouse_id' => $sourceWarehouseId,
            'destination_warehouse_id' => $destinationWarehouseId,
            'reference_type' => 'manual',
            'reference_id' => null,
            'movement_type' => $dbMovementType,
            'project_id' => $projectId,
            'task_id' => $taskId,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $quantity * $unitCost,
            'batch_number' => $batchNumber,
            'serial_numbers' => $serialNumbers ? json_encode($serialNumbers) : null,
            'expiry_date' => $expiryDate,
            'notes' => $notes,
            'moved_by' => $performedBy,
            'performed_by' => $performedBy
        ];

        // Log the data being inserted for debugging
        log_message('debug', 'Inserting stock movement data: ' . json_encode($data));

        $result = $this->insert($data);

        // Log any database errors
        if (!$result) {
            $error = $this->db->error();
            log_message('error', 'Stock movement insert failed: ' . json_encode($error));
        }

        return $result;
    }
    
    public function processMovement($companyId, $materialId, $movementType, $quantity, $unit, $unitCost,
                                    $sourceWarehouseId = null, $destinationWarehouseId = null,
                                    $projectId = null, $taskId = null, $notes = null, $performedBy = null,
                                    $referenceNumber = null, $batchNumber = null)
    {
        // Initialize warehouse stock model
        $warehouseStockModel = new WarehouseStockModel();
        $materialModel = new MaterialModel();
        
        // Begin transaction
        $this->db->transBegin();
        
        try {
            switch ($movementType) {
                case 'purchase':
                    // Add stock to destination warehouse
                    if (!$destinationWarehouseId) {
                        throw new \Exception('Destination warehouse is required for purchases');
                    }
                    
                    $warehouseStockModel->addInitialStock($companyId, $destinationWarehouseId, $materialId, $quantity);
                    break;
                    
                case 'stock_transfer':
                    // Check if source has enough stock
                    if (!$sourceWarehouseId || !$destinationWarehouseId) {
                        throw new \Exception('Source and destination warehouses are required for transfers');
                    }
                    
                    if (!$warehouseStockModel->hasStock($sourceWarehouseId, $materialId, $quantity)) {
                        throw new \Exception('Insufficient stock in the source warehouse');
                    }
                    
                    // Remove stock from source warehouse
                    $warehouseStockModel->updateStockQuantity($sourceWarehouseId, $materialId, $quantity, 'subtract');
                    
                    // Add stock to destination warehouse
                    $warehouseStockModel->addInitialStock($companyId, $destinationWarehouseId, $materialId, $quantity);
                    break;
                    
                case 'project_usage':
                    // Check if source has enough stock
                    if (!$sourceWarehouseId || !$projectId) {
                        throw new \Exception('Source warehouse and project are required for project usage');
                    }
                    
                    if (!$warehouseStockModel->hasStock($sourceWarehouseId, $materialId, $quantity)) {
                        throw new \Exception('Insufficient stock in the source warehouse');
                    }
                    
                    // Remove stock from source warehouse
                    $warehouseStockModel->updateStockQuantity($sourceWarehouseId, $materialId, $quantity, 'subtract');
                    break;
                    
                case 'return':
                    // Add stock back to destination warehouse
                    if (!$destinationWarehouseId) {
                        throw new \Exception('Destination warehouse is required for returns');
                    }
                    
                    $warehouseStockModel->addInitialStock($companyId, $destinationWarehouseId, $materialId, $quantity);
                    break;
                    
                case 'adjustment':
                    // For adjustments, if destination warehouse is provided, it's an increase
                    // If source warehouse is provided, it's a decrease
                    if ($destinationWarehouseId) {
                        // Stock increase
                        $warehouseStockModel->addInitialStock($companyId, $destinationWarehouseId, $materialId, $quantity);
                    } elseif ($sourceWarehouseId) {
                        // Stock decrease
                        if (!$warehouseStockModel->hasStock($sourceWarehouseId, $materialId, $quantity)) {
                            throw new \Exception('Insufficient stock in the source warehouse');
                        }

                        $warehouseStockModel->updateStockQuantity($sourceWarehouseId, $materialId, $quantity, 'subtract');
                    } else {
                        throw new \Exception('Either source or destination warehouse is required for adjustments');
                    }
                    break;
                    
                case 'disposal':
                case 'loss':
                    // Remove stock from source warehouse
                    if (!$sourceWarehouseId) {
                        throw new \Exception('Source warehouse is required for disposals/losses');
                    }
                    
                    if (!$warehouseStockModel->hasStock($sourceWarehouseId, $materialId, $quantity)) {
                        throw new \Exception('Insufficient stock in the source warehouse');
                    }
                    
                    $warehouseStockModel->updateStockQuantity($sourceWarehouseId, $materialId, $quantity, 'subtract');
                    break;
            }
            
            // Record the stock movement
            $movementId = $this->recordMovement(
                $companyId,
                $materialId,
                $sourceWarehouseId,
                $destinationWarehouseId,
                $movementType,
                $quantity,
                $unit,
                $unitCost,
                $projectId,
                $taskId,
                $notes,
                $performedBy,
                $referenceNumber,
                $batchNumber
            );
            
            if (!$movementId) {
                // Get the last database error for more details
                $error = $this->db->error();
                $errorMessage = 'Failed to record stock movement';
                if (!empty($error['message'])) {
                    $errorMessage .= ': ' . $error['message'];
                }
                throw new \Exception($errorMessage);
            }
            
            // Update material's overall stock level
            $material = $materialModel->find($materialId);
            $currentStock = 0;
            
            // Calculate total stock across all warehouses
            $totalStock = $this->db->table('warehouse_stock')
                ->selectSum('current_quantity')
                ->where('material_id', $materialId)
                ->get()
                ->getRow()
                ->current_quantity ?? 0;
                
            $materialModel->update($materialId, ['current_stock' => $totalStock]);
            
            // Commit the transaction
            $this->db->transCommit();
            
            return ['success' => true, 'message' => 'Stock movement recorded successfully'];
        } catch (\Exception $e) {
            // Rollback the transaction on error
            $this->db->transRollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function getStockMovementReport($companyId, $startDate = null, $endDate = null, $warehouseId = null, $categoryId = null)
    {
        $builder = $this->db->table('stock_movements');
        $builder->select('stock_movements.*, 
                materials.name as material_name, 
                materials.item_code,
                material_categories.name as category_name,
                source.name as source_warehouse_name,
                destination.name as destination_warehouse_name,
                projects.name as project_name,
                CONCAT(performer.first_name, " ", performer.last_name) as performed_by_name');
        $builder->join('materials', 'materials.id = stock_movements.material_id');
        $builder->join('material_categories', 'material_categories.id = materials.category_id', 'left');
        $builder->join('warehouses as source', 'source.id = stock_movements.source_warehouse_id', 'left');
        $builder->join('warehouses as destination', 'destination.id = stock_movements.destination_warehouse_id', 'left');
        $builder->join('projects', 'projects.id = stock_movements.project_id', 'left');
        $builder->join('users as performer', 'performer.id = stock_movements.performed_by', 'left');
        $builder->where('stock_movements.company_id', $companyId);
        
        if ($startDate) {
            $builder->where('stock_movements.created_at >=', $startDate . ' 00:00:00');
        }
        
        if ($endDate) {
            $builder->where('stock_movements.created_at <=', $endDate . ' 23:59:59');
        }
        
        if ($warehouseId) {
            $builder->groupStart()
                ->where('stock_movements.source_warehouse_id', $warehouseId)
                ->orWhere('stock_movements.destination_warehouse_id', $warehouseId)
                ->groupEnd();
        }
        
        if ($categoryId) {
            $builder->where('materials.category_id', $categoryId);
        }
        
        $builder->orderBy('stock_movements.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    public function getProjectUsageReport($companyId, $projectId = null, $startDate = null, $endDate = null)
    {
        // Check if tasks table exists and if task_id column exists in stock_movements
        $tasksTableExists = $this->db->tableExists('tasks');
        $taskIdColumnExists = $this->db->fieldExists('task_id', 'stock_movements');
        $projectsTableExists = $this->db->tableExists('projects');
        $projectIdColumnExists = $this->db->fieldExists('project_id', 'stock_movements');

        $canJoinTasks = $tasksTableExists && $taskIdColumnExists;
        $canJoinProjects = $projectsTableExists && $projectIdColumnExists;

        $builder = $this->db->table('stock_movements');
        $builder->select('stock_movements.*,
                materials.name as material_name,
                materials.item_code,
                materials.unit_cost,
                material_categories.name as category_name,
                source.name as source_warehouse_name,' .
                ($canJoinProjects ? 'projects.name as project_name,' : 'NULL as project_name,') .
                ($canJoinTasks ? 'tasks.title as task_name,' : 'NULL as task_name,') .
                'CONCAT(performer.first_name, " ", performer.last_name) as performed_by_name');
        $builder->join('materials', 'materials.id = stock_movements.material_id');
        $builder->join('material_categories', 'material_categories.id = materials.category_id', 'left');
        $builder->join('warehouses as source', 'source.id = stock_movements.source_warehouse_id', 'left');

        // Only join projects table if both table and column exist
        if ($canJoinProjects) {
            $builder->join('projects', 'projects.id = stock_movements.project_id', 'left');
        }

        // Only join tasks table if both table and column exist
        if ($canJoinTasks) {
            $builder->join('tasks', 'tasks.id = stock_movements.task_id', 'left');
        }

        $builder->join('users as performer', 'performer.id = stock_movements.performed_by', 'left');
        $builder->where('stock_movements.company_id', $companyId);
        $builder->where('stock_movements.movement_type', 'project_usage');
        
        if ($projectId) {
            $builder->where('stock_movements.project_id', $projectId);
        } else {
            $builder->where('stock_movements.project_id IS NOT NULL');
        }
        
        if ($startDate) {
            $builder->where('stock_movements.created_at >=', $startDate . ' 00:00:00');
        }
        
        if ($endDate) {
            $builder->where('stock_movements.created_at <=', $endDate . ' 23:59:59');
        }
        
        $builder->orderBy('stock_movements.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}
