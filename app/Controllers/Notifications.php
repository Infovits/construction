<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        helper(['url']);
    }

    public function index()
    {
        $this->checkPermission('notifications.view');

        $userId = session('user_id');
        $companyId = session('company_id');

        $notifications = $this->notificationModel->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'DESC')
            ->findAll(50);

        $data = [
            'title' => 'Notifications',
            'pageTitle' => 'Notifications',
            'notifications' => $notifications,
        ];

        return view('notifications/index', $data);
    }

    public function recent()
    {
        $this->checkPermission('notifications.view');

        $userId = session('user_id');
        $companyId = session('company_id');

        $notifications = $this->notificationModel->getRecent($userId, $companyId, 10);
        $unreadCount = $this->notificationModel->getUnreadCount($userId, $companyId);

        foreach ($notifications as &$notification) {
            $notification['time'] = format_datetime($notification['created_at']);
        }
        unset($notification);

        return $this->response->setJSON([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markRead($id)
    {
        $this->checkPermission('notifications.view');

        $userId = session('user_id');
        $companyId = session('company_id');

        $notification = $this->notificationModel->find($id);
        if (!$notification || (int)$notification['user_id'] !== (int)$userId || (int)$notification['company_id'] !== (int)$companyId) {
            return $this->response->setJSON(['success' => false]);
        }

        $this->notificationModel->update($id, ['is_read' => 1]);
        return $this->response->setJSON(['success' => true]);
    }

    private function checkPermission($permission)
    {
        if (!hasPermission($permission)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }
    }
}
