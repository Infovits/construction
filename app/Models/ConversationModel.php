<?php

namespace App\Models;

use CodeIgniter\Model;

class ConversationModel extends Model
{
    protected $table = 'conversations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['company_id', 'subject', 'created_by', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    public function getUserConversations($userId, $companyId)
    {
        $db = $this->db;
        $sql = "
            SELECT c.*, 
                   lm.body AS last_message,
                   lm.created_at AS last_message_at,
                   SUM(CASE WHEN cp.last_read_at IS NULL OR m.created_at > cp.last_read_at THEN 1 ELSE 0 END) AS unread_count
            FROM conversations c
            JOIN conversation_participants cp ON cp.conversation_id = c.id
            LEFT JOIN messages m ON m.conversation_id = c.id
            LEFT JOIN (
                SELECT conversation_id, MAX(created_at) AS max_created
                FROM messages
                GROUP BY conversation_id
            ) lm_max ON lm_max.conversation_id = c.id
            LEFT JOIN messages lm ON lm.conversation_id = c.id AND lm.created_at = lm_max.max_created
            WHERE cp.user_id = ? AND c.company_id = ?
            GROUP BY c.id, lm.body, lm.created_at
            ORDER BY lm.created_at DESC
        ";

        return $db->query($sql, [$userId, $companyId])->getResultArray();
    }

    public function getActiveConversationCount($companyId)
    {
        return $this->where('company_id', $companyId)
            ->countAllResults();
    }

    public function getConversationParticipantNames($conversationId)
    {
        $db = $this->db;
        $participants = $db->table('conversation_participants cp')
            ->select('u.first_name, u.last_name')
            ->join('users u', 'u.id = cp.user_id', 'left')
            ->where('cp.conversation_id', $conversationId)
            ->get()
            ->getResultArray();

        $names = array_map(fn($p) => $p['first_name'] . ' ' . $p['last_name'], $participants);
        return implode(', ', $names);
    }
}
