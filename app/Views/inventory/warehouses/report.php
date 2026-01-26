<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= esc($title) ?></h1>
                <p class="text-gray-600">Generated on <?= date('F d, Y \a\t H:i') ?></p>
            </div>
            <div class="flex gap-2">
                <a href="<?= base_url('admin/warehouses/' . $warehouse['id']) ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Warehouse
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Print Report
                </button>
            </div>
        </div>
    </div>

    <!-- Warehouse Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Warehouse Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500">Warehouse Name</p>
                <p class="font-medium"><?= esc($warehouse['name']) ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Code</p>
                <p class="font-medium"><?= esc($warehouse['code'] ?? 'N/A') ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Type</p>
                <p class="font-medium"><?= ucfirst($warehouse['warehouse_type'] ?? 'main') ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p class="font-medium">
                    <?php
                    $statusClass = '';
                    $status = $warehouse['status'] ?? 'active';
                    switch($status) {
                        case 'active':
                            $statusClass = 'text-green-600';
                            break;
                        case 'inactive':
                            $statusClass = 'text-gray-600';
                            break;
                        case 'maintenance':
                            $statusClass = 'text-yellow-600';
                            break;
                        default:
                            $statusClass = 'text-gray-600';
                    }
                    ?>
                    <span class="<?= $statusClass ?>"><?= ucfirst($status) ?></span>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Address</p>
                <p class="font-medium"><?= esc($warehouse['address'] ?? 'Not specified') ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Manager</p>
                <p class="font-medium"><?= esc($warehouse['manager_name'] ?? 'Unassigned') ?></p>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full mr-4">
                    <i data-lucide="package" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Materials</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['total_materials'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-amber-100 rounded-full mr-4">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Low Stock Items</p>
                    <p class="text-2xl font-bold text-amber-600"><?= $stats['low_stock_count'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full mr-4">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Value</p>
                    <p class="text-2xl font-bold text-green-600">$<?= number_format($stats['total_value'] ?? 0, 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Inventory -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Current Inventory</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min. Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($materials)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No materials in this warehouse
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($materials as $material): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= esc($material['name']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= esc($material['item_code']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $stockClass = 'text-green-600';
                                if ($material['current_quantity'] <= 0) {
                                    $stockClass = 'text-red-600';
                                } elseif ($material['current_quantity'] <= $material['minimum_quantity']) {
                                    $stockClass = 'text-amber-600';
                                }
                                ?>
                                <span class="text-sm font-medium <?= $stockClass ?>">
                                    <?= number_format($material['current_quantity']) ?> <?= esc($material['unit']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= number_format($material['minimum_quantity']) ?> <?= esc($material['unit']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                $<?= number_format($material['unit_cost'], 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                $<?= number_format($material['current_quantity'] * $material['unit_cost'], 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusText = '';
                                $statusClass = '';
                                if ($material['current_quantity'] <= 0) {
                                    $statusText = 'Out of Stock';
                                    $statusClass = 'bg-red-100 text-red-800';
                                } elseif ($material['current_quantity'] <= $material['minimum_quantity']) {
                                    $statusText = 'Low Stock';
                                    $statusClass = 'bg-amber-100 text-amber-800';
                                } else {
                                    $statusText = 'In Stock';
                                    $statusClass = 'bg-green-100 text-green-800';
                                }
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Items (if any) -->
    <?php if (!empty($lowStockItems)): ?>
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Low Stock Alert Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min. Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shortage</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($lowStockItems as $item): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= esc($item['name']) ?></div>
                            <div class="text-xs text-gray-500"><?= esc($item['item_code']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-amber-600 font-medium">
                            <?= number_format($item['current_quantity']) ?> <?= esc($item['unit']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= number_format($item['minimum_quantity']) ?> <?= esc($item['unit']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                            <?= number_format($item['minimum_quantity'] - $item['current_quantity']) ?> <?= esc($item['unit']) ?>
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
    // Initialize Lucide icons
    lucide.createIcons();
});
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        font-size: 12px;
    }
    .container {
        max-width: none;
    }
}
</style>
<?= $this->endSection() ?>
