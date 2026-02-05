<?php

namespace App\Models;

use CodeIgniter\Model;

class FileAccessControlModel extends Model
{
    protected $table = 'file_access_controls';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'file_id', 'user_id', 'role_id', 'access_type',
        'granted_by', 'expires_at', 'is_revoked'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'granted_at';
    protected $updatedField = '';

    protected $validationRules = [
        'file_id' => 'required|integer',
        'access_type' => 'required|in_list[view,edit,delete,manage]',
    ];

    public function checkAccess($fileId, $userId, $accessType)
    {
        $now = date('Y-m-d H:i:s');
        
        return $this->where('file_id', $fileId)
                    ->where('user_id', $userId)
                    ->where('access_type', $accessType)
                    ->where('is_revoked', 0)
                    ->where(function($builder) use ($now) {
                        $builder->where('expires_at >=', $now)
                               ->orWhere('expires_at', null);
                    })
                    ->first();
    }

    public function checkAccessByRole($fileId, $roleId, $accessType)
    {
        $now = date('Y-m-d H:i:s');

        return $this->where('file_id', $fileId)
                    ->where('role_id', $roleId)
                    ->where('access_type', $accessType)
                    ->where('is_revoked', 0)
                    ->where(function($builder) use ($now) {
                        $builder->where('expires_at >=', $now)
                               ->orWhere('expires_at', null);
                    })
                    ->first();
    }

    public function getUsersWithAccess($fileId)
    {
        return $this->where('file_id', $fileId)
                    ->where('is_revoked', 0)
                    ->select('id, user_id, role_id, access_type, expires_at')
                    ->findAll();
    }

    public function getFileAccessByUser($userId, $fileId)
    {
        return $this->where('file_id', $fileId)
                    ->where('user_id', $userId)
                    ->where('is_revoked', 0)
                    ->findAll();
    }

    public function revokeAccess($accessId)
    {
        return $this->update($accessId, ['is_revoked' => 1]);
    }

    public function getAccessLevel($fileId, $userId)
    {
        $access = $this->where('file_id', $fileId)
                       ->where('user_id', $userId)
                       ->where('is_revoked', 0)
                       ->select('access_type')
                       ->findAll();

        if (empty($access)) {
            return null;
        }

        // Determine highest access level
        $accessLevels = ['manage', 'delete', 'edit', 'view'];
        foreach ($accessLevels as $level) {
            foreach ($access as $a) {
                if ($a['access_type'] === $level) {
                    return $level;
                }
            }
        }

        return 'view';
    }
}
