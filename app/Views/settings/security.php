<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
	<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
		<div>
			<h1 class="text-2xl font-bold text-gray-900">Security Settings</h1>
			<p class="text-gray-600">Configure security policies and authentication</p>
		</div>
	</div>

	<div class="bg-white rounded-lg shadow-sm border">
		<div class="p-6">
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
		</div>
	</div>
</div>

<?= $this->endSection() ?>
