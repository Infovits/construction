<?php

namespace App\Controllers;

use App\Models\MilestoneModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\UserModel;

class Milestones extends BaseController
{
    protected $milestoneModel;
    protected $projectModel;
    protected $taskModel;
    protected $userModel;

    public function __construct()
    {
        helper('project');
        $this->milestoneModel = new MilestoneModel();
        $this->projectModel = new ProjectModel();
        $this->taskModel = new TaskModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $projectId = $this->request->getGet('project_id');
        $status = $this->request->getGet('status');

        $builder = $this->milestoneModel->select('tasks.*, projects.name as project_name, projects.project_code')
                                       ->join('projects', 'tasks.project_id = projects.id')
                                       ->where('projects.company_id', session('company_id'))
                                       ->where('tasks.is_milestone', 1);

        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }
        if ($status) {
            $builder->where('tasks.status', $status);
        }

        $milestones = $builder->orderBy('tasks.planned_end_date', 'ASC')->findAll();
        
        // Calculate milestone statistics
        $stats = [
            'total' => count($milestones),
            'completed' => 0,
            'in_progress' => 0,
            'pending' => 0,
            'overdue' => 0
        ];
        
        foreach ($milestones as $milestone) {
            if ($milestone['status'] === 'completed') {
                $stats['completed']++;
            } elseif ($milestone['status'] === 'in_progress') {
                $stats['in_progress']++;
            } else {
                $stats['pending']++;
            }
            
            // Check if milestone is overdue
            if ($milestone['planned_end_date'] && strtotime($milestone['planned_end_date']) < time() && $milestone['status'] !== 'completed') {
                $stats['overdue']++;
            }
        }

        $data = [
            'title' => 'Milestones',
            'milestones' => $milestones,
            'projects' => $this->projectModel->getActiveProjects(),
            'overdue_milestones' => $this->milestoneModel->getOverdueMilestones(),
            'filters' => [
                'project_id' => $projectId,
                'status' => $status
            ],
            'stats' => $stats
        ];

