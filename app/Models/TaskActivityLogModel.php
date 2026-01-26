<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskActivityLogModel extends Model
{
    protected $table = 'task_activity_log';
    protected $primaryKey = 'id';
    protected $allowedFields = ['task_id', 'user_id', 'activity_type', 'description', 'old_value', 'new_value', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get activity log for a specific task
     */
    public function getTaskActivityLog($taskId, $limit = 50)
    {
        return $this->select('task_activity_log.*, users.first_name, users.last_name')
            ->join('users', 'users.id = task_activity_log.user_id')
            ->where('task_activity_log.task_id', $taskId)
            ->orderBy('task_activity_log.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Log an activity for a task
     */
    public function logActivity($taskId, $userId, $activityType, $description, $oldValue = null, $newValue = null)
    {
        $data = [
            'task_id' => $taskId,
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $this->insert($data);
    }

    /**
     * Log task creation
     */
    public function logTaskCreated($taskId, $userId)
    {
        return $this->logActivity(
            $taskId,
            $userId,
            'task_created',
            'Task created'
        );
    }

    /**
     * Log task update
     */
    public function logTaskUpdated($taskId, $userId, $fieldName, $oldValue, $newValue)
    {
        return $this->logActivity(
            $taskId,
            $userId,
            'task_updated',
            "Updated {$fieldName}",
            $oldValue,
            $newValue
        );
    }

    /**
     * Log task status change
     */
    public function logTaskStatusChanged($taskId, $userId, $oldStatus, $newStatus)
    {
        return $this->logActivity(
            $taskId,
            $userId,
            'status_changed',
            "Status changed from {$oldStatus} to {$newStatus}",
            $oldStatus,
            $newStatus
        );
    }

    /**
     * Log task assignment
     */
    public function logTaskAssigned($taskId, $userId, $assignedTo)
    {
        return $this->logActivity(
            $taskId,
            $userId,
            'task_assigned',
            "Task assigned to {$assignedTo}"
        );
    }

    /**
     * Log comment added
     */
    public function logCommentAdded($taskId, $userId)
    {
        return $this->logActivity(
            $taskId,
            $userId,
            'comment_added',
            'Comment added to task'
        );
    }

    /**
     * Log attachment added
     */
    public function logAttachmentAdded($taskId, $userId, $fileName)
    {
        return $this->logActivity(
            $taskId,
            $userId,
            'attachment_added',
            "Attachment '{$fileName}' added"
        );
    }

    /**
     * Log attachment deleted
     */
    public function logAttachmentDeleted($taskId, $userId, $fileName)
    {
        return $this->logActivity(
            $taskId,
            $userId,
            'attachment_deleted',
            "Attachment '{$fileName}' deleted"
        );
    }
}