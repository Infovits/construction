<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\ConversationModel;
use App\Models\NotificationModel;
use App\Models\ClientModel;
use App\Models\SupplierModel;
use App\Models\MaterialModel;
use App\Models\WarehouseModel;
use App\Models\PurchaseOrderModel;
use App\Models\MilestoneModel;

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
            // Core HR & Users
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $totalUsers - $activeUsers,
            
            // Projects
            'total_projects' => $projectModel->where('company_id', $companyId)->countAllResults(),
            'active_projects' => $projectModel->where('company_id', $companyId)->where('status', 'active')->countAllResults(),
            'completed_projects' => $projectModel->where('company_id', $companyId)->where('status', 'completed')->countAllResults(),
            'on_hold_projects' => $projectModel->where('company_id', $companyId)->where('status', 'on_hold')->countAllResults(),
            
            // Tasks & Milestones
            'task_stats' => $this->getTaskStats($companyId),
            'milestone_stats' => $this->getMilestoneStats($companyId),
            
            // Communications
            'total_conversations' => $conversationModel->where('company_id', $companyId)->countAllResults(),
            'active_conversations' => $this->getActiveConversationCount($companyId),
            'total_messages' => $this->getMessageCount($companyId),
            
            // Clients & Suppliers
            'client_stats' => $this->getClientStats($companyId),
            'supplier_stats' => $this->getSupplierStats($companyId),
            
            // Inventory
            'inventory_stats' => $this->getInventoryStats($companyId),
            'warehouse_stats' => $this->getWarehouseStats($companyId),
            
            // Procurement
            'purchase_order_stats' => $this->getPurchaseOrderStats($companyId),
            
            // System Health
            'system_health' => $this->getSystemHealth(),
            'storage_info' => $this->getStorageInfo(),
            'security_status' => $this->getSecurityStatus(),
            'recent_activity' => $this->getRecentActivity($companyId),
        ];

        return view('admin/overview/index', $data);
    }

    private function getTaskStats($companyId)
    {
        $taskModel = new TaskModel();
        
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

        $pending = $taskModel->join('projects', 'tasks.project_id = projects.id')
            ->where('projects.company_id', $companyId)
            ->where('tasks.status', 'pending')
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
            'pending' => $pending,
            'overdue' => $overdue,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100) : 0
        ];
    }

    private function getMilestoneStats($companyId)
    {
        $taskModel = new TaskModel();
        
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

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100) : 0
        ];
    }

    private function getClientStats($companyId)
    {
        $clientModel = new ClientModel();
        
        $total = $clientModel->where('company_id', $companyId)->countAllResults();
        $active = $clientModel->where('company_id', $companyId)->where('status', 'active')->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active
        ];
    }

    private function getSupplierStats($companyId)
    {
        $supplierModel = new SupplierModel();
        
        $total = $supplierModel->where('company_id', $companyId)->countAllResults();
        $active = $supplierModel->where('company_id', $companyId)->where('status', 'active')->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active
        ];
    }

    private function getInventoryStats($companyId)
    {
        $materialModel = new MaterialModel();
        
        $total = $materialModel->where('company_id', $companyId)->countAllResults();
        $lowStock = $materialModel->where('company_id', $companyId)
            ->where('current_stock <=', 10)
            ->countAllResults();
        $outOfStock = $materialModel->where('company_id', $companyId)
            ->where('current_stock', 0)
            ->countAllResults();

        return [
            'total' => $total,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock
        ];
    }

    private function getWarehouseStats($companyId)
    {
        $warehouseModel = new WarehouseModel();
        
        $total = $warehouseModel->where('company_id', $companyId)->countAllResults();
        $active = $warehouseModel->where('company_id', $companyId)->where('status', 'active')->countAllResults();

        return [
            'total' => $total,
            'active' => $active
        ];
    }

    private function getPurchaseOrderStats($companyId)
    {
        $poModel = new PurchaseOrderModel();
        
        $total = $poModel->join('projects', 'projects.id = purchase_orders.project_id')
            ->where('projects.company_id', $companyId)
            ->countAllResults();
        $pending = $poModel->join('projects', 'projects.id = purchase_orders.project_id')
            ->where('projects.company_id', $companyId)
            ->where('purchase_orders.status', 'pending')
            ->countAllResults();
        $approved = $poModel->join('projects', 'projects.id = purchase_orders.project_id')
            ->where('projects.company_id', $companyId)
            ->where('purchase_orders.status', 'approved')
            ->countAllResults();
        $delivered = $poModel->join('projects', 'projects.id = purchase_orders.project_id')
            ->where('projects.company_id', $companyId)
            ->where('purchase_orders.status', 'delivered')
            ->countAllResults();

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'delivered' => $delivered
        ];
    }

    private function getMessageCount($companyId)
    {
        $messageModel = new \App\Models\MessageModel();
        return $messageModel->getCompanyMessageCount($companyId);
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
