<?php

namespace App\Models;

use CodeIgniter\Model;

class BudgetLineItemModel extends Model
{
    protected $table = 'budget_line_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'budget_id',
        'category_id',
        'cost_code_id',
        'description',
        'budgeted_amount',
        'actual_amount',
        'variance'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'budget_id' => 'required|integer',
        'category_id' => 'required|integer',
        'cost_code_id' => 'permit_empty|integer',
        'description' => 'required|max_length[255]',
        'budgeted_amount' => 'required|decimal',
        'actual_amount' => 'permit_empty|decimal',
        'variance' => 'permit_empty|decimal'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['calculateVariance'];
    protected $beforeUpdate = ['calculateVariance'];

    /**
     * Calculate variance
     */
    protected function calculateVariance(array $data)
    {
        if (isset($data['data']['budgeted_amount'])) {
            $actual = $data['data']['actual_amount'] ?? 0;
            $budgeted = $data['data']['budgeted_amount'];
            $data['data']['variance'] = $actual - $budgeted;
        }
        return $data;
    }

    /**
     * Update actual amounts based on job cost tracking
     */
    public function updateActualAmounts($budgetId)
    {
        $lineItems = $this->where('budget_id', $budgetId)->findAll();

        foreach ($lineItems as $item) {
            if (!$item['cost_code_id']) {
                continue;
            }

            // Get budget details
            $budgetModel = new JobBudgetModel();
            $budget = $budgetModel->find($budgetId);

            if (!$budget) {
                continue;
            }

            // Calculate actual amount from job cost tracking
            $builder = $this->db->table('job_cost_lines');
            $builder->selectSum('total_cost', 'actual');
            $builder->where('project_id', $budget['project_id']);
            $builder->where('cost_code_id', $item['cost_code_id']);
            $builder->where('cost_date >=', $budget['start_date']);
            $builder->where('cost_date <=', $budget['end_date']);

            $result = $builder->get()->getRow();
            $actualAmount = $result->actual ?? 0;

            // Update line item
            $this->update($item['id'], [
                'actual_amount' => $actualAmount,
                'variance' => $actualAmount - $item['budgeted_amount']
            ]);
        }

        return true;
    }
}
