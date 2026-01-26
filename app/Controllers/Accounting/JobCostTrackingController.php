<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\JobCostTrackingModel;
use App\Models\CostCodeModel;

class JobCostTrackingController extends BaseController
{
    protected $jobCostModel;
    protected $costCodeModel;

    public function __construct()
    {
        $this->jobCostModel = new JobCostTrackingModel();
        $this->costCodeModel = new CostCodeModel();
    }

    public function index()
    {
        $filters = [
            'project_id' => $this->request->getGet('project_id'),
            'cost_code_id' => $this->request->getGet('cost_code_id'),
            'cost_category' => $this->request->getGet('cost_category'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search')
        ];

        try {
            $data = [
                'title' => 'Job Cost Tracking',
                'jobCosts' => $this->jobCostModel->getJobCostsWithDetails($filters),
                'projects' => $this->jobCostModel->getActiveProjects(),
                'costCodes' => $this->costCodeModel->getActiveCostCodes(),
                'costCategories' => JobCostTrackingModel::getCostCategories(),
                'filters' => $filters,
                'stats' => $this->jobCostModel->getCostTrackingStats($filters)
            ];

            return view('accounting/job_cost_tracking/index', $data);

        } catch (\Exception $e) {
            log_message('error', 'Job Cost Tracking Error: ' . $e->getMessage());
            
            $data = [
                'title' => 'Job Cost Tracking',
                'jobCosts' => [],
                'projects' => [],
                'costCodes' => [],
                'costCategories' => JobCostTrackingModel::getCostCategories(),
                'filters' => $filters,
                'stats' => ['total_entries' => 0, 'total_cost' => 0, 'avg_cost' => 0, 'by_category' => []],
                'error_message' => 'Unable to load job costs. Please ensure the database table exists.'
            ];
            
            return view('accounting/job_cost_tracking/index', $data);
        }
    }

    public function create()
    {
        $data = [
            'title' => 'Add Job Cost Entry',
            'projects' => $this->jobCostModel->getActiveProjects(),
            'costCodes' => $this->costCodeModel->getActiveCostCodes(),
            'costCategories' => JobCostTrackingModel::getCostCategories()
        ];

        return view('accounting/job_cost_tracking/create', $data);
    }

    public function store()
    {
        $rules = $this->jobCostModel->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $quantity = (float)$this->request->getPost('quantity');
        $unitCost = (float)$this->request->getPost('unit_cost');
        $totalCost = $quantity * $unitCost;

        $data = [
            'company_id' => session('company_id') ?? 1,
            'project_id' => $this->request->getPost('project_id'),
            'cost_code_id' => $this->request->getPost('cost_code_id'),
            'description' => $this->request->getPost('description'),
            'cost_date' => $this->request->getPost('cost_date'),
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
            'vendor_supplier' => $this->request->getPost('vendor_supplier'),
            'reference_number' => $this->request->getPost('reference_number'),
            'cost_category' => $this->request->getPost('cost_category'),
            'is_billable' => $this->request->getPost('is_billable') ? 1 : 0,
            'created_by' => session('user_id') ?? 1
        ];

        try {
            if ($this->jobCostModel->insert($data)) {
                return redirect()->to('/admin/accounting/job-cost-tracking')
                               ->with('success', 'Job cost entry added successfully!');
            } else {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Failed to save job cost entry.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Job Cost Store Error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error saving job cost entry: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $jobCost = $this->jobCostModel->getJobCostsWithDetails(['id' => $id]);
            
            if (empty($jobCost)) {
                return redirect()->to('/admin/accounting/job-cost-tracking')
                               ->with('error', 'Job cost entry not found.');
            }

            $data = [
                'title' => 'View Job Cost Entry',
                'jobCost' => $jobCost[0]
            ];

            return view('accounting/job_cost_tracking/show', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Job Cost Show Error: ' . $e->getMessage());
            return redirect()->to('/admin/accounting/job-cost-tracking')
                           ->with('error', 'Error loading job cost entry.');
        }
    }

    public function edit($id)
    {
        try {
            $jobCost = $this->jobCostModel->find($id);
            
            if (!$jobCost || $jobCost['company_id'] != (session('company_id') ?? 1)) {
                return redirect()->to('/admin/accounting/job-cost-tracking')
                               ->with('error', 'Job cost entry not found.');
            }

            $data = [
                'title' => 'Edit Job Cost Entry',
                'jobCost' => $jobCost,
                'projects' => $this->jobCostModel->getActiveProjects(),
                'costCodes' => $this->costCodeModel->getActiveCostCodes(),
                'costCategories' => JobCostTrackingModel::getCostCategories()
            ];

            return view('accounting/job_cost_tracking/edit', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Job Cost Edit Error: ' . $e->getMessage());
            return redirect()->to('/admin/accounting/job-cost-tracking')
                           ->with('error', 'Error loading job cost entry for editing.');
        }
    }

    public function update($id)
    {
        try {
            $jobCost = $this->jobCostModel->find($id);

            if (!$jobCost || $jobCost['company_id'] != (session('company_id') ?? 1)) {
                return redirect()->to('/admin/accounting/job-cost-tracking')
                               ->with('error', 'Job cost entry not found.');
            }

            $rules = $this->jobCostModel->getValidationRules();

            if (!$this->validate($rules)) {
                return redirect()->back()
                               ->withInput()
                               ->with('errors', $this->validator->getErrors());
            }

            $quantity = (float)$this->request->getPost('quantity');
            $unitCost = (float)$this->request->getPost('unit_cost');
            $totalCost = $quantity * $unitCost;

            $data = [
                'project_id' => $this->request->getPost('project_id'),
                'cost_code_id' => $this->request->getPost('cost_code_id'),
                'description' => $this->request->getPost('description'),
                'cost_date' => $this->request->getPost('cost_date'),
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'vendor_supplier' => $this->request->getPost('vendor_supplier'),
                'reference_number' => $this->request->getPost('reference_number'),
                'cost_category' => $this->request->getPost('cost_category'),
                'is_billable' => $this->request->getPost('is_billable') ? 1 : 0
            ];

            if ($this->jobCostModel->update($id, $data)) {
                return redirect()->to('/admin/accounting/job-cost-tracking')
                               ->with('success', 'Job cost entry updated successfully!');
            } else {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Failed to update job cost entry.');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Job Cost Update Error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error updating job cost entry: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $jobCost = $this->jobCostModel->find($id);
            
            if (!$jobCost || $jobCost['company_id'] != (session('company_id') ?? 1)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Job cost entry not found.'
                ]);
            }

            if ($this->jobCostModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Job cost entry deleted successfully!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete job cost entry.'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Job Cost Delete Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error deleting job cost entry: ' . $e->getMessage()
            ]);
        }
    }

    public function projectSummary($projectId)
    {
        try {
            $summary = $this->jobCostModel->getProjectCostSummary($projectId);
            
            $data = [
                'title' => 'Project Cost Summary',
                'projectId' => $projectId,
                'summary' => $summary
            ];

            return view('accounting/job_cost_tracking/project_summary', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Project Summary Error: ' . $e->getMessage());
            return redirect()->to('/admin/accounting/job-cost-tracking')
                           ->with('error', 'Error loading project summary.');
        }
    }
}