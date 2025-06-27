<?php

namespace App\Models;

use CodeIgniter\Model;

class MilestoneModel extends Model
{
    protected $table = 'tasks'; // Using tasks table with task_type = 'milestone'
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    // Updated to include all the new fields from the form
    protected $allowedFields = [
        'project_id', 'parent_task_id', 'category_id', 'task_code', 'title', 'description', 
        'task_type', 'priority', 'status', 'progress_percentage', 'assigned_to', 'assigned_by',
        'planned_start_date', 'planned_end_date', 'actual_start_date', 'actual_end_date',
        'estimated_hours', 'actual_hours', 'estimated_cost', 'actual_cost', 'depends_on',
        'is_critical_path', 'requires_approval', 'is_billable', 'created_by',
        // Additional milestone-specific fields (these might need to be added to the tasks table)
        'milestone_type', 'is_critical', 'deliverables', 'success_criteria', 
        'budget_variance', 'risk_level', 'risk_description', 'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'project_id' => 'required|numeric',
        'title' => 'required|min_length[3]|max_length[255]',
        'planned_end_date' => 'required|valid_date',
        'task_type' => 'required|in_list[milestone,task,subtask]',
        'priority' => 'required|in_list[low,medium,high,critical,urgent]',
        'status' => 'required|in_list[not_started,pending,in_progress,review,completed,cancelled,on_hold]'
    ];

    protected $validationMessages = [
        'project_id' => [
            'required' => 'Project is required',
            'numeric' => 'Invalid project selected'
        ],
        'title' => [
            'required' => 'Milestone title is required',
            'min_length' => 'Milestone title must be at least 3 characters',
            'max_length' => 'Milestone title cannot exceed 255 characters'
        ],
        'planned_end_date' => [
            'required' => 'Due date is required',
            'valid_date' => 'Please provide a valid due date'
        ]
    ];

    // Remove the constructor that automatically filters by task_type
    // We'll handle this in individual methods instead
    public function __construct()
    {
        parent::__construct();
    }

    // Override insert to always set task_type to milestone
    public function insert($data = null, $returnID = true)
    {
        if (is_array($data)) {
            $data['task_type'] = 'milestone';
        }
        return parent::insert($data, $returnID);
    }

    // Override update to ensure we're only updating milestones
    public function update($id = null, $data = null): bool
    {
        // Verify we're updating a milestone
        $existing = $this->where('id', $id)->where('task_type', 'milestone')->first();
        if (!$existing) {
            return false;
        }
        
        return parent::update($id, $data);
    }

    // Override find to ensure we only get milestones
    public function find($id = null)
    {
        if ($id !== null) {
            return $this->where('task_type', 'milestone')->where('id', $id)->first();
        }
        return $this->where('task_type', 'milestone')->findAll();
    }

    // Override delete to ensure we only delete milestones
    public function delete($id = null, $purge = false)
    {
        $existing = $this->where('id', $id)->where('task_type', 'milestone')->first();
        if (!$existing) {
            return false;
        }
        
        return parent::delete($id, $purge);
    }

    public function getProjectMilestones($projectId)
    {
        return $this->where('project_id', $projectId)
                   ->where('task_type', 'milestone')
                   ->orderBy('planned_end_date', 'ASC')
                   ->findAll();
    }

    public function getUpcomingMilestones($projectId = null, $days = 30)
    {
        $endDate = date('Y-m-d', strtotime('+' . $days . ' days'));
        
        $builder = $this->where('task_type', 'milestone')
                       ->where('planned_end_date <=', $endDate)
                       ->where('planned_end_date >=', date('Y-m-d'))
                       ->whereNotIn('status', ['completed', 'cancelled']);

        if ($projectId) {
            $builder->where('project_id', $projectId);
        }

        return $builder->orderBy('planned_end_date', 'ASC')->findAll();
    }

    public function getOverdueMilestones($projectId = null)
    {
        $builder = $this->select('tasks.*, projects.name as project_name, projects.project_code')
                       ->join('projects', 'tasks.project_id = projects.id')
                       ->where('tasks.task_type', 'milestone')
                       ->where('tasks.planned_end_date <', date('Y-m-d'))
                       ->whereNotIn('tasks.status', ['completed', 'cancelled']);

        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }

