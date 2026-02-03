<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\ConversationModel;
use App\Models\MessageModel;
use App\Models\NotificationModel;

class Analytics extends BaseController
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
        $notificationModel = new NotificationModel();

        // Get analytics data
        $data = [
            'title' => 'Analytics',
            'messages_sent' => $messageModel->getCompanyMessageCount($companyId),
            'active_conversations' => $conversationModel->getActiveConversationCount($companyId),
            'tasks_completed' => $taskModel->getCompletedTasksCount($companyId),
            'milestones_completed' => $this->getCompletedMilestonesCount($companyId),
            'task_completion_rate' => $this->getTaskCompletionRate($companyId),
            'milestone_completion_rate' => $this->getMilestoneCompletionRate($companyId),
            'daily_active_users' => $userModel->getDailyActiveCount($companyId),
            'top_users' => $this->getTopUsers($companyId),
            'project_progress' => $this->getProjectProgress($companyId),
            'milestone_progress' => $this->getMilestoneProgress($companyId),
        ];

        return view('admin/analytics/index', $data);
    }

    private function getTopUsers($companyId)
    {
        $messageModel = new MessageModel();
        
        $users = $messageModel->select('m.sender_id, u.first_name, u.last_name, COUNT(m.id) as messages_count')
            ->from('messages m')
            ->join('conversations c', 'c.id = m.conversation_id', 'left')
            ->join('users u', 'u.id = m.sender_id', 'left')
            ->where('c.company_id', $companyId)
            ->groupBy('m.sender_id')
            ->orderBy('messages_count', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $result = [];
        foreach ($users as $user) {
            $result[] = [
                'id' => $user['sender_id'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'messages_count' => $user['messages_count'],
                'engagement_score' => min(100, intval(($user['messages_count'] / 50) * 100))
            ];
        }

        return $result;
    }

    private function getProjectProgress($companyId)
    {
        $projectModel = new ProjectModel();
        
        $projects = $projectModel->where('company_id', $companyId)
            ->limit(5)
            ->get()
            ->getResultArray();

        $result = [];
        foreach ($projects as $project) {
            $result[] = [
                'id' => $project['id'],
                'name' => $project['name'],
                'progress' => $project['progress'] ?? 0,
            ];
        }

        return $result;
    }

    private function getCompletedMilestonesCount($companyId)
    {
        $taskModel = new TaskModel();
        
        return $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.status', 'completed')
            ->where('tasks.is_milestone', 1)
            ->countAllResults();
    }

    private function getTaskCompletionRate($companyId)
    {
        $taskModel = new TaskModel();
        
        $total = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.is_milestone', 0)
            ->countAllResults();

        if ($total === 0) {
            return 0;
        }

        $completed = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.status', 'completed')
            ->where('tasks.is_milestone', 0)
            ->countAllResults();

        return round(($completed / $total) * 100);
    }

    private function getMilestoneCompletionRate($companyId)
    {
        $taskModel = new TaskModel();
        
        $total = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.is_milestone', 1)
            ->countAllResults();

        if ($total === 0) {
            return 0;
        }

        $completed = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.status', 'completed')
            ->where('tasks.is_milestone', 1)
            ->countAllResults();

        return round(($completed / $total) * 100);
    }

    private function getMilestoneProgress($companyId)
    {
        $taskModel = new TaskModel();
        
        $milestones = $taskModel->select('tasks.*')
            ->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.is_milestone', 1)
            ->limit(5)
            ->get()
            ->getResultArray();

        $result = [];
        foreach ($milestones as $milestone) {
            $result[] = [
                'id' => $milestone['id'],
                'name' => $milestone['title'],
                'progress' => $milestone['progress_percentage'] ?? 0,
            ];
        }

        return $result;
    }
}
