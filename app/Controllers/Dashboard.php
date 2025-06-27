<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Initialize models
        $userModel = new \App\Models\UserModel();
        $projectModel = new \App\Models\ProjectModel();
        $taskModel = new \App\Models\TaskModel();

        // Get dashboard data
        $data = [
            'title' => 'Dashboard',
            'user_stats' => $this->getUserStats(),
            'project_stats' => $this->getProjectStats(),
            'task_stats' => $this->getTaskStats(),
            'recent_projects' => $this->getRecentProjects(),
            'pending_tasks' => $this->getPendingTasks(),
            'recent_activities' => $this->getRecentActivities()
        ];

        return view('admin/dashboard/index', $data);
    }

    private function getUserStats()
    {
        $userModel = new \App\Models\UserModel();
        $companyId = session('company_id');

        $total = $userModel->where('company_id', $companyId)->countAllResults();
        $active = $userModel->where('company_id', $companyId)->where('status', 'active')->countAllResults();
        $newThisMonth = $userModel->where('company_id', $companyId)
            ->where('created_at >=', date('Y-m-01'))
            ->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'new_this_month' => $newThisMonth
        ];
    }

    private function getProjectStats()
    {
        $projectModel = new \App\Models\ProjectModel();
        return $projectModel->getProjectStats();
    }

    private function getTaskStats()
    {
        $taskModel = new \App\Models\TaskModel();
        $companyId = session('company_id');

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

        $overdue = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.planned_end_date <', date('Y-m-d'))
            ->whereNotIn('tasks.status', ['completed', 'cancelled'])
            ->countAllResults();

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'overdue' => $overdue
        ];
    }

    private function getRecentProjects($limit = 5)
    {
        $projectModel = new \App\Models\ProjectModel();
        return $projectModel->getRecentProjects($limit);
    }

    private function getPendingTasks($limit = 10)
    {
        $taskModel = new \App\Models\TaskModel();
        return $taskModel->getPendingTasks(session('user_id'), $limit);
    }

    private function getRecentActivities($limit = 10)
    {
        // For now, return dummy data. You can implement audit log later
        return [
            [
                'type' => 'task_completed',
                'title' => 'Task Completed',
                'description' => 'Foundation work completed for Lilongwe Office Complex',
                'time' => '2 hours ago',
                'icon' => 'fas fa-check',
                'color' => 'success'
            ],
            [
                'type' => 'user_added',
                'title' => 'New Team Member',
                'description' => 'John Doe added to Blantyre Warehouse project',
                'time' => '4 hours ago',
                'icon' => 'fas fa-user-plus',
                'color' => 'primary'
            ],
            [
                'type' => 'low_stock',
                'title' => 'Low Stock Alert',
                'description' => 'Cement stock running low at Main Warehouse',
                'time' => '6 hours ago',
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'warning'
            ],
            [
                'type' => 'invoice_sent',
                'title' => 'Invoice Generated',
                'description' => 'Invoice #INV-2025-045 sent to ABC Corporation',
                'time' => '1 day ago',
                'icon' => 'fas fa-file-invoice',
                'color' => 'info'
            ]
        ];
    }
}