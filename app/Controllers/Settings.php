<?php

namespace App\Controllers;

use App\Models\SettingModel;

class Settings extends BaseController
{
    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        return redirect()->to('/admin/settings/general');
    }

    public function general()
    {
        $this->checkPermission('settings.view');

        return $this->renderSettingsPage('general', 'General Settings');
    }

    public function security()
    {
        $this->checkPermission('settings.view');

        return $this->renderSettingsPage('security', 'Security Settings');
    }

    public function preferences()
    {
        $this->checkPermission('settings.view');

        return $this->renderSettingsPage('preferences', 'Preferences');
    }

    public function integrations()
    {
        $this->checkPermission('settings.view');

        return $this->renderSettingsPage('integrations', 'Integrations');
    }

    public function save($section)
    {
        $this->checkPermission('settings.edit');

        $allowedSections = ['general', 'security', 'preferences', 'integrations'];
        if (!in_array($section, $allowedSections, true)) {
            return redirect()->back()->with('error', 'Invalid settings section.');
        }

        $companyId = session('company_id');
        $userId = session('user_id');

        $payload = $this->buildSectionPayload($section);
        
        // Save each setting individually
        $success = true;
        foreach ($payload as $key => $value) {
            $settingKey = $section . '_' . $key;
            if (!$this->settingModel->saveSystemSetting($companyId, $settingKey, $value, $userId, $section)) {
                $success = false;
            }
        }

        if ($success) {
            return redirect()->back()->with('success', ucfirst($section) . ' settings updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to save settings.');
    }

    private function renderSettingsPage(string $activeTab, string $pageTitle)
    {
        $companyId = session('company_id');
        $allSettings = $this->settingModel->getSystemSettings($companyId);
        
        // Extract settings for the active tab, removing the prefix
        $settings = [];
        if (isset($allSettings[$activeTab])) {
            foreach ($allSettings[$activeTab] as $key => $value) {
                // Remove the section prefix (e.g., 'general_' from 'general_company_name')
                $cleanKey = str_replace($activeTab . '_', '', $key);
                $settings[$cleanKey] = $value;
            }
        }

        $data = [
            'title' => 'Settings',
            'pageTitle' => $pageTitle,
            'settings' => $settings
        ];

        return view('settings/' . $activeTab, $data);
    }

    private function buildSectionPayload(string $section): array
    {
        switch ($section) {
            case 'general':
                return [
                    'company_name' => $this->request->getPost('company_name'),
                    'timezone' => $this->request->getPost('timezone'),
                    'date_format' => $this->request->getPost('date_format'),
                    'currency' => $this->request->getPost('currency')
                ];
            case 'security':
                return [
                    'password_policy' => $this->request->getPost('password_policy'),
                    'session_timeout' => $this->request->getPost('session_timeout'),
                    'two_factor' => $this->request->getPost('two_factor') ? true : false
                ];
            case 'preferences':
                return [
                    'theme' => $this->request->getPost('theme'),
                    'items_per_page' => (int) $this->request->getPost('items_per_page'),
                    'email_notifications' => $this->request->getPost('email_notifications') ? true : false
                ];
            case 'integrations':
                return [
                    'webhook_url' => $this->request->getPost('webhook_url'),
                    'slack_webhook' => $this->request->getPost('slack_webhook')
                ];
            default:
                return [];
        }
    }

    private function checkPermission($permission)
    {
        if (!hasPermission($permission)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }
    }
}
