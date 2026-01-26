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
        $clientModel = new ClientModel();
        
        if ($clientModel->delete($id)) {
            return redirect()->to('/admin/clients')->with('success', 'Client deleted successfully');
        } else {
            return redirect()->to('/admin/clients')->with('error', 'Failed to delete client');
        }
    }

    public function toggle($id)
    {
        $client = $this->clientModel->find($id);
        if (!$client) {
            session()->setFlashdata('error', 'Client not found');
            return redirect()->to('/admin/clients');
        }

        $newStatus = $client['status'] === 'active' ? 'inactive' : 'active';

        try {
            $this->clientModel->update($id, ['status' => $newStatus]);
            session()->setFlashdata('success', 'Client status updated successfully');
            return redirect()->to('/admin/clients');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Failed to update status: ' . $e->getMessage());
            return redirect()->to('/admin/clients');
        }
    }

    public function exportPdf()
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

        $clients = $builder->orderBy('clients.created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Clients Report',
            'clients' => $clients,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'client_type' => $client_type
            ],
            'stats' => [
                'total_clients' => $this->clientModel->countAll(),
                'active_clients' => $this->clientModel->where('status', 'active')->countAllResults(),
                'clients_this_month' => $this->clientModel->where('created_at >=', date('Y-m-01'))->countAllResults()
            ]
        ];

        // Generate PDF using mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_right' => 15
        ]);

        $html = view('clients/export_pdf', $data);
        $mpdf->WriteHTML($html);
        $mpdf->Output('clients_report_' . date('Y-m-d_H-i-s') . '.pdf', 'D');
        exit();
    }

    public function exportExcel()
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

        $clients = $builder->orderBy('clients.created_at', 'DESC')->findAll();

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set company header
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'Helmet Construction Management System');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Set report title
        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'Clients Report');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Set report metadata
        $sheet->mergeCells('A3:H3');
        $sheet->setCellValue('A3', 'Generated on: ' . date('F j, Y \a\t g:i A') . ' | Report ID: CL-' . date('Ymd-His'));
        $sheet->getStyle('A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(18);

        // Set filters information
        $filters = [];
        if ($search) $filters[] = 'Search: ' . $search;
        if ($status) $filters[] = 'Status: ' . ucfirst($status);
        if ($client_type) $filters[] = 'Type: ' . ucfirst($client_type);
        
        if (!empty($filters)) {
            $sheet->mergeCells('A4:H4');
            $sheet->setCellValue('A4', 'Applied Filters: ' . implode(' | ', $filters));
            $sheet->getStyle('A4')->getFont()->setSize(10)->setItalic(true);
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension(4)->setRowHeight(18);
        }

        // Set statistics
        $totalClients = $this->clientModel->countAll();
        $activeClients = $this->clientModel->where('status', 'active')->countAllResults();
        $newThisMonth = $this->clientModel->where('created_at >=', date('Y-m-01'))->countAllResults();
        
        $sheet->mergeCells('A5:H5');
        $sheet->setCellValue('A5', 'Total Clients: ' . number_format($totalClients) . ' | Active: ' . number_format($activeClients) . ' | New This Month: ' . number_format($newThisMonth));
        $sheet->getStyle('A5')->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E5E7EB');
        $sheet->getRowDimension(5)->setRowHeight(18);

        // Set headers
        $headers = ['#', 'Client Name', 'Client Code', 'Client Type', 'Status', 'Company', 'Email Address', 'Phone Number', 'Registration Date'];
        $column = 'A';
        $row = 7;
        foreach ($headers as $header) {
            $sheet->setCellValue($column . $row, $header);
            $sheet->getStyle($column . $row)->getFont()->setBold(true);
            $sheet->getStyle($column . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F3F4F6');
            $column++;
        }

        // Set data
        $row = 8;
        $counter = 1;
        foreach ($clients as $client) {
            $sheet->setCellValue('A' . $row, $counter++);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row)->getFont()->getColor()->setARGB('6B7280');
            $sheet->setCellValue('B' . $row, $client['name']);
            $sheet->setCellValue('C' . $row, $client['client_code']);
            $sheet->setCellValue('D' . $row, ucfirst($client['client_type']));
            $sheet->setCellValue('E' . $row, ucfirst($client['status']));
            $sheet->setCellValue('F' . $row, $client['company_name'] ?? 'N/A');
            $sheet->setCellValue('G' . $row, $client['email']);
            $sheet->setCellValue('H' . $row, $client['phone']);
            $sheet->setCellValue('I' . $row, date('M j, Y', strtotime($client['created_at'])));
            $row++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(18);

        // Add borders to data area
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A7:' . $highestColumn . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'clients_report_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }
}
