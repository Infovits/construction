<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id', 'client_id', 'category_id', 'project_code', 'name', 'description',
        'project_type', 'priority', 'status', 'progress_percentage', 'estimated_budget',
        'actual_cost', 'contract_value', 'currency', 'start_date', 'planned_end_date',
        'actual_end_date', 'site_address', 'site_city', 'site_state', 'site_coordinates',
        'project_manager_id', 'site_supervisor_id', 'is_template', 'is_archived',
        'requires_permit', 'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'company_id' => 'required|numeric',
        'name' => 'required|min_length[3]|max_length[255]',
        'project_code' => 'required|max_length[50]',
        'project_type' => 'required|in_list[residential,commercial,industrial,infrastructure,renovation]',
        'estimated_budget' => 'required|numeric|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Project name is required',
            'min_length' => 'Project name must be at least 3 characters long'
        ],
        'project_code' => [
            'required' => 'Project code is required'
        ],
        'estimated_budget' => [
            'required' => 'Estimated budget is required',
            'numeric' => 'Budget must be a valid number'
        ]
    ];

    protected $skipValidation = false;

    public function getProjectsWithDetailsQuery($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->select('projects.*, clients.name as client_name, project_categories.name as category_name,
                            CONCAT(pm.first_name, " ", pm.last_name) as project_manager_name,
                            CONCAT(ss.first_name, " ", ss.last_name) as site_supervisor_name')
            ->join('clients', 'projects.client_id = clients.id', 'left')
            ->join('project_categories', 'projects.category_id = project_categories.id', 'left')
            ->join('users pm', 'projects.project_manager_id = pm.id', 'left')
            ->join('users ss', 'projects.site_supervisor_id = ss.id', 'left')
            ->where('projects.company_id', $companyId)
            ->where('projects.is_archived', false)
            ->orderBy('projects.created_at', 'DESC');
    }

    public function getProjectsWithDetails($companyId = null)
    {
        return $this->getProjectsWithDetailsQuery($companyId)->findAll();
    }

    public function getRecentProjects($limit = 5, $companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->select('projects.*, clients.name as client_name')
            ->join('clients', 'projects.client_id = clients.id', 'left')
            ->where('projects.company_id', $companyId)
            ->where('projects.is_archived', false)
            ->orderBy('projects.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getActiveProjects($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->where('company_id', $companyId)
            ->where('status', 'active')
            ->where('is_archived', false)
            ->findAll();
    }

    public function getProjectStats($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        $total = $this->where('company_id', $companyId)->where('is_archived', false)->countAllResults();
        $active = $this->where('company_id', $companyId)->where('status', 'active')->countAllResults();
        $completed = $this->where('company_id', $companyId)->where('status', 'completed')->countAllResults();
        $planning = $this->where('company_id', $companyId)->where('status', 'planning')->countAllResults();
        $onHold = $this->where('company_id', $companyId)->where('status', 'on_hold')->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'completed' => $completed,
            'planning' => $planning,
            'on_hold' => $onHold
        ];
    }

    public function getProjectWithTeam($projectId)
    {
        $db = \Config\Database::connect();
        
        // Get project details
        $project = $this->getProjectsWithDetailsQuery()
                       ->where('projects.id', $projectId)
                       ->first();
        
        if (!$project) {
            return null;
        }
        
        // Get team members
        $teamQuery = $db->query("
            SELECT ptm.*, u.first_name, u.last_name, u.email
            FROM project_team_members ptm
            JOIN users u ON ptm.user_id = u.id
            WHERE ptm.project_id = ? AND ptm.removed_at IS NULL
            ORDER BY ptm.role, u.first_name
        ", [$projectId]);
        
        $project['team_members'] = $teamQuery->getResultArray();
        
        // Get project tasks summary
        $taskModel = new TaskModel();
        $project['task_summary'] = $taskModel->getTaskSummaryByProject($projectId);
        
        return $project;
    }

    public function getProjectTimeline($projectId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                t.id,
                t.title,
                t.description,
                t.task_type,
                t.status,
                t.priority,
                t.progress_percentage,
                t.planned_start_date,
                t.planned_end_date,
                t.actual_start_date,
                t.actual_end_date,
                t.depends_on,
                t.is_critical_path,
                COALESCE(t.planned_start_date, t.planned_end_date) as date,
                CONCAT(u.first_name, ' ', u.last_name) as assigned_name
            FROM tasks t
            LEFT JOIN users u ON t.assigned_to = u.id
            WHERE t.project_id = ?
            ORDER BY t.planned_start_date, t.id
        ", [$projectId]);
        
        return $query->getResultArray();
    }

    public function getBudgetTracking($projectId)
    {
        $project = $this->find($projectId);
        
        if (!$project) {
            return null;
        }
        
        $result = [
            'estimated_budget' => $project['estimated_budget'],
            'actual_cost' => $project['actual_cost'] ?? 0,
            'contract_value' => $project['contract_value'] ?? 0,
            'total_expenses' => $project['actual_cost'] ?? 0, // Using actual_cost as total expenses
            'total_revenue' => 0 // Set to 0 for now since we don't have journal entries
        ];
        
        // Calculate derived values
        $result['budget_variance'] = $result['actual_cost'] - $result['estimated_budget'];
        $result['budget_utilization'] = $result['estimated_budget'] > 0 
            ? ($result['actual_cost'] / $result['estimated_budget']) * 100 
            : 0;
        
        return $result;
    }

    public function getOverdueProjects($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');
        
        return $this->select('projects.*, clients.name as client_name')
            ->join('clients', 'projects.client_id = clients.id', 'left')
            ->where('projects.company_id', $companyId)
            ->where('projects.planned_end_date <', date('Y-m-d'))
            ->whereNotIn('projects.status', ['completed', 'cancelled'])
            ->where('projects.is_archived', false)
            ->orderBy('projects.planned_end_date', 'ASC')
            ->findAll();
    }

    public function updateProjectProgress($projectId)
    {
        $taskModel = new TaskModel();
        $tasks = $taskModel->where('project_id', $projectId)
                          ->whereNotIn('status', ['cancelled'])
                          ->findAll();
        
        if (empty($tasks)) {
            return $this->update($projectId, ['progress_percentage' => 0]);
        }
        
        $totalTasks = count($tasks);
        $completedTasks = count(array_filter($tasks, function($task) {
            return $task['status'] === 'completed';
        }));
        
        $progressPercentage = ($completedTasks / $totalTasks) * 100;
        
    }

    public function getCompanyStats($companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        $total = $this->where('company_id', $companyId)->where('is_archived', false)->countAllResults();
        $active = $this->where('company_id', $companyId)->where('status', 'active')->countAllResults();
        $completed = $this->where('company_id', $companyId)->where('status', 'completed')->countAllResults();
        $planning = $this->where('company_id', $companyId)->where('status', 'planning')->countAllResults();
        $onHold = $this->where('company_id', $companyId)->where('status', 'on_hold')->countAllResults();

        // Calculate total budget
        $totalBudget = $this->selectSum('estimated_budget')
            ->where('company_id', $companyId)
            ->where('is_archived', false)
            ->get()
            ->getRowArray()['estimated_budget'] ?? 0;

        // Calculate actual cost
        $actualCost = $this->selectSum('actual_cost')
            ->where('company_id', $companyId)
            ->where('is_archived', false)
            ->get()
            ->getRowArray()['actual_cost'] ?? 0;

        return [
            'total' => $total,
            'active' => $active,
            'completed' => $completed,
            'planning' => $planning,
            'on_hold' => $onHold,
            'total_budget' => $totalBudget,
            'actual_cost' => $actualCost,
            'budget_utilization' => $totalBudget > 0 ? ($actualCost / $totalBudget * 100) : 0
        ];
    }

    public function generateProjectCode($prefix = 'PROJ')
    {
        $year = date('Y');
        $lastProject = $this->where('company_id', session('company_id'))
            ->where('project_code LIKE', $prefix . '-' . $year . '-%')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastProject) {
            $lastNumber = (int) substr($lastProject['project_code'], -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . $year . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function getProjectsByManager($managerId, $companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->select('projects.*, clients.name as client_name')
            ->join('clients', 'projects.client_id = clients.id', 'left')
            ->where('projects.company_id', $companyId)
            ->where('projects.project_manager_id', $managerId)
            ->where('projects.is_archived', false)
            ->orderBy('projects.created_at', 'DESC')
            ->findAll();
    }

    public function updateProgress($projectId, $percentage)
    {
        return $this->update($projectId, [
            'progress_percentage' => $percentage,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function addTeamMember($projectId, $userId, $role, $assignedBy = null)
    {
        $teamData = [
            'project_id' => $projectId,
            'user_id' => $userId,
            'role' => $role,
            'assigned_by' => $assignedBy ?: session('user_id'),
            'assigned_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->table('project_team_members')->insert($teamData);
    }

    public function removeTeamMember($projectId, $userId)
    {
        return $this->db->table('project_team_members')
            ->where('project_id', $projectId)
            ->where('user_id', $userId)
            ->update([
                'removed_at' => date('Y-m-d H:i:s')
            ]);
    }

    public function getTeamMembers($projectId)
    {
        return $this->db->table('project_team_members ptm')
            ->select('ptm.*, users.first_name, users.last_name, users.email, users.phone')
            ->join('users', 'ptm.user_id = users.id')
            ->where('ptm.project_id', $projectId)
            ->where('ptm.removed_at IS NULL')
            ->get()
            ->getResultArray();
    }

    public function searchProjects($keyword, $companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $this->select('projects.*, clients.name as client_name')
            ->join('clients', 'projects.client_id = clients.id', 'left')
            ->where('projects.company_id', $companyId)
            ->where('projects.is_archived', false)
            ->groupStart()
            ->like('projects.name', $keyword)
            ->orLike('projects.project_code', $keyword)
            ->orLike('projects.description', $keyword)
            ->orLike('clients.name', $keyword)
            ->groupEnd()
            ->orderBy('projects.created_at', 'DESC')
            ->findAll();
    }

    public function getProjectFinancialSummary($projectId)
    {
        $project = $this->find($projectId);

        if (!$project) {
            return null;
        }

        // Get total expenses (from various tables - simplified for now)
        $totalExpenses = $project['actual_cost'];

        // Calculate remaining budget
        $remainingBudget = $project['estimated_budget'] - $totalExpenses;

        // Calculate budget variance
        $budgetVariance = $project['estimated_budget'] - $totalExpenses;
        $budgetVariancePercent = $project['estimated_budget'] > 0 ?
            ($budgetVariance / $project['estimated_budget'] * 100) : 0;

        return [
            'estimated_budget' => $project['estimated_budget'],
            'actual_cost' => $totalExpenses,
            'remaining_budget' => $remainingBudget,
            'budget_variance' => $budgetVariance,
            'budget_variance_percent' => $budgetVariancePercent,
            'budget_utilization_percent' => $project['estimated_budget'] > 0 ?
                ($totalExpenses / $project['estimated_budget'] * 100) : 0
        ];
    }
}