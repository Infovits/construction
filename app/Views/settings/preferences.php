<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
	<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
		<div>
			<h1 class="text-2xl font-bold text-gray-900">Preferences</h1>
			<p class="text-gray-600">Customize your application experience</p>
		</div>
	</div>

	<div class="bg-white rounded-lg shadow-sm border">
		<div class="p-6">
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
		</div>
	</div>
</div>

<?= $this->endSection() ?>
