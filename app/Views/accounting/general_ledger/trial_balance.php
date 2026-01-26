<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.balance-debit { @apply text-gray-900 font-medium; }
.balance-credit { @apply text-gray-900 font-medium; }
.total-row { @apply bg-gray-50 font-bold; }
.account-asset { @apply bg-blue-50; }
.account-liability { @apply bg-orange-50; }
.account-equity { @apply bg-purple-50; }
.account-revenue { @apply bg-green-50; }
.account-expense { @apply bg-red-50; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Trial Balance</h1>
            <p class="text-gray-600">Verify that total debits equal total credits</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/accounting/general-ledger') ?>" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to General Ledger
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

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">As of Date</label>
                <input type="date" name="as_of_date" value="<?= $as_of_date ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i data-lucide="filter" class="w-4 h-4 mr-2 inline"></i>
                    Update
                </button>
            </div>
        </form>
    </div>

    <!-- Trial Balance Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Trial Balance as of <?= date('F j, Y', strtotime($as_of_date)) ?></h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($trial_balance)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="book-open" class="w-12 h-12 mx-auto mb-4 text-gray-400"></i>
                                <p class="text-lg font-medium">No trial balance data found</p>
                                <p class="text-sm">Make sure you have posted journal entries for the selected date.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $currentType = '';
                        foreach ($trial_balance as $account): 
                            $debitBalance = floatval($account['debit_balance'] ?? 0);
                            $creditBalance = floatval($account['credit_balance'] ?? 0);
                        ?>
                            <?php if ($currentType !== $account['account_type']): ?>
                                <?php $currentType = $account['account_type']; ?>
                                <tr class="bg-gray-100">
                                    <td colspan="4" class="px-6 py-2 text-sm font-semibold text-gray-700 uppercase tracking-wide">
                                        <?= ucfirst($account['account_type']) ?>s
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                            <tr class="hover:bg-gray-50 account-<?= $account['account_type'] ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= esc($account['account_code']) ?> - <?= esc($account['account_name']) ?>
                                    </div>
                                    <?php if ($account['description']): ?>
                                        <div class="text-sm text-gray-500"><?= esc($account['description']) ?></div>
                                    <?php endif; ?>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right balance-debit">
                                    <?php if ($debitBalance > 0): ?>
                                        MWK <?= number_format($debitBalance, 2) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right balance-credit">
                                    <?php if ($creditBalance > 0): ?>
                                        MWK <?= number_format($creditBalance, 2) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <!-- Totals Row -->
                        <tr class="total-row border-t-2 border-gray-300">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900" colspan="2">
                                TOTAL
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                MWK <?= number_format($totals['total_debits'], 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                MWK <?= number_format($totals['total_credits'], 2) ?>
                            </td>
                        </tr>
                        
                        <!-- Difference Row -->
                        <?php if (abs($totals['difference']) > 0.01): ?>
                        <tr class="bg-red-50 border-t border-red-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-800" colspan="2">
                                <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                                DIFFERENCE (Trial Balance is OUT OF BALANCE!)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-800 text-right" colspan="2">
                                MWK <?= number_format(abs($totals['difference']), 2) ?>
                            </td>
                        </tr>
                        <?php else: ?>
                        <tr class="bg-green-50 border-t border-green-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-800 text-center" colspan="4">
                                <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                                Trial Balance is IN BALANCE ✓
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary Statistics -->
    <?php if (!empty($trial_balance)): ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i data-lucide="calculator" class="w-8 h-8 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Debits</p>
                    <p class="text-2xl font-bold text-gray-900">MWK <?= number_format($totals['total_debits'], 2) ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i data-lucide="calculator" class="w-8 h-8 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Credits</p>
                    <p class="text-2xl font-bold text-gray-900">MWK <?= number_format($totals['total_credits'], 2) ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <?php if (abs($totals['difference']) > 0.01): ?>
                        <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600"></i>
                    <?php else: ?>
                        <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
                    <?php endif; ?>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Balance Status</p>
                    <?php if (abs($totals['difference']) > 0.01): ?>
                        <p class="text-2xl font-bold text-red-600">Out of Balance</p>
                        <p class="text-sm text-red-500">Difference: MWK <?= number_format(abs($totals['difference']), 2) ?></p>
                    <?php else: ?>
                        <p class="text-2xl font-bold text-green-600">In Balance</p>
                        <p class="text-sm text-green-500">Debits = Credits ✓</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>

<?= $this->endSection() ?>