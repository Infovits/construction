<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\ConversationModel;
use App\Models\MessageModel;
use App\Libraries\DomPDFWrapper;
use App\Libraries\ExcelExport;

class Reports extends BaseController
{
    public function index()
    {
        $userId = session('user_id');
        $companyId = session('company_id');

        // Initialize models
        $userModel = new UserModel();
        $projectModel = new ProjectModel();
        $taskModel = new TaskModel();
        $conversationModel = new ConversationModel();
        $messageModel = new MessageModel();

        // Get recent reports - from session first, then database
        $sessionReports = session()->get('recent_reports') ?? [];
        $dbReports = $this->getRecentReports($companyId);
        
        // Merge both, with session reports at top
        $recentReports = array_values(array_unique(
            array_merge($sessionReports, $dbReports),
            SORT_REGULAR
        ));
        $recentReports = array_slice($recentReports, 0, 5); // Keep top 5

        // Get recent reports (sample data)
        $data = [
            'title' => 'Reports',
            'recent_reports' => $recentReports,
            'user_stats' => $this->getUserStats($companyId),
            'project_stats' => $this->getProjectStats($companyId),
            'task_stats' => $this->getTaskStats($companyId),
        ];

        return view('admin/reports/index', $data);
    }

    /**
     * Generate a custom report
     */
    public function generate()
    {
        if ($this->request->getMethod() === 'post') {
            $type = $this->request->getPost('report_type');
            $dateRange = $this->request->getPost('date_range');
            $department = $this->request->getPost('department');
            $format = $this->request->getPost('format');

            // Validate inputs
            if (!$type || !$dateRange || !$format) {
                return redirect()->back()->with('error', 'Please fill in all required fields');
            }

            // Generate report based on type
            $reportData = $this->generateReportByType($type, $dateRange, $department);

            // Check if report data is empty
            if (empty($reportData)) {
                return redirect()->back()->with('error', 'No data available for the selected report');
            }

            // Store report in session for recent reports display
            $session = session();
            $recentReports = $session->get('recent_reports') ?? [];
            
            // Add current report to beginning of array
            array_unshift($recentReports, [
                'id' => uniqid(),
                'name' => $reportData['title'] ?? 'Report',
                'type' => $type,
                'date_range' => $dateRange,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'Generated'
            ]);
            
            // Keep only last 10 reports
            $recentReports = array_slice($recentReports, 0, 10);
            $session->set('recent_reports', $recentReports);

            // If format is a display format (view), show the results page
            if ($format === 'view') {
                $data = [
                    'title' => 'Report Results',
                    'report_title' => $reportData['title'] ?? 'Report',
                    'report_type' => $type,
                    'date_range' => $dateRange,
                    'department' => $department ?? '',
                    'report_data' => $reportData['data'] ?? []
                ];
                return view('admin/reports/results', $data);
            }

            // Export based on format
            return $this->exportReport($reportData, $format, $type);
        }

        return redirect()->back();
    }

    private function generateReportByType($type, $dateRange, $department)
    {
        $companyId = session('company_id');

        switch ($type) {
            case 'messaging_activity':
                return $this->getMessagingActivityReport($companyId, $dateRange);
            case 'project_performance':
                return $this->getProjectPerformanceReport($companyId, $dateRange);
            case 'task_summary':
                return $this->getTaskSummaryReport($companyId, $dateRange);
            case 'user_engagement':
                return $this->getUserEngagementReport($companyId, $dateRange);
            case 'client_summary':
                return $this->getClientSummaryReport($companyId, $dateRange);
            case 'supplier_summary':
                return $this->getSupplierSummaryReport($companyId, $dateRange);
            case 'material_usage':
                return $this->getMaterialUsageReport($companyId, $dateRange);
            case 'purchase_orders':
                return $this->getPurchaseOrdersReport($companyId, $dateRange);
            case 'warehouse_inventory':
                return $this->getWarehouseInventoryReport($companyId, $dateRange);
            default:
                return [];
        }
    }

    private function getMessagingActivityReport($companyId, $dateRange)
    {
        $messageModel = new MessageModel();
        $startDate = $this->getStartDate($dateRange);

        // Use the model's method that properly joins with conversations
        $messages = $messageModel->getMessagesByDateRange($companyId, $startDate, date('Y-m-d H:i:s'));

        return [
            'title' => 'Messaging Activity Report',
            'type' => 'messaging_activity',
            'date_range' => $dateRange,
            'total_messages' => count($messages),
            'data' => $messages
        ];
    }

