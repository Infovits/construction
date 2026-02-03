<?php

/**
 * Settings Helper Functions
 * Provides easy access to system settings throughout the application
 */

if (!function_exists('get_setting')) {
    /**
     * Get a single setting value
     * 
     * @param string $key The setting key (e.g., 'general_company_name')
     * @param mixed $default Default value if setting not found
     * @return mixed
     */
    function get_setting($key, $default = null)
    {
        static $settingModel = null;
        static $cache = [];
        
        if ($settingModel === null) {
            $settingModel = new \App\Models\SettingModel();
        }
        
        // Check cache first
        $cacheKey = session('company_id') . '_' . $key;
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }
        
        // Get from database
        $companyId = session('company_id');
        if (!$companyId) {
            return $default;
        }
        
        $setting = $settingModel
            ->where('company_id', $companyId)
            ->where('setting_key', $key)
            ->first();
        
        if ($setting) {
            $value = $setting['setting_value'];
            
            // Type conversion based on setting_type
            switch ($setting['setting_type']) {
                case 'boolean':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'number':
                    $value = is_numeric($value) ? (float)$value : $default;
                    break;
                case 'json':
                    $value = json_decode($value, true);
                    break;
            }
            
            $cache[$cacheKey] = $value;
            return $value;
        }
        
        return $default;
    }
}

if (!function_exists('get_company_name')) {
    /**
     * Get company name from settings
     * 
     * @return string
     */
    function get_company_name()
    {
        return get_setting('general_company_name', 'Construction Management System');
    }
}

if (!function_exists('get_company_logo')) {
    /**
     * Get company logo URL from settings
     * 
     * @return string
     */
    function get_company_logo()
    {
        $logo = get_setting('general_company_logo', '');
        if ($logo && file_exists(FCPATH . $logo)) {
            return base_url($logo);
        }
        return base_url('assets/images/logo-placeholder.png');
    }
}

if (!function_exists('get_timezone')) {
    /**
     * Get timezone from settings
     * 
     * @return string
     */
    function get_timezone()
    {
        return get_setting('general_timezone', 'UTC');
    }
}

if (!function_exists('get_date_format')) {
    /**
     * Get date format from settings
     * 
     * @return string
     */
    function get_date_format()
    {
        return get_setting('general_date_format', 'Y-m-d');
    }
}

if (!function_exists('get_currency')) {
    /**
     * Get default currency from settings
     * 
     * @return string
     */
    function get_currency()
    {
        return get_setting('general_currency', 'MWK');
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date according to system settings
     * 
     * @param string|null $date
     * @param string|null $format Override format
     * @return string
     */
    function format_date($date, $format = null)
    {
        if (empty($date)) {
            return '';
        }
        
        $format = $format ?? get_date_format();
        
        try {
            $dateTime = new \DateTime($date);
            return $dateTime->format($format);
        } catch (\Exception $e) {
            return $date;
        }
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format datetime according to system settings
     * 
     * @param string|null $datetime
     * @return string
     */
    function format_datetime($datetime)
    {
        if (empty($datetime)) {
            return '';
        }
        
        $format = get_date_format() . ' H:i:s';
        
        try {
            $dt = new \DateTime($datetime);
            return $dt->format($format);
        } catch (\Exception $e) {
            return $datetime;
        }
    }
}

if (!function_exists('format_money')) {
    /**
     * Format money according to system currency settings
     * 
     * @param float $amount
     * @param string|null $currency Override currency
     * @return string
     */
    function format_money($amount, $currency = null)
    {
        $currency = $currency ?? get_currency();
        
        // Currency symbols
        $symbols = [
            'MWK' => 'MK',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'ZAR' => 'R',
        ];
        
        $symbol = $symbols[$currency] ?? $currency;
        
        return $symbol . ' ' . number_format($amount, 2);
    }
}

if (!function_exists('get_items_per_page')) {
    /**
     * Get items per page from settings
     * 
     * @return int
     */
    function get_items_per_page()
    {
        return (int)get_setting('preferences_items_per_page', 25);
    }
}

if (!function_exists('get_theme')) {
    /**
     * Get theme from settings
     * 
     * @return string
     */
    function get_theme()
    {
        return get_setting('preferences_theme', 'light');
    }
}

if (!function_exists('is_setting_enabled')) {
    /**
     * Check if a boolean setting is enabled
     * 
     * @param string $key
     * @return bool
     */
    function is_setting_enabled($key)
    {
        return (bool)get_setting($key, false);
    }
}

if (!function_exists('clear_settings_cache')) {
    /**
     * Clear settings cache (call after updating settings)
     * 
     * @return void
     */
    function clear_settings_cache()
    {
        // This will be cleared on next request since static cache is per-request
        // For persistent cache, implement cache clearing here
    }
}
