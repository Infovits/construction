<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Account Category</h1>
            <p class="text-gray-600">Add a new category for organizing your chart of accounts</p>
        </div>
        <div>
            <a href="<?= base_url('admin/accounting/account-categories') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Categories
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6">
            <form action="<?= base_url('admin/accounting/account-categories') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="<?= old('name') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('name') ? 'border-red-500' : '' ?>"
                               placeholder="e.g., Current Assets, Fixed Assets"
                               required>
                        <?php if ($validation->hasError('name')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $validation->getError('name') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Category Code -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Category Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="code" 
                               id="code" 
                               value="<?= old('code') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('code') ? 'border-red-500' : '' ?>"
                               placeholder="e.g., CA, FA, CL"
                               maxlength="20"
                               required>
                        <?php if ($validation->hasError('code')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $validation->getError('code') ?></p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">Unique code for this category (max 20 characters)</p>
                    </div>
                </div>

                <!-- Account Type -->
                <div>
                    <label for="account_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Account Type <span class="text-red-500">*</span>
                    </label>
                    <select name="account_type" 
                            id="account_type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('account_type') ? 'border-red-500' : '' ?>"
                            required>
                        <option value="">Select Account Type</option>
                        <option value="asset" <?= old('account_type') === 'asset' ? 'selected' : '' ?>>Assets</option>
                        <option value="liability" <?= old('account_type') === 'liability' ? 'selected' : '' ?>>Liabilities</option>
                        <option value="equity" <?= old('account_type') === 'equity' ? 'selected' : '' ?>>Equity</option>
                        <option value="revenue" <?= old('account_type') === 'revenue' ? 'selected' : '' ?>>Revenue</option>
                        <option value="expense" <?= old('account_type') === 'expense' ? 'selected' : '' ?>>Expenses</option>
                    </select>
                    <?php if ($validation->hasError('account_type')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $validation->getError('account_type') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('description') ? 'border-red-500' : '' ?>"
                              placeholder="Optional description for this category"><?= old('description') ?></textarea>
                    <?php if ($validation->hasError('description')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $validation->getError('description') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Status -->
                <div>
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active" 
                               value="1"
                               <?= old('is_active', '1') ? 'checked' : '' ?>
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Inactive categories won't be available for new accounts</p>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="<?= base_url('admin/accounting/account-categories') ?>" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="save" class="w-4 h-4 mr-2 inline"></i>
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="info" class="w-5 h-5 text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Account Type Guidelines</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Assets:</strong> Resources owned by the company (Cash, Inventory, Equipment)</li>
                        <li><strong>Liabilities:</strong> Debts and obligations (Accounts Payable, Loans)</li>
                        <li><strong>Equity:</strong> Owner's stake in the company (Capital, Retained Earnings)</li>
                        <li><strong>Revenue:</strong> Income from business operations (Sales, Service Revenue)</li>
                        <li><strong>Expenses:</strong> Costs of doing business (Rent, Utilities, Salaries)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-generate code from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const codeField = document.getElementById('code');
    
    // Only auto-generate if code field is empty
    if (!codeField.value) {
        // Extract first letters of each word and convert to uppercase
        const code = name.split(' ')
                        .map(word => word.charAt(0))
                        .join('')
                        .toUpperCase()
                        .substring(0, 20); // Limit to 20 characters
        
        codeField.value = code;
    }
});

// Validate form before submission
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const code = document.getElementById('code').value.trim();
    const accountType = document.getElementById('account_type').value;
    
    if (!name || !code || !accountType) {
        e.preventDefault();
        alert('Please fill in all required fields.');
        return false;
    }
    
    if (code.length > 20) {
        e.preventDefault();
        alert('Category code cannot exceed 20 characters.');
        return false;
    }
});
</script>

<?= $this->endSection() ?>
