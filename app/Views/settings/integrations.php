<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="space-y-6">
	<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
		<div>
			<h1 class="text-2xl font-bold text-gray-900">Integrations</h1>
			<p class="text-gray-600">Connect external services and webhooks</p>
		</div>
	</div>

	<div class="bg-white rounded-lg shadow-sm border">
		<div class="p-6">
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
		</div>
	</div>
</div>

<?= $this->endSection() ?>
