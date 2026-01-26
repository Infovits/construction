<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Account Details</h1>
            <p class="text-gray-600">View detailed information for this account</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/accounting/chart-of-accounts/' . $account['id'] . '/edit') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit Account
            </a>
            <a href="<?= base_url('admin/accounting/chart-of-accounts') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Account Information Card -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Account Information</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Account Code</label>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <?= esc($account['account_code']) ?>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Account Name</label>
                        <p class="text-lg font-semibold text-gray-900"><?= esc($account['account_name']) ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Account Type</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            <?= $account['account_type'] === 'asset' ? 'bg-blue-100 text-blue-800' : 
                               ($account['account_type'] === 'liability' ? 'bg-red-100 text-red-800' :
                               ($account['account_type'] === 'equity' ? 'bg-purple-100 text-purple-800' :
                               ($account['account_type'] === 'revenue' ? 'bg-green-100 text-green-800' :
                               'bg-orange-100 text-orange-800'))) ?>">
                            <i data-lucide="<?= $account['account_type'] === 'asset' ? 'trending-up' : 
                                              ($account['account_type'] === 'liability' ? 'trending-down' :
                                              ($account['account_type'] === 'equity' ? 'pie-chart' :
                                              ($account['account_type'] === 'revenue' ? 'dollar-sign' :
                                              'credit-card'))) ?>" class="w-4 h-4 mr-2"></i>
                            <?= ucfirst($account['account_type']) ?>
                        </span>
                    </div>

                    <?php if (!empty($account['account_subtype'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Account Subtype</label>
                        <p class="text-sm text-gray-700"><?= esc($account['account_subtype']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Additional Information -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Current Balance</label>
                        <p class="text-2xl font-bold text-gray-900">
                            MWK <?= number_format($account['balance'], 2) ?>
                        </p>
                    </div>

                    <?php if (!empty($account['parent_account_id'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Parent Account</label>
                        <div class="flex items-center">
                            <i data-lucide="link" class="w-4 h-4 text-blue-500 mr-2"></i>
                            <a href="<?= base_url('admin/accounting/chart-of-accounts/' . $account['parent_account_id']) ?>" 
                               class="text-blue-600 hover:text-blue-800">
                                <?= esc($account['parent_account_code'] ?? '') ?> - <?= esc($account['parent_account_name'] ?? '') ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($account['category_id'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                        <span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium bg-blue-100 text-blue-800">
                            <i data-lucide="folder" class="w-3 h-3 mr-1"></i>
                            <?= esc($account['category_name'] ?? 'N/A') ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            <?= $account['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <i data-lucide="<?= $account['is_active'] ? 'check-circle' : 'x-circle' ?>" class="w-4 h-4 mr-2"></i>
                            <?= $account['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>

                    <?php if ($account['is_system_account']): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Account Type</label>
                        <span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium bg-gray-100 text-gray-800">
                            <i data-lucide="shield" class="w-3 h-3 mr-1"></i>
                            System Account
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Description -->
            <?php if (!empty($account['description'])): ?>
            <div class="mt-6 pt-6 border-t border-gray-200">
                <label class="block text-sm font-medium text-gray-500 mb-2">Description</label>
                <p class="text-gray-700"><?= esc($account['description']) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Account Activity Summary -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Account Activity</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Transaction Count -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="activity" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-900"><?= $account['transaction_count'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Total Transactions</p>
                </div>

                <!-- Account Age -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="calendar" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-900">
                        Created: <?= date('M j, Y', strtotime($account['created_at'])) ?>
                    </p>
                    <p class="text-sm text-gray-500">
                        <?= date('M j, Y', strtotime($account['updated_at'])) !== date('M j, Y', strtotime($account['created_at'])) 
                            ? 'Updated: ' . date('M j, Y', strtotime($account['updated_at'])) 
                            : 'Never updated' ?>
                    </p>
                </div>

                <!-- Quick Actions -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="settings" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div class="space-y-2">
                        <button class="block w-full px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors"
                                onclick="alert('Transaction history feature coming soon')">
                            View Transactions
                        </button>
                        <button class="block w-full px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors"
                                onclick="alert('Account reports feature coming soon')">
                            Generate Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Child Accounts (if any) -->
    <?php if (!empty($childAccounts)): ?>
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Sub-Accounts</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($childAccounts as $child): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i data-lucide="corner-down-right" class="w-4 h-4 text-gray-400 mr-2"></i>
                                <div class="text-sm font-medium text-gray-900">
                                    <?= esc($child['account_name']) ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900"><?= esc($child['account_code']) ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            MWK <?= number_format($child['balance'], 2) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                <?= $child['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $child['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="<?= base_url('admin/accounting/chart-of-accounts/' . $child['id']) ?>" 
                               class="text-indigo-600 hover:text-indigo-900">
                                View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>