<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\JobBudgetModel;
use App\Models\BudgetLineItemModel;
use App\Models\CostCodeModel;

class JobBudgetsController extends BaseController
{
    protected $budgetModel;
    protected $lineItemModel;
    protected $costCodeModel;

    public function __construct()
    {
        $this->budgetModel = new JobBudgetModel();
        $this->lineItemModel = new BudgetLineItemModel();
        $this->costCodeModel = new CostCodeModel();
    }

    public function index()
    {
        $filters = [
            'project_id' => $this->request->getGet('project_id'),
            'status' => $this->request->getGet('status'),
            'budget_period' => $this->request->getGet('budget_period'),
            'search' => $this->request->getGet('search')
        ];

        try {
            $data = [
                'title' => 'Job Budgets',
                'budgets' => $this->budgetModel->getBudgetsWithDetails($filters),
                'projects' => $this->budgetModel->getActiveProjects(),
                'budgetPeriods' => JobBudgetModel::getBudgetPeriods(),
                'budgetStatuses' => JobBudgetModel::getBudgetStatuses(),
                'filters' => $filters,
                'stats' => $this->budgetModel->getBudgetStats($filters)
            ];

            return view('accounting/job_budgets/index', $data);

        } catch (\Exception $e) {
            log_message('error', 'Job Budgets Error: ' . $e->getMessage());

            $data = [
                'title' => 'Job Budgets',
                'budgets' => [],
                'projects' => [],
                'budgetPeriods' => JobBudgetModel::getBudgetPeriods(),
                'budgetStatuses' => JobBudgetModel::getBudgetStatuses(),
                'filters' => $filters,
                'stats' => ['total_budgets' => 0, 'total_budgeted' => 0, 'total_spent' => 0, 'by_status' => []],
                'error_message' => 'Unable to load budgets. Please ensure the database tables exist.'
            ];

            return view('accounting/job_budgets/index', $data);
        }
    }

    public function create()
    {
        $data = [
            'title' => 'Create Job Budget',
            'projects' => $this->budgetModel->getActiveProjects(),
            'budgetCategories' => $this->budgetModel->getBudgetCategories(),
            'costCodes' => $this->costCodeModel->getActiveCostCodes(),
            'budgetPeriods' => JobBudgetModel::getBudgetPeriods(),
            'budgetStatuses' => JobBudgetModel::getBudgetStatuses()
        ];

        return view('accounting/job_budgets/create', $data);
    }

