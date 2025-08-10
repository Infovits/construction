<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.tree-line {
    border-left: 1px solid #e5e7eb;
    min-height: 20px;
}

.tree-connector:last-child .tree-line {
    border-left: none;
}

.account-row:hover .tree-line {
    border-color: #6366f1;
}

.parent-account {
    background: linear-gradient(to right, #f8fafc, #ffffff);
}

.child-account {
    background: linear-gradient(to right, #f1f5f9, #ffffff);
}

.account-depth-0 {
    font-weight: 600;
    background: linear-gradient(to right, #eff6ff, #ffffff);
}

.account-depth-1 {
    background: linear-gradient(to right, #f8fafc, #ffffff);
}

.account-depth-2 {
    background: linear-gradient(to right, #f1f5f9, #ffffff);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Chart of Accounts</h1>
            <p class="text-gray-600">Manage your company's chart of accounts</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/accounting/chart-of-accounts/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                New Account
            </a>
        </div>
    </div>

    <!-- Account Statistics -->
    <?php if (!empty($stats)): ?>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <?php foreach ($stats as $stat): ?>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 
                <?= $stat['account_type'] === 'asset' ? 'border-l-blue-500' : 
                   ($stat['account_type'] === 'liability' ? 'border-l-red-500' :
                   ($stat['account_type'] === 'equity' ? 'border-l-purple-500' :
                   ($stat['account_type'] === 'revenue' ? 'border-l-green-500' :
                   'border-l-orange-500'))) ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide
                            <?= $stat['account_type'] === 'asset' ? 'text-blue-600' : 
                               ($stat['account_type'] === 'liability' ? 'text-red-600' :
                               ($stat['account_type'] === 'equity' ? 'text-purple-600' :
                               ($stat['account_type'] === 'revenue' ? 'text-green-600' :
                               'text-orange-600'))) ?>">
                            <?= ucfirst($stat['account_type']) ?>
                        </p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stat['active_accounts'] ?></p>
                        <p class="text-sm text-gray-500">of <?= $stat['total_accounts'] ?> accounts</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center
                        <?= $stat['account_type'] === 'asset' ? 'bg-blue-100' : 
                           ($stat['account_type'] === 'liability' ? 'bg-red-100' :
                           ($stat['account_type'] === 'equity' ? 'bg-purple-100' :
                           ($stat['account_type'] === 'revenue' ? 'bg-green-100' :
                           'bg-orange-100'))) ?>">
                        <i data-lucide="<?= $stat['account_type'] === 'asset' ? 'trending-up' : 
                                          ($stat['account_type'] === 'liability' ? 'trending-down' :
                                          ($stat['account_type'] === 'equity' ? 'pie-chart' :
                                          ($stat['account_type'] === 'revenue' ? 'dollar-sign' :
                                          'credit-card'))) ?>" 
                           class="w-6 h-6 <?= $stat['account_type'] === 'asset' ? 'text-blue-600' : 
                                            ($stat['account_type'] === 'liability' ? 'text-red-600' :
                                            ($stat['account_type'] === 'equity' ? 'text-purple-600' :
                                            ($stat['account_type'] === 'revenue' ? 'text-green-600' :
                                            'text-orange-600'))) ?>"></i>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search accounts..." 
                       value="<?= esc($filters['search']) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select name="account_type" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="asset" <?= $filters['account_type'] === 'asset' ? 'selected' : '' ?>>Assets</option>
                    <option value="liability" <?= $filters['account_type'] === 'liability' ? 'selected' : '' ?>>Liabilities</option>
                    <option value="equity" <?= $filters['account_type'] === 'equity' ? 'selected' : '' ?>>Equity</option>
                    <option value="revenue" <?= $filters['account_type'] === 'revenue' ? 'selected' : '' ?>>Revenue</option>
                    <option value="expense" <?= $filters['account_type'] === 'expense' ? 'selected' : '' ?>>Expenses</option>
                </select>
            </div>
            <div>
                <select name="category_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $filters['category_id'] == $category['id'] ? 'selected' : '' ?>>
                            <?= esc($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <select name="is_active" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="1" <?= $filters['is_active'] === '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= $filters['is_active'] === '0' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="search" class="w-4 h-4"></i>
            </button>
        </form>
    </div>

    <!-- Accounts Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($accounts)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="folder-x" class="w-12 h-12 mx-auto mb-4 text-gray-400"></i>
                                <p class="text-lg font-medium">No accounts found</p>
                                <p class="text-sm">Get started by creating your first account.</p>
                                <a href="<?= base_url('admin/accounting/chart-of-accounts/create') ?>" class="inline-flex items-center mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                    Create Account
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($accounts as $account): ?>
                            <tr class="hover:bg-gray-50 account-row <?= isset($account['depth']) ? 'account-depth-' . min($account['depth'], 2) : 'account-depth-0' ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php if (isset($account['depth'])): ?>
                                            <?php for ($i = 0; $i < $account['depth']; $i++): ?>
                                                <div class="w-6 mr-1">
                                                    <?php if ($i == $account['depth'] - 1): ?>
                                                        <i data-lucide="corner-down-right" class="w-4 h-4 text-gray-400"></i>
                                                    <?php else: ?>
                                                        <div class="w-px h-6 bg-gray-300 mx-auto"></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                        <div class="flex-1">
                                            <div class="flex items-center">
                                                <?php if (!empty($account['children']) && count($account['children']) > 0): ?>
                                                    <i data-lucide="folder" class="w-4 h-4 mr-2 text-blue-500"></i>
                                                <?php else: ?>
                                                    <i data-lucide="file-text" class="w-4 h-4 mr-2 text-gray-400"></i>
                                                <?php endif; ?>
                                                <div class="text-sm font-medium text-gray-900 <?= isset($account['depth']) && $account['depth'] > 0 ? '' : 'font-bold' ?>">
                                                    <?= esc($account['account_name']) ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($account['description'])): ?>
                                                <div class="text-sm text-gray-500 ml-6"><?= esc($account['description']) ?></div>
                                            <?php endif; ?>
                                            <div class="flex items-center mt-1">
                                                <?php if ($account['is_system_account']): ?>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-6">
                                                        System Account
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!empty($account['parent_account_name'])): ?>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                                        <i data-lucide="link" class="w-3 h-3 mr-1"></i>
                                                        Child of: <?= esc($account['parent_account_name']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <?= esc($account['account_code']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        <?= $account['account_type'] === 'asset' ? 'bg-blue-100 text-blue-800' : 
                                           ($account['account_type'] === 'liability' ? 'bg-red-100 text-red-800' :
                                           ($account['account_type'] === 'equity' ? 'bg-purple-100 text-purple-800' :
                                           ($account['account_type'] === 'revenue' ? 'bg-green-100 text-green-800' :
                                           'bg-orange-100 text-orange-800'))) ?>">
                                        <?= esc($account['account_type_label']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $account['category_name'] ? esc($account['category_name']) : 'N/A' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    MWK <?= number_format($account['balance'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="toggleStatus(<?= $account['id'] ?>, <?= $account['is_active'] ?>)" 
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors
                                                <?= $account['is_active'] ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' ?>">
                                        <?= $account['is_active'] ? 'Active' : 'Inactive' ?>
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="<?= base_url('admin/accounting/chart-of-accounts/' . $account['id']) ?>" 
                                           class="text-indigo-600 hover:text-indigo-900" title="View">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="<?= base_url('admin/accounting/chart-of-accounts/' . $account['id'] . '/edit') ?>" 
                                           class="text-blue-600 hover:text-blue-900" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <?php if (!$account['is_system_account'] && $account['transaction_count'] == 0): ?>
                                            <button onclick="deleteAccount(<?= $account['id'] ?>, '<?= esc($account['account_name']) ?>')" 
                                                    class="text-red-600 hover:text-red-900" title="Delete">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Delete</h3>
        <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete the account "<span id="accountName"></span>"? This action cannot be undone.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                Cancel
            </button>
            <form id="deleteForm" method="POST" class="inline">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleStatus(id, currentStatus) {
    fetch(`<?= base_url('admin/accounting/chart-of-accounts') ?>/${id}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the status.');
    });
}

function deleteAccount(id, name) {
    document.getElementById('accountName').textContent = name;
    document.getElementById('deleteForm').action = `<?= base_url('admin/accounting/chart-of-accounts') ?>/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

// Tree view toggle functionality
function toggleChildren(accountId) {
    const rows = document.querySelectorAll(`[data-parent-id="${accountId}"]`);
    const toggleIcon = document.querySelector(`[data-toggle-id="${accountId}"] i`);
    
    rows.forEach(row => {
        if (row.style.display === 'none') {
            row.style.display = 'table-row';
            toggleIcon.setAttribute('data-lucide', 'chevron-down');
        } else {
            row.style.display = 'none';
            toggleIcon.setAttribute('data-lucide', 'chevron-right');
        }
    });
    
    // Re-initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}
</script>

<?= $this->endSection() ?>
