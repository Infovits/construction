<?php

namespace App\Models;

use CodeIgniter\Model;

class FileCommentModel extends Model
{
    protected $table = 'file_comments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'file_id', 'user_id', 'comment_text', 'mentions',
        'is_resolved', 'resolved_by', 'resolved_at', 'is_deleted'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'file_id' => 'required|integer',
        'user_id' => 'required|integer',
        'comment_text' => 'required|string|min_length[1]',
    ];

    public function getFileComments($fileId, $limit = 50)
    {
        $userModel = new \App\Models\UserModel();
        
        $comments = $this->where('file_id', $fileId)
                         ->where('is_deleted', 0)
                         ->orderBy('created_at', 'DESC')
                         ->limit($limit)
                         ->findAll();

        // Attach user information
        foreach ($comments as &$comment) {
            $user = $userModel->find($comment['user_id']);
            $comment['user'] = $user ? [
                'id' => $user['id'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email'],
                'profile_photo' => $user['profile_photo_url'] ?? null
            ] : null;

            if ($comment['resolved_by']) {
                $resolvedUser = $userModel->find($comment['resolved_by']);
                $comment['resolved_by_user'] = $resolvedUser ? [
                    'name' => $resolvedUser['first_name'] . ' ' . $resolvedUser['last_name']
                ] : null;
            }
        }

        return $comments;
    }

    public function getUnresolvedComments($fileId)
    {
        return $this->where('file_id', $fileId)
                    ->where('is_resolved', 0)
                    ->where('is_deleted', 0)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    public function resolveComment($commentId, $userId)
    {
        return $this->update($commentId, [
            'is_resolved' => 1,
            'resolved_by' => $userId,
            'resolved_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function deleteComment($commentId)
    {
        return $this->update($commentId, ['is_deleted' => 1]);
    }

    public function getCommentCount($fileId)
    {
        return $this->where('file_id', $fileId)
                    ->where('is_deleted', 0)
                    ->countAllResults();
    }

    public function getUnresolvedCommentCount($fileId)
    {
        return $this->where('file_id', $fileId)
                    ->where('is_resolved', 0)
                    ->where('is_deleted', 0)
                    ->countAllResults();
    }
}
