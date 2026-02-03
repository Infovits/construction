<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'company_id', 'notification_type', 'title', 'message', 'related_type', 'related_id', 'priority', 'status', 'is_read', 'created_at'];
    protected $useTimestamps = false;

    public function getRecent($userId, $companyId, $limit = 10)
    {
        return $this->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->where('status !=', 'failed')
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    public function getUnreadCount($userId, $companyId)
    {
        return $this->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->where('is_read', 0)
            ->countAllResults();
    }
}
