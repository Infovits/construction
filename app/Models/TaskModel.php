<?php

namespace App\Models;
use CodeIgniter\Model;
class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'project_id', 'parent_task_id', 'category_id', 'task_code', 'title', 'description',
        'task_type', 'priority', 'status', 'progress_percentage', 'assigned_to', 'assigned_by',
        'planned_start_date', 'planned_end_date', 'actual_start_date', 'actual_end_date',
        'estimated_hours', 'actual_hours', 'estimated_cost', 'actual_cost', 'depends_on',
        'is_critical_path', 'requires_approval', 'is_billable', 'created_by', 'tags', 'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'project_id' => 'required|numeric',
        'title' => 'required|min_length[3]|max_length[255]',
        'task_type' => 'in_list[task,subtask]',
        'priority' => 'in_list[low,medium,high,urgent]',
        'status' => 'in_list[not_started,pending,in_progress,review,completed,cancelled,on_hold]'
    ];

    public function getPendingTasks($userId, $limit = 10)
    {
        return $this->select('tasks.*, projects.name as project_name')
            ->join('projects', 'tasks.project_id = projects.id')
            ->where('tasks.assigned_to', $userId)
            ->whereNotIn('tasks.status', ['completed', 'cancelled'])
            ->orderBy('tasks.planned_end_date', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    public function getTasksByProject($projectId)
    {
        return $this->select('tasks.*, CONCAT(u.first_name, " ", u.last_name) as assigned_name')
            ->join('users u', 'tasks.assigned_to = u.id', 'left')
            ->where('tasks.project_id', $projectId)
            ->orderBy('tasks.created_at', 'DESC')
            ->findAll();
    }

    public function getTaskSummaryByProject($projectId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                COUNT(*) as total_tasks,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks,
                SUM(CASE WHEN status = 'not_started' THEN 1 ELSE 0 END) as not_started_tasks,
                SUM(CASE WHEN status = 'on_hold' THEN 1 ELSE 0 END) as on_hold_tasks,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_tasks,
                SUM(CASE WHEN planned_end_date < CURDATE() AND status NOT IN ('completed', 'cancelled') THEN 1 ELSE 0 END) as overdue_tasks,
                AVG(CASE WHEN status != 'cancelled' THEN progress_percentage ELSE NULL END) as avg_progress
            FROM tasks 
            WHERE project_id = ?
        ", [$projectId]);
        
        return $query->getRowArray();
    }

    public function getOverdueTasks($userId = null, $projectId = null)
    {
        $builder = $this->select('tasks.*, projects.name as project_name, CONCAT(u.first_name, " ", u.last_name) as assigned_name')
            ->join('projects', 'tasks.project_id = projects.id')
            ->join('users u', 'tasks.assigned_to = u.id', 'left')
            ->where('tasks.planned_end_date <', date('Y-m-d'))
            ->whereNotIn('tasks.status', ['completed', 'cancelled']);
        
        if ($userId) {
            $builder->where('tasks.assigned_to', $userId);
        }
        
        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }
        
        return $builder->orderBy('tasks.planned_end_date', 'ASC')->findAll();
    }

    public function getTasksWithDependencies($projectId)
    {
        return $this->select('tasks.*, 
                            CONCAT(u.first_name, " ", u.last_name) as assigned_name,
                            dep.title as dependency_title')
            ->join('users u', 'tasks.assigned_to = u.id', 'left')
            ->join('tasks dep', 'FIND_IN_SET(dep.id, tasks.depends_on)', 'left')
            ->where('tasks.project_id', $projectId)
            ->orderBy('tasks.planned_start_date', 'ASC')
            ->findAll();
    }

    public function updateTaskProgress($taskId, $progressPercentage, $status = null)
    {
        $updateData = ['progress_percentage' => $progressPercentage];
        
        if ($status) {
            $updateData['status'] = $status;
            
            if ($status === 'in_progress' && !$this->find($taskId)['actual_start_date']) {
                $updateData['actual_start_date'] = date('Y-m-d');
            }
            
            if ($status === 'completed') {
                $updateData['actual_end_date'] = date('Y-m-d');
                $updateData['progress_percentage'] = 100;
            }
        }
        
        $result = $this->update($taskId, $updateData);
        
        // Update project progress
        if ($result) {
            $task = $this->find($taskId);
            if ($task) {
                $projectModel = new \App\Models\ProjectModel();
                $projectModel->updateProjectProgress($task['project_id']);
            }
        }
        
        return $result;
    }

    public function getTaskComments($taskId)
    {
        return $this->db->table('task_comments tc')
                       ->select('tc.*, u.first_name, u.last_name')
                       ->join('users u', 'tc.user_id = u.id')
                       ->where('tc.task_id', $taskId)
                       ->orderBy('tc.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getTaskAttachments($taskId)
    {
        return $this->db->table('task_attachments')
                       ->where('task_id', $taskId)
                       ->orderBy('uploaded_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getTaskAttachment($attachmentId)
    {
        return $this->db->table('task_attachments')
                       ->where('id', $attachmentId)
                       ->get()
                       ->getRowArray();
    }

    public function getTaskDependencies($taskId)
    {
        return $this->db->table('task_dependencies td')
                       ->select('t.id, t.title, t.status, t.progress_percentage, t.due_date')
                       ->join('tasks t', 'td.dependency_task_id = t.id')
                       ->where('td.task_id', $taskId)
                       ->get()
                       ->getResultArray();
    }

    public function addTaskComment($data)
    {
        return $this->db->table('task_comments')->insert($data);
    }

    public function deleteTaskComment($commentId, $userId)
    {
        return $this->db->table('task_comments')
                       ->where('id', $commentId)
                       ->where('user_id', $userId)
                       ->delete();
    }

    public function addTaskAttachment($data)
    {
        return $this->db->table('task_attachments')->insert($data);
    }

    public function deleteTaskAttachment($attachmentId, $userId)
    {
        $attachment = $this->getTaskAttachment($attachmentId);
        if ($attachment && file_exists($attachment['file_path'])) {
            unlink($attachment['file_path']);
        }
        
        return $this->db->table('task_attachments')
                       ->where('id', $attachmentId)
                       ->delete();
    }

    public function addTimeLog($data)
    {
        return $this->db->table('task_time_logs')->insert($data);
    }

    public function getTotalLoggedHours($taskId)
    {
        $result = $this->db->table('task_time_logs')
                          ->selectSum('hours')
                          ->where('task_id', $taskId)
                          ->get()
                          ->getRowArray();
        
        return $result['hours'] ?? 0;
    }

    public function getMilestones($projectId)
    {
        return $this->where('project_id', $projectId)
            ->where('task_type', 'milestone')
            ->orderBy('planned_end_date', 'ASC')
            ->findAll();
    }

    public function getCriticalPath($projectId)
    {
        return $this->where('project_id', $projectId)
            ->where('is_critical_path', true)
            ->orderBy('planned_start_date', 'ASC')
            ->findAll();
    }

    public function generateTaskCode($projectId, $prefix = 'TSK')
    {
        $project = $this->db->table('projects')->where('id', $projectId)->get()->getRowArray();
        $projectCode = $project['project_code'] ?? 'PROJ';
        
        $lastTask = $this->where('project_id', $projectId)
            ->where('task_code LIKE', $projectCode . '-' . $prefix . '-%')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastTask) {
            $lastNumber = (int) substr($lastTask['task_code'], -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $projectCode . '-' . $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
