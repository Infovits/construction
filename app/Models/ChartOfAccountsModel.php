<?php

namespace App\Models;

use CodeIgniter\Model;

class ChartOfAccountsModel extends Model
{
    protected $table = 'chart_of_accounts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id',
        'category_id',
        'parent_account_id',
        'account_code',
        'account_name',
        'account_type',
        'account_subtype',
        'description',
        'is_system_account',
        'is_active',
        'balance'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'company_id' => 'required|integer',
        'account_code' => 'required|max_length[50]',
        'account_name' => 'required|max_length[255]',
        'account_type' => 'required|in_list[asset,liability,equity,revenue,expense]',
        'account_subtype' => 'permit_empty|max_length[100]',
        'description' => 'permit_empty',
        'is_system_account' => 'permit_empty|in_list[0,1]',
        'is_active' => 'permit_empty|in_list[0,1]',
        'balance' => 'permit_empty|decimal'
    ];

    protected $validationMessages = [
        'account_code' => [
            'required' => 'Account code is required',
            'max_length' => 'Account code cannot exceed 50 characters'
        ],
        'account_name' => [
            'required' => 'Account name is required',
            'max_length' => 'Account name cannot exceed 255 characters'
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
     * Get chart of accounts with additional details
     */
    public function getAccountsWithDetails($filters = [])
    {
        $builder = $this->db->table($this->table . ' coa');
        
        $builder->select("
            coa.*,
            ac.name as category_name,
            parent.account_name as parent_account_name,
            parent.account_code as parent_account_code,
            CASE 
                WHEN coa.account_type = 'asset' THEN 'Assets'
                WHEN coa.account_type = 'liability' THEN 'Liabilities'
                WHEN coa.account_type = 'equity' THEN 'Equity'
                WHEN coa.account_type = 'revenue' THEN 'Revenue'
                WHEN coa.account_type = 'expense' THEN 'Expenses'
                ELSE coa.account_type
            END as account_type_label,
            (SELECT COUNT(*) FROM journal_entry_lines jel WHERE jel.account_id = coa.id) as transaction_count
        ", false);
        
        $builder->join('account_categories ac', 'coa.category_id = ac.id', 'left');
        $builder->join('chart_of_accounts parent', 'coa.parent_account_id = parent.id', 'left');
        $builder->where('coa.company_id', session('company_id') ?? 1);
        
        // Apply filters
        if (!empty($filters['account_id'])) {
            $builder->where('coa.id', $filters['account_id']);
        }
        
        if (!empty($filters['account_type'])) {
            $builder->where('coa.account_type', $filters['account_type']);
        }
        
        if (!empty($filters['category_id'])) {
            $builder->where('coa.category_id', $filters['category_id']);
        }
        
        if (!empty($filters['is_active'])) {
            $builder->where('coa.is_active', $filters['is_active']);
        }
        
        if (!empty($filters['search'])) {
            $builder->groupStart()
                   ->like('coa.account_name', $filters['search'])
                   ->orLike('coa.account_code', $filters['search'])
                   ->orLike('coa.description', $filters['search'])
                   ->groupEnd();
        }
        
        $builder->orderBy('coa.account_type', 'ASC');
        $builder->orderBy('coa.account_code', 'ASC');
        
        $accounts = $builder->get()->getResultArray();
        
        // Add depth information for simple hierarchical display
        foreach ($accounts as &$account) {
            $account['depth'] = 0; // Default depth
            if (!empty($account['parent_account_id'])) {
                // Find parent depth and add 1
                foreach ($accounts as $parentAccount) {
                    if ($parentAccount['id'] == $account['parent_account_id']) {
                        $account['depth'] = 1; // Simple: all children at depth 1
                        break;
                    }
                }
            }
        }
        
        return $accounts;
    }

    /**
     * Build hierarchical structure for accounts with depth level
     */
    private function buildAccountHierarchy($accounts, $parentId = null, $depth = 0)
    {
        $hierarchy = [];
        
        foreach ($accounts as $account) {
            if ($account['parent_account_id'] == $parentId) {
                $account['depth'] = $depth;
                $account['children'] = $this->buildAccountHierarchy($accounts, $account['id'], $depth + 1);
                $hierarchy[] = $account;
            }
        }
        
        return $hierarchy;
    }

    /**
     * Flatten hierarchical structure for display
     */
    public function flattenHierarchy($hierarchy)
    {
        $flattened = [];
        
        foreach ($hierarchy as $account) {
            $flattened[] = $account;
            if (!empty($account['children'])) {
                $flattened = array_merge($flattened, $this->flattenHierarchy($account['children']));
            }
        }
        
        return $flattened;
    }

    /**
     * Get accounts by type
     */
    public function getAccountsByType($accountType)
    {
        return $this->where('account_type', $accountType)
                   ->where('is_active', 1)
                   ->where('company_id', session('company_id') ?? 1)
                   ->orderBy('account_code', 'ASC')
                   ->findAll();
    }

    /**
     * Get active accounts for dropdown
     */
    public function getActiveAccounts()
    {
        return $this->where('is_active', 1)
                   ->where('company_id', session('company_id') ?? 1)
                   ->orderBy('account_type', 'ASC')
                   ->orderBy('account_code', 'ASC')
                   ->findAll();
    }

    /**
     * Get account hierarchy (parent-child relationships)
     */
    public function getAccountHierarchy()
    {
        $accounts = $this->where('company_id', session('company_id') ?? 1)
                        ->where('is_active', 1)
                        ->orderBy('account_type', 'ASC')
                        ->orderBy('account_code', 'ASC')
                        ->findAll();
        
        return $this->buildHierarchy($accounts);
    }

    /**
     * Build hierarchical structure
     */
    private function buildHierarchy($accounts, $parentId = null)
    {
        $hierarchy = [];
        
        foreach ($accounts as $account) {
            if ($account['parent_account_id'] == $parentId) {
                $account['children'] = $this->buildHierarchy($accounts, $account['id']);
                $hierarchy[] = $account;
            }
        }
        
        return $hierarchy;
    }

    /**
     * Get account statistics
     */
    public function getAccountStats()
    {
        $builder = $this->db->table($this->table);
        
        $builder->select("
            account_type,
            COUNT(*) as total_accounts,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_accounts,
            SUM(balance) as total_balance
        ", false);
        
        $builder->where('company_id', session('company_id') ?? 1);
        $builder->groupBy('account_type');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Check if account code is unique
     */
    public function isAccountCodeUnique($code, $excludeId = null)
    {
        $builder = $this->where('account_code', $code)
                       ->where('company_id', session('company_id') ?? 1);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() === 0;
    }

    /**
     * Get next available account code for a type
     */
    public function getNextAccountCode($accountType)
    {
        $prefixes = [
            'asset' => '1',
            'liability' => '2',
            'equity' => '3',
            'revenue' => '4',
            'expense' => '5'
        ];
        
        $prefix = $prefixes[$accountType] ?? '9';
        
        $builder = $this->where('account_type', $accountType)
                       ->where('company_id', session('company_id') ?? 1)
                       ->like('account_code', $prefix, 'after')
                       ->orderBy('account_code', 'DESC')
                       ->limit(1);
        
        $lastAccount = $builder->first();
        
        if ($lastAccount) {
            $lastCode = intval($lastAccount['account_code']);
            return str_pad($lastCode + 10, 4, '0', STR_PAD_LEFT);
        } else {
            return $prefix . '000';
        }
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

    /**
     * Get accounts with their current balances
     */
    public function getAccountsWithBalances($filters = [])
    {
        $builder = $this->db->table($this->table . ' coa');
        
        $builder->select("
            coa.*,
            COALESCE(bal.balance, 0) as balance,
            cat.name as category_name
        ", false);
        
        // Join with calculated balances
        $builder->join('(
            SELECT 
                jel.account_id,
                CASE 
                    WHEN coa_inner.account_type IN (\'asset\', \'expense\') 
                    THEN SUM(jel.debit_amount) - SUM(jel.credit_amount)
                    ELSE SUM(jel.credit_amount) - SUM(jel.debit_amount)
                END as balance
            FROM journal_entry_lines jel
            JOIN journal_entries je ON jel.journal_entry_id = je.id
            JOIN chart_of_accounts coa_inner ON jel.account_id = coa_inner.id
            WHERE je.status = \'posted\'
            ' . (!empty($filters['date_to']) ? 'AND je.entry_date <= \'' . $filters['date_to'] . '\'' : '') . '
            GROUP BY jel.account_id
        ) bal', 'coa.id = bal.account_id', 'left');
        
        $builder->join('account_categories cat', 'coa.category_id = cat.id', 'left');
        
        $builder->where('coa.company_id', session('company_id') ?? 1);
        $builder->where('coa.is_active', 1);
        
        // Apply filters
        if (!empty($filters['account_type'])) {
            $builder->where('coa.account_type', $filters['account_type']);
        }
        
        if (!empty($filters['category_id'])) {
            $builder->where('coa.category_id', $filters['category_id']);
        }
        
        if (!empty($filters['account_id'])) {
            $builder->where('coa.id', $filters['account_id']);
        }
        
        $builder->orderBy('coa.account_type', 'ASC');
        $builder->orderBy('coa.account_code', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}