    public function store()
    {
        $rules = [
            'project_id' => 'required|integer',
            'name' => 'required|max_length[255]',
            'budget_period' => 'required|in_list[monthly,quarterly,yearly,project]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'total_budget' => 'required|decimal|greater_than[0]',
            'status' => 'permit_empty|in_list[draft,active,completed,cancelled]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $budgetData = [
            'project_id' => $this->request->getPost('project_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'budget_period' => $this->request->getPost('budget_period'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'total_budget' => $this->request->getPost('total_budget'),
            'status' => $this->request->getPost('status') ?? 'draft'
        ];

        // Get line items from POST
        $lineItems = [];
        $categories = $this->request->getPost('line_category_id') ?? [];
        $costCodes = $this->request->getPost('line_cost_code_id') ?? [];
        $descriptions = $this->request->getPost('line_description') ?? [];
        $amounts = $this->request->getPost('line_amount') ?? [];

        $totalAllocated = 0;
        for ($i = 0; $i < count($categories); $i++) {
            if (!empty($categories[$i]) && !empty($amounts[$i])) {
                $lineItems[] = [
                    'category_id' => $categories[$i],
                    'cost_code_id' => !empty($costCodes[$i]) ? $costCodes[$i] : null,
                    'description' => $descriptions[$i] ?? '',
                    'budgeted_amount' => $amounts[$i]
                ];
                $totalAllocated += floatval($amounts[$i]);
            }
        }

        $budgetData['allocated_budget'] = $totalAllocated;
        $budgetData['line_items'] = $lineItems;

        try {
            if ($budgetId = $this->budgetModel->insert($budgetData)) {
                // Create line items
                foreach ($lineItems as $item) {
                    $item['budget_id'] = $budgetId;
                    $this->lineItemModel->insert($item);
                }

                return redirect()->to('/admin/accounting/job-budgets')
                               ->with('success', 'Job budget created successfully!');
            } else {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Failed to create job budget.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Job Budget Store Error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error creating job budget: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $budget = $this->budgetModel->getBudgetWithLineItems($id);

            if (!$budget) {
                return redirect()->to('/admin/accounting/job-budgets')
                               ->with('error', 'Budget not found.');
            }

            // Update actual amounts
            $this->lineItemModel->updateActualAmounts($id);
            $this->budgetModel->updateBudgetSpentAmount($id);

            // Refresh budget data
            $budget = $this->budgetModel->getBudgetWithLineItems($id);

            $data = [
                'title' => 'Budget Details',
                'budget' => $budget
            ];

            return view('accounting/job_budgets/show', $data);

        } catch (\Exception $e) {
            log_message('error', 'Job Budget Show Error: ' . $e->getMessage());
            return redirect()->to('/admin/accounting/job-budgets')
                           ->with('error', 'Error loading budget details.');
        }
    }

    public function edit($id)
    {
        try {
            $budget = $this->budgetModel->getBudgetWithLineItems($id);

            if (!$budget || $budget['company_id'] != (session('company_id') ?? 1)) {
                return redirect()->to('/admin/accounting/job-budgets')
                               ->with('error', 'Budget not found.');
            }

            $data = [
                'title' => 'Edit Job Budget',
                'budget' => $budget,
                'projects' => $this->budgetModel->getActiveProjects(),
                'budgetCategories' => $this->budgetModel->getBudgetCategories(),
                'costCodes' => $this->costCodeModel->getActiveCostCodes(),
                'budgetPeriods' => JobBudgetModel::getBudgetPeriods(),
                'budgetStatuses' => JobBudgetModel::getBudgetStatuses()
            ];

            return view('accounting/job_budgets/edit', $data);

        } catch (\Exception $e) {
            log_message('error', 'Job Budget Edit Error: ' . $e->getMessage());
            return redirect()->to('/admin/accounting/job-budgets')
                           ->with('error', 'Error loading budget for editing.');
        }
    }

    public function update($id)
    {
        try {
            $budget = $this->budgetModel->find($id);

            if (!$budget || $budget['company_id'] != (session('company_id') ?? 1)) {
                return redirect()->to('/admin/accounting/job-budgets')
                               ->with('error', 'Budget not found.');
            }

            $rules = [
                'project_id' => 'required|integer',
                'name' => 'required|max_length[255]',
                'budget_period' => 'required|in_list[monthly,quarterly,yearly,project]',
                'start_date' => 'required|valid_date',
                'end_date' => 'required|valid_date',
                'total_budget' => 'required|decimal|greater_than[0]',
                'status' => 'permit_empty|in_list[draft,active,completed,cancelled]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                               ->withInput()
                               ->with('errors', $this->validator->getErrors());
            }

            $budgetData = [
                'project_id' => $this->request->getPost('project_id'),
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'budget_period' => $this->request->getPost('budget_period'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'total_budget' => $this->request->getPost('total_budget'),
                'status' => $this->request->getPost('status') ?? 'draft'
            ];

            // Delete existing line items
            $this->lineItemModel->where('budget_id', $id)->delete();

            // Get new line items from POST
            $lineItems = [];
            $categories = $this->request->getPost('line_category_id') ?? [];
            $costCodes = $this->request->getPost('line_cost_code_id') ?? [];
            $descriptions = $this->request->getPost('line_description') ?? [];
            $amounts = $this->request->getPost('line_amount') ?? [];

            $totalAllocated = 0;
            for ($i = 0; $i < count($categories); $i++) {
                if (!empty($categories[$i]) && !empty($amounts[$i])) {
                    $lineItems[] = [
                        'budget_id' => $id,
                        'category_id' => $categories[$i],
                        'cost_code_id' => !empty($costCodes[$i]) ? $costCodes[$i] : null,
                        'description' => $descriptions[$i] ?? '',
                        'budgeted_amount' => $amounts[$i]
                    ];
                    $totalAllocated += floatval($amounts[$i]);
                }
            }

            $budgetData['allocated_budget'] = $totalAllocated;

            if ($this->budgetModel->update($id, $budgetData)) {
                // Create new line items
                foreach ($lineItems as $item) {
                    $this->lineItemModel->insert($item);
                }

                return redirect()->to('/admin/accounting/job-budgets')
                               ->with('success', 'Job budget updated successfully!');
            } else {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Failed to update job budget.');
            }

        } catch (\Exception $e) {
            log_message('error', 'Job Budget Update Error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error updating job budget: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $budget = $this->budgetModel->find($id);

            if (!$budget || $budget['company_id'] != (session('company_id') ?? 1)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Budget not found.'
                ]);
            }

            // Delete line items first
            $this->lineItemModel->where('budget_id', $id)->delete();

            // Delete budget
            if ($this->budgetModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Job budget deleted successfully!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete job budget.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Job Budget Delete Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error deleting job budget: ' . $e->getMessage()
            ]);
        }
    }

    public function updateActuals($id)
    {
        try {
            $budget = $this->budgetModel->find($id);

            if (!$budget || $budget['company_id'] != (session('company_id') ?? 1)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Budget not found.'
                ]);
            }

            // Update actual amounts for line items
            $this->lineItemModel->updateActualAmounts($id);

            // Update budget spent amount
            $this->budgetModel->updateBudgetSpentAmount($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Budget actuals updated successfully!'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Update Actuals Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating budget actuals: ' . $e->getMessage()
            ]);
        }
    }

    public function projectBudgetComparison($projectId)
    {
        try {
            $summary = $this->budgetModel->getProjectBudgetSummary($projectId);

            if (!$summary) {
                return redirect()->to('/admin/accounting/job-budgets')
                               ->with('error', 'No budgets found for this project.');
            }

            $data = [
                'title' => 'Project Budget vs Actual Comparison',
                'projectId' => $projectId,
                'summary' => $summary
            ];

            return view('accounting/job_budgets/project_comparison', $data);

        } catch (\Exception $e) {
            log_message('error', 'Project Budget Comparison Error: ' . $e->getMessage());
            return redirect()->to('/admin/accounting/job-budgets')
                           ->with('error', 'Error loading project budget comparison.');
        }
    }
}
