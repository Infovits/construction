<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountCategoryModel extends Model
{
    protected $table = 'account_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id',
        'name',
        'code',
        'account_type',
        'description',
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
        'name' => 'required|max_length[255]',
        'code' => 'required|max_length[20]',
        'account_type' => 'required|in_list[asset,liability,equity,revenue,expense]',
        'description' => 'permit_empty|max_length[1000]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Category name is required',
            'max_length' => 'Category name cannot exceed 255 characters'
        ],
        'code' => [
            'required' => 'Category code is required',
            'max_length' => 'Category code cannot exceed 20 characters'
        ],
        'account_type' => [
            'required' => 'Account type is required',
            'in_list' => 'Account type must be one of: asset, liability, equity, revenue, expense'
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
     * Get account categories with additional details
     */
    public function getAccountCategoriesWithDetails($filters = [])
    {
        $builder = $this->db->table($this->table . ' ac');
        
        $builder->select("
            ac.*,
            COUNT(coa.id) as accounts_count,
            CASE 
                WHEN ac.account_type = 'asset' THEN 'Assets'
                WHEN ac.account_type = 'liability' THEN 'Liabilities'
                WHEN ac.account_type = 'equity' THEN 'Equity'
                WHEN ac.account_type = 'revenue' THEN 'Revenue'
                WHEN ac.account_type = 'expense' THEN 'Expenses'
                ELSE ac.account_type
            END as account_type_label
        ", false);
        
        $builder->join('chart_of_accounts coa', 'ac.id = coa.category_id', 'left');
        $builder->where('ac.company_id', session('company_id') ?? 1);
        
        // Apply filters
        if (!empty($filters['account_type'])) {
            $builder->where('ac.account_type', $filters['account_type']);
        }
        
        if (!empty($filters['is_active'])) {
            $builder->where('ac.is_active', $filters['is_active']);
        }
        
        if (!empty($filters['search'])) {
            $builder->groupStart()
                   ->like('ac.name', $filters['search'])
                   ->orLike('ac.code', $filters['search'])
                   ->orLike('ac.description', $filters['search'])
                   ->groupEnd();
        }
        
        $builder->groupBy('ac.id');
        $builder->orderBy('ac.account_type', 'ASC');
        $builder->orderBy('ac.name', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get categories by account type
     */
    public function getCategoriesByType($accountType)
    {
        return $this->where('account_type', $accountType)
                   ->where('is_active', 1)
                   ->where('company_id', session('company_id') ?? 1)
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }

    /**
     * Get active categories for dropdown
     */
    public function getActiveCategories()
    {
        return $this->where('is_active', 1)
                   ->where('company_id', session('company_id') ?? 1)
                   ->orderBy('account_type', 'ASC')
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }

    /**
     * Get category statistics
     */
    public function getCategoryStats()
    {
        $builder = $this->db->table($this->table);
        
        $builder->select("
            account_type,
            COUNT(*) as total_categories,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_categories
        ", false);
        
        $builder->where('company_id', session('company_id') ?? 1);
        $builder->groupBy('account_type');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Check if category code is unique
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
     * Get account types with labels
     */
    public static function getAccountTypes()
    {
        return [
            'asset' => 'Assets',
            'liability' => 'Liabilities',
            'equity' => 'Equity',
            'revenue' => 'Revenue',
            'expense' => 'Expenses'
        ];
    }
}
