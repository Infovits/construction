<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.category-labor { @apply bg-blue-50 border-l-4 border-blue-400; }
.category-material { @apply bg-green-50 border-l-4 border-green-400; }
.category-equipment { @apply bg-yellow-50 border-l-4 border-yellow-400; }
.category-subcontractor { @apply bg-purple-50 border-l-4 border-purple-400; }
.category-overhead { @apply bg-red-50 border-l-4 border-red-400; }
.category-other { @apply bg-gray-50 border-l-4 border-gray-400; }

.cost-direct { @apply text-green-600 font-semibold; }
.cost-indirect { @apply text-orange-600 font-semibold; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cost Codes</h1>
            <p class="text-gray-600">Manage cost codes for job costing and project tracking</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/accounting/cost-codes/create') ?>" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                New Cost Code
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

    <!-- Statistics Cards -->
    <?php if (!empty($stats)): ?>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <?php foreach ($stats as $stat): ?>
        <div class="bg-white p-4 rounded-lg shadow-sm border category-<?= $stat['category'] ?>">
            <div class="text-center">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-600"><?= ucfirst($stat['category']) ?></p>
                <p class="text-xl font-bold text-gray-900 mt-1"><?= $stat['active_codes'] ?></p>
                <p class="text-xs text-gray-500">of <?= $stat['total_codes'] ?> codes</p>
                <?php if ($stat['avg_rate']): ?>
                    <p class="text-xs text-gray-500 mt-1">Avg: MWK <?= number_format($stat['avg_rate'], 2) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $filters['category'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cost Type</label>
                <select name="cost_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Types</option>
                    <?php foreach ($costTypes as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $filters['cost_type'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="is_active" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="1" <?= $filters['is_active'] === '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= $filters['is_active'] === '0' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="<?= esc($filters['search']) ?>" placeholder="Search codes..."
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

    <!-- Cost Codes Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($costCodes)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="code" class="w-12 h-12 mx-auto mb-4 text-gray-400"></i>
                                <p class="text-lg font-medium">No cost codes found</p>
                                <p class="text-sm">Create your first cost code to start tracking project costs.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $currentCategory = '';
                        foreach ($costCodes as $costCode): 
                        ?>
                            <?php if ($currentCategory !== $costCode['category']): ?>
                                <?php $currentCategory = $costCode['category']; ?>
                                <tr class="bg-gray-100">
                                    <td colspan="8" class="px-6 py-2 text-sm font-semibold text-gray-700 uppercase tracking-wide">
                                        <?= ucfirst($costCode['category']) ?> Codes
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                            <tr class="hover:bg-gray-50 category-<?= $costCode['category'] ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= esc($costCode['code']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= esc($costCode['name']) ?></div>
                                    <?php if ($costCode['description']): ?>
                                        <div class="text-sm text-gray-500"><?= esc($costCode['description']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <?= ucfirst($costCode['category']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="cost-<?= $costCode['cost_type'] ?>">
                                        <?= ucfirst($costCode['cost_type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($costCode['standard_rate']): ?>
                                        MWK <?= number_format($costCode['standard_rate'], 2) ?>
                                        <?php if ($costCode['unit_of_measure']): ?>
                                            <span class="text-gray-500">/ <?= esc($costCode['unit_of_measure']) ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium"><?= $costCode['usage_count'] ?></span>
                                        <span class="text-xs text-gray-500 ml-1">entries</span>
                                    </div>
                                    <?php if ($costCode['total_cost_tracked']): ?>
                                        <div class="text-xs text-gray-500">
                                            MWK <?= number_format($costCode['total_cost_tracked'], 2) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($costCode['is_active']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="<?= base_url('admin/accounting/cost-codes/' . $costCode['id']) ?>" 
                                           class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="<?= base_url('admin/accounting/cost-codes/' . $costCode['id'] . '/edit') ?>" 
                                           class="text-blue-600 hover:text-blue-900" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <form method="POST" action="<?= base_url('admin/accounting/cost-codes/' . $costCode['id'] . '/toggle') ?>" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" 
                                                    class="<?= $costCode['is_active'] ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' ?>" 
                                                    title="<?= $costCode['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                                <i data-lucide="<?= $costCode['is_active'] ? 'toggle-right' : 'toggle-left' ?>" class="w-4 h-4"></i>
                                            </button>
                                        </form>
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
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>

<?= $this->endSection() ?>