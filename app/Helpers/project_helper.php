<?php

if (!function_exists('formatBytes')) {
    /**
     * Format bytes into human readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    function formatBytes($bytes, $precision = 2)
    {
        if ($bytes == 0) return '0 Bytes';
        
        $k = 1024;
        $dm = $precision < 0 ? 0 : $precision;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB'];
        
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), $dm) . ' ' . $sizes[$i];
    }
}

if (!function_exists('getStatusBadgeClass')) {
    /**
     * Get Bootstrap badge class for status
     *
     * @param string $status
     * @param string $type (task, project, milestone)
     * @return string
     */
    function getStatusBadgeClass($status, $type = 'task')
    {
        $classes = [
            'task' => [
                'pending' => 'warning',
                'in_progress' => 'primary',
                'review' => 'info',
                'completed' => 'success',
                'cancelled' => 'danger'
            ],
            'project' => [
                'planning' => 'warning',
                'active' => 'primary',
                'on_hold' => 'secondary',
                'completed' => 'success',
                'cancelled' => 'danger'
            ],
            'milestone' => [
                'pending' => 'warning',
                'in_progress' => 'primary',
                'completed' => 'success',
                'cancelled' => 'danger'
            ]
        ];
        
        return $classes[$type][$status] ?? 'secondary';
    }
}

if (!function_exists('getPriorityBadgeClass')) {
    /**
     * Get Bootstrap badge class for priority
     *
     * @param string $priority
     * @return string
     */
    function getPriorityBadgeClass($priority)
    {
        $classes = [
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'urgent' => 'dark'
        ];
        
        return $classes[$priority] ?? 'secondary';
    }
}

if (!function_exists('formatStatus')) {
    /**
     * Format status for display
     *
     * @param string $status
     * @return string
     */
    function formatStatus($status)
    {
        return ucwords(str_replace('_', ' ', $status));
    }
}

if (!function_exists('isOverdue')) {
    /**
     * Check if a date is overdue
     *
     * @param string $dueDate
     * @param string $status
     * @return bool
     */
    function isOverdue($dueDate, $status = null)
    {
        if (!$dueDate || $status === 'completed') {
            return false;
        }
        
        return strtotime($dueDate) < time();
    }
}

if (!function_exists('getFileIcon')) {
    /**
     * Get Font Awesome icon class for file type
     *
     * @param string $filename
     * @return string
     */
    function getFileIcon($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $icons = [
            'pdf' => 'fas fa-file-pdf text-danger',
            'doc' => 'fas fa-file-word text-primary',
            'docx' => 'fas fa-file-word text-primary',
            'xls' => 'fas fa-file-excel text-success',
            'xlsx' => 'fas fa-file-excel text-success',
            'ppt' => 'fas fa-file-powerpoint text-warning',
            'pptx' => 'fas fa-file-powerpoint text-warning',
            'jpg' => 'fas fa-file-image text-info',
            'jpeg' => 'fas fa-file-image text-info',
            'png' => 'fas fa-file-image text-info',
            'gif' => 'fas fa-file-image text-info',
            'zip' => 'fas fa-file-archive text-secondary',
            'rar' => 'fas fa-file-archive text-secondary',
            'txt' => 'fas fa-file-alt text-muted',
            'csv' => 'fas fa-file-csv text-success'
        ];
        
        return $icons[$ext] ?? 'fas fa-file text-muted';
    }
}

if (!function_exists('calculateProgress')) {
    /**
     * Calculate progress percentage
     *
     * @param int $completed
     * @param int $total
     * @return float
     */
    function calculateProgress($completed, $total)
    {
        if ($total == 0) return 0;
        return round(($completed / $total) * 100, 1);
    }
}

