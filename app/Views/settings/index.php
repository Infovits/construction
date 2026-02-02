<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
	$tab = $activeTab ?? 'general';
?>

<div class="space-y-6">
	<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
		<div>
			<?php if ($tab === 'general'): ?>
				<h1 class="text-2xl font-bold text-gray-900">General Settings</h1>
				<p class="text-gray-600">Manage company information and regional settings</p>
			<?php elseif ($tab === 'security'): ?>
				<h1 class="text-2xl font-bold text-gray-900">Security Settings</h1>
				<p class="text-gray-600">Configure security policies and authentication</p>
			<?php elseif ($tab === 'preferences'): ?>
				<h1 class="text-2xl font-bold text-gray-900">Preferences</h1>
				<p class="text-gray-600">Customize your application experience</p>
			<?php else: ?>
				<h1 class="text-2xl font-bold text-gray-900">Integrations</h1>
				<p class="text-gray-600">Connect external services and webhooks</p>
			<?php endif; ?>
		</div>
	</div>

	<div class="bg-white rounded-lg shadow-sm border">
		<div class="p-6">
			<?php if ($tab === 'general'): ?>
				<form method="POST" action="<?= base_url('admin/settings/save/general') ?>" class="space-y-6">
					<?= csrf_field() ?>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
							<input type="text" name="company_name" value="<?= esc($settings['company_name'] ?? '') ?>"
								   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
							<input type="text" name="timezone" value="<?= esc($settings['timezone'] ?? 'UTC') ?>"
								   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
							<select name="date_format" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
								<?php $dateFormat = $settings['date_format'] ?? 'Y-m-d'; ?>
								<option value="Y-m-d" <?= $dateFormat === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD</option>
								<option value="d/m/Y" <?= $dateFormat === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY</option>
								<option value="m/d/Y" <?= $dateFormat === 'm/d/Y' ? 'selected' : '' ?>>MM/DD/YYYY</option>
							</select>
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Default Currency</label>
							<input type="text" name="currency" value="<?= esc($settings['currency'] ?? 'USD') ?>"
								   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
						</div>
					</div>
					<div class="flex justify-end">
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
							Save General Settings
						</button>
					</div>
				</form>
			<?php elseif ($tab === 'security'): ?>
				<form method="POST" action="<?= base_url('admin/settings/save/security') ?>" class="space-y-6">
					<?= csrf_field() ?>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Password Policy</label>
							<select name="password_policy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
								<?php $policy = $settings['password_policy'] ?? 'standard'; ?>
								<option value="standard" <?= $policy === 'standard' ? 'selected' : '' ?>>Standard</option>
								<option value="strong" <?= $policy === 'strong' ? 'selected' : '' ?>>Strong</option>
								<option value="very_strong" <?= $policy === 'very_strong' ? 'selected' : '' ?>>Very Strong</option>
							</select>
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Session Timeout (minutes)</label>
							<input type="number" name="session_timeout" min="5" max="240" value="<?= esc($settings['session_timeout'] ?? 30) ?>"
								   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
						</div>
						<div class="flex items-center gap-3">
							<input type="checkbox" id="two_factor" name="two_factor" value="1"
								   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
								   <?= !empty($settings['two_factor']) ? 'checked' : '' ?>>
							<label for="two_factor" class="text-sm text-gray-700">Require Two-Factor Authentication</label>
						</div>
					</div>
					<div class="flex justify-end">
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
							Save Security Settings
						</button>
					</div>
				</form>
			<?php elseif ($tab === 'preferences'): ?>
				<form method="POST" action="<?= base_url('admin/settings/save/preferences') ?>" class="space-y-6">
					<?= csrf_field() ?>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
							<?php $theme = $settings['theme'] ?? 'light'; ?>
							<select name="theme" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
								<option value="light" <?= $theme === 'light' ? 'selected' : '' ?>>Light</option>
								<option value="dark" <?= $theme === 'dark' ? 'selected' : '' ?>>Dark</option>
							</select>
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Items per page</label>
							<input type="number" name="items_per_page" min="5" max="100" value="<?= esc($settings['items_per_page'] ?? 25) ?>"
								   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
						</div>
						<div class="flex items-center gap-3">
							<input type="checkbox" id="email_notifications" name="email_notifications" value="1"
								   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
								   <?= !empty($settings['email_notifications']) ? 'checked' : '' ?>>
							<label for="email_notifications" class="text-sm text-gray-700">Email Notifications</label>
						</div>
					</div>
					<div class="flex justify-end">
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
							Save Preferences
						</button>
					</div>
				</form>
			<?php else: ?>
				<form method="POST" action="<?= base_url('admin/settings/save/integrations') ?>" class="space-y-6">
					<?= csrf_field() ?>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Webhook URL</label>
							<input type="url" name="webhook_url" value="<?= esc($settings['webhook_url'] ?? '') ?>"
								   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Slack Webhook</label>
							<input type="url" name="slack_webhook" value="<?= esc($settings['slack_webhook'] ?? '') ?>"
								   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
						</div>
					</div>
					<div class="flex justify-end">
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
							Save Integrations
						</button>
					</div>
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>

<?= $this->endSection() ?>
