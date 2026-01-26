<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                <p><?= session()->getFlashdata('success') ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
            <div class="flex items-center">
                <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                <p><?= session()->getFlashdata('error') ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Categories</p>
                    <h3 class="text-3xl font-bold mt-2"><?= number_format($stats['total_categories']) ?></h3>
                </div>
                <div class="bg-blue-400 bg-opacity-30 p-3 rounded-lg">
                    <i data-lucide="folder" class="w-8 h-8"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Active Categories</p>
                    <h3 class="text-3xl font-bold mt-2"><?= number_format($stats['active_categories']) ?></h3>
                </div>
                <div class="bg-green-400 bg-opacity-30 p-3 rounded-lg">
                    <i data-lucide="check-circle" class="w-8 h-8"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Budget Types</p>
                    <h3 class="text-3xl font-bold mt-2"><?= count($stats['by_type']) ?></h3>
                </div>
                <div class="bg-purple-400 bg-opacity-30 p-3 rounded-lg">
                    <i data-lucide="layers" class="w-8 h-8"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-lg">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i data-lucide="folder" class="w-6 h-6 mr-2 text-blue-600"></i>
                        Budget Categories
                    </h2>
                    <p class="text-gray-600 text-sm mt-1">Manage budget categories for cost tracking</p>
                </div>
                <a href="<?= base_url('admin/accounting/budget-categories/create') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Create Category
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Budget Type</label>
                    <select name="budget_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <?php foreach ($budgetTypes as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $filters['budget_type'] == $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="is_active" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="1" <?= $filters['is_active'] === '1' ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= $filters['is_active'] === '0' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="<?= esc($filters['search']) ?>" placeholder="Search categories..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <i data-lucide="inbox" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                                <p class="text-gray-500 text-lg">No budget categories found</p>
                                <p class="text-gray-400 mt-2">Create your first budget category to get started</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?= esc($category['name']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $typeColors = [
                                        'labor' => 'blue',
                                        'materials' => 'green',
                                        'equipment' => 'yellow',
                                        'subcontractor' => 'purple',
                                        'overhead' => 'gray',
                                        'other' => 'pink'
                                    ];
                                    $color = $typeColors[$category['budget_type']] ?? 'gray';
                                    ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800">
                                        <?= ucfirst($category['budget_type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">
                                        <?= $category['description'] ? esc(substr($category['description'], 0, 50)) . (strlen($category['description']) > 50 ? '...' : '') : '<span class="text-gray-400">No description</span>' ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($category['is_active']): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i data-lucide="check" class="w-3 h-3 inline"></i> Active
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <i data-lucide="x" class="w-3 h-3 inline"></i> Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="<?= base_url('admin/accounting/budget-categories/' . $category['id'] . '/edit') ?>" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <button onclick="deleteCategory(<?= $category['id'] ?>)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
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

<script>
lucide.createIcons();

function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this budget category? This action cannot be undone.')) {
        fetch('<?= base_url('admin/accounting/budget-categories') ?>/' + id, {
            method: 'DELETE',
            headers: {
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
            alert('Error deleting category');
            console.error('Error:', error);
        });
    }
}
</script>
<?= $this->endSection() ?>