    private function getProjectPerformanceReport($companyId, $dateRange)
    {
        $projectModel = new ProjectModel();
        $startDate = $this->getStartDate($dateRange);

        $projects = $projectModel->where('company_id', $companyId)
            ->where('created_at >=', $startDate)
            ->get()
            ->getResultArray();

        return [
            'title' => 'Project Performance Report',
            'type' => 'project_performance',
            'date_range' => $dateRange,
            'total_projects' => count($projects),
            'data' => $projects
        ];
    }

    private function getTaskSummaryReport($companyId, $dateRange)
    {
        $taskModel = new TaskModel();
        $startDate = $this->getStartDate($dateRange);

        $tasks = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.created_at >=', $startDate)
            ->get()
            ->getResultArray();

        return [
            'title' => 'Task Summary Report',
            'type' => 'task_summary',
            'date_range' => $dateRange,
            'total_tasks' => count($tasks),
            'data' => $tasks
        ];
    }

    private function getUserEngagementReport($companyId, $dateRange)
    {
        $userModel = new UserModel();
        $messageModel = new MessageModel();
        $startDate = $this->getStartDate($dateRange);

        $users = $userModel->where('company_id', $companyId)->get()->getResultArray();

        $engagementData = [];
        foreach ($users as $user) {
            $messageCount = $messageModel->where('sender_id', $user['id'])
                ->where('created_at >=', $startDate)
                ->countAllResults();

            $engagementData[] = [
                'user_id' => $user['id'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'message_count' => $messageCount,
                'engagement_level' => $messageCount > 20 ? 'High' : ($messageCount > 5 ? 'Medium' : 'Low')
            ];
        }

        return [
            'title' => 'User Engagement Report',
            'type' => 'user_engagement',
            'date_range' => $dateRange,
            'data' => $engagementData
        ];
    }

    private function getClientSummaryReport($companyId, $dateRange)
    {
        $clientModel = new \App\Models\ClientModel();
        $startDate = $this->getStartDate($dateRange);

        $clients = $clientModel->where('company_id', $companyId)
            ->where('created_at >=', $startDate)
            ->get()->getResultArray();

        return [
            'title' => 'Client Summary Report',
            'type' => 'client_summary',
            'date_range' => $dateRange,
            'data' => $clients
        ];
    }

    private function getSupplierSummaryReport($companyId, $dateRange)
    {
        $supplierModel = new \App\Models\SupplierModel();
        $startDate = $this->getStartDate($dateRange);

        $suppliers = $supplierModel->where('company_id', $companyId)
            ->where('created_at >=', $startDate)
            ->get()->getResultArray();

        return [
            'title' => 'Supplier Summary Report',
            'type' => 'supplier_summary',
            'date_range' => $dateRange,
            'data' => $suppliers
        ];
    }

    private function getMaterialUsageReport($companyId, $dateRange)
    {
        $materialModel = new \App\Models\MaterialModel();
        $startDate = $this->getStartDate($dateRange);

        $materials = $materialModel->where('company_id', $companyId)
            ->where('created_at >=', $startDate)
            ->get()->getResultArray();

        return [
            'title' => 'Material Usage Report',
            'type' => 'material_usage',
            'date_range' => $dateRange,
            'data' => $materials
        ];
    }

    private function getPurchaseOrdersReport($companyId, $dateRange)
    {
        $poModel = new \App\Models\PurchaseOrderModel();
        $startDate = $this->getStartDate($dateRange);

        $orders = $poModel->where('company_id', $companyId)
            ->where('created_at >=', $startDate)
            ->get()->getResultArray();

        return [
            'title' => 'Purchase Orders Report',
            'type' => 'purchase_orders',
            'date_range' => $dateRange,
            'data' => $orders
        ];
    }

    private function getWarehouseInventoryReport($companyId, $dateRange)
    {
        $warehouseModel = new \App\Models\WarehouseModel();
        $startDate = $this->getStartDate($dateRange);

        $inventory = $warehouseModel->where('company_id', $companyId)
            ->where('created_at >=', $startDate)
            ->get()->getResultArray();

        return [
            'title' => 'Warehouse Inventory Report',
            'type' => 'warehouse_inventory',
            'date_range' => $dateRange,
            'data' => $inventory
        ];
    }

    private function getStartDate($dateRange)
    {
        switch ($dateRange) {
            case 'this_week':
                return date('Y-m-d', strtotime('Monday this week'));
            case 'this_month':
                return date('Y-m-01');
            case 'last_90_days':
                return date('Y-m-d', strtotime('-90 days'));
            case 'this_year':
                return date('Y-01-01');
            default:
                return date('Y-m-01');
        }
    }

    private function exportReport($reportData, $format, $type)
    {
        switch ($format) {
            case 'pdf':
                return $this->exportPDF($reportData);
            case 'excel':
                return $this->exportExcel($reportData);
            case 'csv':
                return $this->exportCSV($reportData);
            case 'html':
                return $this->exportHTML($reportData);
            default:
                return redirect()->back();
        }
    }

    private function exportPDF($reportData)
    {
        // Get company information from database
        $companyId = session('company_id');
        $companyModel = new \App\Models\CompanyModel();
        $company = $companyModel->find($companyId);
        
        // Get system settings
        $settingModel = new \App\Models\SettingModel();
        $allSettings = $settingModel->getSystemSettings($companyId);
        
        // Extract general settings
        $generalSettings = $allSettings['general'] ?? [];
        
        // Remove the section prefix from keys
        $cleanSettings = [];
        foreach ($generalSettings as $key => $value) {
            $cleanKey = str_replace('general_', '', $key);
            $cleanSettings[$cleanKey] = $value;
        }
        
        // Build comprehensive company info with system settings
        $companyInfo = [
            'name' => $cleanSettings['company_name'] ?? $company['name'] ?? 'Construction Management System',
            'address' => ($company['address'] ?? '') . (($company['city'] ?? '') ? ', ' . $company['city'] : ''),
            'phone' => $company['phone'] ?? '',
            'email' => $company['email'] ?? '',
            'logo_url' => $cleanSettings['company_logo'] ?? $company['logo_url'] ?? null,
            'currency' => $cleanSettings['currency'] ?? 'USD',
            'timezone' => $cleanSettings['timezone'] ?? 'UTC',
            'date_format' => $cleanSettings['date_format'] ?? 'Y-m-d',
            'website' => $company['website'] ?? ''
        ];

        // Initialize PDF wrapper
        $pdf = new DomPDFWrapper($companyInfo);

        // Generate HTML content for the PDF
        $html = $this->generateReportPDFContent($reportData, $companyInfo);

        // Generate and return PDF
        $title = preg_replace('/[^a-zA-Z0-9-_]/', '_', $reportData['title'] ?? 'Report');
        $filename = $title . '-' . date('Y-m-d') . '.pdf';
        return $pdf->generatePdf($html, $filename, 'D');
    }

    /**
     * Generate HTML content for report PDF
     */
    private function generateReportPDFContent($reportData, $companyInfo = [])
    {
        $html = '<!DOCTYPE html>';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<style>';
        $html .= $this->getPDFStyles();
        $html .= '</style>';
        $html .= '</head>';
        $html .= '<body>';

        // Company Header with Logo and Info
        $html .= '<div class="company-header">';
        if (!empty($companyInfo['logo_url'])) {
            $html .= '<img src="' . esc($companyInfo['logo_url']) . '" class="company-logo" alt="Company Logo">';
        }
        $html .= '<div class="company-info">';
        $html .= '<h2 class="company-name">' . esc($companyInfo['name'] ?? 'Construction Management System') . '</h2>';
        if (!empty($companyInfo['address'])) {
            $html .= '<p class="company-detail">' . esc($companyInfo['address']) . '</p>';
        }
        if (!empty($companyInfo['phone'])) {
            $html .= '<p class="company-detail">Phone: ' . esc($companyInfo['phone']) . '</p>';
        }
        if (!empty($companyInfo['email'])) {
            $html .= '<p class="company-detail">Email: ' . esc($companyInfo['email']) . '</p>';
        }
        if (!empty($companyInfo['website'])) {
            $html .= '<p class="company-detail">Website: ' . esc($companyInfo['website']) . '</p>';
        }
        $html .= '</div>';
        $html .= '<div class="system-info">';
        if (!empty($companyInfo['currency'])) {
            $html .= '<p class="detail-text"><strong>Currency:</strong> ' . esc($companyInfo['currency']) . '</p>';
        }
        if (!empty($companyInfo['timezone'])) {
            $html .= '<p class="detail-text"><strong>Timezone:</strong> ' . esc($companyInfo['timezone']) . '</p>';
        }
        $html .= '</div>';
        $html .= '</div>';

        // Report Title
        $html .= '<div class="header">';
        $html .= '<h1>' . esc($reportData['title'] ?? 'Report') . '</h1>';
        $html .= '<p class="generated-date">Generated: ' . date('Y-m-d H:i:s') . '</p>';
        $html .= '</div>';

        // Report Content based on type
        switch ($reportData['type'] ?? null) {
            case 'messaging_activity':
                $html .= $this->generateMessagingActivityPDF($reportData);
                break;
            case 'project_performance':
                $html .= $this->generateProjectPerformancePDF($reportData);
                break;
            case 'task_summary':
                $html .= $this->generateTaskSummaryPDF($reportData);
                break;
            case 'user_engagement':
                $html .= $this->generateUserEngagementPDF($reportData);
                break;
            case 'client_summary':
                $html .= $this->generateClientSummaryPDF($reportData);
                break;
            case 'supplier_summary':
                $html .= $this->generateSupplierSummaryPDF($reportData);
                break;
            case 'material_usage':
                $html .= $this->generateMaterialUsagePDF($reportData);
                break;
            case 'purchase_orders':
                $html .= $this->generatePurchaseOrdersPDF($reportData);
                break;
            case 'warehouse_inventory':
                $html .= $this->generateWarehouseInventoryPDF($reportData);
                break;
            default:
                $html .= '<p>No report content available</p>';
        }

        $html .= '</body>';
        $html .= '</html>';

        return $html;
    }

    /**
     * Generate messaging activity PDF content
     */
    private function generateMessagingActivityPDF($reportData)
    {
        $html = '<div class="section">';
        $html .= '<h2>Summary</h2>';
        $html .= '<table class="summary-table">';
        $html .= '<tr>';
        $html .= '<td><strong>Total Messages:</strong></td>';
        $html .= '<td>' . ($reportData['total_messages'] ?? 0) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Date Range:</strong></td>';
        $html .= '<td>' . esc($reportData['date_range'] ?? 'N/A') . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';

        if (!empty($reportData['data'])) {
            $html .= '<div class="section">';
            $html .= '<h2>Message Details</h2>';
            $html .= '<table class="data-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>ID</th>';
            $html .= '<th>Created At</th>';
            $html .= '<th>Body (First 50 chars)</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($reportData['data'] as $message) {
                $html .= '<tr>';
                $html .= '<td>' . esc($message['id'] ?? '') . '</td>';
                $html .= '<td>' . esc(date('Y-m-d H:i', strtotime($message['created_at'] ?? ''))) . '</td>';
                $html .= '<td>' . esc(substr($message['body'] ?? '', 0, 50)) . '...</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Generate project performance PDF content
     */
    private function generateProjectPerformancePDF($reportData)
    {
        $html = '<div class="section">';
        $html .= '<h2>Summary</h2>';
        $html .= '<table class="summary-table">';
        $html .= '<tr>';
        $html .= '<td><strong>Total Projects:</strong></td>';
        $html .= '<td>' . ($reportData['total_projects'] ?? 0) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Date Range:</strong></td>';
        $html .= '<td>' . esc($reportData['date_range'] ?? 'N/A') . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';

        if (!empty($reportData['data'])) {
            $html .= '<div class="section">';
            $html .= '<h2>Project Details</h2>';
            $html .= '<table class="data-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>Project Name</th>';
            $html .= '<th>Status</th>';
            $html .= '<th>Created</th>';
            $html .= '<th>Due Date</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($reportData['data'] as $project) {
                $html .= '<tr>';
                $html .= '<td>' . esc($project['name'] ?? '') . '</td>';
                $html .= '<td>' . esc($project['status'] ?? '') . '</td>';
                $html .= '<td>' . esc(date('Y-m-d', strtotime($project['created_at'] ?? ''))) . '</td>';
                $html .= '<td>' . esc(date('Y-m-d', strtotime($project['end_date'] ?? ''))) . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Generate task summary PDF content
     */
    private function generateTaskSummaryPDF($reportData)
    {
        $html = '<div class="section">';
        $html .= '<h2>Summary</h2>';
        $html .= '<table class="summary-table">';
        $html .= '<tr>';
        $html .= '<td><strong>Total Tasks:</strong></td>';
        $html .= '<td>' . ($reportData['total_tasks'] ?? 0) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Date Range:</strong></td>';
        $html .= '<td>' . esc($reportData['date_range'] ?? 'N/A') . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';

        if (!empty($reportData['data'])) {
            $html .= '<div class="section">';
            $html .= '<h2>Task Details</h2>';
            $html .= '<table class="data-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>Task Title</th>';
            $html .= '<th>Status</th>';
            $html .= '<th>Assigned To</th>';
            $html .= '<th>Due Date</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($reportData['data'] as $task) {
                $html .= '<tr>';
                $html .= '<td>' . esc($task['title'] ?? '') . '</td>';
                $html .= '<td>' . esc($task['status'] ?? '') . '</td>';
                $html .= '<td>' . esc($task['assigned_to'] ?? 'N/A') . '</td>';
                $html .= '<td>' . esc(date('Y-m-d', strtotime($task['planned_end_date'] ?? ''))) . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Generate user engagement PDF content
     */
    private function generateUserEngagementPDF($reportData)
    {
        $html = '<div class="section">';
        $html .= '<h2>User Engagement Report</h2>';
        $html .= '<p><strong>Date Range:</strong> ' . esc($reportData['date_range'] ?? 'N/A') . '</p>';
        $html .= '</div>';

        if (!empty($reportData['data'])) {
            $html .= '<div class="section">';
            $html .= '<h2>User Engagement Details</h2>';
            $html .= '<table class="data-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>User Name</th>';
            $html .= '<th>Message Count</th>';
            $html .= '<th>Engagement Level</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($reportData['data'] as $user) {
                $html .= '<tr>';
                $html .= '<td>' . esc($user['name'] ?? '') . '</td>';
                $html .= '<td>' . ($user['message_count'] ?? 0) . '</td>';
                $html .= '<td>' . esc($user['engagement_level'] ?? 'N/A') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }

        return $html;
    }

    private function generateClientSummaryPDF($reportData)
    {
        $html = '<div class="section">';
        $html .= '<h2>Client Summary Report</h2>';
        $html .= '<p><strong>Date Range:</strong> ' . esc($reportData['date_range'] ?? 'N/A') . '</p>';
        $html .= '</div>';

        if (!empty($reportData['data'])) {
            $html .= '<div class="section">';
            $html .= '<table class="data-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>Client Name</th>';
            $html .= '<th>Email</th>';
            $html .= '<th>Phone</th>';
            $html .= '<th>Status</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($reportData['data'] as $client) {
                $html .= '<tr>';
                $html .= '<td>' . esc($client['name'] ?? '') . '</td>';
                $html .= '<td>' . esc($client['email'] ?? '') . '</td>';
                $html .= '<td>' . esc($client['phone'] ?? '') . '</td>';
                $html .= '<td>' . esc($client['status'] ?? 'Active') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }

        return $html;
    }

    private function generateSupplierSummaryPDF($reportData)
    {
        $html = '<div class="section">';
        $html .= '<h2>Supplier Summary Report</h2>';
        $html .= '<p><strong>Date Range:</strong> ' . esc($reportData['date_range'] ?? 'N/A') . '</p>';
        $html .= '</div>';

        if (!empty($reportData['data'])) {
            $html .= '<div class="section">';
            $html .= '<table class="data-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>Supplier Name</th>';
            $html .= '<th>Contact</th>';
            $html .= '<th>Category</th>';
            $html .= '<th>Status</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($reportData['data'] as $supplier) {
                $html .= '<tr>';
                $html .= '<td>' . esc($supplier['name'] ?? '') . '</td>';
                $html .= '<td>' . esc($supplier['contact_person'] ?? '') . '</td>';
                $html .= '<td>' . esc($supplier['category'] ?? '') . '</td>';
                $html .= '<td>' . esc($supplier['status'] ?? 'Active') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }

        return $html;
    }

    private function generateMaterialUsagePDF($reportData)
    {
        $html = '<div class="section">';
        $html .= '<h2>Material Usage Report</h2>';
        $html .= '<p><strong>Date Range:</strong> ' . esc($reportData['date_range'] ?? 'N/A') . '</p>';
        $html .= '</div>';

        if (!empty($reportData['data'])) {
            $html .= '<div class="section">';
            $html .= '<table class="data-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>Material Name</th>';
            $html .= '<th>Category</th>';
            $html .= '<th>Quantity</th>';
            $html .= '<th>Unit</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($reportData['data'] as $material) {
                $html .= '<tr>';
                $html .= '<td>' . esc($material['name'] ?? '') . '</td>';
                $html .= '<td>' . esc($material['category'] ?? '') . '</td>';
                $html .= '<td>' . esc($material['quantity'] ?? '0') . '</td>';
                $html .= '<td>' . esc($material['unit'] ?? '') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }

        return $html;
    }

    private function generatePurchaseOrdersPDF($reportData)
    {
        $html = '<div class="section">';
        $html .= '<h2>Purchase Orders Report</h2>';
        $html .= '<p><strong>Date Range:</strong> ' . esc($reportData['date_range'] ?? 'N/A') . '</p>';
        $html .= '</div>';

        if (!empty($reportData['data'])) {
            $html .= '<div class="section">';
            $html .= '<table class="data-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>PO Number</th>';
            $html .= '<th>Supplier</th>';
            $html .= '<th>Total Amount</th>';
            $html .= '<th>Status</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($reportData['data'] as $order) {
                $html .= '<tr>';
                $html .= '<td>' . esc($order['po_number'] ?? '') . '</td>';
                $html .= '<td>' . esc($order['supplier_id'] ?? '') . '</td>';
                $html .= '<td>' . number_format($order['total_amount'] ?? 0, 2) . '</td>';
                $html .= '<td>' . esc($order['status'] ?? 'Pending') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }

        return $html;
    }

    private function generateWarehouseInventoryPDF($reportData)
    {
        $html = '<div class="section">';
        $html .= '<h2>Warehouse Inventory Report</h2>';
        $html .= '<p><strong>Date Range:</strong> ' . esc($reportData['date_range'] ?? 'N/A') . '</p>';
        $html .= '</div>';

        if (!empty($reportData['data'])) {
            $html .= '<div class="section">';
            $html .= '<table class="data-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>Warehouse Name</th>';
            $html .= '<th>Location</th>';
            $html .= '<th>Capacity</th>';
            $html .= '<th>Status</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($reportData['data'] as $warehouse) {
                $html .= '<tr>';
                $html .= '<td>' . esc($warehouse['name'] ?? '') . '</td>';
                $html .= '<td>' . esc($warehouse['location'] ?? '') . '</td>';
                $html .= '<td>' . esc($warehouse['capacity'] ?? '0') . '</td>';
                $html .= '<td>' . esc($warehouse['status'] ?? 'Active') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Get PDF styles
     */
    private function getPDFStyles()
    {
        return '
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 20px;
        }
        .company-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4F46E5;
        }
        .company-logo {
            max-width: 80px;
            max-height: 80px;
            margin-right: 20px;
        }
        .company-info {
            flex: 2;
        }
        .system-info {
            flex: 1;
            text-align: right;
        }
        .company-name {
            margin: 0 0 5px 0;
            color: #1F2937;
            font-size: 18px;
            font-weight: bold;
        }
        .company-detail {
            margin: 2px 0;
            font-size: 11px;
            color: #6B7280;
        }
        .detail-text {
            margin: 2px 0;
            font-size: 10px;
            color: #6B7280;
        }
        .header {
            border-bottom: 2px solid #4F46E5;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            color: #1F2937;
            font-size: 24px;
        }
        .generated-date {
            margin: 0;
            font-size: 12px;
            color: #6B7280;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section h2 {
            color: #1F2937;
            font-size: 16px;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 8px;
        }
        .summary-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .summary-table tr {
            border-bottom: 1px solid #E5E7EB;
        }
        .summary-table td {
            padding: 8px 0;
        }
        .summary-table td:first-child {
            width: 40%;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .data-table thead {
            background-color: #F3F4F6;
        }
        .data-table thead th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            color: #1F2937;
            border-bottom: 2px solid #D1D5DB;
        }
        .data-table tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #E5E7EB;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        ';
    }

    private function exportExcel($reportData)
    {
        // Prepare data for Excel export based on report type
        $rows = [];
        $headers = [];

        switch ($reportData['type'] ?? null) {
            case 'messaging_activity':
                $headers = ['Message ID', 'Created At', 'Sender ID', 'Message Body'];
                if (!empty($reportData['data'])) {
                    foreach ($reportData['data'] as $message) {
                        $rows[] = [
                            $message['id'] ?? '',
                            $message['created_at'] ?? '',
                            $message['sender_id'] ?? '',
                            substr($message['body'] ?? '', 0, 100)
                        ];
                    }
                }
                break;

            case 'project_performance':
                $headers = ['Project Name', 'Status', 'Created', 'Due Date', 'Budget', 'Progress'];
                if (!empty($reportData['data'])) {
                    foreach ($reportData['data'] as $project) {
                        $rows[] = [
                            $project['name'] ?? '',
                            $project['status'] ?? '',
                            date('Y-m-d', strtotime($project['created_at'] ?? '')),
                            date('Y-m-d', strtotime($project['end_date'] ?? '')),
                            $project['budget'] ?? 0,
                            $project['progress'] ?? 0
                        ];
                    }
                }
                break;

            case 'task_summary':
                $headers = ['Task Title', 'Status', 'Assigned To', 'Due Date', 'Priority', 'Progress'];
                if (!empty($reportData['data'])) {
                    foreach ($reportData['data'] as $task) {
                        $rows[] = [
                            $task['title'] ?? '',
                            $task['status'] ?? '',
                            $task['assigned_to'] ?? 'N/A',
                            date('Y-m-d', strtotime($task['planned_end_date'] ?? '')),
                            $task['priority'] ?? 'Medium',
                            $task['progress'] ?? 0
                        ];
                    }
                }
                break;

            case 'user_engagement':
                $headers = ['User Name', 'Message Count', 'Engagement Level'];
                if (!empty($reportData['data'])) {
                    foreach ($reportData['data'] as $user) {
                        $rows[] = [
                            $user['name'] ?? '',
                            $user['message_count'] ?? 0,
                            $user['engagement_level'] ?? 'N/A'
                        ];
                    }
                }
                break;

            default:
                return false;
        }

        // Create Excel export
        $excelExport = new ExcelExport();
        $excelExport->setTitle($reportData['title'] ?? 'Report');
        $excelExport->setHeaders($headers);
        $excelExport->setData($rows);
        $excelExport->setGeneratedDate(date('Y-m-d H:i:s'));

        // Generate filename
        $title = preg_replace('/[^a-zA-Z0-9-_]/', '_', $reportData['title'] ?? 'Report');
        $filename = $title . '-' . date('Y-m-d') . '.xlsx';

        // Export to file
        $excelExport->exportToFile($filename);
        
        // This line won't be reached because exportToFile calls exit
        return true;
    }

    private function exportCSV($reportData)
    {
        $rows = [];
        $headers = [];

        // Prepare data based on report type
        switch ($reportData['type'] ?? null) {
            case 'messaging_activity':
                $headers = ['Message ID', 'Created At', 'Sender ID', 'Message Body'];
                if (!empty($reportData['data'])) {
                    foreach ($reportData['data'] as $message) {
                        $rows[] = [
                            $message['id'] ?? '',
                            $message['created_at'] ?? '',
                            $message['sender_id'] ?? '',
                            substr($message['body'] ?? '', 0, 100)
                        ];
                    }
                }
                break;

            case 'project_performance':
                $headers = ['Project Name', 'Status', 'Created', 'Due Date'];
                if (!empty($reportData['data'])) {
                    foreach ($reportData['data'] as $project) {
                        $rows[] = [
                            $project['name'] ?? '',
                            $project['status'] ?? '',
                            date('Y-m-d', strtotime($project['created_at'] ?? '')),
                            date('Y-m-d', strtotime($project['end_date'] ?? ''))
                        ];
                    }
                }
                break;

            case 'task_summary':
                $headers = ['Task Title', 'Status', 'Due Date', 'Priority'];
                if (!empty($reportData['data'])) {
                    foreach ($reportData['data'] as $task) {
                        $rows[] = [
                            $task['title'] ?? '',
                            $task['status'] ?? '',
                            date('Y-m-d', strtotime($task['planned_end_date'] ?? '')),
                            $task['priority'] ?? 'Medium'
                        ];
                    }
                }
                break;

            case 'user_engagement':
                $headers = ['User Name', 'Message Count', 'Engagement Level'];
                if (!empty($reportData['data'])) {
                    foreach ($reportData['data'] as $user) {
                        $rows[] = [
                            $user['name'] ?? '',
                            $user['message_count'] ?? 0,
                            $user['engagement_level'] ?? 'N/A'
                        ];
                    }
                }
                break;

            default:
                return false;
        }

        // Generate CSV
        $title = preg_replace('/[^a-zA-Z0-9-_]/', '_', $reportData['title'] ?? 'Report');
        $filename = $title . '-' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write headers
        fputcsv($output, $headers);

        // Write data rows
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    private function exportHTML($reportData)
    {
        $html = '<!DOCTYPE html>';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>' . esc($reportData['title'] ?? 'Report') . '</title>';
        $html .= '<style>';
        $html .= 'body { font-family: Arial, sans-serif; margin: 20px; }';
        $html .= 'h1 { color: #333; }';
        $html .= 'table { border-collapse: collapse; width: 100%; margin-top: 20px; }';
        $html .= 'th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }';
        $html .= 'th { background-color: #4F46E5; color: white; }';
        $html .= 'tr:nth-child(even) { background-color: #f9f9f9; }';
        $html .= '.generated-date { color: #666; font-size: 12px; margin-top: 20px; }';
        $html .= '</style>';
        $html .= '</head>';
        $html .= '<body>';

        // Report Title
        $html .= '<h1>' . esc($reportData['title'] ?? 'Report') . '</h1>';
        $html .= '<p class="generated-date">Generated: ' . date('Y-m-d H:i:s') . '</p>';

        // Report Summary
        $html .= '<div class="summary">';
        $html .= '<p><strong>Date Range:</strong> ' . esc($reportData['date_range'] ?? 'N/A') . '</p>';
        $html .= '</div>';

        // Report Data Table
        if (!empty($reportData['data'])) {
            $html .= '<table>';
            $html .= '<thead>';
            $html .= '<tr>';

            // Determine headers based on report type
            switch ($reportData['type'] ?? null) {
                case 'messaging_activity':
                    $html .= '<th>Message ID</th><th>Created At</th><th>Sender ID</th><th>Message Body</th>';
                    break;
                case 'project_performance':
                    $html .= '<th>Project Name</th><th>Status</th><th>Created</th><th>Due Date</th>';
                    break;
                case 'task_summary':
                    $html .= '<th>Task Title</th><th>Status</th><th>Due Date</th><th>Priority</th>';
                    break;
                case 'user_engagement':
                    $html .= '<th>User Name</th><th>Message Count</th><th>Engagement Level</th>';
                    break;
            }

            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            // Add data rows
            foreach ($reportData['data'] as $item) {
                $html .= '<tr>';

                switch ($reportData['type'] ?? null) {
                    case 'messaging_activity':
                        $html .= '<td>' . esc($item['id'] ?? '') . '</td>';
                        $html .= '<td>' . esc($item['created_at'] ?? '') . '</td>';
                        $html .= '<td>' . esc($item['sender_id'] ?? '') . '</td>';
                        $html .= '<td>' . esc(substr($item['body'] ?? '', 0, 100)) . '</td>';
                        break;
                    case 'project_performance':
                        $html .= '<td>' . esc($item['name'] ?? '') . '</td>';
                        $html .= '<td>' . esc($item['status'] ?? '') . '</td>';
                        $html .= '<td>' . esc(date('Y-m-d', strtotime($item['created_at'] ?? ''))) . '</td>';
                        $html .= '<td>' . esc(date('Y-m-d', strtotime($item['end_date'] ?? ''))) . '</td>';
                        break;
                    case 'task_summary':
                        $html .= '<td>' . esc($item['title'] ?? '') . '</td>';
                        $html .= '<td>' . esc($item['status'] ?? '') . '</td>';
                        $html .= '<td>' . esc(date('Y-m-d', strtotime($item['planned_end_date'] ?? ''))) . '</td>';
                        $html .= '<td>' . esc($item['priority'] ?? 'Medium') . '</td>';
                        break;
                    case 'user_engagement':
                        $html .= '<td>' . esc($item['name'] ?? '') . '</td>';
                        $html .= '<td>' . ($item['message_count'] ?? 0) . '</td>';
                        $html .= '<td>' . esc($item['engagement_level'] ?? 'N/A') . '</td>';
                        break;
                }

                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';
        } else {
            $html .= '<p>No data available for this report.</p>';
        }

        $html .= '</body>';
        $html .= '</html>';

        // Output the HTML
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    private function getRecentReports($companyId)
    {
        // In a real system, you'd have a ReportModel
        // For now, we'll return generated reports from database data
        $projectModel = new ProjectModel();
        $taskModel = new TaskModel();
        $messageModel = new MessageModel();
        
        $reports = [];
        
        // Get projects completed recently
        $recentProjects = $projectModel->where('company_id', $companyId)
            ->where('status', 'completed')
            ->orderBy('updated_at', 'DESC')
            ->limit(3)
            ->get()
            ->getResultArray();
        
        foreach ($recentProjects as $project) {
            $reports[] = [
                'id' => $project['id'],
                'name' => 'Project: ' . $project['name'],
                'type' => 'Project Report',
                'date_range' => date('F Y', strtotime($project['updated_at'])),
                'created_at' => $project['updated_at']
            ];
        }
        
        // Get task completion reports
        $completedTasks = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.status', 'completed')
            ->orderBy('tasks.updated_at', 'DESC')
            ->limit(2)
            ->get()
            ->getResultArray();
        
        foreach ($completedTasks as $task) {
            $reports[] = [
                'id' => $task['id'],
                'name' => 'Task Summary: ' . $task['title'],
                'type' => 'Task Report',
                'date_range' => date('F Y', strtotime($task['updated_at'])),
                'created_at' => $task['updated_at']
            ];
        }
        
        // Sort by creation date
        usort($reports, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($reports, 0, 5);
    }

    private function getUserStats($companyId)
    {
        $userModel = new UserModel();
        
        $total = $userModel->where('company_id', $companyId)->countAllResults();
        $active = $userModel->where('company_id', $companyId)->where('status', 'active')->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active
        ];
    }

    private function getProjectStats($companyId)
    {
        $projectModel = new ProjectModel();
        
        $total = $projectModel->where('company_id', $companyId)->countAllResults();
        $active = $projectModel->where('company_id', $companyId)->where('status', 'active')->countAllResults();
        $completed = $projectModel->where('company_id', $companyId)->where('status', 'completed')->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'completed' => $completed
        ];
    }

    private function getTaskStats($companyId)
    {
        $taskModel = new TaskModel();
        
        $total = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->countAllResults();
        
        $completed = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.status', 'completed')
            ->countAllResults();
        
        $inProgress = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.status', 'in_progress')
            ->countAllResults();

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress
        ];
    }
}