        return $builder->orderBy('tasks.planned_end_date', 'ASC')->findAll();
    }

    public function createMilestone($data)
    {
        $data['task_type'] = 'milestone';
        $data['created_by'] = $data['created_by'] ?? session('user_id');
        
        // Generate milestone code if not provided
        if (!isset($data['task_code']) || empty($data['task_code'])) {
            $data['task_code'] = $this->generateMilestoneCode($data['project_id']);
        }

        return $this->insert($data, true);
    }

    public function updateMilestone($milestoneId, $data)
    {
        return $this->update($milestoneId, $data);
    }

    public function completeMilestone($milestoneId)
    {
        $data = [
            'status' => 'completed',
            'progress_percentage' => 100,
            'actual_end_date' => date('Y-m-d')
        ];

        return $this->update($milestoneId, $data);
    }

    public function getMilestoneProgress($projectId)
    {
        $total = $this->where('project_id', $projectId)
                     ->where('task_type', 'milestone')
                     ->countAllResults();
                     
        $completed = $this->where('project_id', $projectId)
                         ->where('task_type', 'milestone')
                         ->where('status', 'completed')
                         ->countAllResults();

        return [
            'total' => $total,
            'completed' => $completed,
            'completion_rate' => $total > 0 ? ($completed / $total) * 100 : 0
        ];
    }

    public function getMilestonesByStatus($projectId, $status)
    {
        return $this->where('project_id', $projectId)
                   ->where('task_type', 'milestone')
                   ->where('status', $status)
                   ->orderBy('planned_end_date', 'ASC')
                   ->findAll();
    }

    private function generateMilestoneCode($projectId)
    {
        // Get project details
        $projectModel = new \App\Models\ProjectModel();
        $project = $projectModel->find($projectId);
        $projectCode = $project['project_code'] ?? 'PROJ';

        // Find the last milestone for this project
        $lastMilestone = $this->where('project_id', $projectId)
                             ->where('task_type', 'milestone')
                             ->where('task_code LIKE', $projectCode . '-MS-%')
                             ->orderBy('id', 'DESC')
                             ->first();

        if ($lastMilestone && isset($lastMilestone['task_code'])) {
            // Extract the number from the last milestone code
            $parts = explode('-', $lastMilestone['task_code']);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $projectCode . '-MS-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function getMilestonesWithTasks($projectId)
    {
        $milestones = $this->getProjectMilestones($projectId);
        
        foreach ($milestones as &$milestone) {
            // Get related tasks for each milestone
            $taskModel = new \App\Models\TaskModel();
            $tasks = $taskModel->where('project_id', $projectId)
                              ->where('planned_end_date <=', $milestone['planned_end_date'])
                              ->where('task_type !=', 'milestone')
                              ->findAll();
            
            $milestone['related_tasks'] = $tasks;
            $milestone['task_count'] = count($tasks);
            $milestone['completed_tasks'] = count(array_filter($tasks, function($task) {
                return $task['status'] === 'completed';
            }));
        }

        return $milestones;
    }

    public function getCriticalMilestones($projectId)
    {
        return $this->where('project_id', $projectId)
                   ->where('task_type', 'milestone')
                   ->where('priority', 'critical')
                   ->orderBy('planned_end_date', 'ASC')
                   ->findAll();
    }

    // Get milestones by company (for multi-tenant support)
    public function getCompanyMilestones($companyId, $filters = [])
    {
        $builder = $this->select('tasks.*, projects.name as project_name, projects.project_code')
                       ->join('projects', 'tasks.project_id = projects.id')
                       ->where('projects.company_id', $companyId)
                       ->where('tasks.task_type', 'milestone');

        // Apply filters
        if (isset($filters['project_id']) && $filters['project_id']) {
            $builder->where('tasks.project_id', $filters['project_id']);
        }

        if (isset($filters['status']) && $filters['status']) {
            $builder->where('tasks.status', $filters['status']);
        }

        if (isset($filters['priority']) && $filters['priority']) {
            $builder->where('tasks.priority', $filters['priority']);
        }

        if (isset($filters['date_from']) && $filters['date_from']) {
            $builder->where('tasks.planned_end_date >=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $builder->where('tasks.planned_end_date <=', $filters['date_to']);
        }

        return $builder->orderBy('tasks.planned_end_date', 'ASC')->findAll();
    }

    // Validate milestone data before saving
    public function validateMilestoneData($data, $id = null)
    {
        $rules = $this->validationRules;
        
        // If updating, make project_id optional
        if ($id) {
            unset($rules['project_id']);
        }

        // Validate dates
        if (isset($data['planned_start_date']) && isset($data['planned_end_date'])) {
            if (strtotime($data['planned_start_date']) > strtotime($data['planned_end_date'])) {
                return ['date_validation' => 'Start date cannot be after end date'];
            }
        }

        // Validate project exists and belongs to user's company
        if (isset($data['project_id'])) {
            $projectModel = new \App\Models\ProjectModel();
            $project = $projectModel->where('id', $data['project_id'])
                                  ->where('company_id', session('company_id'))
                                  ->first();
            if (!$project) {
                return ['project_validation' => 'Invalid project selected'];
            }
        }

        return [];
    }
}