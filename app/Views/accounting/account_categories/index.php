<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Account Categories</h1>
            <p class="text-gray-600">Manage account categories for your chart of accounts</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/accounting/account-categories/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                New Category
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search categories..." 
                       value="<?= esc($this->request->getGet('search')) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select name="account_type" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="asset" <?= $this->request->getGet('account_type') === 'asset' ? 'selected' : '' ?>>Assets</option>
                    <option value="liability" <?= $this->request->getGet('account_type') === 'liability' ? 'selected' : '' ?>>Liabilities</option>
                    <option value="equity" <?= $this->request->getGet('account_type') === 'equity' ? 'selected' : '' ?>>Equity</option>
                    <option value="revenue" <?= $this->request->getGet('account_type') === 'revenue' ? 'selected' : '' ?>>Revenue</option>
                    <option value="expense" <?= $this->request->getGet('account_type') === 'expense' ? 'selected' : '' ?>>Expenses</option>
                </select>
            </div>
            <div>
                <select name="is_active" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="1" <?= $this->request->getGet('is_active') === '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= $this->request->getGet('is_active') === '0' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="search" class="w-4 h-4"></i>
            </button>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accounts</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="folder-x" class="w-12 h-12 mx-auto mb-4 text-gray-400"></i>
                                <p class="text-lg font-medium">No account categories found</p>
                                <p class="text-sm">Get started by creating your first account category.</p>
                                <a href="<?= base_url('admin/accounting/account-categories/create') ?>" class="inline-flex items-center mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                    Create Category
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= esc($category['name']) ?></div>
                                        <?php if (!empty($category['description'])): ?>
                                            <div class="text-sm text-gray-500"><?= esc($category['description']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <?= esc($category['code']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        <?= $category['account_type'] === 'asset' ? 'bg-blue-100 text-blue-800' : 
                                           ($category['account_type'] === 'liability' ? 'bg-red-100 text-red-800' :
                                           ($category['account_type'] === 'equity' ? 'bg-purple-100 text-purple-800' :
                                           ($category['account_type'] === 'revenue' ? 'bg-green-100 text-green-800' :
                                           'bg-orange-100 text-orange-800'))) ?>">
                                        <?= esc($category['account_type_label']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $category['accounts_count'] ?> accounts
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="toggleStatus(<?= $category['id'] ?>, <?= $category['is_active'] ?>)" 
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors
                                                <?= $category['is_active'] ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' ?>">
                                        <?= $category['is_active'] ? 'Active' : 'Inactive' ?>
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="<?= base_url('admin/accounting/account-categories/' . $category['id']) ?>" 
                                           class="text-indigo-600 hover:text-indigo-900" title="View">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="<?= base_url('admin/accounting/account-categories/' . $category['id'] . '/edit') ?>" 
                                           class="text-blue-600 hover:text-blue-900" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <?php if ($category['accounts_count'] == 0): ?>
                                            <button onclick="deleteCategory(<?= $category['id'] ?>, '<?= esc($category['name']) ?>')" 
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
        <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete the category "<span id="categoryName"></span>"? This action cannot be undone.</p>
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
    fetch(`<?= base_url('admin/accounting/account-categories') ?>/${id}/toggle`, {
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

function deleteCategory(id, name) {
    document.getElementById('categoryName').textContent = name;
    document.getElementById('deleteForm').action = `<?= base_url('admin/accounting/account-categories') ?>/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}
</script>

<?= $this->endSection() ?>
