<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Account</h1>
            <p class="text-gray-600">Add a new account to your chart of accounts</p>
        </div>
        <div>
            <a href="<?= base_url('admin/accounting/chart-of-accounts') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Accounts
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6">
            <form action="<?= base_url('admin/accounting/chart-of-accounts') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Account Code -->
                    <div>
                        <label for="account_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Account Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="account_code" 
                               id="account_code" 
                               value="<?= old('account_code') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('account_code') ? 'border-red-500' : '' ?>"
                               placeholder="e.g., 1000, 2000, 4000"
                               maxlength="50"
                               required>
                        <?php if ($validation->hasError('account_code')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $validation->getError('account_code') ?></p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">Unique code for this account (max 50 characters)</p>
                    </div>

                    <!-- Account Name -->
                    <div>
                        <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Account Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="account_name" 
                               id="account_name" 
                               value="<?= old('account_name') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('account_name') ? 'border-red-500' : '' ?>"
                               placeholder="e.g., Cash in Bank, Accounts Receivable"
                               maxlength="255"
                               required>
                        <?php if ($validation->hasError('account_name')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $validation->getError('account_name') ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                            <?php foreach ($accountTypes as $key => $label): ?>
                                <option value="<?= $key ?>" <?= old('account_type') === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($validation->hasError('account_type')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $validation->getError('account_type') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Account Subtype -->
                    <div>
                        <label for="account_subtype" class="block text-sm font-medium text-gray-700 mb-2">
                            Account Subtype
                        </label>
                        <input type="text" 
                               name="account_subtype" 
                               id="account_subtype" 
                               value="<?= old('account_subtype') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('account_subtype') ? 'border-red-500' : '' ?>"
                               placeholder="e.g., Current Asset, Fixed Asset"
                               maxlength="100">
                        <?php if ($validation->hasError('account_subtype')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $validation->getError('account_subtype') ?></p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">Optional subtype classification</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Category
                        </label>
                        <div class="flex gap-2">
                            <select name="category_id" 
                                    id="category_id" 
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('category_id') ? 'border-red-500' : '' ?>">
                                <option value="">Select Category (Optional)</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                        <?= esc($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <a href="<?= base_url('admin/accounting/account-categories/create') ?>" 
                               target="_blank"
                               class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm"
                               title="Create New Category">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </a>
                        </div>
                        <?php if ($validation->hasError('category_id')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $validation->getError('category_id') ?></p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">Optional category for better organization. <a href="<?= base_url('admin/accounting/account-categories/create') ?>" target="_blank" class="text-green-600 hover:text-green-800">Create new category</a></p>
                    </div>

                    <!-- Parent Account -->
                    <div>
                        <label for="parent_account_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Parent Account
                        </label>
                        <select name="parent_account_id" 
                                id="parent_account_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('parent_account_id') ? 'border-red-500' : '' ?>">
                            <option value="">No Parent (Top Level)</option>
                            <?php foreach ($parentAccounts as $parent): ?>
                                <option value="<?= $parent['id'] ?>" <?= old('parent_account_id') == $parent['id'] ? 'selected' : '' ?>>
                                    <?= esc($parent['account_code']) ?> - <?= esc($parent['account_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($validation->hasError('parent_account_id')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $validation->getError('parent_account_id') ?></p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">Select a parent account to create a sub-account</p>
                    </div>
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
                              placeholder="Optional description for this account"><?= old('description') ?></textarea>
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
                    <p class="mt-1 text-sm text-gray-500">Inactive accounts won't be available for transactions</p>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="<?= base_url('admin/accounting/chart-of-accounts') ?>" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="save" class="w-4 h-4 mr-2 inline"></i>
                        Create Account
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
                <h3 class="text-sm font-medium text-blue-800">Account Guidelines</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Account Code:</strong> Use a numbering system (1000s for Assets, 2000s for Liabilities, etc.)</li>
                        <li><strong>Parent Accounts:</strong> Create sub-accounts by selecting a parent for better organization</li>
                        <li><strong>Account Types:</strong>
                            <ul class="list-disc list-inside ml-4 mt-1">
                                <li>Assets: What the company owns</li>
                                <li>Liabilities: What the company owes</li>
                                <li>Equity: Owner's stake in the company</li>
                                <li>Revenue: Income from operations</li>
                                <li>Expenses: Costs of doing business</li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-suggest account code based on account type
document.getElementById('account_type').addEventListener('change', function() {
    const accountType = this.value;
    const codeField = document.getElementById('account_code');
    
    // Only auto-suggest if code field is empty
    if (!codeField.value && accountType) {
        const suggestions = {
            'asset': '1',
            'liability': '2',
            'equity': '3',
            'revenue': '4',
            'expense': '5'
        };
        
        const prefix = suggestions[accountType];
        if (prefix) {
            codeField.placeholder = `e.g., ${prefix}000, ${prefix}100, ${prefix}200`;
        }
    }
});

// Filter parent accounts based on selected account type
document.getElementById('account_type').addEventListener('change', function() {
    const selectedType = this.value;
    const parentSelect = document.getElementById('parent_account_id');
    const options = parentSelect.querySelectorAll('option[data-type]');
    
    // Show/hide parent options based on account type
    options.forEach(option => {
        if (selectedType && option.getAttribute('data-type') !== selectedType) {
            option.style.display = 'none';
        } else {
            option.style.display = 'block';
        }
    });
});

// Validate form before submission
document.querySelector('form').addEventListener('submit', function(e) {
    const accountCode = document.getElementById('account_code').value.trim();
    const accountName = document.getElementById('account_name').value.trim();
    const accountType = document.getElementById('account_type').value;
    
    if (!accountCode || !accountName || !accountType) {
        e.preventDefault();
        alert('Please fill in all required fields.');
        return false;
    }
    
    if (accountCode.length > 50) {
        e.preventDefault();
        alert('Account code cannot exceed 50 characters.');
        return false;
    }
    
    if (accountName.length > 255) {
        e.preventDefault();
        alert('Account name cannot exceed 255 characters.');
        return false;
    }
});
</script>

<?= $this->endSection() ?>