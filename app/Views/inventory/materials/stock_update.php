<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Update Stock Level<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Update Material Stock</h1>
                <p class="text-gray-600">Record stock movements for <?= esc($material['name']) ?></p>
            </div>
            <div>
                <a href="<?= base_url('admin/materials/edit/' . $material['id']) ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Material
                </a>
            </div>
        </div>
    </div>

    <!-- Stock Update Cards -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Material Info Card -->
        <div class="w-full lg:w-1/3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i data-lucide="package" class="w-8 h-8 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900"><?= esc($material['name']) ?></h3>
                        <p class="text-gray-600"><?= esc($material['sku']) ?></p>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Category:</span>
                            <span class="font-medium"><?= esc($category['name'] ?? 'None') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Unit:</span>
                            <span class="font-medium"><?= esc(ucfirst($material['unit'])) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Cost per Unit:</span>
                            <span class="font-medium">$<?= number_format($material['unit_cost'], 2) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Current Stock:</span>
                            <span class="text-xl font-bold"><?= number_format($material['current_stock']) ?> <?= esc($material['unit']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Min. Level:</span>
                            <span class="<?= $material['current_stock'] < $material['min_stock_level'] ? 'text-red-600 font-bold' : 'font-medium' ?>">
                                <?= number_format($material['min_stock_level']) ?> <?= esc($material['unit']) ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Value:</span>
                            <span class="font-medium">$<?= number_format($material['current_stock'] * $material['unit_cost'], 2) ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if ($material['current_stock'] < $material['min_stock_level']): ?>
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <div class="bg-red-50 text-red-700 p-4 rounded-lg border border-red-200">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                            <p>Stock level is below the minimum threshold. Consider ordering more.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Stock Movement Form -->
        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Record Stock Movement</h3>
                </div>
                <div class="p-6">
                    <?php if (session('errors')): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (session('success')): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
                        <p><?= session('success') ?></p>
                    </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/materials/update-stock/' . $material['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="movement_type" class="block text-sm font-medium text-gray-700 mb-2">Movement Type <span class="text-red-500">*</span></label>
                                    <select name="movement_type" id="movement_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                        <option value="">Select Movement Type</option>
                                        <option value="in_purchase">Stock In - Purchase</option>
                                        <option value="in_return">Stock In - Return from Project</option>
                                        <option value="in_adjustment">Stock In - Inventory Adjustment</option>
                                        <option value="in_transfer">Stock In - Warehouse Transfer</option>
                                        <option value="out_project">Stock Out - Assigned to Project</option>
                                        <option value="out_damaged">Stock Out - Damaged/Discarded</option>
                                        <option value="out_adjustment">Stock Out - Inventory Adjustment</option>
                                        <option value="out_transfer">Stock Out - Warehouse Transfer</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                                    <input type="number" name="quantity" id="quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0.01" step="<?= $material['is_bulk'] ? '0.01' : '1' ?>" required>
                                    <p class="mt-1 text-sm text-gray-500">Unit: <?= esc(ucfirst($material['unit'])) ?></p>
                                </div>
                            </div>
                            
                            <div id="projectSection" class="hidden">
                                <div>
                                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                                    <select name="project_id" id="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Project</option>
                                        <?php foreach ($projects as $project): ?>
                                        <option value="<?= $project['id'] ?>">
                                            <?= esc($project['name']) ?> (<?= esc($project['project_code']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div id="warehouseSection" class="hidden">
                                <div>
                                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">Warehouse</label>
                                    <select name="warehouse_id" id="warehouse_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Warehouse</option>
                                        <?php foreach ($warehouses as $warehouse): ?>
                                        <option value="<?= $warehouse['id'] ?>">
                                            <?= esc($warehouse['name']) ?> (<?= esc($warehouse['location']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div id="supplierSection" class="hidden">
                                <div>
                                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                                    <select name="supplier_id" id="supplier_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Supplier</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?= $supplier['id'] ?>" <?= $supplier['id'] == $material['primary_supplier_id'] ? 'selected' : '' ?>>
                                            <?= esc($supplier['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">Unit Cost</label>
                                <input type="number" name="unit_cost" id="unit_cost" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" step="0.01" value="<?= $material['unit_cost'] ?>">
                                <p class="mt-1 text-sm text-gray-500">Leave as is unless the cost has changed</p>
                            </div>
                            
                            <div>
                                <label for="reference_no" class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                                <input type="text" name="reference_no" id="reference_no" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-sm text-gray-500">E.g., Invoice number, delivery note, etc.</p>
                            </div>
                            
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                            
                            <div class="pt-4">
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i> Record Stock Movement
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Recent Stock Movements -->
            <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Stock Movements</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
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
                                        <?= $movement['created_by_name'] ? esc($movement['created_by_name']) : 'System' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (!empty($stockMovements) && count($stockMovements) >= 5): ?>
                <div class="px-6 py-3 border-t border-gray-200">
                    <a href="<?= base_url('admin/materials/stock-history/' . $material['id']) ?>" class="text-sm text-blue-600 hover:underline">
                        View Full History
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Show/hide relevant sections based on movement type
    const movementTypeSelect = document.getElementById('movement_type');
    const projectSection = document.getElementById('projectSection');
    const warehouseSection = document.getElementById('warehouseSection');
    const supplierSection = document.getElementById('supplierSection');
    
    movementTypeSelect.addEventListener('change', function() {
        const selectedValue = this.value;
        
        // Hide all sections first
        projectSection.classList.add('hidden');
        warehouseSection.classList.add('hidden');
        supplierSection.classList.add('hidden');
        
        // Show relevant sections based on selection
        if (selectedValue.includes('project')) {
            projectSection.classList.remove('hidden');
        }
        
        if (selectedValue.includes('transfer')) {
            warehouseSection.classList.remove('hidden');
        }
        
        if (selectedValue === 'in_purchase') {
            supplierSection.classList.remove('hidden');
        }
    });
});
</script>
<?= $this->endSection() ?>
