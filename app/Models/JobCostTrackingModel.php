<?php

namespace App\Models;

use CodeIgniter\Model;

class JobCostTrackingModel extends Model
{
    protected $table = 'job_cost_lines';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id',
        'project_id',
        'cost_code_id',
        'description',
        'cost_date',
        'quantity',
        'unit_cost',
        'total_cost',
        'vendor_supplier',
        'reference_number',
        'cost_category',
        'is_billable',
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
        'cost_code_id' => 'required|integer',
        'description' => 'required|max_length[255]',
        'cost_date' => 'required|valid_date',
        'quantity' => 'required|decimal',
        'unit_cost' => 'required|decimal',
        'total_cost' => 'permit_empty|decimal',
        'vendor_supplier' => 'permit_empty|max_length[255]',
        'reference_number' => 'permit_empty|max_length[100]',
        'cost_category' => 'required|in_list[actual,estimated,budgeted]',
        'is_billable' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'project_id' => [
            'required' => 'Project is required',
            'integer' => 'Invalid project selected'
        ],
        'cost_code_id' => [
            'required' => 'Cost code is required',
            'integer' => 'Invalid cost code selected'
        ],
        'description' => [
            'required' => 'Description is required',
            'max_length' => 'Description cannot exceed 255 characters'
        ],
        'cost_date' => [
            'required' => 'Cost date is required',
            'valid_date' => 'Please enter a valid date'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'decimal' => 'Quantity must be a valid number'
        ],
        'unit_cost' => [
            'required' => 'Unit cost is required',
            'decimal' => 'Unit cost must be a valid number'
        ],
        'total_cost' => [
            'required' => 'Total cost is required',
            'decimal' => 'Total cost must be a valid number'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['addCompanyIdAndUser', 'calculateTotalCost'];
    protected $beforeUpdate = ['calculateTotalCost'];

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
        return $data;
    }

    /**
     * Calculate total cost from quantity and unit cost
     */
    protected function calculateTotalCost(array $data)
    {
        if (isset($data['data']['quantity']) && isset($data['data']['unit_cost'])) {
            $data['data']['total_cost'] = $data['data']['quantity'] * $data['data']['unit_cost'];
        }
        return $data;
    }

    /**
     * Get job costs with related data
     */
    public function getJobCostsWithDetails($filters = [])
    {
        $builder = $this->db->table($this->table . ' jcl');
        
        $builder->select("
            jcl.*,
            p.name as project_name,
            p.project_code as project_number,
            cc.code as cost_code,
            cc.name as cost_code_name,
            cc.category as cost_category_type,
            u.username as created_by_name
        ", false);
        
        $builder->join('projects p', 'jcl.project_id = p.id', 'left');
        $builder->join('cost_codes cc', 'jcl.cost_code_id = cc.id', 'left');
        $builder->join('users u', 'jcl.created_by = u.id', 'left');
        
        $builder->where('jcl.company_id', session('company_id') ?? 1);
        
        // Apply filters
        if (!empty($filters['project_id'])) {
            $builder->where('jcl.project_id', $filters['project_id']);
        }
        
        if (!empty($filters['cost_code_id'])) {
            $builder->where('jcl.cost_code_id', $filters['cost_code_id']);
        }
        
        if (!empty($filters['cost_category'])) {
            $builder->where('jcl.cost_category', $filters['cost_category']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('jcl.cost_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('jcl.cost_date <=', $filters['date_to']);
        }
        
        if (!empty($filters['search'])) {
            $builder->groupStart()
                   ->like('jcl.description', $filters['search'])
                   ->orLike('jcl.vendor_supplier', $filters['search'])
                   ->orLike('jcl.reference_number', $filters['search'])
                   ->orLike('p.project_name', $filters['search'])
                   ->orLike('cc.name', $filters['search'])
                   ->groupEnd();
        }
        
        $builder->orderBy('jcl.cost_date', 'DESC');
        $builder->orderBy('jcl.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get project cost summary
     */
    public function getProjectCostSummary($projectId)
    {
        $builder = $this->db->table($this->table . ' jcl');
        
        $builder->select("
            cc.category,
            cc.name as cost_code_name,
            COUNT(*) as line_count,
            SUM(jcl.total_cost) as total_cost,
            AVG(jcl.unit_cost) as avg_unit_cost,
            SUM(jcl.quantity) as total_quantity
        ", false);
        
        $builder->join('cost_codes cc', 'jcl.cost_code_id = cc.id', 'left');
        $builder->where('jcl.project_id', $projectId);
        $builder->where('jcl.company_id', session('company_id') ?? 1);
        $builder->groupBy(['cc.category', 'cc.id']);
        $builder->orderBy('cc.category', 'ASC');
        $builder->orderBy('total_cost', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get cost tracking statistics
     */
    public function getCostTrackingStats($filters = [])
    {
        $builder = $this->db->table($this->table);
        $builder->where('company_id', session('company_id') ?? 1);
        
        // Apply date filters if provided
        if (!empty($filters['date_from'])) {
            $builder->where('cost_date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('cost_date <=', $filters['date_to']);
        }
        
        $stats = [
            'total_entries' => $builder->countAllResults(false),
            'total_cost' => $builder->selectSum('total_cost')->get()->getRow()->total_cost ?? 0,
            'avg_cost' => $builder->selectAvg('total_cost')->get()->getRow()->total_cost ?? 0,
        ];
        
        // Get cost by category
        $builder = $this->db->table($this->table . ' jcl');
        $builder->select("
            cc.category,
            COUNT(*) as entry_count,
            SUM(jcl.total_cost) as category_total
        ", false);
        $builder->join('cost_codes cc', 'jcl.cost_code_id = cc.id', 'left');
        $builder->where('jcl.company_id', session('company_id') ?? 1);
        
        if (!empty($filters['date_from'])) {
            $builder->where('jcl.cost_date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('jcl.cost_date <=', $filters['date_to']);
        }
        
        $builder->groupBy('cc.category');
        $stats['by_category'] = $builder->get()->getResultArray();
        
        return $stats;
    }

    /**
     * Get active projects for dropdown
     */
    public function getActiveProjects()
    {
        $builder = $this->db->table('projects');
        return $builder->select('id, name as project_name, project_code as project_number')
                      ->where('company_id', session('company_id') ?? 1)
                      ->where('status !=', 'completed')
                      ->orderBy('name', 'ASC')
                      ->get()
                      ->getResultArray();
    }

    /**
     * Get cost categories
     */
    public static function getCostCategories()
    {
        return [
            'actual' => 'Actual Cost',
            'estimated' => 'Estimated Cost',
            'budgeted' => 'Budgeted Cost'
        ];
    }
}