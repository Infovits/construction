<?php

namespace App\Models;

use CodeIgniter\Model;

class QualityInspectionModel extends Model
{
    protected $table = 'quality_inspections';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'inspection_number', 'grn_item_id', 'material_id', 'inspector_id',
        'inspection_date', 'inspection_type', 'status', 'overall_grade',
        'quantity_inspected', 'quantity_passed', 'quantity_failed',
        'defect_description', 'corrective_action', 'inspector_notes',
        'attachments', 'completed_at', 'criteria'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_PASSED = 'passed';
    const STATUS_FAILED = 'failed';
    const STATUS_CONDITIONAL = 'conditional';

    // Inspection type constants
    const TYPE_INCOMING = 'incoming';
    const TYPE_RANDOM = 'random';
    const TYPE_COMPLAINT = 'complaint';
    const TYPE_AUDIT = 'audit';

    // Grade constants
    const GRADE_A = 'A';
    const GRADE_B = 'B';
    const GRADE_C = 'C';
    const GRADE_D = 'D';
    const GRADE_F = 'F';

    /**
     * Get quality inspections with related information
     *
     * @param array $filters Optional filters
     * @return array List of quality inspections
     */
    public function getInspectionsWithDetails($filters = [])
    {
        $builder = $this->select('quality_inspections.*, 
                materials.name as material_name,
                inspector.first_name as inspector_first_name,
                inspector.last_name as inspector_last_name,
                CONCAT(inspector.first_name, " ", inspector.last_name) as inspector_name,
                goods_receipt_items.grn_id,
                goods_receipt_notes.grn_number,
                goods_receipt_notes.supplier_id,
                suppliers.name as supplier_name')
            ->join('materials', 'materials.id = quality_inspections.material_id', 'left')
            ->join('users as inspector', 'inspector.id = quality_inspections.inspector_id', 'left')
            ->join('goods_receipt_items', 'goods_receipt_items.id = quality_inspections.grn_item_id', 'left')
            ->join('goods_receipt_notes', 'goods_receipt_notes.id = goods_receipt_items.grn_id', 'left')
            ->join('suppliers', 'suppliers.id = goods_receipt_notes.supplier_id', 'left');

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('quality_inspections.status', $filters['status']);
        }

        if (!empty($filters['inspection_type'])) {
            $builder->where('quality_inspections.inspection_type', $filters['inspection_type']);
        }

        if (!empty($filters['inspector_id'])) {
            $builder->where('quality_inspections.inspector_id', $filters['inspector_id']);
        }

        if (!empty($filters['supplier_id'])) {
            $builder->where('goods_receipt_notes.supplier_id', $filters['supplier_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('quality_inspections.inspection_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('quality_inspections.inspection_date <=', $filters['date_to']);
        }

        if (!empty($filters['project_id'])) {
            $builder->join('purchase_orders', 'purchase_orders.id = goods_receipt_notes.purchase_order_id', 'left')
                   ->where('purchase_orders.project_id', $filters['project_id']);
        }

        return $builder->orderBy('quality_inspections.inspection_date', 'DESC')->findAll();
    }

    /**
     * Get inspection details
     *
     * @param int $id Inspection ID
     * @return array|null Inspection details
     */
    public function getInspectionDetails($id)
    {
        return $this->select('quality_inspections.*, 
                materials.name as material_name,
                materials.item_code,
                materials.unit,
                materials.specifications,
                inspector.first_name as inspector_first_name,
                inspector.last_name as inspector_last_name,
                CONCAT(inspector.first_name, " ", inspector.last_name) as inspector_name,
                inspector.email as inspector_email,
                inspector.phone as inspector_phone,
                departments.name as inspector_department,
                goods_receipt_items.quantity_delivered,
                goods_receipt_notes.grn_number,
                goods_receipt_notes.delivery_date,
                suppliers.name as supplier_name,
                suppliers.contact_person as supplier_contact')
            ->join('materials', 'materials.id = quality_inspections.material_id', 'left')
            ->join('users as inspector', 'inspector.id = quality_inspections.inspector_id', 'left')
            ->join('employee_details', 'employee_details.user_id = inspector.id', 'left')
            ->join('departments', 'departments.id = employee_details.department_id', 'left')
            ->join('goods_receipt_items', 'goods_receipt_items.id = quality_inspections.grn_item_id', 'left')
            ->join('goods_receipt_notes', 'goods_receipt_notes.id = goods_receipt_items.grn_id', 'left')
            ->join('suppliers', 'suppliers.id = goods_receipt_notes.supplier_id', 'left')
            ->find($id);
    }

    /**
     * Generate next inspection number
     *
     * @return string Next inspection number
     */
    public function generateInspectionNumber()
    {
        $lastInspection = $this->orderBy('id', 'DESC')->first();
        $prefix = 'QI-' . date('Ymd') . '-';

        if (!$lastInspection) {
            return $prefix . '0001';
        }

        // Extract sequence number from last inspection
        $lastInspectionNumber = $lastInspection['inspection_number'];
        $lastSequence = 0;

        if (preg_match('/QI-\d{8}-(\d+)/', $lastInspectionNumber, $matches)) {
            $lastSequence = (int) $matches[1];
        }

        // Increment sequence
        $nextSequence = $lastSequence + 1;

        // Format with leading zeros
        return $prefix . sprintf('%04d', $nextSequence);
    }

    /**
     * Create inspection from GRN item
     *
     * @param int $grnItemId GRN item ID
     * @param int $inspectorId Inspector user ID
     * @param string $inspectionType Type of inspection
     * @return int|bool Inspection ID or false on failure
     */
    public function createFromGRNItem($grnItemId, $inspectorId, $inspectionType = self::TYPE_INCOMING)
    {
        $db = \Config\Database::connect();
        
        // Get GRN item details
        $grnItem = $db->table('goods_receipt_items')
            ->select('goods_receipt_items.*, materials.id as material_id')
            ->join('materials', 'materials.id = goods_receipt_items.material_id')
            ->where('goods_receipt_items.id', $grnItemId)
            ->get()
            ->getRowArray();

        if (!$grnItem) {
            return false;
        }

        // Generate inspection number
        $inspectionNumber = $this->generateInspectionNumber();

        // Create inspection
        $inspectionData = [
            'inspection_number' => $inspectionNumber,
            'grn_item_id' => $grnItemId,
            'material_id' => $grnItem['material_id'],
            'inspector_id' => $inspectorId,
            'inspection_date' => date('Y-m-d H:i:s'),
            'inspection_type' => $inspectionType,
            'status' => self::STATUS_PENDING,
            'quantity_inspected' => $grnItem['quantity_delivered']
        ];

        $this->insert($inspectionData);
        return $db->insertID();
    }

    /**
     * Complete inspection
     *
     * @param int $id Inspection ID
     * @param array $results Inspection results
     * @return bool Success status
     */
    public function completeInspection($id, $results)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Update inspection
        $inspectionData = [
            'status' => $results['status'],
            'overall_grade' => $results['overall_grade'] ?? null,
            'quantity_passed' => $results['quantity_passed'],
            'quantity_failed' => $results['quantity_failed'],
            'defect_description' => $results['defect_description'] ?? null,
            'corrective_action' => $results['corrective_action'] ?? null,
            'inspector_notes' => $results['inspector_notes'] ?? null,
            'completed_at' => date('Y-m-d H:i:s')
        ];

        if (!$this->update($id, $inspectionData)) {
            $db->transRollback();
            return false;
        }

        // Update corresponding GRN item
        $inspection = $this->find($id);
        if ($inspection) {
            $grnItemModel = new GoodsReceiptItemModel();
            $grnItemModel->updateQualityInspection(
                $inspection['grn_item_id'],
                $results['status'],
                $results['quantity_passed'],
                $results['quantity_failed'],
                $results['defect_description'] ?? null,
                $results['inspector_notes'] ?? null
            );
        }

        $db->transComplete();
        return $db->transStatus() !== false;
    }

    /**
     * Get pending inspections
     *
     * @return array List of pending inspections
     */
    public function getPendingInspections()
    {
        return $this->getInspectionsWithDetails(['status' => self::STATUS_PENDING]);
    }

    /**
     * Get inspections by inspector
     *
     * @param int $inspectorId Inspector user ID
     * @return array List of inspections
     */
    public function getInspectionsByInspector($inspectorId)
    {
        return $this->getInspectionsWithDetails(['inspector_id' => $inspectorId]);
    }

    /**
     * Get summary statistics for quality inspections based on filters
     *
     * @param array $filters Optional filters for date range, supplier, project
     * @return array Summary statistics
     */
    public function getSummaryStats($filters = [])
    {
        $builder = $this->db->table($this->table);

        $builder->select('
            COUNT(*) as total,
            SUM(CASE WHEN status = "passed" THEN 1 ELSE 0 END) as passed,
            SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN status = "pending" OR status = "in_progress" THEN 1 ELSE 0 END) as pending,
            SUM(quantity_passed) as total_quantity_passed,
            SUM(quantity_failed) as total_quantity_failed'
        );

        // Join tables for filtering
        $builder->join('goods_receipt_items', 'goods_receipt_items.id = quality_inspections.grn_item_id', 'left')
                ->join('goods_receipt_notes', 'goods_receipt_notes.id = goods_receipt_items.grn_id', 'left');

        // Apply date filters if provided
        if (!empty($filters['date_from'])) {
            $builder->where('quality_inspections.inspection_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('quality_inspections.inspection_date <=', $filters['date_to']);
        }

        // Apply supplier filter if provided
        if (!empty($filters['supplier_id'])) {
            $builder->where('goods_receipt_notes.supplier_id', $filters['supplier_id']);
        }

        // Apply project filter if provided
        if (!empty($filters['project_id'])) {
            $builder->join('purchase_orders', 'purchase_orders.id = goods_receipt_notes.purchase_order_id', 'left')
                   ->where('purchase_orders.project_id', $filters['project_id']);
        }

        $result = $builder->get()->getRowArray();

        return [
            'total' => (int)($result['total'] ?? 0),
            'passed' => (int)($result['passed'] ?? 0),
            'failed' => (int)($result['failed'] ?? 0),
            'pending' => (int)($result['pending'] ?? 0),
            'total_quantity_passed' => (float)($result['total_quantity_passed'] ?? 0),
            'total_quantity_failed' => (float)($result['total_quantity_failed'] ?? 0)
        ];
    }
}
