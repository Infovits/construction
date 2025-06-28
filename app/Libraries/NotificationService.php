<?php

namespace App\Libraries;

/**
 * NotificationService - Handles sending notifications via various channels
 */
class NotificationService
{
    protected $email;
    
    public function __construct()
    {
        $this->email = \Config\Services::email();
    }
    
    /**
     * Send low stock notification
     * 
     * @param array $notificationSettings The notification settings
     * @param array $lowStockItems List of items with low stock
     * @param array $userData User data for fallback recipient
     * @return array Results of notification attempts
     */
    public function sendLowStockNotification(array $notificationSettings, array $lowStockItems, array $userData)
    {
        $result = [
            'emailSent' => false,
            'pushSent' => false,
            'message' => ''
        ];
        
        if (empty($lowStockItems)) {
            $result['message'] = 'No low stock items to notify about';
            return $result;
        }
        
        // Send email notification if enabled
        if (!empty($notificationSettings['email_enabled'])) {
            $result['emailSent'] = $this->sendLowStockEmail($notificationSettings, $lowStockItems, $userData);
        }
        
        // Send push notification if enabled
        if (!empty($notificationSettings['push_enabled'])) {
            $result['pushSent'] = $this->sendLowStockPushNotification($notificationSettings, $lowStockItems, $userData);
        }
        
        if ($result['emailSent'] || $result['pushSent']) {
            $result['message'] = 'Notifications sent successfully';
        } else {
            $result['message'] = 'No notification methods are enabled';
        }
        
        return $result;
    }
    
    /**
     * Send low stock email notification
     * 
     * @param array $notificationSettings The notification settings
     * @param array $lowStockItems List of items with low stock
     * @param array $userData User data for fallback recipient
     * @return bool True if email sent successfully
     */
    protected function sendLowStockEmail(array $notificationSettings, array $lowStockItems, array $userData)
    {
        // Get email recipients
        $recipients = [];
        if (!empty($notificationSettings['email_recipients'])) {
            $recipients = explode(',', $notificationSettings['email_recipients']);
            $recipients = array_map('trim', $recipients);
        } else {
            // Use user's email as default
            $recipients[] = $userData['email'];
        }
        
        // Prepare email content
        $message = $this->prepareLowStockEmailContent($lowStockItems);
        
        // Configure email
        $this->email->clear();
        $this->email->setFrom('no-reply@constructionapp.com', 'Inventory System');
        $this->email->setTo($recipients);
        $this->email->setSubject('Low Stock Notification - Action Required');
        $this->email->setMessage($message);
        $this->email->setMailType('html');
        
        // Send email
        return $this->email->send();
    }
    
    /**
     * Prepare the HTML content for low stock email
     * 
     * @param array $lowStockItems List of items with low stock
     * @return string HTML content
     */
    protected function prepareLowStockEmailContent(array $lowStockItems)
    {
        $baseUrl = base_url();
        
        $content = "
            <h2>Low Stock Items Alert</h2>
            <p>The following items are currently below their recommended stock levels and may require restocking:</p>
            <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
                <thead>
                    <tr style='background-color: #f1f1f1;'>
                        <th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>Item Code</th>
                        <th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>Name</th>
                        <th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>Category</th>
                        <th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>Current Stock</th>
                        <th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>Reorder Level</th>
                        <th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>Warehouse</th>
                    </tr>
                </thead>
                <tbody>
        ";
        
        foreach ($lowStockItems as $item) {
            $content .= "
                <tr>
                    <td style='padding: 10px; text-align: left; border: 1px solid #ddd;'>{$item['item_code']}</td>
                    <td style='padding: 10px; text-align: left; border: 1px solid #ddd;'>{$item['name']}</td>
                    <td style='padding: 10px; text-align: left; border: 1px solid #ddd;'>{$item['category_name']}</td>
                    <td style='padding: 10px; text-align: left; border: 1px solid #ddd;'>{$item['current_quantity']} {$item['unit']}</td>
                    <td style='padding: 10px; text-align: left; border: 1px solid #ddd;'>{$item['minimum_quantity']} {$item['unit']}</td>
                    <td style='padding: 10px; text-align: left; border: 1px solid #ddd;'>{$item['warehouse_name']}</td>
                </tr>
            ";
        }
        
        $content .= "
                </tbody>
            </table>
            <div style='margin-top: 20px;'>
                <p>Please take action to restock these items to maintain optimal inventory levels.</p>
                <a href='{$baseUrl}/materials/lowStockNotifications' 
                   style='display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>
                    View Low Stock Items
                </a>
            </div>
            <hr style='margin-top: 30px;'>
            <p style='font-size: 12px; color: #777;'>This is an automated message from your Inventory Management System.</p>
        ";
        
        return $content;
    }
    