if (!function_exists('getProgressBarClass')) {
    /**
     * Get progress bar color class based on percentage
     *
     * @param float $percentage
     * @return string
     */
    function getProgressBarClass($percentage)
    {
        if ($percentage >= 75) return 'bg-success';
        if ($percentage >= 50) return 'bg-info';
        if ($percentage >= 25) return 'bg-warning';
        return 'bg-danger';
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format date for display
     */
    function formatDate($date, $format = 'M d, Y') {
        if (empty($date) || $date === '0000-00-00') {
            return 'N/A';
        }
        
        return date($format, strtotime($date));
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format datetime for display
     */
    function formatDateTime($datetime, $format = 'M d, Y g:i A') {
        if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
            return 'N/A';
        }
        
        return date($format, strtotime($datetime));
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Convert timestamp to time ago format
     *
     * @param string $datetime
     * @return string
     */
    function timeAgo($datetime)
    {
        if (empty($datetime)) {
            return 'N/A';
        }
        
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2592000) return floor($time/86400) . ' days ago';
        if ($time < 31104000) return floor($time/2592000) . ' months ago';
        
        return floor($time/31104000) . ' years ago';
    }
}

if (!function_exists('generateAvatarInitials')) {
    /**
     * Generate initials for avatar
     *
     * @param string $firstName
     * @param string $lastName
     * @return string
     */
    function generateAvatarInitials($firstName, $lastName = '')
    {
        $firstInitial = $firstName ? strtoupper(substr($firstName, 0, 1)) : '';
        $lastInitial = $lastName ? strtoupper(substr($lastName, 0, 1)) : '';
        
        return $firstInitial . $lastInitial;
    }
}

if (!function_exists('getTaskRowClass')) {
    /**
     * Get table row class for task based on status and due date
     *
     * @param array $task
     * @return string
     */
    function getTaskRowClass($task)
    {
        if (isset($task['planned_end_date']) && $task['planned_end_date'] < date('Y-m-d') && $task['status'] !== 'completed') {
            return 'table-warning'; // Overdue
        }
        if ($task['status'] === 'completed') {
            return 'table-success'; // Completed
        }
        return '';
    }
}

if (!function_exists('getStatusBadge')) {
    /**
     * Get Tailwind CSS status badge HTML
     *
     * @param string $status
     * @param string $type (task, project, milestone)
     * @return string
     */
    function getStatusBadge($status, $type = 'task')
    {
        $classes = [
            'task' => [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'in_progress' => 'bg-blue-100 text-blue-800',
                'review' => 'bg-purple-100 text-purple-800',
                'completed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800'
            ],
            'project' => [
                'planning' => 'bg-yellow-100 text-yellow-800',
                'active' => 'bg-blue-100 text-blue-800',
                'on_hold' => 'bg-gray-100 text-gray-800',
                'completed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800'
            ],
            'milestone' => [
                'upcoming' => 'bg-blue-100 text-blue-800',
                'in_progress' => 'bg-yellow-100 text-yellow-800',
                'completed' => 'bg-green-100 text-green-800',
                'delayed' => 'bg-red-100 text-red-800'
            ]
        ];
        
        $class = $classes[$type][$status] ?? 'bg-gray-100 text-gray-800';
        $statusText = formatStatus($status);
        
        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$class}\">{$statusText}</span>";
    }
}

if (!function_exists('getPriorityBadge')) {
    /**
     * Get Tailwind CSS priority badge HTML
     *
     * @param string $priority
     * @return string
     */
    function getPriorityBadge($priority)
    {
        $classes = [
            'low' => 'bg-gray-100 text-gray-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800'
        ];
        
        $class = $classes[$priority] ?? 'bg-gray-100 text-gray-800';
        $priorityText = ucfirst($priority);
        
        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$class}\">{$priorityText}</span>";
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format currency with symbol
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    function formatCurrency($amount, $currency = 'USD')
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'JPY' => '¥'
        ];
        
        $symbol = $symbols[$currency] ?? $currency;
        return $symbol . number_format($amount, 2);
    }
}