        return view('milestones/index', $data);
    }

    public function create()
    {
        $projectId = $this->request->getGet('project_id');
        
        // Get all active users for the assignment dropdown
        $users = $this->userModel->where('status', 'active')
                               ->where('company_id', session('company_id'))
                               ->findAll();

        $data = [
            'title' => 'Create New Milestone',
            'projects' => $this->projectModel->getActiveProjects(),
            'selected_project' => $projectId,
            'users' => $users
        ];

        return view('milestones/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'project_id' => 'required|numeric',
            'title' => 'required|min_length[3]|max_length[255]',
            'planned_end_date' => 'required|valid_date',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'status' => 'permit_empty|in_list[pending,in_progress,completed,cancelled]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'project_id' => $this->request->getPost('project_id'),
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'task_type' => 'task',
            'priority' => $this->request->getPost('priority'),
            'status' => $this->request->getPost('status') ?: 'pending',
            'planned_start_date' => $this->request->getPost('start_date') ?: null,
            'planned_end_date' => $this->request->getPost('planned_end_date'),
            'estimated_cost' => $this->request->getPost('estimated_cost') ?: 0,
            'actual_cost' => $this->request->getPost('actual_cost') ?: 0,
            'is_milestone' => 1,
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'progress_percentage' => $this->request->getPost('progress_percentage') ?: 0,
            'company_id' => session('company_id')
        ];

        log_message('info', 'Attempting to create milestone with data: ' . json_encode($data));

        $milestoneId = $this->milestoneModel->createMilestone($data);

        if ($milestoneId) {
            log_message('info', 'Milestone created successfully with ID: ' . $milestoneId);
            return redirect()->to('/admin/milestones')->with('success', 'Milestone created successfully');
        }

        log_message('error', 'Failed to create milestone. Data: ' . json_encode($data));
        return redirect()->back()->withInput()->with('error', 'Failed to create milestone');
    }

    public function show($id)
    {
        $milestone = $this->milestoneModel->select('tasks.*, projects.name as project_name, projects.project_code')
                                         ->join('projects', 'tasks.project_id = projects.id')
                                         ->where('tasks.id', $id)
                                         ->where('tasks.is_milestone', 1)
                                         ->first();

        if (!$milestone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Milestone not found');
        }

        // Get project details
        $project = $this->projectModel->find($milestone['project_id']);

        // Get assigned user details
        $assigned_user = null;
        if ($milestone['assigned_to']) {
            $assigned_user = $this->userModel->find($milestone['assigned_to']);
        }

        // Get related tasks (tasks that should be completed by this milestone)
        $relatedTasks = $this->taskModel->select('tasks.*, CONCAT(u.first_name, " ", u.last_name) as assigned_name')
                                       ->join('users u', 'tasks.assigned_to = u.id', 'left')
                                       ->where('tasks.project_id', $milestone['project_id'])
                                       ->where('tasks.planned_end_date <=', $milestone['planned_end_date'])
                                       ->where('tasks.id !=', $milestone['id']) // Exclude the milestone itself
                                       ->findAll();

        $data = [
            'title' => $milestone['title'],
            'milestone' => $milestone,
            'project' => $project,
            'assigned_user' => $assigned_user,
            'related_tasks' => $relatedTasks,
            'completion_stats' => $this->calculateMilestoneCompletion($milestone, $relatedTasks)
        ];

        return view('milestones/view', $data);
    }

    public function edit($id)
    {
        $milestone = $this->milestoneModel->find($id);

        if (!$milestone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Milestone not found');
        }
        
        // Get all active users for the assignment dropdown
        $users = $this->userModel->where('status', 'active')
                               ->where('company_id', session('company_id'))
                               ->findAll();

        $data = [
            'title' => 'Edit Milestone',
            'milestone' => $milestone,
            'projects' => $this->projectModel->getActiveProjects(),
            'users' => $users
        ];

        return view('milestones/edit', $data);
    }

    public function update($id)
    {
        log_message('info', 'Milestone update called for ID: ' . $id);

        $milestone = $this->milestoneModel->find($id);

        if (!$milestone || !$milestone['is_milestone']) {
            log_message('error', 'Milestone not found or not a milestone: ' . $id);
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Milestone not found');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'title' => 'required|min_length[3]|max_length[255]',
            'planned_end_date' => 'required|valid_date',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'status' => 'required|in_list[not_started,in_progress,completed,cancelled,on_hold]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            log_message('error', 'Validation failed: ' . json_encode($validation->getErrors()));
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'priority' => $this->request->getPost('priority'),
            'status' => $this->request->getPost('status'),
            'planned_start_date' => $this->request->getPost('planned_start_date'),
            'planned_end_date' => $this->request->getPost('planned_end_date'),
            'estimated_cost' => $this->request->getPost('estimated_cost') ?: 0,
            'actual_cost' => $this->request->getPost('actual_cost') ?: 0,
            'progress_percentage' => $this->request->getPost('progress_percentage') ?: 0
        ];

        // Auto-set actual_end_date if status is completed
        if ($data['status'] === 'completed' && empty($data['actual_end_date'])) {
            $data['actual_end_date'] = date('Y-m-d');
            $data['progress_percentage'] = 100;
        }

        log_message('info', 'Attempting to update milestone with data: ' . json_encode($data));

        if ($this->milestoneModel->updateMilestone($id, $data)) {
            log_message('info', 'Milestone updated successfully');
            // Update project progress
            $this->projectModel->updateProjectProgress($milestone['project_id']);

            return redirect()->to('/admin/milestones/' . $id)->with('success', 'Milestone updated successfully');
        }

        log_message('error', 'Failed to update milestone in database');
        return redirect()->back()->withInput()->with('error', 'Failed to update milestone');
    }

    public function complete($id)
    {
        if ($this->milestoneModel->completeMilestone($id)) {
            $milestone = $this->milestoneModel->find($id);
            $this->projectModel->updateProjectProgress($milestone['project_id']);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'message' => 'Milestone marked as completed']);
            }
            return redirect()->back()->with('success', 'Milestone marked as completed');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to complete milestone']);
        }
        return redirect()->back()->with('error', 'Failed to complete milestone');
    }

    public function updateProgress($id)
    {
        $milestone = $this->milestoneModel->find($id);

        if (!$milestone || !$milestone['is_milestone']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Milestone not found']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'progress_percentage' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'notes' => 'permit_empty|max_length[1000]'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->response->setJSON(['success' => false, 'message' => 'Validation failed: ' . implode(', ', $validation->getErrors())]);
        }

        $data = [
            'progress_percentage' => $this->request->getPost('progress_percentage'),
        ];

        // Auto-set status based on progress
        $progress = (int) $data['progress_percentage'];
        if ($progress == 100) {
            $data['status'] = 'completed';
            $data['actual_end_date'] = date('Y-m-d');
        } elseif ($progress > 0) {
            $data['status'] = 'in_progress';
            if (empty($milestone['actual_start_date'])) {
                $data['actual_start_date'] = date('Y-m-d');
            }
        }

        if ($this->milestoneModel->updateMilestone($id, $data)) {
            // Update project progress
            $this->projectModel->updateProjectProgress($milestone['project_id']);

            // Log the progress update if notes were provided
            $notes = $this->request->getPost('notes');
            if (!empty($notes)) {
                // You could add activity logging here if needed
                log_message('info', 'Milestone progress updated: ' . $id . ' - ' . $progress . '% - ' . $notes);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Progress updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update progress']);
    }

    public function delete($id)
    {
        $milestone = $this->milestoneModel->find($id);

        if (!$milestone || !$milestone['is_milestone']) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Milestone not found']);
            }
            return redirect()->back()->with('error', 'Milestone not found');
        }

        if ($this->milestoneModel->delete($id)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'message' => 'Milestone deleted successfully']);
            }
            return redirect()->back()->with('success', 'Milestone deleted successfully');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete milestone']);
        }
        return redirect()->back()->with('error', 'Failed to delete milestone');
    }

    public function upcoming()
    {
        $days = $this->request->getGet('days') ?: 30;
        $projectId = $this->request->getGet('project_id');

        $builder = $this->milestoneModel->select('tasks.*, projects.name as project_name')
                                       ->join('projects', 'tasks.project_id = projects.id')
                                       ->where('projects.company_id', session('company_id'))
                                       ->where('tasks.is_milestone', 1)
                                       ->where('tasks.planned_end_date <=', date('Y-m-d', strtotime('+' . $days . ' days')))
                                       ->where('tasks.planned_end_date >=', date('Y-m-d'))
                                       ->whereNotIn('tasks.status', ['completed', 'cancelled']);

        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }

        $milestones = $builder->orderBy('tasks.planned_end_date', 'ASC')->findAll();

        $data = [
            'title' => 'Upcoming Milestones',
            'milestones' => $milestones,
            'projects' => $this->projectModel->getActiveProjects(),
            'days' => $days,
            'project_id' => $projectId
        ];

        return view('milestones/upcoming', $data);
    }

    public function calendar()
    {
        $projectId = $this->request->getGet('project_id');

        $data = [
            'title' => 'Milestone Calendar',
            'projects' => $this->projectModel->getActiveProjects(),
            'project_id' => $projectId
        ];

        return view('milestones/calendar', $data);
    }

    public function report()
    {
        $projectId = $this->request->getGet('project_id');
        $dateFrom = $this->request->getGet('date_from') ?: date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?: date('Y-m-t');

        $builder = $this->milestoneModel->select('tasks.*, projects.name as project_name')
                                       ->join('projects', 'tasks.project_id = projects.id')
                                       ->where('projects.company_id', session('company_id'))
                                       ->where('tasks.is_milestone', 1)
                                       ->where('tasks.planned_end_date >=', $dateFrom)
                                       ->where('tasks.planned_end_date <=', $dateTo);

        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }

        $milestones = $builder->orderBy('tasks.planned_end_date', 'ASC')->findAll();

        // Calculate statistics
        $stats = [
            'total' => count($milestones),
            'completed' => count(array_filter($milestones, function($m) { return $m['status'] === 'completed'; })),
            'overdue' => count(array_filter($milestones, function($m) { 
                return $m['planned_end_date'] < date('Y-m-d') && $m['status'] !== 'completed'; 
            })),
            'upcoming' => count(array_filter($milestones, function($m) { 
                return $m['planned_end_date'] >= date('Y-m-d') && $m['status'] !== 'completed'; 
            }))
        ];

        $data = [
            'title' => 'Milestone Report',
            'milestones' => $milestones,
            'projects' => $this->projectModel->getActiveProjects(),
            'stats' => $stats,
            'filters' => [
                'project_id' => $projectId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]
        ];

        return view('milestones/report', $data);
    }

    public function exportPdf()
    {
        $projectId = $this->request->getGet('project_id');
        $dateFrom = $this->request->getGet('date_from') ?: date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?: date('Y-m-t');

        $builder = $this->milestoneModel->select('tasks.*, projects.name as project_name')
                                       ->join('projects', 'tasks.project_id = projects.id')
                                       ->where('projects.company_id', session('company_id'))
                                       ->where('tasks.is_milestone', 1)
                                       ->where('tasks.planned_end_date >=', $dateFrom)
                                       ->where('tasks.planned_end_date <=', $dateTo);

        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }

        $milestones = $builder->orderBy('tasks.planned_end_date', 'ASC')->findAll();

        // Calculate statistics
        $stats = [
            'total' => count($milestones),
            'completed' => count(array_filter($milestones, function($m) { return $m['status'] === 'completed'; })),
            'overdue' => count(array_filter($milestones, function($m) { 
                return $m['planned_end_date'] < date('Y-m-d') && $m['status'] !== 'completed'; 
            })),
            'upcoming' => count(array_filter($milestones, function($m) { 
                return $m['planned_end_date'] >= date('Y-m-d') && $m['status'] !== 'completed'; 
            }))
        ];

        $data = [
            'title' => 'Milestone Report',
            'milestones' => $milestones,
            'projects' => $this->projectModel->getActiveProjects(),
            'stats' => $stats,
            'filters' => [
                'project_id' => $projectId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ],
            'export_date' => date('F j, Y'),
            'company_name' => session('company_name') ?? 'Construction Management System'
        ];

        // Load the PDF library
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        // Set header and footer
        $mpdf->SetHTMLHeader('
            <table width="100%" style="border-bottom: 2px solid #333; padding-bottom: 10px;">
                <tr>
                    <td style="font-size: 16px; font-weight: bold; color: #333;">' . $data['company_name'] . '</td>
                    <td style="text-align: right; font-size: 12px; color: #666;">Report Generated: ' . $data['export_date'] . '</td>
                </tr>
                <tr>
                    <td style="font-size: 14px; color: #666;">Milestone Report</td>
                    <td style="text-align: right; font-size: 12px; color: #666;">Page {PAGENO} of {nbpg}</td>
                </tr>
            </table>
        ');

        $mpdf->SetHTMLFooter('
            <table width="100%" style="border-top: 1px solid #ccc; padding-top: 10px;">
                <tr>
                    <td style="font-size: 10px; color: #999;">Confidential Document</td>
                    <td style="text-align: right; font-size: 10px; color: #999;">Generated by Construction Management System</td>
                </tr>
            </table>
        ');

        // Generate the PDF content
        $html = view('milestones/report_pdf', $data);
        
        $mpdf->WriteHTML($html);
        
        // Output the PDF
        $filename = 'milestone_report_' . date('Y-m-d_H-i-s') . '.pdf';
        $mpdf->Output($filename, 'D'); // D for download
    }

    public function exportExcel()
    {
        $milestones = $this->milestoneModel->select('tasks.*, projects.name as project_name')
                                         ->join('projects', 'tasks.project_id = projects.id')
                                         ->where('projects.company_id', session('company_id'))
                                         ->where('tasks.is_milestone', 1)
                                         ->orderBy('tasks.planned_end_date', 'ASC')
                                         ->findAll();
        
        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set company header
        $sheet->setCellValue('A1', session('company_name') ?? 'Construction Management System');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set report title
        $sheet->setCellValue('A2', 'Milestone Report');
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set report date
        $sheet->setCellValue('A3', 'Generated: ' . date('F j, Y \a\t g:i A'));
        $sheet->mergeCells('A3:I3');
        $sheet->getStyle('A3')->getFont()->setItalic(true);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set headers
        $headers = [
            'No.', 'Milestone Title', 'Project Name', 'Status', 'Priority', 
            'Due Date', 'Completion Date', 'Progress (%)', 'Days Late', 'Assigned To'
        ];
        
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '5', $header);
            $sheet->getStyle($column . '5')->getFont()->setBold(true);
            $sheet->getStyle($column . '5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E3F2FD');
            $column++;
        }
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);   // No.
        $sheet->getColumnDimension('B')->setWidth(35);  // Milestone Title
        $sheet->getColumnDimension('C')->setWidth(30);  // Project Name
        $sheet->getColumnDimension('D')->setWidth(15);  // Status
        $sheet->getColumnDimension('E')->setWidth(12);  // Priority
        $sheet->getColumnDimension('F')->setWidth(15);  // Due Date
        $sheet->getColumnDimension('G')->setWidth(18);  // Completion Date
        $sheet->getColumnDimension('H')->setWidth(12);  // Progress
        $sheet->getColumnDimension('I')->setWidth(12);  // Days Late
        $sheet->getColumnDimension('J')->setWidth(25);  // Assigned To
        
        // Add data with numbering
        $row = 6;
        $counter = 1;
        foreach ($milestones as $milestone) {
            $daysLate = 0;
            if ($milestone['planned_end_date'] && $milestone['status'] !== 'completed') {
                $dueDate = strtotime($milestone['planned_end_date']);
                $today = strtotime(date('Y-m-d'));
                if ($today > $dueDate) {
                    $daysLate = floor(($today - $dueDate) / (60 * 60 * 24));
                }
            } elseif ($milestone['actual_end_date'] && $milestone['planned_end_date']) {
                $dueDate = strtotime($milestone['planned_end_date']);
                $completedDate = strtotime($milestone['actual_end_date']);
                if ($completedDate > $dueDate) {
                    $daysLate = floor(($completedDate - $dueDate) / (60 * 60 * 24));
                }
            }
            
            $assignedTo = '';
            if ($milestone['assigned_to']) {
                $user = $this->userModel->find($milestone['assigned_to']);
                if ($user) {
                    $assignedTo = $user['first_name'] . ' ' . $user['last_name'];
                }
            }
            
            // Add row number
            $sheet->setCellValue('A' . $row, $counter);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Add milestone data
            $sheet->setCellValue('B' . $row, $milestone['title']);
            $sheet->setCellValue('C' . $row, $milestone['project_name']);
            $sheet->setCellValue('D' . $row, ucwords(str_replace('_', ' ', $milestone['status'] ?: 'not_started')));
            $sheet->setCellValue('E' . $row, ucwords($milestone['priority']));
            $sheet->setCellValue('F' . $row, $milestone['planned_end_date'] ? date('M d, Y', strtotime($milestone['planned_end_date'])) : 'Not set');
            $sheet->setCellValue('G' . $row, $milestone['status'] === 'completed' && $milestone['actual_end_date'] ? date('M d, Y', strtotime($milestone['actual_end_date'])) : 'Not completed');
            $sheet->setCellValue('H' . $row, $milestone['progress_percentage']);
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('0');
            $sheet->setCellValue('I' . $row, $daysLate > 0 ? $daysLate : ($milestone['status'] === 'completed' && $daysLate <= 0 ? 0 : ''));
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('J' . $row, $assignedTo);
            
            $row++;
            $counter++;
        }
        
        // Add summary statistics
        $totalMilestones = count($milestones);
        $completedMilestones = count(array_filter($milestones, function($m) { return $m['status'] === 'completed'; }));
        $overdueMilestones = count(array_filter($milestones, function($m) { 
            return $m['planned_end_date'] < date('Y-m-d') && $m['status'] !== 'completed'; 
        }));
        $upcomingMilestones = count(array_filter($milestones, function($m) { 
            return $m['planned_end_date'] >= date('Y-m-d') && $m['status'] !== 'completed'; 
        }));
        
        $summaryRow = $row + 2;
        $sheet->setCellValue('A' . $summaryRow, 'Summary Statistics:');
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
        
        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Total Milestones:');
        $sheet->setCellValue('B' . $summaryRow, $totalMilestones);
        $sheet->getStyle('B' . $summaryRow)->getFont()->setBold(true);
        
        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Completed:');
        $sheet->setCellValue('B' . $summaryRow, $completedMilestones);
        $sheet->getStyle('B' . $summaryRow)->getFont()->setBold(true);
        
        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Overdue:');
        $sheet->setCellValue('B' . $summaryRow, $overdueMilestones);
        $sheet->getStyle('B' . $summaryRow)->getFont()->setBold(true);
        
        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Upcoming:');
        $sheet->setCellValue('B' . $summaryRow, $upcomingMilestones);
        $sheet->getStyle('B' . $summaryRow)->getFont()->setBold(true);
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'milestone_report_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    public function previewPdf()
    {
        $projectId = $this->request->getGet('project_id');
        $dateFrom = $this->request->getGet('date_from') ?: date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?: date('Y-m-t');

        $builder = $this->milestoneModel->select('tasks.*, projects.name as project_name')
                                       ->join('projects', 'tasks.project_id = projects.id')
                                       ->where('projects.company_id', session('company_id'))
                                       ->where('tasks.is_milestone', 1)
                                       ->where('tasks.planned_end_date >=', $dateFrom)
                                       ->where('tasks.planned_end_date <=', $dateTo);

        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }

        $milestones = $builder->orderBy('tasks.planned_end_date', 'ASC')->findAll();

        // Calculate statistics
        $stats = [
            'total' => count($milestones),
            'completed' => count(array_filter($milestones, function($m) { return $m['status'] === 'completed'; })),
            'overdue' => count(array_filter($milestones, function($m) { 
                return $m['planned_end_date'] < date('Y-m-d') && $m['status'] !== 'completed'; 
            })),
            'upcoming' => count(array_filter($milestones, function($m) { 
                return $m['planned_end_date'] >= date('Y-m-d') && $m['status'] !== 'completed'; 
            }))
        ];

        $data = [
            'title' => 'Milestone Report',
            'milestones' => $milestones,
            'projects' => $this->projectModel->getActiveProjects(),
            'stats' => $stats,
            'filters' => [
                'project_id' => $projectId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ],
            'export_date' => date('F j, Y'),
            'company_name' => session('company_name') ?? 'Construction Management System'
        ];

        // Load the PDF library
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        // Set header and footer
        $mpdf->SetHTMLHeader('
            <table width="100%" style="border-bottom: 2px solid #333; padding-bottom: 10px;">
                <tr>
                    <td style="font-size: 16px; font-weight: bold; color: #333;">' . $data['company_name'] . '</td>
                    <td style="text-align: right; font-size: 12px; color: #666;">Report Generated: ' . $data['export_date'] . '</td>
                </tr>
                <tr>
                    <td style="font-size: 14px; color: #666;">Milestone Report</td>
                    <td style="text-align: right; font-size: 12px; color: #666;">Page {PAGENO} of {nbpg}</td>
                </tr>
            </table>
        ');

        $mpdf->SetHTMLFooter('
            <table width="100%" style="border-top: 1px solid #ccc; padding-top: 10px;">
                <tr>
                    <td style="font-size: 10px; color: #999;">Confidential Document</td>
                    <td style="text-align: right; font-size: 10px; color: #999;">Generated by Construction Management System</td>
                </tr>
            </table>
        ');

        // Generate the PDF content
        $html = view('milestones/report_pdf', $data);
        
        $mpdf->WriteHTML($html);
        
        // Output the PDF for viewing (I for inline)
        $filename = 'milestone_report_' . date('Y-m-d_H-i-s') . '.pdf';
        $mpdf->Output($filename, 'I'); // I for inline viewing
    }

    private function calculateMilestoneCompletion($milestone, $relatedTasks)
    {
        $totalTasks = count($relatedTasks);
        $completedTasks = count(array_filter($relatedTasks, function($task) {
            return $task['status'] === 'completed';
        }));

        $completionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'completion_rate' => round($completionRate, 2),
            'is_ready_for_completion' => $completedTasks === $totalTasks
        ];
    }

    private function prepareCalendarEvents($milestones)
    {
        $events = [];
        
        foreach ($milestones as $milestone) {
            $events[] = [
                'id' => $milestone['id'],
                'title' => $milestone['title'],
                'start' => $milestone['planned_end_date'],
                'end' => $milestone['planned_end_date'],
                'className' => $this->getMilestoneStatusClass($milestone['status']),
                'url' => '/milestones/' . $milestone['id'],
                'rendering' => 'background'
            ];
        }
        
        return $events;
    }

    public function getProjectMilestones($projectId)
    {
        // Validate project access
        $project = $this->projectModel->where('id', $projectId)
                                     ->where('company_id', session('company_id'))
                                     ->first();
        
        if (!$project) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Project not found or access denied',
                'milestones' => []
            ]);
        }

        // Get milestones for the project
        $milestones = $this->milestoneModel->select('id, title, planned_end_date, status')
                                          ->where('project_id', $projectId)
                                          ->where('is_milestone', 1)
                                          ->orderBy('planned_end_date', 'ASC')
                                          ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'milestones' => $milestones
        ]);
    }

    private function getMilestoneStatusClass($status)
    {
        $classes = [
            'not_started' => 'fc-event-info',
            'in_progress' => 'fc-event-warning',
            'completed' => 'fc-event-success',
            'cancelled' => 'fc-event-danger',
            'on_hold' => 'fc-event-secondary'
        ];

        return $classes[$status] ?? 'fc-event-default';
    }

    /**
     * API endpoint for calendar events
     */
    public function apiCalendarEvents()
    {
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');

        $milestones = $this->milestoneModel->select('tasks.*, projects.name as project_name, users.first_name, users.last_name')
                                          ->join('projects', 'tasks.project_id = projects.id')
                                          ->join('users', 'tasks.assigned_to = users.id', 'left')
                                          ->where('projects.company_id', session('company_id'))
                                          ->where('tasks.is_milestone', 1)
                                          ->where('tasks.planned_end_date IS NOT NULL');

        if ($start && $end) {
            $milestones->groupStart()
                      ->where('tasks.planned_end_date >=', $start)
                      ->where('tasks.planned_end_date <=', $end)
                      ->groupEnd();
        }

        $milestones = $milestones->findAll();

        // Format the assigned user name for the frontend
        foreach ($milestones as &$milestone) {
            if ($milestone['first_name'] && $milestone['last_name']) {
                $milestone['assigned_to_name'] = $milestone['first_name'] . ' ' . $milestone['last_name'];
            } else {
                $milestone['assigned_to_name'] = 'Unassigned';
            }
        }

        return $this->response->setJSON(['milestones' => $milestones]);
    }
}
