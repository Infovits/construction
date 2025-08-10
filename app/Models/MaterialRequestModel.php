<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialRequestModel extends Model
{
    protected $table = 'material_requests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'request_number', 'project_id', 'requested_by', 'department_id',
        'request_date', 'required_date', 'status', 'priority',
        'total_estimated_cost', 'approved_by', 'approved_date',
        'rejection_reason', 'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PARTIALLY_FULFILLED = 'partially_fulfilled';
    const STATUS_COMPLETED = 'completed';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Get material requests with related information
     *
     * @param array $filters Optional filters
     * @return array List of material requests
     */
    public function getMaterialRequestsWithDetails($filters = [])
    {
        $builder = $this->select('material_requests.*, 
                projects.name as project_name,
                departments.name as department_name,
                requester.first_name as requester_first_name,
                requester.last_name as requester_last_name,
                approver.first_name as approver_first_name,
                approver.last_name as approver_last_name')
            ->join('projects', 'projects.id = material_requests.project_id', 'left')
            ->join('departments', 'departments.id = material_requests.department_id', 'left')
            ->join('users as requester', 'requester.id = material_requests.requested_by', 'left')
            ->join('users as approver', 'approver.id = material_requests.approved_by', 'left');

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('material_requests.status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $builder->where('material_requests.priority', $filters['priority']);
        }

        if (!empty($filters['project_id'])) {
            $builder->where('material_requests.project_id', $filters['project_id']);
        }

        if (!empty($filters['requested_by'])) {
            $builder->where('material_requests.requested_by', $filters['requested_by']);
        }

        return $builder->orderBy('material_requests.created_at', 'DESC')->findAll();
    }

    /**
     * Get material request with items
     *
     * @param int $id Material request ID
     * @return array|null Material request with items
     */
    public function getMaterialRequestWithItems($id)
    {
        $request = $this->select('material_requests.*, 
                projects.name as project_name,
                departments.name as department_name,
                requester.first_name as requester_first_name,
                requester.last_name as requester_last_name,
                approver.first_name as approver_first_name,
                approver.last_name as approver_last_name')
            ->join('projects', 'projects.id = material_requests.project_id', 'left')
            ->join('departments', 'departments.id = material_requests.department_id', 'left')
            ->join('users as requester', 'requester.id = material_requests.requested_by', 'left')
            ->join('users as approver', 'approver.id = material_requests.approved_by', 'left')
            ->find($id);

        if (!$request) {
            return null;
        }

        // Get request items
        $db = \Config\Database::connect();
        $items = $db->table('material_request_items')
            ->select('material_request_items.*, 
                materials.name as material_name,
                materials.item_code,
                materials.unit,
                materials.unit_cost as current_unit_cost')
            ->join('materials', 'materials.id = material_request_items.material_id', 'left')
            ->where('material_request_id', $id)
            ->get()
            ->getResultArray();

        $request['items'] = $items;

        return $request;
    }

    /**
     * Generate next request number
     *
     * @return string Next request number
     */
    public function generateRequestNumber()
    {
        $lastRequest = $this->orderBy('id', 'DESC')->first();
        $prefix = 'MR-' . date('Ymd') . '-';

        if (!$lastRequest) {
            return $prefix . '0001';
        }

        // Extract sequence number from last request
        $lastRequestNumber = $lastRequest['request_number'];
        $lastSequence = 0;

        if (preg_match('/MR-\d{8}-(\d+)/', $lastRequestNumber, $matches)) {
            $lastSequence = (int) $matches[1];
        }

        // Increment sequence
        $nextSequence = $lastSequence + 1;

        // Format with leading zeros
        return $prefix . sprintf('%04d', $nextSequence);
    }

    /**
     * Approve material request
     *
     * @param int $id Material request ID
     * @param int $approvedBy User ID who approved
     * @param string $notes Optional approval notes
     * @return bool Success status
     */
    public function approveMaterialRequest($id, $approvedBy, $notes = null)
    {
        $data = [
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approvedBy,
            'approved_date' => date('Y-m-d H:i:s')
        ];

        if ($notes) {
            $data['notes'] = $notes;
        }

        return $this->update($id, $data);
    }

    /**
     * Reject material request
     *
     * @param int $id Material request ID
     * @param int $rejectedBy User ID who rejected
     * @param string $reason Rejection reason
     * @return bool Success status
     */
    public function rejectMaterialRequest($id, $rejectedBy, $reason)
    {
        $data = [
            'status' => self::STATUS_REJECTED,
            'approved_by' => $rejectedBy,
            'approved_date' => date('Y-m-d H:i:s'),
            'rejection_reason' => $reason
        ];

        return $this->update($id, $data);
    }

    /**
     * Get pending approval requests
     *
     * @return array List of pending requests
     */
    public function getPendingApprovalRequests()
    {
        return $this->getMaterialRequestsWithDetails(['status' => self::STATUS_PENDING_APPROVAL]);
    }

    /**
     * Get urgent requests
     *
     * @return array List of urgent requests
     */
    public function getUrgentRequests()
    {
        return $this->getMaterialRequestsWithDetails(['priority' => self::PRIORITY_URGENT]);
    }

    /**
     * Get approved requests available for purchase orders
     *
     * @return array List of approved requests
     */
    public function getApprovedRequests()
    {
        return $this->select('material_requests.*, 
                projects.name as project_name')
            ->join('projects', 'projects.id = material_requests.project_id', 'left')
            ->where('material_requests.status', self::STATUS_APPROVED)
            ->orderBy('material_requests.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get summary statistics for material requests based on filters
     *
     * @param array $filters Optional filters for date range, supplier, project
     * @return array Summary statistics
     */
    public function getSummaryStats($filters = [])
    {
        $builder = $this->db->table($this->table);

        // Apply date filters if provided
        if (!empty($filters['date_from'])) {
            $builder->where('request_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('request_date <=', $filters['date_to']);
        }

        // Apply project filter if provided
        if (!empty($filters['project_id'])) {
            $builder->where('project_id', $filters['project_id']);
        }

        // Get total count
        $total = $builder->countAllResults(false);

        // Get pending count
        $pending = (clone $builder)->where('status', 'pending_approval')->countAllResults();

        // Get approved count
        $approved = (clone $builder)->where('status', 'approved')->countAllResults();

        // Get rejected count
        $rejected = (clone $builder)->where('status', 'rejected')->countAllResults();

        // Get completed count
        $completed = (clone $builder)->where('status', 'completed')->countAllResults();

        // Get urgent priority count
        $urgent = (clone $builder)->where('priority', 'urgent')->countAllResults();

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'completed' => $completed,
            'urgent' => $urgent
        ];
    }
}
