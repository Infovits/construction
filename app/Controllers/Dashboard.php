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
        $conversationModel = new \App\Models\ConversationModel();
        $notificationModel = new \App\Models\NotificationModel();

        $userId = session('user_id');
        $companyId = session('company_id');

        // Get messaging and notification data
        $conversations = $conversationModel->getUserConversations($userId, $companyId);
        $unreadMessageCount = 0;
        foreach ($conversations as $conv) {
            if ((int)$conv['unread_count'] > 0) {
                $unreadMessageCount += $conv['unread_count'];
            }
        }

        $unreadNotificationCount = $notificationModel->getUnreadCount($userId, $companyId);
        
        // Get recent conversations
        $recentConversations = [];
        foreach (array_slice($conversations, 0, 5) as $conv) {
            $recentConversations[] = [
                'id' => $conv['id'],
                'participant_names' => $conv['participant_names'],
                'last_message' => $conv['last_message'],
                'unread_count' => $conv['unread_count'],
            ];
        }
        
        // Get recent notifications
        $recentNotifications = $notificationModel->getRecent($userId, $companyId, 5);

        // Get dashboard data
        $data = [
            'title' => 'Dashboard',
            'user_stats' => $this->getUserStats(),
            'project_stats' => $this->getProjectStats(),
            'task_stats' => $this->getTaskStats(),
            'milestone_stats' => $this->getMilestoneStats(),
            'recent_projects' => $this->getRecentProjects(),
            'pending_tasks' => $this->getPendingTasks(),
            'recent_activities' => $this->getRecentActivities(),
            'unreadMessageCount' => $unreadMessageCount,
            'unreadNotificationCount' => $unreadNotificationCount,
            'activeConversations' => count($conversations),
            'recentConversations' => $recentConversations,
            'recentNotifications' => $recentNotifications,
            'client_stats' => $this->getClientStats(),
            'site_health' => $this->getSiteHealthScore(),
            'daily_tasks' => $this->getDailyTasks(),
            'daily_milestones' => $this->getDailyMilestones(),
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
            ->where('tasks.is_milestone', 0)
            ->countAllResults();

        $completed = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.status', 'completed')
            ->where('tasks.is_milestone', 0)
            ->countAllResults();

        $inProgress = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.status', 'in_progress')
            ->where('tasks.is_milestone', 0)
            ->countAllResults();

        $overdue = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.planned_end_date <', date('Y-m-d'))
            ->where('tasks.is_milestone', 0)
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
        // Get recent task activities from the database
        $taskModel = new \App\Models\TaskModel();
        $companyId = session('company_id');
        
        $recentTasks = $taskModel->select('tasks.*, projects.name as project_name, users.first_name, users.last_name')
            ->join('projects', 'tasks.project_id = projects.id')
            ->join('users', 'tasks.assigned_to = users.id', 'left')
            ->where('projects.company_id', $companyId)
            ->where('tasks.updated_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->orderBy('tasks.updated_at', 'DESC')
            ->limit($limit)
            ->findAll();
        
        $activities = [];
        foreach ($recentTasks as $task) {
            $timeAgo = $this->timeAgo($task['updated_at']);
            $assignedTo = trim($task['first_name'] . ' ' . $task['last_name']);
            
            if ($task['status'] === 'completed') {
                $activities[] = [
                    'type' => 'task_completed',
                    'title' => 'Task Completed',
                    'description' => esc($task['title']) . ' for ' . esc($task['project_name']),
                    'time' => $timeAgo,
                    'icon' => 'fas fa-check',
                    'color' => 'success'
                ];
            } elseif ($task['status'] === 'in_progress') {
                $activities[] = [
                    'type' => 'task_started',
                    'title' => 'Task In Progress',
                    'description' => esc($task['title']) . ($assignedTo ? ' assigned to ' . esc($assignedTo) : ''),
                    'time' => $timeAgo,
                    'icon' => 'fas fa-play',
                    'color' => 'primary'
                ];
            } else {
                $activities[] = [
                    'type' => 'task_created',
                    'title' => 'New Task',
                    'description' => esc($task['title']) . ' for ' . esc($task['project_name']),
                    'time' => $timeAgo,
                    'icon' => 'fas fa-plus',
                    'color' => 'info'
                ];
            }
        }
        
        return $activities;
    }
    
    private function timeAgo($datetime)
    {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', $timestamp);
        }
    }
    
    private function getClientStats()
    {
        $projectModel = new \App\Models\ProjectModel();
        $companyId = session('company_id');
        
        // Get projects by status over the last 12 months
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $count = $projectModel->where('company_id', $companyId)
                ->where("DATE_FORMAT(created_at, '%Y-%m')", $month)
                ->countAllResults();
            $monthlyData[] = [
                'month' => date('M', strtotime("-$i months")),
                'count' => $count
            ];
        }
        
        return $monthlyData;
    }
    
    private function getSiteHealthScore()
    {
        $companyId = session('company_id');
        
        // Calculate health score based on various metrics
        $projectModel = new \App\Models\ProjectModel();
        $taskModel = new \App\Models\TaskModel();
        
        $totalProjects = $projectModel->where('company_id', $companyId)->countAllResults();
        $activeProjects = $projectModel->where('company_id', $companyId)->where('status', 'active')->countAllResults();
        
        $totalTasks = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->countAllResults();
            
        $completedTasks = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.status', 'completed')
            ->countAllResults();
        
        // Calculate score (weighted average)
        $projectHealth = $totalProjects > 0 ? ($activeProjects / $totalProjects) * 100 : 0;
        $taskHealth = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
        
        $overallHealth = round(($projectHealth * 0.4) + ($taskHealth * 0.6));
        
        return [
            'score' => $overallHealth,
            'project_health' => round($projectHealth),
            'task_health' => round($taskHealth)
        ];
    }
    
    private function getDailyTasks()
    {
        $taskModel = new \App\Models\TaskModel();
        $companyId = session('company_id');
        $userId = session('user_id');
        
        // Get regular tasks for today assigned to current user (exclude milestones)
        $today = date('Y-m-d');
        $tasks = $taskModel->select('tasks.*, projects.name as project_name')
            ->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.assigned_to', $userId)
            ->where('tasks.is_milestone', 0)
            ->where('tasks.planned_start_date <=', $today)
            ->where('tasks.planned_end_date >=', $today)
            ->whereIn('tasks.status', ['pending', 'in_progress'])
            ->orderBy('tasks.planned_start_date', 'ASC')
            ->limit(10)
            ->findAll();
        
        return $tasks;
    }

    private function getDailyMilestones()
    {
        $taskModel = new \App\Models\TaskModel();
        $companyId = session('company_id');
        $userId = session('user_id');
        
        // Get milestones for today assigned to current user
        $today = date('Y-m-d');
        $milestones = $taskModel->select('tasks.*, projects.name as project_name')
            ->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.assigned_to', $userId)
            ->where('tasks.is_milestone', 1)
            ->where('tasks.planned_start_date <=', $today)
            ->where('tasks.planned_end_date >=', $today)
            ->whereIn('tasks.status', ['pending', 'in_progress'])
            ->orderBy('tasks.planned_start_date', 'ASC')
            ->limit(10)
            ->findAll();
        
        return $milestones;
    }

    private function getMilestoneStats()
    {
        $taskModel = new \App\Models\TaskModel();
        $companyId = session('company_id');

        $total = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.is_milestone', 1)
            ->countAllResults();

        $completed = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.status', 'completed')
            ->where('tasks.is_milestone', 1)
            ->countAllResults();

        $inProgress = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.status', 'in_progress')
            ->where('tasks.is_milestone', 1)
            ->countAllResults();

        $overdue = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.planned_end_date <', date('Y-m-d'))
            ->where('tasks.is_milestone', 1)
            ->whereNotIn('tasks.status', ['completed', 'cancelled'])
            ->countAllResults();

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'overdue' => $overdue
        ];
    }
}
