<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add Job Cost Entry</h1>
            <p class="text-gray-600 mt-1">Record a new cost entry for a project</p>
        </div>
        <a href="<?= base_url('admin/accounting/job-cost-tracking') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to List
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-lg">
        <form action="<?= base_url('admin/accounting/job-cost-tracking') ?>" method="post" class="p-6">
            <?= csrf_field() ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                    <div class="flex items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-2 mt-0.5"></i>
                        <div>
                            <p class="font-medium">Please fix the following errors:</p>
                            <ul class="mt-2 ml-4 list-disc list-inside text-sm">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Project & Cost Code -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select name="project_id" id="project_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>" <?= old('project_id') == $project['id'] ? 'selected' : '' ?>>
                                <?= esc($project['project_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="cost_code_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Cost Code <span class="text-red-500">*</span>
                    </label>
                    <select name="cost_code_id" id="cost_code_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Cost Code</option>
                        <?php foreach ($costCodes as $code): ?>
                            <option value="<?= $code['id'] ?>" <?= old('cost_code_id') == $code['id'] ? 'selected' : '' ?>>
                                <?= esc($code['code']) ?> - <?= esc($code['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Description & Date -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="description" id="description" required value="<?= old('description') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter cost description">
                </div>

                <div>
                    <label for="cost_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Cost Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="cost_date" id="cost_date" required value="<?= old('cost_date', date('Y-m-d')) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Quantity & Unit Cost -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" name="quantity" id="quantity" required value="<?= old('quantity', '1') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="calculateTotal()">
                </div>

                <div>
                    <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">
                        Unit Cost <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number" step="0.01" name="unit_cost" id="unit_cost" required value="<?= old('unit_cost') ?>" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="calculateTotal()">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Total Cost
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="text" id="total_display" readonly class="w-full pl-8 pr-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-700 font-semibold">
                    </div>
                </div>
            </div>

            <!-- Vendor & Reference -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="vendor_supplier" class="block text-sm font-medium text-gray-700 mb-2">
                        Vendor/Supplier
                    </label>
                    <input type="text" name="vendor_supplier" id="vendor_supplier" value="<?= old('vendor_supplier') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter vendor name">
                </div>

                <div>
                    <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Reference Number
                    </label>
                    <input type="text" name="reference_number" id="reference_number" value="<?= old('reference_number') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Invoice or PO number">
                </div>
            </div>

            <!-- Category & Billable -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="cost_category" class="block text-sm font-medium text-gray-700 mb-2">
                        Cost Category <span class="text-red-500">*</span>
                    </label>
                    <select name="cost_category" id="cost_category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php foreach ($costCategories as $key => $label): ?>
                            <option value="<?= $key ?>" <?= old('cost_category', 'actual') == $key ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex items-end pb-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_billable" value="1" <?= old('is_billable') ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">This cost is billable to client</span>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="<?= base_url('admin/accounting/job-cost-tracking') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                    Save Job Cost Entry
                </button>
            </div>
        </form>
    </div>
</div>

<script>
lucide.createIcons();

function calculateTotal() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const unitCost = parseFloat(document.getElementById('unit_cost').value) || 0;
    const total = quantity * unitCost;

    document.getElementById('total_display').value = total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
<?= $this->endSection() ?>
