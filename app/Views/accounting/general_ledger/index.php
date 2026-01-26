<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.balance-positive { @apply text-green-600 font-semibold; }
.balance-negative { @apply text-red-600 font-semibold; }
.balance-zero { @apply text-gray-500; }

.account-asset { @apply bg-blue-50 border-l-4 border-blue-400; }
.account-liability { @apply bg-orange-50 border-l-4 border-orange-400; }
.account-equity { @apply bg-purple-50 border-l-4 border-purple-400; }
.account-revenue { @apply bg-green-50 border-l-4 border-green-400; }
.account-expense { @apply bg-red-50 border-l-4 border-red-400; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">General Ledger</h1>
            <p class="text-gray-600">View account balances and transaction summaries</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/accounting/general-ledger/trial-balance') ?>" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="balance-scale" class="w-4 h-4 mr-2"></i>
                Trial Balance
            </a>
            <a href="<?= base_url('admin/accounting/journal-entries/create') ?>" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                New Journal Entry
            </a>
        </div>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-red-800"><?= esc($error_message) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <?php if (!empty($totals)): ?>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Total Assets</p>
                    <p class="text-2xl font-bold text-gray-900">MWK <?= number_format($totals['total_assets'], 2) ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="building" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-orange-600">Total Liabilities</p>
                    <p class="text-2xl font-bold text-gray-900">MWK <?= number_format($totals['total_liabilities'], 2) ?></p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="credit-card" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-purple-600">Total Equity</p>
                    <p class="text-2xl font-bold text-gray-900">MWK <?= number_format($totals['total_equity'], 2) ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="pie-chart" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-green-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">MWK <?= number_format($totals['total_revenue'], 2) ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-red-600">Total Expenses</p>
                    <p class="text-2xl font-bold text-gray-900">MWK <?= number_format($totals['total_expenses'], 2) ?></p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="trending-down" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Account Type</label>
                <select name="account_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Types</option>
                    <option value="asset" <?= $filters['account_type'] === 'asset' ? 'selected' : '' ?>>Assets</option>
                    <option value="liability" <?= $filters['account_type'] === 'liability' ? 'selected' : '' ?>>Liabilities</option>
                    <option value="equity" <?= $filters['account_type'] === 'equity' ? 'selected' : '' ?>>Equity</option>
                    <option value="revenue" <?= $filters['account_type'] === 'revenue' ? 'selected' : '' ?>>Revenue</option>
                    <option value="expense" <?= $filters['account_type'] === 'expense' ? 'selected' : '' ?>>Expenses</option>
                </select>
            </div>

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $filters['category_id'] == $category['id'] ? 'selected' : '' ?>>
                            <?= esc($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" name="date_to" value="<?= $filters['date_to'] ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i data-lucide="filter" class="w-4 h-4 mr-2 inline"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Accounts List -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($accounts)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="book-open" class="w-12 h-12 mx-auto mb-4 text-gray-400"></i>
                                <p class="text-lg font-medium">No accounts found</p>
                                <p class="text-sm">Try adjusting your filters or add some accounts to get started.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $currentType = '';
                        foreach ($accounts as $account): 
                            $balance = floatval($account['balance']);
                            $balanceClass = $balance > 0 ? 'balance-positive' : ($balance < 0 ? 'balance-negative' : 'balance-zero');
                        ?>
                            <?php if ($currentType !== $account['account_type']): ?>
                                <?php $currentType = $account['account_type']; ?>
                                <tr class="bg-gray-100">
                                    <td colspan="5" class="px-6 py-2 text-sm font-semibold text-gray-700 uppercase tracking-wide">
                                        <?= ucfirst($account['account_type']) ?>s
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                            <tr class="hover:bg-gray-50 account-<?= $account['account_type'] ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= esc($account['account_code']) ?> - <?= esc($account['account_name']) ?>
                                            </div>
                                            <?php if ($account['description']): ?>
                                                <div class="text-sm text-gray-500"><?= esc($account['description']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        <?= $account['account_type'] === 'asset' ? 'bg-blue-100 text-blue-800' : 
                                           ($account['account_type'] === 'liability' ? 'bg-orange-100 text-orange-800' : 
                                           ($account['account_type'] === 'equity' ? 'bg-purple-100 text-purple-800' : 
                                           ($account['account_type'] === 'revenue' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'))) ?>">
                                        <?= ucfirst($account['account_type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= esc($account['category_name'] ?: 'Uncategorized') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right <?= $balanceClass ?>">
                                    MWK <?= number_format(abs($balance), 2) ?>
                                    <?php if ($balance < 0): ?>
                                        <span class="text-xs text-gray-500">(CR)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <a href="<?= base_url('admin/accounting/general-ledger/account/' . $account['id']) ?>" 
                                       class="text-indigo-600 hover:text-indigo-900 mr-3" title="View Transactions">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="<?= base_url('admin/accounting/chart-of-accounts/' . $account['id'] . '/edit') ?>" 
                                       class="text-blue-600 hover:text-blue-900" title="Edit Account">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>

<?= $this->endSection() ?>