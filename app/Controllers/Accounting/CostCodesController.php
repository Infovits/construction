<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\CostCodeModel;

class CostCodesController extends BaseController
{
    protected $costCodeModel;

    public function __construct()
    {
        $this->costCodeModel = new CostCodeModel();
    }

    public function index()
    {
        $filters = [
            'category' => $this->request->getGet('category'),
            'cost_type' => $this->request->getGet('cost_type'),
            'is_active' => $this->request->getGet('is_active'),
            'search' => $this->request->getGet('search')
        ];

        try {
            $data = [
                'title' => 'Cost Codes',
                'costCodes' => $this->costCodeModel->getCostCodesWithStats($filters),
                'categories' => CostCodeModel::getCategories(),
                'costTypes' => CostCodeModel::getCostTypes(),
                'filters' => $filters,
                'stats' => $this->costCodeModel->getCostCodeStats()
            ];

            return view('accounting/cost_codes/index', $data);

        } catch (\Exception $e) {
            log_message('error', 'Cost Codes Error: ' . $e->getMessage());
            
            $data = [
                'title' => 'Cost Codes',
                'costCodes' => [],
                'categories' => CostCodeModel::getCategories(),
                'costTypes' => CostCodeModel::getCostTypes(),
                'filters' => $filters,
                'stats' => [],
                'error_message' => 'Unable to load cost codes: ' . $e->getMessage()
            ];
            
            return view('accounting/cost_codes/index', $data);
        }
    }

    public function create()
    {
        $data = [
            'title' => 'Create Cost Code',
            'categories' => CostCodeModel::getCategories(),
            'costTypes' => CostCodeModel::getCostTypes()
        ];

        return view('accounting/cost_codes/create', $data);
    }

    public function store()
    {
        try {
            $data = [
                'company_id' => session('company_id') ?? 1,
                'code' => $this->request->getPost('code'),
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'category' => $this->request->getPost('category'),
                'cost_type' => $this->request->getPost('cost_type'),
                'unit_of_measure' => $this->request->getPost('unit_of_measure'),
                'standard_rate' => $this->request->getPost('standard_rate'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
                'created_by' => session('user_id')
            ];

            $validation = \Config\Services::validation();
            $validation->setRules($this->costCodeModel->getValidationRules());

            if (!$validation->run($data)) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            // Check if code is unique
            if (!$this->costCodeModel->isCodeUnique($data['code'])) {
                return redirect()->back()->withInput()->with('error', 'Cost code already exists');
            }

            $costCodeId = $this->costCodeModel->insert($data);

            if ($costCodeId) {
                return redirect()->to(base_url('admin/accounting/cost-codes'))->with('success', 'Cost code created successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to create cost code');
            }

        } catch (\Exception $e) {
            log_message('error', 'Cost Code Creation Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the cost code');
        }
    }

    public function show($id)
    {
        try {
            $costCode = $this->costCodeModel->find($id);

            if (!$costCode) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Cost code not found');
            }

            $data = [
                'title' => 'Cost Code Details - ' . $costCode['name'],
                'costCode' => $costCode,
                'categories' => CostCodeModel::getCategories(),
                'costTypes' => CostCodeModel::getCostTypes()
            ];

            return view('accounting/cost_codes/show', $data);

        } catch (\Exception $e) {
            log_message('error', 'Cost Code Show Error: ' . $e->getMessage());
            return redirect()->to(base_url('admin/accounting/cost-codes'))->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $costCode = $this->costCodeModel->find($id);

            if (!$costCode) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Cost code not found');
            }

            $data = [
                'title' => 'Edit Cost Code',
                'costCode' => $costCode,
                'categories' => CostCodeModel::getCategories(),
                'costTypes' => CostCodeModel::getCostTypes()
            ];

            return view('accounting/cost_codes/edit', $data);

        } catch (\Exception $e) {
            log_message('error', 'Cost Code Edit Error: ' . $e->getMessage());
            return redirect()->to(base_url('admin/accounting/cost-codes'))->with('error', $e->getMessage());
        }
    }

    public function update($id)
    {
        try {
            $costCode = $this->costCodeModel->find($id);

            if (!$costCode || $costCode['company_id'] != (session('company_id') ?? 1)) {
                return redirect()->to(base_url('admin/accounting/cost-codes'))->with('error', 'Cost code not found.');
            }

            $data = [
                'company_id' => session('company_id') ?? 1,
                'code' => $this->request->getPost('code'),
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'category' => $this->request->getPost('category'),
                'cost_type' => $this->request->getPost('cost_type'),
                'unit_of_measure' => $this->request->getPost('unit_of_measure'),
                'standard_rate' => $this->request->getPost('standard_rate'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            $validation = \Config\Services::validation();
            $validation->setRules($this->costCodeModel->getValidationRules());

            if (!$validation->run($data)) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            // Check if code is unique (excluding current record)
            if (!$this->costCodeModel->isCodeUnique($data['code'], $id)) {
                return redirect()->back()->withInput()->with('error', 'Cost code already exists');
            }

            if ($this->costCodeModel->update($id, $data)) {
                return redirect()->to(base_url('admin/accounting/cost-codes'))->with('success', 'Cost code updated successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update cost code');
            }

        } catch (\Exception $e) {
            log_message('error', 'Cost Code Update Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the cost code');
        }
    }

    public function delete($id)
    {
        try {
            $costCode = $this->costCodeModel->find($id);

            if (!$costCode || $costCode['company_id'] != (session('company_id') ?? 1)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cost code not found.'
                ]);
            }

            if ($this->costCodeModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Cost code deleted successfully!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete cost code.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Cost Code Delete Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error deleting cost code: ' . $e->getMessage()
            ]);
        }
    }

    public function toggle($id)
    {
        try {
            $costCode = $this->costCodeModel->find($id);

            if (!$costCode || $costCode['company_id'] != (session('company_id') ?? 1)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cost code not found.'
                ]);
            }

            $newStatus = $costCode['is_active'] ? 0 : 1;

            if ($this->costCodeModel->update($id, ['is_active' => $newStatus])) {
                $message = $newStatus ? 'Cost code activated successfully!' : 'Cost code deactivated successfully!';
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to toggle cost code status.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Cost Code Toggle Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error toggling cost code: ' . $e->getMessage()
            ]);
        }
    }
}