<?php

namespace App\Models;

use CodeIgniter\Model;

class JobBudgetModel extends Model
{
    protected $table = 'budgets';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id',
        'project_id',
        'name',
        'description',
        'budget_period',
        'start_date',
        'end_date',
        'total_budget',
        'allocated_budget',
        'spent_amount',
        'remaining_budget',
        'status',
        'created_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'company_id' => 'permit_empty|integer',
        'project_id' => 'required|integer',
        'name' => 'required|max_length[255]',
        'description' => 'permit_empty|max_length[1000]',
        'budget_period' => 'required|in_list[monthly,quarterly,yearly,project]',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date',
        'total_budget' => 'required|decimal|greater_than[0]',
        'status' => 'permit_empty|in_list[draft,active,completed,cancelled]'
    ];

    protected $validationMessages = [
        'project_id' => [
            'required' => 'Project is required',
            'integer' => 'Invalid project selected'
        ],
        'name' => [
            'required' => 'Budget name is required',
            'max_length' => 'Budget name cannot exceed 255 characters'
        ],
        'budget_period' => [
            'required' => 'Budget period is required',
            'in_list' => 'Invalid budget period selected'
        ],
        'start_date' => [
            'required' => 'Start date is required',
            'valid_date' => 'Please enter a valid start date'
        ],
        'end_date' => [
            'required' => 'End date is required',
            'valid_date' => 'Please enter a valid end date'
        ],
        'total_budget' => [
            'required' => 'Total budget is required',
            'decimal' => 'Total budget must be a valid number',
            'greater_than' => 'Total budget must be greater than 0'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['addCompanyIdAndUser', 'calculateRemainingBudget'];
    protected $beforeUpdate = ['calculateRemainingBudget'];
    protected $afterInsert = ['createBudgetLineItems'];

    /**
     * Add company_id and created_by if not provided
     */
    protected function addCompanyIdAndUser(array $data)
    {
        if (!isset($data['data']['company_id'])) {
            $data['data']['company_id'] = session('company_id') ?? 1;
        }
        if (!isset($data['data']['created_by'])) {
            $data['data']['created_by'] = session('user_id') ?? 1;
        }
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'draft';
        }
        if (!isset($data['data']['spent_amount'])) {
            $data['data']['spent_amount'] = 0;
        }
        if (!isset($data['data']['allocated_budget'])) {
            $data['data']['allocated_budget'] = 0;
        }
        return $data;
    }

    /**
     * Calculate remaining budget
     */
    protected function calculateRemainingBudget(array $data)
    {
        if (isset($data['data']['total_budget'])) {
            $spent = $data['data']['spent_amount'] ?? 0;
            $data['data']['remaining_budget'] = $data['data']['total_budget'] - $spent;
        }
        return $data;
    }

    /**
     * Create default budget line items for major cost categories
     */
    protected function createBudgetLineItems(array $data)
    {
        if (isset($data['id']) && isset($data['data']['line_items'])) {
            $lineItemModel = new \App\Models\BudgetLineItemModel();
            $budgetId = $data['id'];

            foreach ($data['data']['line_items'] as $item) {
                $lineItemModel->insert([
                    'budget_id' => $budgetId,
                    'category_id' => $item['category_id'],
                    'cost_code_id' => $item['cost_code_id'] ?? null,
                    'description' => $item['description'],
                    'budgeted_amount' => $item['budgeted_amount'],
                    'actual_amount' => 0,
                    'variance' => -$item['budgeted_amount']
                ]);
            }
        }
        return $data;
    }

    /**
     * Get budgets with project details
     */
    public function getBudgetsWithDetails($filters = [])
    {
        $builder = $this->db->table($this->table . ' b');

        $builder->select("
            b.*,
            p.name as project_name,
            p.project_code,
            p.estimated_budget as project_estimated_budget,
            p.actual_cost as project_actual_cost,
            u.username as created_by_name,
            (SELECT COUNT(*) FROM budget_line_items WHERE budget_id = b.id) as line_item_count
        ", false);

        $builder->join('projects p', 'b.project_id = p.id', 'left');
        $builder->join('users u', 'b.created_by = u.id', 'left');

        $builder->where('b.company_id', session('company_id') ?? 1);

        // Apply filters
        if (!empty($filters['id'])) {
            $builder->where('b.id', $filters['id']);
        }

        if (!empty($filters['project_id'])) {
            $builder->where('b.project_id', $filters['project_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('b.status', $filters['status']);
        }

        if (!empty($filters['budget_period'])) {
            $builder->where('b.budget_period', $filters['budget_period']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                   ->like('b.name', $filters['search'])
                   ->orLike('b.description', $filters['search'])
                   ->orLike('p.name', $filters['search'])
                   ->orLike('p.project_code', $filters['search'])
                   ->groupEnd();
        }

        $builder->orderBy('b.start_date', 'DESC');
        $builder->orderBy('b.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get budget details with line items
     */
    public function getBudgetWithLineItems($budgetId)
    {
        $budget = $this->getBudgetsWithDetails(['id' => $budgetId]);

        if (empty($budget)) {
            return null;
        }

        $budget = $budget[0];

        // Get line items
        $builder = $this->db->table('budget_line_items bli');
        $builder->select("
            bli.*,
            bc.name as category_name,
            bc.budget_type,
            cc.code as cost_code,
            cc.name as cost_code_name
        ", false);
        $builder->join('budget_categories bc', 'bli.category_id = bc.id', 'left');
        $builder->join('cost_codes cc', 'bli.cost_code_id = cc.id', 'left');
        $builder->where('bli.budget_id', $budgetId);
        $builder->orderBy('bc.budget_type', 'ASC');
        $builder->orderBy('bc.name', 'ASC');

        $budget['line_items'] = $builder->get()->getResultArray();

        return $budget;
    }

    /**
     * Get project budget summary with actual costs
     */
    public function getProjectBudgetSummary($projectId)
    {
        // Get active budgets for project
        $budgets = $this->where('project_id', $projectId)
                        ->where('company_id', session('company_id') ?? 1)
                        ->whereIn('status', ['active', 'completed'])
                        ->findAll();

        if (empty($budgets)) {
            return null;
        }

        // Get actual costs from job cost tracking
        $builder = $this->db->table('job_cost_lines jcl');
        $builder->select("
            cc.category,
            cc.id as cost_code_id,
            cc.name as cost_code_name,
            SUM(jcl.total_cost) as actual_cost,
            COUNT(*) as transaction_count
        ", false);
        $builder->join('cost_codes cc', 'jcl.cost_code_id = cc.id', 'left');
        $builder->where('jcl.project_id', $projectId);
        $builder->where('jcl.company_id', session('company_id') ?? 1);
        $builder->groupBy(['cc.category', 'cc.id']);

        $actualCosts = $builder->get()->getResultArray();

        return [
            'budgets' => $budgets,
            'actual_costs' => $actualCosts
        ];
    }

    /**
     * Update budget spent amount based on actual costs
     */
    public function updateBudgetSpentAmount($budgetId)
    {
        $budget = $this->find($budgetId);

        if (!$budget) {
            return false;
        }

        // Calculate total spent from job cost tracking
        $builder = $this->db->table('job_cost_lines');
        $builder->selectSum('total_cost', 'total_spent');
        $builder->where('project_id', $budget['project_id']);
        $builder->where('company_id', session('company_id') ?? 1);
        $builder->where('cost_date >=', $budget['start_date']);
        $builder->where('cost_date <=', $budget['end_date']);

        $result = $builder->get()->getRow();
        $totalSpent = $result->total_spent ?? 0;

        // Update budget
        return $this->update($budgetId, [
            'spent_amount' => $totalSpent,
            'remaining_budget' => $budget['total_budget'] - $totalSpent
        ]);
    }

    /**
     * Get budget statistics
     */
    public function getBudgetStats($filters = [])
    {
        $builder = $this->db->table($this->table);
        $builder->where('company_id', session('company_id') ?? 1);

        if (!empty($filters['project_id'])) {
            $builder->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        $stats = [
            'total_budgets' => $builder->countAllResults(false),
            'total_budgeted' => $builder->selectSum('total_budget')->get()->getRow()->total_budget ?? 0,
            'total_spent' => $builder->selectSum('spent_amount')->get()->getRow()->spent_amount ?? 0
        ];

        $stats['total_remaining'] = $stats['total_budgeted'] - $stats['total_spent'];
        $stats['utilization_percentage'] = $stats['total_budgeted'] > 0
            ? round(($stats['total_spent'] / $stats['total_budgeted']) * 100, 2)
            : 0;

        // Get budget status breakdown
        $builder = $this->db->table($this->table);
        $builder->select('status, COUNT(*) as count', false);
        $builder->where('company_id', session('company_id') ?? 1);
        if (!empty($filters['project_id'])) {
            $builder->where('project_id', $filters['project_id']);
        }
        $builder->groupBy('status');
        $stats['by_status'] = $builder->get()->getResultArray();

        return $stats;
    }

    /**
     * Get active projects for dropdown
     */
    public function getActiveProjects()
    {
        $builder = $this->db->table('projects');
        return $builder->select('id, name as project_name, project_code, estimated_budget')
                      ->where('company_id', session('company_id') ?? 1)
                      ->orderBy('name', 'ASC')
                      ->get()
                      ->getResultArray();
    }

    /**
     * Get budget categories
     */
    public function getBudgetCategories()
    {
        $builder = $this->db->table('budget_categories');
        return $builder->where('company_id', session('company_id') ?? 1)
                      ->orderBy('budget_type', 'ASC')
                      ->orderBy('name', 'ASC')
                      ->get()
                      ->getResultArray();
    }

    /**
     * Get budget periods
     */
    public static function getBudgetPeriods()
    {
        return [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            'project' => 'Project-based'
        ];
    }

    /**
     * Get budget statuses
     */
    public static function getBudgetStatuses()
    {
        return [
            'draft' => 'Draft',
            'active' => 'Active',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];
    }
}
