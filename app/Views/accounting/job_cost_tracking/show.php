<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Job Cost Entry Details</h1>
            <p class="text-gray-600 mt-1">View cost entry information</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/accounting/job-cost-tracking') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
            <a href="<?= base_url('admin/accounting/job-cost-tracking/' . $jobCost['id'] . '/edit') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit
            </a>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Cost Information</h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Project -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Project</label>
                    <p class="mt-1 text-gray-900 font-medium"><?= esc($jobCost['project_name']) ?></p>
                    <p class="text-sm text-gray-600"><?= esc($jobCost['project_number']) ?></p>
                </div>

                <!-- Cost Code -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Cost Code</label>
                    <p class="mt-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                            <?= esc($jobCost['cost_code']) ?>
                        </span>
                    </p>
                    <p class="text-sm text-gray-600 mt-1"><?= esc($jobCost['cost_code_name']) ?></p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-500">Description</label>
                    <p class="mt-1 text-gray-900"><?= esc($jobCost['description']) ?></p>
                </div>

                <!-- Cost Date -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Cost Date</label>
                    <p class="mt-1 text-gray-900"><?= date('F j, Y', strtotime($jobCost['cost_date'])) ?></p>
                </div>

                <!-- Category -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Cost Category</label>
                    <p class="mt-1">
                        <?php
                        $categoryColors = [
                            'actual' => 'green',
                            'estimated' => 'blue',
                            'budgeted' => 'yellow'
                        ];
                        $color = $categoryColors[$jobCost['cost_category']] ?? 'gray';
                        ?>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800">
                            <?= ucfirst($jobCost['cost_category']) ?>
                        </span>
                    </p>
                </div>

                <!-- Quantity -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Quantity</label>
                    <p class="mt-1 text-gray-900 font-medium"><?= number_format($jobCost['quantity'], 2) ?></p>
                </div>

                <!-- Unit Cost -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Unit Cost</label>
                    <p class="mt-1 text-gray-900 font-medium">$<?= number_format($jobCost['unit_cost'], 2) ?></p>
                </div>

                <!-- Total Cost -->
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-500">Total Cost</label>
                    <p class="mt-1 text-2xl font-bold text-blue-600">$<?= number_format($jobCost['total_cost'], 2) ?></p>
                </div>

                <!-- Vendor -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Vendor/Supplier</label>
                    <p class="mt-1 text-gray-900"><?= $jobCost['vendor_supplier'] ? esc($jobCost['vendor_supplier']) : '<span class="text-gray-400">Not specified</span>' ?></p>
                </div>

                <!-- Reference Number -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Reference Number</label>
                    <p class="mt-1 text-gray-900"><?= $jobCost['reference_number'] ? esc($jobCost['reference_number']) : '<span class="text-gray-400">Not specified</span>' ?></p>
                </div>

                <!-- Billable -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Billable to Client</label>
                    <p class="mt-1">
                        <?php if ($jobCost['is_billable']): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i data-lucide="check" class="w-3 h-3 inline"></i> Yes
                            </span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                <i data-lucide="x" class="w-3 h-3 inline"></i> No
                            </span>
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Created By -->
                <div>
                    <label class="text-sm font-medium text-gray-500">Created By</label>
                    <p class="mt-1 text-gray-900"><?= esc($jobCost['created_by_name'] ?? 'Unknown') ?></p>
                    <p class="text-sm text-gray-600"><?= date('M j, Y g:i A', strtotime($jobCost['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-end gap-3">
                <a href="<?= base_url('admin/accounting/job-cost-tracking') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 bg-white rounded-lg transition-colors">
                    Back to List
                </a>
                <a href="<?= base_url('admin/accounting/job-cost-tracking/' . $jobCost['id'] . '/edit') ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i data-lucide="edit" class="w-4 h-4 inline mr-2"></i>
                    Edit Entry
                </a>
            </div>
        </div>
    </div>
</div>

<script>
lucide.createIcons();
</script>
<?= $this->endSection() ?>
