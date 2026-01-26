<?php

namespace App\Models;

use CodeIgniter\Model;

class BudgetCategoryModel extends Model
{
    protected $table = 'budget_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'company_id',
        'name',
        'description',
        'budget_type',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'company_id' => 'permit_empty|integer',
        'name' => 'required|max_length[255]',
        'description' => 'permit_empty|max_length[1000]',
        'budget_type' => 'required|in_list[labor,materials,equipment,subcontractor,overhead,other]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Category name is required',
            'max_length' => 'Category name cannot exceed 255 characters'
        ],
        'budget_type' => [
            'required' => 'Budget type is required',
            'in_list' => 'Invalid budget type selected'
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
        if (!isset($data['data']['is_active'])) {
            $data['data']['is_active'] = 1;
        }
        return $data;
    }

    /**
     * Get all budget categories for the current company
     */
    public function getCategoriesForCompany($filters = [])
    {
        $builder = $this->where('company_id', session('company_id') ?? 1);

        if (isset($filters['budget_type']) && !empty($filters['budget_type'])) {
            $builder->where('budget_type', $filters['budget_type']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $builder->groupStart()
                   ->like('name', $filters['search'])
                   ->orLike('description', $filters['search'])
                   ->groupEnd();
        }

        return $builder->orderBy('budget_type', 'ASC')
                      ->orderBy('name', 'ASC')
                      ->findAll();
    }

    /**
     * Get budget types
     */
    public static function getBudgetTypes()
    {
        return [
            'labor' => 'Labor',
            'materials' => 'Materials',
            'equipment' => 'Equipment',
            'subcontractor' => 'Subcontractor',
            'overhead' => 'Overhead',
            'other' => 'Other'
        ];
    }

    /**
     * Get category statistics
     */
    public function getCategoryStats()
    {
        $builder = $this->db->table($this->table);
        $builder->where('company_id', session('company_id') ?? 1);

        $stats = [
            'total_categories' => $builder->countAllResults(false),
            'active_categories' => $builder->where('is_active', 1)->countAllResults()
        ];

        // Get count by type
        $builder = $this->db->table($this->table);
        $builder->select('budget_type, COUNT(*) as count', false);
        $builder->where('company_id', session('company_id') ?? 1);
        $builder->groupBy('budget_type');
        $stats['by_type'] = $builder->get()->getResultArray();

        return $stats;
    }
}
