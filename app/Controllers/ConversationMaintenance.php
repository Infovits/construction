<?php

namespace App\Controllers;

use App\Models\ConversationTypingModel;

/**
 * Maintenance controller for cleaning up messaging-related data
 * Run periodically via cron or scheduled task
 */
class ConversationMaintenance extends BaseController
{
    protected $typingModel;

    public function __construct()
    {
        $this->typingModel = new ConversationTypingModel();
    }

    /**
     * Clean up expired typing indicators (older than 5 seconds)
     * Called via cron: /api/conversations/cleanup-typing
     */
    public function cleanupTyping()
    {
        // Only allow CLI or authorized requests
        if (!is_cli() && !hasPermission('admin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        try {
            $fiveSecondsAgo = date('Y-m-d H:i:s', strtotime('-5 seconds'));
            
            $deleted = $this->typingModel
                ->where('updated_at <', $fiveSecondsAgo)
                ->delete();

            return $this->response->setJSON([
                'success' => true,
                'message' => "Cleaned up $deleted expired typing records"
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Typing cleanup failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Cleanup failed']);
        }
    }
}
