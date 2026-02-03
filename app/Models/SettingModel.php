<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'system_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'company_id', 'setting_key', 'setting_value', 'setting_type',
        'category', 'description', 'is_public', 'updated_by', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = '';
    protected $updatedField = 'updated_at';
    
    /**
     * Get notification settings for a specific company and type
     * 
     * @param int $companyId The company ID
     * @param string $type The notification type (e.g., 'inventory_low_stock')
     * @return array The notification settings
     */
    public function getNotificationSettings($companyId, $type)
    {
        $settings = $this->where('company_id', $companyId)
            ->where('category', 'notification')
            ->where('setting_key', $type)
            ->first();
            
        if ($settings && !empty($settings['setting_value'])) {
            return json_decode($settings['setting_value'], true);
        }
        
        // Return default settings if none found
        return [
            'email_enabled' => false,
            'email_recipients' => '',
            'push_enabled' => false,
            'frequency' => 'daily',
            'threshold' => 'reorder_level',
            'custom_threshold_value' => 10,
            'updated_at' => null,
            'updated_by' => null
        ];
    }
    
    /**
     * Save notification settings for a specific company and type
     * 
     * @param int $companyId The company ID
     * @param string $type The notification type (e.g., 'inventory_low_stock')
     * @param array $settings The settings to save
     * @return bool True if saved successfully, false otherwise
     */
    public function saveNotificationSettings($companyId, $type, array $settings)
    {
        $existingSettings = $this->where('company_id', $companyId)
            ->where('category', 'notification')
            ->where('setting_key', $type)
            ->first();
            
        $settingValue = json_encode($settings);
        
        if ($existingSettings) {
            // Update existing settings
            return $this->update($existingSettings['id'], [
                'setting_value' => $settingValue,
                'setting_type' => 'json',
                'updated_by' => $settings['updated_by'] ?? null
            ]);
        } else {
            // Create new settings
            return $this->insert([
                'company_id' => $companyId,
                'category' => 'notification',
                'setting_key' => $type,
                'setting_value' => $settingValue,
                'setting_type' => 'json',
                'updated_by' => $settings['updated_by'] ?? null
            ]);
        }
    }
    
    /**
     * Get general system settings
     * 
     * @param int $companyId The company ID
     * @param string $key The setting key (optional)
     * @return mixed The setting value or array of settings
     */
    public function getSystemSettings($companyId, $key = null)
    {
        $query = $this->where('company_id', $companyId);
            
        if ($key !== null) {
            $query->where('setting_key', $key);
            $setting = $query->first();
            
            if ($setting) {
                // Decode based on setting_type
                if ($setting['setting_type'] === 'json') {
                    return json_decode($setting['setting_value'], true);
                } elseif ($setting['setting_type'] === 'boolean') {
                    return (bool) $setting['setting_value'];
                } elseif ($setting['setting_type'] === 'number') {
                    return (int) $setting['setting_value'];
                }
                return $setting['setting_value'];
            }
            
            return null;
        }
        
        $settings = $query->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $category = $setting['category'] ?? 'general';
            if (!isset($result[$category])) {
                $result[$category] = [];
            }
            
            // Decode based on setting_type
            if ($setting['setting_type'] === 'json') {
                $result[$category][$setting['setting_key']] = json_decode($setting['setting_value'], true);
            } elseif ($setting['setting_type'] === 'boolean') {
                $result[$category][$setting['setting_key']] = (bool) $setting['setting_value'];
            } elseif ($setting['setting_type'] === 'number') {
                $result[$category][$setting['setting_key']] = (int) $setting['setting_value'];
            } else {
                $result[$category][$setting['setting_key']] = $setting['setting_value'];
            }
        }
        
        return $result;
    }
    
    /**
     * Save system setting
     * 
     * @param int $companyId The company ID
     * @param string $key The setting key
     * @param mixed $value The setting value
     * @param int $userId The user ID making the change
     * @return bool True if saved successfully, false otherwise
     */
    public function saveSystemSetting($companyId, $key, $value, $userId, $category = 'general')
    {
        $existingSetting = $this->where('company_id', $companyId)
            ->where('setting_key', $key)
            ->first();
        
        // Determine setting type
        $settingType = 'string';
        if (is_array($value)) {
            $settingType = 'json';
            $settingValue = json_encode($value);
        } elseif (is_bool($value)) {
            $settingType = 'boolean';
            $settingValue = $value ? '1' : '0';
        } elseif (is_numeric($value)) {
            $settingType = 'number';
            $settingValue = (string) $value;
        } else {
            $settingValue = $value;
        }
        
        if ($existingSetting) {
            // Update existing setting
            return $this->update($existingSetting['id'], [
                'setting_value' => $settingValue,
                'setting_type' => $settingType,
                'category' => $category,
                'updated_by' => $userId
            ]);
        } else {
            // Create new setting
            return $this->insert([
                'company_id' => $companyId,
                'setting_key' => $key,
                'setting_value' => $settingValue,
                'setting_type' => $settingType,
                'category' => $category,
                'updated_by' => $userId
            ]);
        }
    }
}
