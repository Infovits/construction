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
    protected $allowedFields = ['conversation_id', 'sender_id', 'body', 'created_at', 'company_id'];
    protected $useTimestamps = false;

    public function getConversationMessages($conversationId)
    {
        return $this->select('messages.*, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = messages.sender_id', 'left')
            ->where('messages.conversation_id', $conversationId)
            ->orderBy('messages.created_at', 'ASC')
            ->findAll();
    }

    public function getCompanyMessageCount($companyId)
    {
        return $this->select('m.id')
            ->from('messages m')
            ->join('conversations c', 'c.id = m.conversation_id', 'left')
            ->where('c.company_id', $companyId)
            ->countAllResults();
    }

    public function getMessagesByDateRange($companyId, $startDate, $endDate)
    {
        return $this->select('m.id, m.conversation_id, m.sender_id, m.body, m.created_at, u.first_name, u.last_name')
            ->from('messages m')
            ->join('conversations c', 'c.id = m.conversation_id', 'left')
            ->join('users u', 'u.id = m.sender_id', 'left')
            ->where('c.company_id', $companyId)
            ->where('m.created_at >=', $startDate)
            ->where('m.created_at <=', $endDate)
            ->orderBy('m.created_at', 'DESC')
            ->findAll();
    }
}

