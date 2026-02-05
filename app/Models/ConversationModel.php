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
                   COALESCE(unread_counts.unread_count, 0) AS unread_count,
                   participants.participant_names
            FROM conversations c
            LEFT JOIN (
                SELECT conversation_id, MAX(created_at) AS max_created
                FROM messages
                GROUP BY conversation_id
            ) lm_max ON lm_max.conversation_id = c.id
            LEFT JOIN messages lm ON lm.conversation_id = c.id AND lm.created_at = lm_max.max_created
            LEFT JOIN (
                SELECT cp.conversation_id,
                       SUM(CASE WHEN cp.last_read_at IS NULL OR m.created_at > cp.last_read_at THEN 1 ELSE 0 END) AS unread_count
                FROM conversation_participants cp
                LEFT JOIN messages m ON m.conversation_id = cp.conversation_id
                WHERE cp.user_id = ?
                GROUP BY cp.conversation_id
            ) unread_counts ON unread_counts.conversation_id = c.id
            LEFT JOIN (
                SELECT cp.conversation_id,
                       GROUP_CONCAT(CONCAT(u.first_name, ' ', u.last_name) SEPARATOR ', ') AS participant_names
                FROM conversation_participants cp
                LEFT JOIN users u ON u.id = cp.user_id
                GROUP BY cp.conversation_id
            ) participants ON participants.conversation_id = c.id
            WHERE c.id IN (
                SELECT DISTINCT cp.conversation_id
                FROM conversation_participants cp
                WHERE cp.user_id = ? AND cp.conversation_id IN (
                    SELECT c2.id FROM conversations c2 WHERE c2.company_id = ?
                )
            )
            ORDER BY lm.created_at DESC
        ";

        return $db->query($sql, [$userId, $userId, $companyId])->getResultArray();
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
