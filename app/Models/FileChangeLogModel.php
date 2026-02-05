<?php

namespace App\Models;

use CodeIgniter\Model;

class FileChangeLogModel extends Model
{
    protected $table = 'file_change_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'file_id', 'action_type', 'action_by', 'action_description',
        'old_values', 'new_values', 'ip_address'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    protected $validationRules = [
        'file_id' => 'required|integer',
        'action_type' => 'required|in_list[uploaded,updated,deleted,commented,shared,tagged,renamed]',
        'action_by' => 'required|integer',
    ];

    public function logAction($fileId, $actionType, $actionBy, $description = null, $oldValues = null, $newValues = null, $ipAddress = null)
    {
        $data = [
            'file_id' => $fileId,
            'action_type' => $actionType,
            'action_by' => $actionBy,
            'action_description' => $description,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $ipAddress ?? $this->getClientIP()
        ];

        return $this->insert($data);
    }

    public function getFileChangeLogs($fileId, $limit = 100)
    {
        $userModel = new \App\Models\UserModel();
        
        $logs = $this->where('file_id', $fileId)
                     ->orderBy('created_at', 'DESC')
                     ->limit($limit)
                     ->findAll();

        foreach ($logs as &$log) {
            $user = $userModel->find($log['action_by']);
            $log['user'] = $user ? [
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email']
            ] : null;

            if ($log['old_values']) {
                $log['old_values'] = json_decode($log['old_values'], true);
            }
            if ($log['new_values']) {
                $log['new_values'] = json_decode($log['new_values'], true);
            }
        }

        return $logs;
    }

    public function getActionsByUser($userId, $fileId = null)
    {
        $query = $this->where('action_by', $userId);
        
        if ($fileId) {
            $query = $query->where('file_id', $fileId);
        }

        return $query->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getActionHistory($fileId, $actionType = null)
    {
        $query = $this->where('file_id', $fileId);
        
        if ($actionType) {
            $query = $query->where('action_type', $actionType);
        }

        return $query->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    private function getClientIP()
    {
        $request = \Config\Services::request();
        return $request->getIPAddress();
    }
}
