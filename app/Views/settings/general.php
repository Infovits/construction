<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
	<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
		<div>
			<h1 class="text-2xl font-bold text-gray-900">General Settings</h1>
			<p class="text-gray-600">Manage company information and regional settings</p>
		</div>
	</div>

	<div class="bg-white rounded-lg shadow-sm border">
		<div class="p-6">
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
		</div>
	</div>
</div>

<?= $this->endSection() ?>
