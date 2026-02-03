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
			<form method="POST" action="<?= base_url('admin/settings/save/general') ?>" enctype="multipart/form-data" class="space-y-6">
				<?= csrf_field() ?>
				
				<!-- Company Logo -->
				<div class="border-b pb-6">
					<h3 class="text-lg font-semibold text-gray-900 mb-4">Company Branding</h3>
					<div class="flex items-start space-x-6">
						<div class="flex-shrink-0">
							<div class="w-32 h-32 border-2 border-gray-300 rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
								<?php if (!empty($settings['company_logo'])): ?>
									<img src="<?= base_url($settings['company_logo']) ?>" alt="Company Logo" class="w-full h-full object-contain">
								<?php else: ?>
									<i data-lucide="building-2" class="w-16 h-16 text-gray-400"></i>
								<?php endif; ?>
							</div>
						</div>
						<div class="flex-1">
							<label class="block text-sm font-medium text-gray-700 mb-2">Upload Company Logo</label>
							<input type="file" name="company_logo" accept="image/*" 
								   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
							<p class="text-sm text-gray-500 mt-2">Recommended size: 200x200px. Max file size: 2MB</p>
							<?php if (!empty($settings['company_logo'])): ?>
								<input type="hidden" name="existing_logo" value="<?= esc($settings['company_logo']) ?>">
								<label class="inline-flex items-center mt-2">
									<input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
									<span class="ml-2 text-sm text-gray-600">Remove current logo</span>
								</label>
							<?php endif; ?>
						</div>
					</div>
				</div>
				
				<!-- Company Information -->
				<div>
					<h3 class="text-lg font-semibold text-gray-900 mb-4">Company Information</h3>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Company Name *</label>
							<input type="text" name="company_name" value="<?= esc($settings['company_name'] ?? '') ?>" required
								   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Timezone *</label>
							<select name="timezone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
								<?php 
								$currentTz = $settings['timezone'] ?? 'UTC';
								$timezones = [
									'UTC' => 'UTC',
									'Africa/Blantyre' => 'Africa/Blantyre (CAT)',
									'Africa/Johannesburg' => 'Africa/Johannesburg (SAST)',
									'Africa/Nairobi' => 'Africa/Nairobi (EAT)',
									'America/New_York' => 'America/New York (EST/EDT)',
									'America/Chicago' => 'America/Chicago (CST/CDT)',
									'America/Los_Angeles' => 'America/Los Angeles (PST/PDT)',
									'Europe/London' => 'Europe/London (GMT/BST)',
									'Europe/Paris' => 'Europe/Paris (CET/CEST)',
									'Asia/Dubai' => 'Asia/Dubai (GST)',
									'Asia/Kolkata' => 'Asia/Kolkata (IST)',
									'Asia/Shanghai' => 'Asia/Shanghai (CST)',
									'Asia/Tokyo' => 'Asia/Tokyo (JST)',
									'Australia/Sydney' => 'Australia/Sydney (AEST/AEDT)',
								];
								foreach ($timezones as $tz => $label): ?>
									<option value="<?= $tz ?>" <?= $currentTz === $tz ? 'selected' : '' ?>><?= $label ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Date Format *</label>
							<select name="date_format" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
								<?php $dateFormat = $settings['date_format'] ?? 'Y-m-d'; ?>
								<option value="Y-m-d" <?= $dateFormat === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD (2026-02-03)</option>
								<option value="d/m/Y" <?= $dateFormat === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY (03/02/2026)</option>
								<option value="m/d/Y" <?= $dateFormat === 'm/d/Y' ? 'selected' : '' ?>>MM/DD/YYYY (02/03/2026)</option>
								<option value="d-M-Y" <?= $dateFormat === 'd-M-Y' ? 'selected' : '' ?>>DD-Mon-YYYY (03-Feb-2026)</option>
							</select>
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Default Currency *</label>
							<select name="currency" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
								<?php 
								$currency = $settings['currency'] ?? 'MWK';
								$currencies = [
									'MWK' => 'MWK - Malawian Kwacha',
									'USD' => 'USD - US Dollar',
									'EUR' => 'EUR - Euro',
									'GBP' => 'GBP - British Pound',
									'ZAR' => 'ZAR - South African Rand',
								];
								foreach ($currencies as $code => $label): ?>
									<option value="<?= $code ?>" <?= $currency === $code ? 'selected' : '' ?>><?= $label ?></option>
								<?php endforeach; ?>
							</select>
						</div>
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
