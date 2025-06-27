<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\CompanyModel;

class Clients extends BaseController
{
    protected $clientModel;
    protected $companyModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->companyModel = new CompanyModel();
    }

    public function index()
    {
        $search = $this->request->getVar('search');
        $status = $this->request->getVar('status');
        $client_type = $this->request->getVar('client_type');
        
        $builder = $this->clientModel->select('clients.*, companies.name as company_name')
                                    ->join('companies', 'companies.id = clients.company_id', 'left');

        if ($search) {
            $builder->groupStart()
                   ->like('clients.name', $search)
                   ->orLike('clients.email', $search)
                   ->orLike('clients.phone', $search)
                   ->orLike('clients.client_code', $search)
                   ->groupEnd();
        }

        if ($status) {
            $builder->where('clients.status', $status);
        }

        if ($client_type) {
            $builder->where('clients.client_type', $client_type);
        }

        $data = [
            'title' => 'Clients',
            'clients' => $builder->orderBy('clients.created_at', 'DESC')->paginate(15),
            'pager' => $this->clientModel->pager,
            'search' => $search,
            'status_filter' => $status,
            'type_filter' => $client_type,
            'stats' => [
                'total_clients' => $this->clientModel->countAll(),
                'active_clients' => $this->clientModel->where('status', 'active')->countAllResults(),
                'clients_this_month' => $this->clientModel->where('created_at >=', date('Y-m-01'))->countAllResults()
            ]
        ];

        return view('clients/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Client',
            'companies' => $this->companyModel->findAll()
        ];

        return view('clients/create', $data);
    }

    public function store()
    {
        $rules = [
            'company_id' => 'required|numeric',
            'name' => 'required|min_length[3]|max_length[255]',
            'email' => 'permit_empty|valid_email',
            'phone' => 'permit_empty|min_length[10]',
            'client_type' => 'required|in_list[individual,company,government]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        
        // Generate client code if not provided
        if (empty($data['client_code'])) {
            $data['client_code'] = $this->clientModel->generateClientCode();
        }

        try {
            $this->clientModel->insert($data);
            return redirect()->to('admin/clients')->with('success', 'Client created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create client: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $client = $this->clientModel->select('clients.*, companies.name as company_name')
                                   ->join('companies', 'companies.id = clients.company_id', 'left')
                                   ->find($id);
        
        if (!$client) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Client not found');
        }

        // Get client's projects
        $projectModel = new \App\Models\ProjectModel();
        $projects = $projectModel->where('client_id', $id)->findAll();

        $data = [
            'title' => 'Client Details',
            'client' => $client,
            'projects' => $projects
        ];

        return view('clients/view', $data);
    }

    public function edit($id)
    {
        $client = $this->clientModel->find($id);
        if (!$client) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Client not found');
        }

        $data = [
            'title' => 'Edit Client',
            'client' => $client,
            'companies' => $this->companyModel->findAll()
        ];

        return view('clients/edit', $data);
    }

    public function update($id)
    {
        $client = $this->clientModel->find($id);
        if (!$client) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Client not found');
        }

        $rules = [
            'company_id' => 'required|numeric',
            'name' => 'required|min_length[3]|max_length[255]',
            'email' => 'permit_empty|valid_email',
            'phone' => 'permit_empty|min_length[10]',
            'client_type' => 'required|in_list[individual,company,government]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        try {
            $this->clientModel->update($id, $data);
            return redirect()->to('admin/clients')->with('success', 'Client updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update client: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $client = $this->clientModel->find($id);
        if (!$client) {
            return $this->response->setJSON(['success' => false, 'message' => 'Client not found']);
        }

        try {
            $this->clientModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Client deleted successfully']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete client: ' . $e->getMessage()]);
        }
    }

    public function toggle($id)
    {
        $client = $this->clientModel->find($id);
        if (!$client) {
            return $this->response->setJSON(['success' => false, 'message' => 'Client not found']);
        }

        $newStatus = $client['status'] === 'active' ? 'inactive' : 'active';

        try {
            $this->clientModel->update($id, ['status' => $newStatus]);
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Client status updated successfully',
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status: ' . $e->getMessage()]);
        }
    }
}
