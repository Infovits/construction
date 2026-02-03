<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\ConversationModel;
use App\Models\MessageModel;

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

        // Get recent reports (sample data)
        $data = [
            'title' => 'Reports',
            'recent_reports' => $this->getRecentReports($companyId),
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

            // Generate report based on type
            $reportData = $this->generateReportByType($type, $dateRange, $department);

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
            default:
                return [];
        }
    }

    private function getMessagingActivityReport($companyId, $dateRange)
    {
        $messageModel = new MessageModel();
        $startDate = $this->getStartDate($dateRange);

        $messages = $messageModel->where('company_id', $companyId)
            ->where('created_at >=', $startDate)
            ->get()
            ->getResultArray();

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
        // Placeholder for PDF export
        return json_encode(['status' => 'PDF export not yet implemented']);
    }

    private function exportExcel($reportData)
    {
        // Placeholder for Excel export
        return json_encode(['status' => 'Excel export not yet implemented']);
    }

    private function exportCSV($reportData)
    {
        // Placeholder for CSV export
        return json_encode(['status' => 'CSV export not yet implemented']);
    }

    private function exportHTML($reportData)
    {
        // Placeholder for HTML export
        return json_encode(['status' => 'HTML export not yet implemented']);
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
