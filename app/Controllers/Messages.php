<?php

namespace App\Controllers;

use App\Models\ConversationModel;
use App\Models\ConversationParticipantModel;
use App\Models\MessageModel;
use App\Models\MessageAttachmentModel;
use App\Models\ConversationTypingModel;
use App\Models\NotificationModel;
use App\Models\UserModel;

class Messages extends BaseController
{
    protected $conversationModel;
    protected $participantModel;
    protected $messageModel;
    protected $attachmentModel;
    protected $typingModel;
    protected $notificationModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->conversationModel = new ConversationModel();
        $this->participantModel = new ConversationParticipantModel();
        $this->messageModel = new MessageModel();
        $this->attachmentModel = new MessageAttachmentModel();
        $this->typingModel = new ConversationTypingModel();
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    public function index()
    {
        $this->checkPermission('messages.view');

        $companyId = session('company_id');
        $userId = session('user_id');
        $box = $this->request->getGet('box') ?? 'inbox';
        $query = trim((string)$this->request->getGet('q'));

        $conversations = $this->conversationModel->getUserConversations($userId, $companyId);

        foreach ($conversations as &$conversation) {
            $participants = $this->participantModel->getParticipants($conversation['id']);
            $names = [];
            foreach ($participants as $p) {
                if ((int)$p['user_id'] === (int)$userId) {
                    continue;
                }
                $names[] = trim($p['first_name'] . ' ' . $p['last_name']);
            }
            $conversation['participant_names'] = implode(', ', $names);
        }
        unset($conversation);

        if ($box === 'sent') {
            $conversations = array_filter($conversations, function ($conv) use ($userId) {
                return (int)$conv['created_by'] === (int)$userId;
            });
        }

        if ($box === 'drafts') {
            $conversations = [];
        }

        if ($query !== '') {
            $conversations = array_filter($conversations, function ($conv) use ($query) {
                $haystack = strtolower(
                    ($conv['participant_names'] ?? '') . ' ' .
                    ($conv['last_message'] ?? '')
                );
                return strpos($haystack, strtolower($query)) !== false;
            });
        }

        $data = [
            'title' => 'Messages',
            'pageTitle' => 'Inbox',
            'box' => $box,
            'query' => $query,
            'conversations' => $conversations,
        ];

        return view('messages/inbox', $data);
    }

    public function sent()
    {
        return redirect()->to(base_url('admin/messages?box=sent'));
    }

    public function drafts()
    {
        return redirect()->to(base_url('admin/messages?box=drafts'));
    }

    public function new()
    {
        $this->checkPermission('messages.create');

        $companyId = session('company_id');
        $userId = session('user_id');

        $users = $this->userModel->where('company_id', $companyId)
            ->where('id !=', $userId)
            ->orderBy('first_name', 'ASC')
            ->findAll();

        $data = [
            'title' => 'New Message',
            'pageTitle' => 'Start Conversation',
            'users' => $users,
        ];

        return view('messages/new', $data);
    }

