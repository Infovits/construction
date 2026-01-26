<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add Cost Code</h1>
            <p class="text-gray-600">Create a new cost code for job costing</p>
        </div>
        <div>
            <a href="<?= base_url('admin/accounting/cost-codes') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Cost Codes
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/accounting/cost-codes') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Cost Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                               id="code" name="code" value="<?= old('code') ?>" required maxlength="50">
                        <p class="mt-1 text-xs text-gray-500">Unique identifier for this cost code (e.g., LAB-001, MAT-001)</p>
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                               id="name" name="name" value="<?= old('name') ?>" required maxlength="255">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                              id="description" name="description" rows="3" maxlength="1000"><?= old('description') ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                id="category" name="category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $key => $category): ?>
                                <option value="<?= $key ?>" <?= old('category') == $key ? 'selected' : '' ?>>
                                    <?= $category ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="cost_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Cost Type <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                id="cost_type" name="cost_type" required>
                            <option value="">Select Cost Type</option>
                            <?php foreach ($costTypes as $key => $type): ?>
                                <option value="<?= $key ?>" <?= old('cost_type') == $key ? 'selected' : '' ?>>
                                    <?= $type ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="unit_of_measure" class="block text-sm font-medium text-gray-700 mb-2">Unit of Measure</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                               id="unit_of_measure" name="unit_of_measure" value="<?= old('unit_of_measure') ?>" 
                               maxlength="50" placeholder="e.g., hour, kg, cubic_meter">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="standard_rate" class="block text-sm font-medium text-gray-700 mb-2">Standard Rate</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                   id="standard_rate" name="standard_rate" value="<?= old('standard_rate') ?>" 
                                   step="0.01" min="0">
                        </div>
                    </div>
                    <div>
                        <div class="mt-6">
                            <label class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" 
                                       id="is_active" name="is_active" value="1" <?= old('is_active', '1') == '1' ? 'checked' : '' ?>>
                                <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="<?= base_url('admin/accounting/cost-codes') ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i data-lucide="x" class="w-4 h-4 mr-2"></i>Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>Save Cost Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
// Auto-generate code based on category and name
document.getElementById('category').addEventListener('change', generateCode);
document.getElementById('name').addEventListener('input', generateCode);

function generateCode() {
    const category = document.getElementById('category').value;
    const name = document.getElementById('name').value;
    const codeField = document.getElementById('code');
    
    if (category && name && !codeField.value) {
        const categoryMap = {
            'labor': 'LAB',
            'material': 'MAT', 
            'equipment': 'EQP',
            'subcontractor': 'SUB',
            'overhead': 'OVH',
            'other': 'OTH'
        };
        
        const prefix = categoryMap[category] || 'OTH';
        const suffix = String(Math.floor(Math.random() * 999) + 1).padStart(3, '0');
        codeField.value = `${prefix}-${suffix}`;
    }
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>