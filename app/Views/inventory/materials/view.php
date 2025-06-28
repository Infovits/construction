<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Material Details<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= esc($material['name']) ?></h1>
                <p class="text-gray-600">Material ID: <?= esc($material['sku']) ?></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?= base_url('admin/materials') ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Materials
                </a>
                <a href="<?= base_url('admin/materials/edit/' . $material['id']) ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i> Edit Material
                </a>
                <a href="<?= base_url('admin/materials/stock/' . $material['id']) ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Update Stock
                </a>
            </div>
        </div>
    </div>

    <!-- Material Details Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Material Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Material Name</dt>
                                    <dd class="mt-1 text-lg font-medium text-gray-900"><?= esc($material['name']) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">SKU/Item Code</dt>
                                    <dd class="mt-1 text-gray-900"><?= esc($material['sku']) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                                    <dd class="mt-1 text-gray-900"><?= esc($category['name'] ?? 'Uncategorized') ?></dd>
                                </div>
                                <?php if ($material['barcode']): ?>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                                    <dd class="mt-1 text-gray-900"><?= esc($material['barcode']) ?></dd>
                                </div>
                                <?php endif; ?>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-gray-900"><?= nl2br(esc($material['description'] ?: 'No description provided.')) ?></dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Unit of Measure</dt>
                                    <dd class="mt-1 text-gray-900"><?= ucfirst(esc($material['unit'])) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Unit Cost</dt>
                                    <dd class="mt-1 text-gray-900">$<?= number_format($material['unit_cost'], 2) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Primary Supplier</dt>
                                    <dd class="mt-1 text-gray-900">
                                        <?php if (isset($supplier)): ?>
                                            <a href="<?= base_url('admin/suppliers/view/' . $supplier['id']) ?>" class="text-blue-600 hover:underline">
                                                <?= esc($supplier['name']) ?>
                                            </a>
                                        <?php else: ?>
                                            No primary supplier
                                        <?php endif; ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Material Properties</dt>
                                    <dd class="mt-1 flex flex-wrap gap-2">
                                        <span class="px-2 py-1 text-xs rounded-full <?= $material['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                            <?= $material['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                        <span class="px-2 py-1 text-xs rounded-full <?= $material['is_bulk'] ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' ?>">
                                            <?= $material['is_bulk'] ? 'Bulk Material' : 'Unit Material' ?>
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created / Updated</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <?= date('M d, Y', strtotime($material['created_at'])) ?> / 
                                        <?= date('M d, Y', strtotime($material['updated_at'])) ?>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stock Movement History -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Stock Movement History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($stockMovements)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No stock movements recorded yet</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($stockMovements as $movement): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M d, Y H:i', strtotime($movement['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                        $movementType = explode('_', $movement['movement_type']);
                                        $direction = $movementType[0] === 'in' ? 'Stock In' : 'Stock Out';
                                        $type = ucfirst($movementType[1] ?? '');
                                        $badgeColor = $movementType[0] === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badgeColor ?>">
                                            <?= $direction ?>: <?= $type ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="font-medium">
                                            <?= $movementType[0] === 'in' ? '+' : '-' ?><?= $movement['quantity'] ?> <?= $material['unit'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $movement['reference_no'] ? esc($movement['reference_no']) : 'N/A' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if ($movement['project_id']): ?>
                                            <span class="text-blue-600">Project: <?= esc($movement['project_name'] ?? 'Unknown') ?></span>
                                        <?php elseif ($movement['warehouse_id']): ?>
                                            <span>Warehouse: <?= esc($movement['warehouse_name'] ?? 'Unknown') ?></span>
                                        <?php elseif ($movement['supplier_id']): ?>
                                            <span>Supplier: <?= esc($movement['supplier_name'] ?? 'Unknown') ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 border-t border-gray-200">
                    <a href="<?= base_url('admin/materials/stock-history/' . $material['id']) ?>" class="text-sm text-blue-600 hover:underline">
                        View Full History
                    </a>
                </div>
            </div>

            <!-- Projects Using This Material -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Projects Using This Material</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Used</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($projectUsage)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No project usage recorded yet</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($projectUsage as $usage): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="<?= base_url('admin/projects/view/' . $usage['project_id']) ?>" class="text-blue-600 hover:underline">
                                            <?= esc($usage['project_name']) ?>
                                        </a>
                                        <div class="text-xs text-gray-500"><?= esc($usage['project_code']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">
                                        <?= $usage['total_quantity'] ?> <?= $material['unit'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M d, Y', strtotime($usage['last_used'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        $<?= number_format($usage['total_quantity'] * $material['unit_cost'], 2) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Stats & Actions -->
        <div class="space-y-6">
            <!-- Stock Status Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Stock Status</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-500">Current Stock:</span>
                            <span class="text-xl font-bold text-gray-900"><?= number_format($material['current_stock']) ?> <?= $material['unit'] ?></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <?php 
                            $stockPercentage = min(100, ($material['current_stock'] / max(1, $material['min_stock_level'] * 2)) * 100);
                            $barColor = $stockPercentage <= 25 ? 'bg-red-600' : ($stockPercentage <= 50 ? 'bg-amber-500' : 'bg-green-600');
                            ?>
                            <div class="<?= $barColor ?> h-2.5 rounded-full" style="width: <?= $stockPercentage ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Min Stock Level</p>
                            <p class="text-lg font-medium"><?= number_format($material['min_stock_level']) ?></p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Reorder Quantity</p>
                            <p class="text-lg font-medium"><?= number_format($material['reorder_quantity']) ?></p>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100">
                        <p class="text-sm text-gray-500">Total Value</p>
                        <p class="text-2xl font-bold">$<?= number_format($material['current_stock'] * $material['unit_cost'], 2) ?></p>
                    </div>
                    
                    <?php if ($material['current_stock'] < $material['min_stock_level']): ?>
                    <div class="bg-red-50 text-red-700 p-4 rounded-lg border border-red-200 mt-4">
                        <div class="flex items-center">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-2"></i>
                            <p class="font-medium">Low Stock Warning</p>
                        </div>
                        <p class="mt-1 text-sm">Current stock level is below the minimum threshold. Consider ordering more.</p>
                        <?php if (isset($supplier)): ?>
                        <a href="<?= base_url('admin/suppliers/view/' . $supplier['id']) ?>" class="mt-2 inline-flex items-center text-sm text-red-700 hover:underline">
                            <i data-lucide="phone" class="w-4 h-4 mr-1"></i>
                            Contact supplier
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Warehouse Distribution -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Warehouse Distribution</h3>
                </div>
                <div class="p-6">
                    <?php if (empty($warehouseStock)): ?>
                    <div class="text-center py-6 text-gray-500">
                        <p>No warehouse distribution data available.</p>
                    </div>
                    <?php else: ?>
                        <ul class="space-y-4">
                            <?php foreach ($warehouseStock as $stock): ?>
                            <li class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium"><?= esc($stock['warehouse_name']) ?></p>
                                    <p class="text-xs text-gray-500"><?= esc($stock['location']) ?></p>
                                </div>
                                <span class="font-medium"><?= number_format($stock['quantity']) ?> <?= $material['unit'] ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Action Links -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 space-y-3">
                    <a href="<?= base_url('admin/materials/stock/' . $material['id']) ?>" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Update Stock
                    </a>
                    <a href="<?= base_url('admin/materials/edit/' . $material['id']) ?>" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="edit" class="w-4 h-4 mr-2"></i> Edit Material
                    </a>
                    <a href="<?= base_url('admin/materials/stock-history/' . $material['id']) ?>" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i data-lucide="history" class="w-4 h-4 mr-2"></i> Full Stock History
                    </a>
                    <a href="<?= base_url('admin/materials/report/' . $material['id']) ?>" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Generate Report
                    </a>
                    <button onclick="confirmDelete(<?= $material['id'] ?>)" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i> Delete Material
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
});

function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this material? This action cannot be undone.')) {
        window.location.href = '<?= base_url('admin/materials/delete') ?>/' + id;
    }
}
</script>
<?= $this->endSection() ?>