    /**
     * Send low stock push notification
     * 
     * @param array $notificationSettings The notification settings
     * @param array $lowStockItems List of items with low stock
     * @param array $userData User data for recipient
     * @return bool True if push notification sent successfully
     */
    protected function sendLowStockPushNotification(array $notificationSettings, array $lowStockItems, array $userData)
    {
        $itemCount = count($lowStockItems);
        $title = "Low Stock Alert";
        $body = "{$itemCount} items require restocking. Tap to view details.";
        
        // In a real implementation, this would integrate with FCM, OneSignal, or another push service
        // For now, we'll log the notification and simulate success
        log_message('info', 'Push notification would be sent: ' . json_encode([
            'title' => $title,
            'body' => $body,
            'userId' => $userData['id'],
            'itemCount' => $itemCount
        ]));
        
        // This would be replaced with actual push notification integration code
        /*
        // Example code for Firebase Cloud Messaging (FCM)
        $fcmTokens = $this->getUserFcmTokens($userData['id']);
        
        if (empty($fcmTokens)) {
            return false;
        }
        
        $fcmData = [
            'notification' => [
                'title' => $title,
                'body' => $body,
                'click_action' => 'OPEN_LOW_STOCK_SCREEN'
            ],
            'data' => [
                'screen' => 'lowStockNotifications',
                'count' => (string) $itemCount
            ],
            'registration_ids' => $fcmTokens
        ];
        
        $headers = [
            'Authorization: key=' . FCM_SERVER_KEY,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmData));
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($result, true);
        return isset($result['success']) && $result['success'] > 0;
        */
        
        // Simulate successful push notification
        return true;
    }
    
    /**
     * Send test notification
     * 
     * @param array $notificationSettings The notification settings
     * @param array $userData User data for recipient
     * @return array Results of test notification attempts
     */
    public function sendTestNotification(array $notificationSettings, array $userData)
    {
        $result = [
            'emailSent' => false,
            'pushSent' => false,
            'message' => ''
        ];
        
        // Send test email notification if enabled
        if (!empty($notificationSettings['email_enabled'])) {
            $result['emailSent'] = $this->sendTestEmail($notificationSettings, $userData);
        }
        
        // Send test push notification if enabled
        if (!empty($notificationSettings['push_enabled'])) {
            $result['pushSent'] = $this->sendTestPushNotification($userData);
        }
        
        if ($result['emailSent'] || $result['pushSent']) {
            $result['message'] = 'Test notifications sent successfully';
        } else {
            $result['message'] = 'No notification methods are enabled';
        }
        
        return $result;
    }
    
    /**
     * Send test email notification
     * 
     * @param array $notificationSettings The notification settings
     * @param array $userData User data for fallback recipient
     * @return bool True if email sent successfully
     */
    protected function sendTestEmail(array $notificationSettings, array $userData)
    {
        // Get email recipients
        $recipients = [];
        if (!empty($notificationSettings['email_recipients'])) {
            $recipients = explode(',', $notificationSettings['email_recipients']);
            $recipients = array_map('trim', $recipients);
        } else {
            // Use user's email as default
            $recipients[] = $userData['email'];
        }
        
        // Configure email
        $this->email->clear();
        $this->email->setFrom('no-reply@constructionapp.com', 'Inventory System');
        $this->email->setTo($recipients);
        $this->email->setSubject('Test Low Stock Notification');
        
        $message = "
            <h2>Low Stock Notification Test</h2>
            <p>This is a test notification from the inventory management system.</p>
            <p>If you received this email, your notification settings are working correctly.</p>
            <p>You will receive notifications about low stock items based on your notification settings.</p>
            <hr>
            <p>This is an automated message, please do not reply to this email.</p>
        ";
        
        $this->email->setMessage($message);
        $this->email->setMailType('html');
        
        // Send email
        return $this->email->send();
    }
    
    /**
     * Send test push notification
     * 
     * @param array $userData User data for recipient
     * @return bool True if push notification sent successfully
     */
    protected function sendTestPushNotification(array $userData)
    {
        $title = "Test Notification";
        $body = "This is a test notification from the inventory management system.";
        
        // In a real implementation, this would integrate with FCM, OneSignal, or another push service
        // For now, we'll log the notification and simulate success
        log_message('info', 'Test push notification would be sent: ' . json_encode([
            'title' => $title,
            'body' => $body,
            'userId' => $userData['id']
        ]));
        
        // Simulate successful push notification
        return true;
    }
}
