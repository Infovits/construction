<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'company_id', 'setting_type', 'setting_key', 'setting_value', 
        'created_by', 'updated_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
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
            ->where('setting_type', 'notification')
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
            ->where('setting_type', 'notification')
            ->where('setting_key', $type)
            ->first();
            
        $settingValue = json_encode($settings);
        
        if ($existingSettings) {
            // Update existing settings
            return $this->update($existingSettings['id'], [
                'setting_value' => $settingValue,
                'updated_by' => $settings['updated_by'] ?? null
            ]);
        } else {
            // Create new settings
            return $this->insert([
                'company_id' => $companyId,
                'setting_type' => 'notification',
                'setting_key' => $type,
                'setting_value' => $settingValue,
                'created_by' => $settings['updated_by'] ?? null,
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
        $query = $this->where('company_id', $companyId)
            ->where('setting_type', 'system');
            
        if ($key !== null) {
            $query->where('setting_key', $key);
            $setting = $query->first();
            
            if ($setting) {
                return json_decode($setting['setting_value'], true);
            }
            
            return null;
        }
        
        $settings = $query->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = json_decode($setting['setting_value'], true);
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
    public function saveSystemSetting($companyId, $key, $value, $userId)
    {
        $existingSetting = $this->where('company_id', $companyId)
            ->where('setting_type', 'system')
            ->where('setting_key', $key)
            ->first();
            
        $settingValue = json_encode($value);
        
        if ($existingSetting) {
            // Update existing setting
            return $this->update($existingSetting['id'], [
                'setting_value' => $settingValue,
                'updated_by' => $userId
            ]);
        } else {
            // Create new setting
            return $this->insert([
                'company_id' => $companyId,
                'setting_type' => 'system',
                'setting_key' => $key,
                'setting_value' => $settingValue,
                'created_by' => $userId,
                'updated_by' => $userId
            ]);
        }
    }
}
