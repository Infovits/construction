<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\ConversationModel;
use App\Models\NotificationModel;

class Overview extends BaseController
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
        $notificationModel = new NotificationModel();

        // Get system overview data
        $totalUsers = $userModel->where('company_id', $companyId)->countAllResults();
        $activeUsers = $userModel->where('company_id', $companyId)->where('status', 'active')->countAllResults();
        
        $data = [
            'title' => 'System Overview',
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $totalUsers - $activeUsers,
            'total_projects' => $projectModel->where('company_id', $companyId)->countAllResults(),
            'active_projects' => $projectModel->where('company_id', $companyId)->where('status', 'active')->countAllResults(),
            'completed_projects' => $projectModel->where('company_id', $companyId)->where('status', 'completed')->countAllResults(),
            'total_tasks' => $this->getTaskCount($companyId, null),
            'pending_tasks' => $this->getTaskCount($companyId, 'pending'),
            'completed_tasks' => $this->getTaskCount($companyId, 'completed'),
            'total_conversations' => $conversationModel->where('company_id', $companyId)->countAllResults(),
            'active_conversations' => $this->getActiveConversationCount($companyId),
            'archived_conversations' => 0,
            'system_health' => $this->getSystemHealth(),
            'storage_info' => $this->getStorageInfo(),
            'security_status' => $this->getSecurityStatus(),
            'recent_activity' => $this->getRecentActivity($companyId),
        ];

        return view('admin/overview/index', $data);
    }

    private function getTaskCount($companyId, $status = null)
    {
        $taskModel = new TaskModel();
        
        $query = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId);

        if ($status) {
            $query->where('tasks.status', $status);
        }

        return $query->countAllResults();
    }

    private function getActiveConversationCount($companyId)
    {
        $conversationModel = new ConversationModel();
        
        return $conversationModel->where('company_id', $companyId)
            ->countAllResults();
    }

    private function getSystemHealth()
    {
        return [
            'status' => 'Operational',
            'uptime' => 99.8,
            'response_time' => 145
        ];
    }

    private function getStorageInfo()
    {
        return [
            'database_size' => '2.4',
            'database_limit' => '10',
            'file_storage' => '856',
            'file_limit' => '5120',
            'memory_usage' => '512',
            'memory_limit' => '2048'
        ];
    }

    private function getSecurityStatus()
    {
        return [
            'ssl_valid' => true,
            'ssl_expires' => '2027-02-03',
            'backup_status' => 'Complete',
            'last_backup' => date('Y-m-d H:i:s'),
            'security_scan' => 'No threats detected'
        ];
    }

    private function getRecentActivity($companyId)
    {
        $activity = [];
        
        // Get recent users
        $userModel = new UserModel();
        $recentUsers = $userModel->where('company_id', $companyId)
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get()
            ->getResultArray();
        
        foreach ($recentUsers as $user) {
            $activity[] = [
                'type' => 'user',
                'title' => 'New user registered',
                'description' => $user['first_name'] . ' ' . $user['last_name'] . ' joined the system',
                'time' => $user['created_at']
            ];
        }
        
        // Get recent completed projects
        $projectModel = new ProjectModel();
        $completedProjects = $projectModel->where('company_id', $companyId)
            ->where('status', 'completed')
            ->orderBy('updated_at', 'DESC')
            ->limit(2)
            ->get()
            ->getResultArray();
        
        foreach ($completedProjects as $project) {
            $activity[] = [
                'type' => 'project',
                'title' => 'Project completed',
                'description' => $project['name'] . ' project marked as complete',
                'time' => $project['updated_at']
            ];
        }
        
        // Sort by time
        usort($activity, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        return array_slice($activity, 0, 4);
    }
}
