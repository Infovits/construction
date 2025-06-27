<?php

if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount, $currency = 'MWK') {
        return $currency . ' ' . number_format($amount, 2);
    }
}

if (!function_exists('get_initials')) {
    function get_initials($name) {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials;
    }
}

if (!function_exists('hasPermission')) {
    function hasPermission($permission) {
        try {
            if (!session('user_id')) {
                return false;
            }
            
            $roleModel = new \App\Models\RoleModel();
            $userPermissions = $roleModel->getUserPermissions(session('user_id'));
            
            // Check for wildcard permission (super admin)
            if (in_array('*', $userPermissions)) {
                return true;
            }
            
            // Check for exact permission
            if (in_array($permission, $userPermissions)) {
                return true;
            }
            
            // Check for module wildcard permission (e.g., users.*)
            $parts = explode('.', $permission);
            if (count($parts) > 1) {
                $moduleWildcard = $parts[0] . '.*';
                if (in_array($moduleWildcard, $userPermissions)) {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            // Log error and return false for safety
            log_message('error', 'Permission check failed: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('getUserRole')) {
    function getUserRole() {
        if (!session('user_id')) {
            return null;
        }
        
        $userModel = new \App\Models\UserModel();
        return $userModel->getUserRole(session('user_id'));
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return session('is_logged_in') === true && session('user_id');
    }
}