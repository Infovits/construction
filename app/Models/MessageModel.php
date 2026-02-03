<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['conversation_id', 'sender_id', 'body', 'created_at'];
    protected $useTimestamps = false;

    public function getConversationMessages($conversationId)
    {
        return $this->select('messages.*, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = messages.sender_id', 'left')
            ->where('messages.conversation_id', $conversationId)
            ->orderBy('messages.created_at', 'ASC')
            ->findAll();
    }
}
