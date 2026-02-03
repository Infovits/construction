<?php

namespace App\Models;

use CodeIgniter\Model;

class ConversationParticipantModel extends Model
{
    protected $table = 'conversation_participants';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['conversation_id', 'user_id', 'last_read_at', 'added_at'];
    protected $useTimestamps = false;

    public function getParticipants($conversationId)
    {
        return $this->select('conversation_participants.*, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = conversation_participants.user_id', 'left')
            ->where('conversation_participants.conversation_id', $conversationId)
            ->findAll();
    }
}