    public function start()
    {
        $this->checkPermission('messages.create');

        $companyId = session('company_id');
        $userId = session('user_id');
        $recipientIdsStr = trim((string)$this->request->getPost('recipient_ids'));
        $messageBody = trim((string)$this->request->getPost('message'));
        $attachment = $this->request->getFile('attachment');
        $hasAttachment = $attachment && $attachment->getError() !== UPLOAD_ERR_NO_FILE;

        if (!$recipientIdsStr || ($messageBody === '' && !$hasAttachment)) {
            return redirect()->back()->with('error', 'Please select at least one recipient and enter a message or attach a file.');
        }

        // Parse recipient IDs
        $recipientIds = array_filter(array_map('intval', explode(',', $recipientIdsStr)));
        
        if (empty($recipientIds)) {
            return redirect()->back()->with('error', 'Please select at least one recipient.');
        }

        $this->db->transStart();

        // Create conversation with all participants
        $conversationId = $this->conversationModel->insert([
            'company_id' => $companyId,
            'subject' => null,
            'created_by' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Add sender as participant
        $this->participantModel->insert([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'last_read_at' => date('Y-m-d H:i:s'),
            'added_at' => date('Y-m-d H:i:s'),
        ]);

        // Add all recipients as participants
        foreach ($recipientIds as $recipientId) {
            if ((int)$recipientId === (int)$userId) {
                continue; // Skip if recipient is sender
            }
            
            $this->participantModel->insert([
                'conversation_id' => $conversationId,
                'user_id' => $recipientId,
                'last_read_at' => null,
                'added_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Insert the message
        $messageId = $this->messageModel->insert([
            'conversation_id' => $conversationId,
            'sender_id' => $userId,
            'body' => $messageBody,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($hasAttachment && $messageId) {
            $this->handleAttachmentUpload($attachment, $messageId);
        }

        $this->participantModel->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->set(['last_read_at' => date('Y-m-d H:i:s')])
            ->update();

        // Send notification to all recipients
        foreach ($recipientIds as $recipientId) {
            if ((int)$recipientId === (int)$userId) {
                continue;
            }
            
            $this->notificationModel->insert([
                'user_id' => $recipientId,
                'company_id' => $companyId,
                'notification_type' => 'in_app',
                'title' => 'New Message',
                'message' => $messageBody ?: '(Message with attachment)',
                'related_type' => 'conversation',
                'related_id' => $conversationId,
                'priority' => 'medium',
                'status' => 'pending',
                'is_read' => 0,
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to start conversation.');
        }

        return redirect()->to(base_url('admin/messages/' . $conversationId));
    }

    public function show($id)
    {
        $this->checkPermission('messages.view');

        $companyId = session('company_id');
        $userId = session('user_id');

        $conversation = $this->conversationModel->find($id);
        if (!$conversation || (int)$conversation['company_id'] !== (int)$companyId) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Conversation not found');
        }

        $participant = $this->participantModel->where('conversation_id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$participant) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }

        $messages = $this->messageModel->getConversationMessages($id);
        $participants = $this->participantModel->getParticipants($id);

        $messageIds = array_column($messages, 'id');
        $attachmentsByMessage = [];
        if (!empty($messageIds)) {
            $attachments = $this->attachmentModel->whereIn('message_id', $messageIds)->findAll();
            foreach ($attachments as $attachment) {
                $attachmentsByMessage[$attachment['message_id']][] = $attachment;
            }
        }

        // Mark as read
        $this->participantModel->update($participant['id'], [
            'last_read_at' => date('Y-m-d H:i:s'),
        ]);

        $data = [
            'title' => 'Conversation',
            'pageTitle' => 'Conversation',
            'conversation' => $conversation,
            'messages' => $messages,
            'participants' => $participants,
            'attachmentsByMessage' => $attachmentsByMessage,
        ];

        return view('messages/conversation', $data);
    }

    public function send($conversationId)
    {
        $this->checkPermission('messages.create');

        $companyId = session('company_id');
        $userId = session('user_id');
        $messageBody = trim((string)$this->request->getPost('message'));
        $attachment = $this->request->getFile('attachment');
        $hasAttachment = $attachment && $attachment->getError() !== UPLOAD_ERR_NO_FILE;

        if ($messageBody === '' && !$hasAttachment) {
            return redirect()->back()->with('error', 'Message cannot be empty.');
        }

        $conversation = $this->conversationModel->find($conversationId);
        if (!$conversation || (int)$conversation['company_id'] !== (int)$companyId) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Conversation not found');
        }

        $participant = $this->participantModel->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->first();

        if (!$participant) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }

        $this->db->transStart();

        $messageId = $this->messageModel->insert([
            'conversation_id' => $conversationId,
            'sender_id' => $userId,
            'body' => $messageBody,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($hasAttachment && $messageId) {
            $this->handleAttachmentUpload($attachment, $messageId);
        }

        $this->conversationModel->update($conversationId, [
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Notify other participants
        $participants = $this->participantModel->where('conversation_id', $conversationId)->findAll();
        foreach ($participants as $p) {
            if ((int)$p['user_id'] === (int)$userId) {
                continue;
            }
            $this->notificationModel->insert([
                'user_id' => $p['user_id'],
                'company_id' => $companyId,
                'notification_type' => 'in_app',
                'title' => 'New Message',
                'message' => $messageBody ?: '(Message with attachment)',
                'related_type' => 'conversation',
                'related_id' => $conversationId,
                'priority' => 'medium',
                'status' => 'pending',
                'is_read' => 0,
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to send message.');
        }

        return redirect()->to(base_url('admin/messages/' . $conversationId));
    }

    public function typing($conversationId)
    {
        $this->checkPermission('messages.create');

        $companyId = session('company_id');
        $userId = session('user_id');

        $conversation = $this->conversationModel->find($conversationId);
        if (!$conversation || (int)$conversation['company_id'] !== (int)$companyId) {
            return $this->response->setJSON(['success' => false]);
        }

        $existing = $this->typingModel->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $this->typingModel->update($existing['id'], ['updated_at' => date('Y-m-d H:i:s')]);
        } else {
            $this->typingModel->insert([
                'conversation_id' => $conversationId,
                'user_id' => $userId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function typingStatus($conversationId)
    {
        $this->checkPermission('messages.view');

        $companyId = session('company_id');
        $userId = session('user_id');

        $conversation = $this->conversationModel->find($conversationId);
        if (!$conversation || (int)$conversation['company_id'] !== (int)$companyId) {
            return $this->response->setJSON(['success' => false, 'users' => []]);
        }

        $since = date('Y-m-d H:i:s', time() - 5);
        $typingUsers = $this->typingModel
            ->select('conversation_typing.*, users.first_name, users.last_name')
            ->join('users', 'users.id = conversation_typing.user_id', 'left')
            ->where('conversation_typing.conversation_id', $conversationId)
            ->where('conversation_typing.updated_at >=', $since)
            ->where('conversation_typing.user_id !=', $userId)
            ->findAll();

        $names = array_map(function ($u) {
            return trim($u['first_name'] . ' ' . $u['last_name']);
        }, $typingUsers);

        return $this->response->setJSON(['success' => true, 'users' => $names]);
    }

    private function handleAttachmentUpload($attachment, $messageId)
    {
        if (!$attachment || $attachment->getError() === UPLOAD_ERR_NO_FILE) {
            return;
        }

        // Get MIME type before moving the file (this is the key fix)
        $mimeType = $attachment->getMimeType();
        $originalName = $attachment->getClientName();
        $fileSize = (int)$attachment->getSize();
        $extension = $attachment->getExtension();

        // Validate file size (2MB max)
        $maxSize = 2 * 1024 * 1024; // 2MB in bytes
        if ($fileSize > $maxSize) {
            log_message('warning', 'File upload rejected: exceeds 2MB limit. Size: ' . $fileSize);
            return;
        }

        $allowedMime = [
            'image/png', 'image/jpeg', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];

        if (!$attachment->isValid() || !in_array($mimeType, $allowedMime, true)) {
            log_message('warning', 'File upload rejected: invalid MIME type. Type: ' . $mimeType);
            return;
        }

        $uploadPath = FCPATH . 'uploads/messages/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = 'msg_' . $messageId . '_' . time() . '.' . $extension;
        if ($attachment->move($uploadPath, $newName)) {
            $this->attachmentModel->insert([
                'message_id' => $messageId,
                'file_name' => $originalName,
                'file_path' => 'uploads/messages/' . $newName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function checkPermission($permission)
    {
        if (!hasPermission($permission)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }
    }

    /**
     * Verify user is a participant in the conversation
     * Prevents data leaks if user manually accesses conversation IDs they're not part of
     */
    private function verifyConversationAccess($conversationId)
    {
        $userId = session('user_id');
        
        $participant = $this->participantModel
            ->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->first();

        if (!$participant) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }

        return $participant;
    }
}
