<?php

namespace App\Models;

use CodeIgniter\Model;

class CostCodeModel extends Model
{
    protected $table = 'cost_codes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id',
        'code',
        'name',
        'description',
        'category',
        'cost_type',
        'unit_of_measure',
        'standard_rate',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'company_id' => 'required|integer',
        'code' => 'required|max_length[50]',
        'name' => 'required|max_length[255]',
        'description' => 'permit_empty|max_length[1000]',
        'category' => 'required|in_list[labor,material,equipment,subcontractor,overhead,other]',
        'cost_type' => 'required|in_list[direct,indirect]',
        'unit_of_measure' => 'permit_empty|max_length[50]',
        'standard_rate' => 'permit_empty|decimal',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'code' => [
            'required' => 'Cost code is required',
            'max_length' => 'Cost code cannot exceed 50 characters'
        ],
        'name' => [
            'required' => 'Cost code name is required',
            'max_length' => 'Name cannot exceed 255 characters'
        ],
        'category' => [
            'required' => 'Category is required',
            'in_list' => 'Category must be one of: labor, material, equipment, subcontractor, overhead, other'
        ],
        'cost_type' => [
            'required' => 'Cost type is required',
            'in_list' => 'Cost type must be either direct or indirect'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['addCompanyId'];
    protected $beforeUpdate = [];

    /**
     * Add company_id if not provided
     */
    protected function addCompanyId(array $data)
    {
        if (!isset($data['data']['company_id'])) {
            $data['data']['company_id'] = session('company_id') ?? 1;
        }
        return $data;
    }

    /**
     * Get cost codes with usage statistics
     */
    public function getCostCodesWithStats($filters = [])
    {
        try {
            $builder = $this->db->table($this->table . ' cc');
            
            $builder->select("
                cc.*,
                0 as usage_count,
                0 as total_cost_tracked
            ", false);
            
            // Note: Removed job_cost_lines join since that table doesn't exist yet
            // $builder->join('job_cost_lines jcl', 'cc.id = jcl.cost_code_id', 'left');
            $builder->where('cc.company_id', session('company_id') ?? 1);
        
        // Apply filters
        if (!empty($filters['category'])) {
            $builder->where('cc.category', $filters['category']);
        }
        
        if (!empty($filters['cost_type'])) {
            $builder->where('cc.cost_type', $filters['cost_type']);
        }
        
        if (!empty($filters['is_active'])) {
            $builder->where('cc.is_active', $filters['is_active']);
        }
        
        if (!empty($filters['search'])) {
            $builder->groupStart()
                   ->like('cc.code', $filters['search'])
                   ->orLike('cc.name', $filters['search'])
                   ->orLike('cc.description', $filters['search'])
                   ->groupEnd();
        }
        
            $builder->orderBy('cc.category', 'ASC');
            $builder->orderBy('cc.code', 'ASC');
            
            return $builder->get()->getResultArray();
            
        } catch (\Exception $e) {
            // If table doesn't exist, return empty array
            return [];
        }
    }

    /**
     * Get cost codes by category
     */
    public function getCostCodesByCategory($category)
    {
        return $this->where('category', $category)
                   ->where('is_active', 1)
                   ->where('company_id', session('company_id') ?? 1)
                   ->orderBy('code', 'ASC')
                   ->findAll();
    }

    /**
     * Get active cost codes for dropdown
     */
    public function getActiveCostCodes()
    {
        return $this->where('is_active', 1)
                   ->where('company_id', session('company_id') ?? 1)
                   ->orderBy('category', 'ASC')
                   ->orderBy('code', 'ASC')
                   ->findAll();
    }

    /**
     * Check if cost code is unique
     */
    public function isCodeUnique($code, $excludeId = null)
    {
        $builder = $this->where('code', $code)
                       ->where('company_id', session('company_id') ?? 1);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() === 0;
    }

    /**
     * Get cost code categories
     */
    public static function getCategories()
    {
        return [
            'labor' => 'Labor',
            'material' => 'Material',
            'equipment' => 'Equipment',
            'subcontractor' => 'Subcontractor',
            'overhead' => 'Overhead',
            'other' => 'Other'
        ];
    }

    /**
     * Get cost types
     */
    public static function getCostTypes()
    {
        return [
            'direct' => 'Direct Cost',
            'indirect' => 'Indirect Cost'
        ];
    }

    /**
     * Get cost code statistics
     */
    public function getCostCodeStats()
    {
        $builder = $this->db->table($this->table);
        
        $builder->select("
            category,
            COUNT(*) as total_codes,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_codes,
            AVG(standard_rate) as avg_rate
        ", false);
        
        $builder->where('company_id', session('company_id') ?? 1);
        $builder->groupBy('category');
        
        return $builder->get()->getResultArray();
    }
}