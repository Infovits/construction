<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Materials Management</h1>
            <p class="text-gray-600">Manage construction materials, track inventory levels, and monitor usage</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/materials/new') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Add Material
            </a>
            <a href="<?= base_url('admin/materials/report') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="file-bar-chart" class="w-4 h-4 mr-2"></i>
                Generate Report
            </a>
            <a href="<?= base_url('admin/materials/barcode-scanner') ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-lucide="scan-barcode" class="w-4 h-4 mr-2"></i>
                Barcode Scanner
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">Total Materials</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($materials) ?></p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="package" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wide">Categories</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($categories) ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="tag" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-amber-600 uppercase tracking-wide">Low Stock Items</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($lowStockItems) ?></p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Total Value</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php
                        $totalValue = 0;
                        foreach ($materials as $material) {
                            $totalValue += $material['unit_cost'] * $material['current_stock'];
                        }
                        echo 'MWK ' . number_format($totalValue, 2);
                        ?>
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="wallet" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <?php if (!empty($lowStockItems)): ?>
    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="alert-triangle" class="h-5 w-5 text-amber-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-amber-800">Low Stock Alert</h3>
                <div class="mt-2 text-sm text-amber-700">
                    <p>There are <?= count($lowStockItems) ?> materials with stock levels below the minimum threshold.</p>
                </div>
                <div class="mt-4">
                    <div class="-mx-2 -my-1.5 flex">
                        <a href="#low-stock-section" class="inline-flex rounded-md bg-amber-50 px-2 py-1.5 text-sm font-medium text-amber-800 hover:bg-amber-100">
                            View details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-4 border-b">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900">Material Inventory</h2>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search materials..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                        </div>
                    </div>
                    <select id="categoryFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="materialsTableBody" class="bg-white divide-y divide-gray-200">
                    <?php if (empty($materials)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            No materials found. <a href="<?= base_url('admin/materials/new') ?>" class="text-indigo-600 hover:text-indigo-900">Add your first material</a>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($materials as $material): ?>
                    <tr class="material-row hover:bg-gray-50" data-category="<?= $material['category_id'] ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?= esc($material['item_code']) ?>
                            <?php if (!empty($material['barcode'])): ?>
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800">
                                <i data-lucide="barcode" class="h-3 w-3 mr-1"></i>
                                <?= substr($material['barcode'], 0, 10) . (strlen($material['barcode']) > 10 ? '...' : '') ?>
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= esc($material['name']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= esc($material['category_id'] ? ($categories[array_search($material['category_id'], array_column($categories, 'id'))]['name'] ?? 'N/A') : 'Uncategorized') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= esc($material['unit']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php 
                            $stockClass = 'text-green-600';
                            $stockIcon = 'check-circle';
                            
                            if ($material['current_stock'] <= $material['minimum_stock']) {
                                $stockClass = 'text-red-600';
                                $stockIcon = 'alert-circle';
                            } else if ($material['current_stock'] <= ($material['minimum_stock'] * 1.25)) {
                                $stockClass = 'text-amber-600';
                                $stockIcon = 'alert-triangle';
                            }
                            ?>
                            <div class="flex items-center">
                                <i data-lucide="<?= $stockIcon ?>" class="h-4 w-4 mr-1 <?= $stockClass ?>"></i>
                                <span class="font-medium <?= $stockClass ?>">
                                    <?= number_format($material['current_stock'], 2) ?>
                                </span>
                                <span class="text-gray-500 ml-1">/ <?= number_format($material['minimum_stock'], 2) ?> min</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($material['status'] === 'active'): ?>
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    Active
                                </span>
                            <?php elseif ($material['status'] === 'inactive'): ?>
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                    Inactive
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                    Discontinued
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex space-x-2">
                                <a href="<?= base_url('admin/materials/stock-movement/' . $material['id']) ?>" class="text-blue-600 hover:text-blue-900" title="Stock Movements">
                                    <i data-lucide="move" class="h-4 w-4"></i>
                                </a>
                                <a href="<?= base_url('admin/materials/edit/' . $material['id']) ?>" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <i data-lucide="edit" class="h-4 w-4"></i>
                                </a>
                                <a href="<?= base_url('admin/materials/delete/' . $material['id']) ?>" class="text-red-600 hover:text-red-900 delete-confirm" title="Delete" data-name="<?= $material['name'] ?>">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Low Stock Section -->
    <?php if (!empty($lowStockItems)): ?>
    <div id="low-stock-section" class="bg-white rounded-lg shadow-sm border">
        <div class="p-4 border-b bg-amber-50">
            <h2 class="text-lg font-semibold text-amber-800">Low Stock Items</h2>
            <p class="text-sm text-amber-700">These items need to be restocked soon</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Minimum Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($lowStockItems as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?= esc($item['item_code']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= esc($item['name']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                            <?= number_format($item['current_stock'], 2) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= number_format($item['minimum_stock'], 2) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="<?= base_url('admin/materials/stock-movement/' . $item['id']) ?>" class="inline-flex items-center px-3 py-1 border border-amber-600 text-amber-600 rounded-md hover:bg-amber-50 hover:text-amber-700 transition-colors">
                                <i data-lucide="plus-circle" class="h-4 w-4 mr-2"></i>
                                Add Stock
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const materialsTableBody = document.getElementById('materialsTableBody');
    const materialRows = materialsTableBody.querySelectorAll('.material-row');
    
    function filterMaterials() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        
        materialRows.forEach(row => {
            const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const code = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const rowCategory = row.dataset.category;
            
            const matchesSearch = name.includes(searchTerm) || code.includes(searchTerm);
            const matchesCategory = !selectedCategory || rowCategory === selectedCategory;
            
            row.style.display = matchesSearch && matchesCategory ? '' : 'none';
        });
    }
    
    searchInput.addEventListener('input', filterMaterials);
    categoryFilter.addEventListener('change', filterMaterials);
    
    // Handle delete confirmations
    document.querySelectorAll('.delete-confirm').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const materialName = this.dataset.name;
            if (confirm(`Are you sure you want to delete the material "${materialName}"? This action cannot be undone.`)) {
                window.location.href = this.href;
            }
        });
    });
    
    // Initialize Lucide icons
    lucide.createIcons();
});
</script>
<?= $this->endSection() ?>
