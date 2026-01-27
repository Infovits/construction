<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Warehouse Inventory<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= esc($warehouse['name']) ?></h1>
                <p class="text-gray-600"><?= esc($warehouse['address'] ?? '') ?><?= $warehouse['city'] ? ', ' . esc($warehouse['city']) : '' ?><?= $warehouse['state'] ? ', ' . esc($warehouse['state']) : '' ?></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?= base_url('admin/warehouses') ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> All Warehouses
                </a>
                <button type="button" onclick="openAddStockModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Add Stock
                </button>
                <a href="<?= base_url('admin/warehouses/report/' . $warehouse['id']) ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Report
                </a>
            </div>
        </div>
    </div>
    
    <!-- Warehouse Info & Stats -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Warehouse Info -->
                <div class="lg:col-span-1">
                    <div class="flex items-start">
                        <div class="p-3 bg-blue-100 rounded-full mr-4">
                            <i data-lucide="warehouse" class="w-8 h-8 text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-1"><?= esc($warehouse['name']) ?></h3>
                            <p class="text-gray-600"><?= esc($warehouse['address'] ?? '') ?><?= $warehouse['city'] ? ', ' . esc($warehouse['city']) : '' ?><?= $warehouse['state'] ? ', ' . esc($warehouse['state']) : '' ?></p>
                            
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">
                                    <span class="font-medium">Manager:</span> <?= esc($warehouse['manager_name'] ?? 'Unassigned') ?>
                                </p>
                                <?php if (!empty($warehouse['contact_info'])): ?>
                                <p class="text-sm text-gray-500">
                                    <span class="font-medium">Contact:</span> <?= esc($warehouse['contact_info']) ?>
                                </p>
                                <?php endif; ?>
                                <?php if (!empty($warehouse['description'])): ?>
                                <p class="text-sm text-gray-500 mt-2">
                                    <?= nl2br(esc($warehouse['description'])) ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Warehouse Stats -->
                <div class="lg:col-span-2">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">Total Materials</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $stats['total_materials'] ?? 0 ?></p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">Low Stock Items</p>
                            <p class="text-2xl font-bold text-amber-600"><?= $stats['low_stock_count'] ?? 0 ?></p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">Total Value</p>
                            <p class="text-2xl font-bold text-blue-600">MWK <?= number_format($stats['total_value'] ?? 0, 2) ?></p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 mb-1">Last Movement</p>
                            <p class="text-lg font-medium text-green-600"><?= !empty($stats['last_movement']) ? date('M d, Y', strtotime($stats['last_movement'])) : 'None' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Filters -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <form action="<?= base_url('admin/warehouses/view/' . $warehouse['id']) ?>" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-1/3">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" id="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= isset($_GET['category_id']) && $_GET['category_id'] == $category['id'] ? 'selected' : '' ?>>
                            <?= esc($category['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w-full md:w-1/3">
                    <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                    <select name="stock_status" id="stock_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="low" <?= isset($_GET['stock_status']) && $_GET['stock_status'] === 'low' ? 'selected' : '' ?>>Low Stock</option>
                        <option value="ok" <?= isset($_GET['stock_status']) && $_GET['stock_status'] === 'ok' ? 'selected' : '' ?>>In Stock</option>
                        <option value="zero" <?= isset($_GET['stock_status']) && $_GET['stock_status'] === 'zero' ? 'selected' : '' ?>>Out of Stock</option>
                    </select>
                </div>
                <div class="w-full md:w-1/3 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex-grow">
                        <i data-lucide="filter" class="w-4 h-4 inline mr-1"></i> Filter
                    </button>
                    <a href="<?= base_url('admin/warehouses/view/' . $warehouse['id']) ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i data-lucide="x" class="w-4 h-4 inline"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Current Inventory</h3>
            
            <div class="flex items-center gap-2">
                <form class="flex items-center" action="<?= base_url('admin/warehouses/view/' . $warehouse['id']) ?>" method="GET">
                    <input type="search" name="search" placeholder="Search materials..." class="text-sm px-3 py-1 border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500" value="<?= $search ?? '' ?>">
                    <button type="submit" class="ml-2 text-blue-600 hover:text-blue-800">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min. Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($materials)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            <?php if (!empty($search)): ?>
                                No materials found matching your search criteria
                            <?php else: ?>
                                No materials in this warehouse yet
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($materials as $material): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= esc($material['name']) ?></div>
                                <div class="text-xs text-gray-500"><?= esc($material['item_code'] ?? $material['sku'] ?? '') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm"><?= esc($material['category_name'] ?? 'Uncategorized') ?></span>
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
                                MWK <?= number_format($material['current_quantity'] * $material['unit_cost'], 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= !empty($material['last_updated']) ? date('M d, Y', strtotime($material['last_updated'])) : 'Never' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <button type="button" onclick="openUpdateStockModal(<?= $material['material_id'] ?>, '<?= esc($material['name']) ?>', <?= $material['current_quantity'] ?>)" class="text-blue-600 hover:text-blue-900 mx-1">
                                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                </button>
                                <a href="<?= base_url('admin/materials/view/' . $material['material_id']) ?>" class="text-indigo-600 hover:text-indigo-900 mx-1">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="<?= base_url('admin/warehouses/stock-history/' . $warehouse['id'] . '/' . $material['material_id']) ?>" class="text-gray-600 hover:text-gray-900 mx-1">
                                    <i data-lucide="list" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if (!empty($pager)): ?>
        <div class="px-6 py-3 border-t border-gray-200">
            <?= $pager->links() ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Stock Modal -->
<div id="addStockModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Add Material Stock</h3>
            <button type="button" onclick="closeAddStockModal()" class="text-gray-400 hover:text-gray-500">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form action="<?= base_url('admin/warehouses/add-stock/' . $warehouse['id']) ?>" method="POST">
            <?= csrf_field() ?>
            <div class="p-6 space-y-4">
                <div>
                    <label for="material_id" class="block text-sm font-medium text-gray-700 mb-1">Material <span class="text-red-500">*</span></label>
                    <select name="material_id" id="material_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select Material</option>
                        <?php 
                        // Get existing materials in this warehouse for comparison
                        $existingMaterials = [];
                        foreach ($materials as $material) {
                            $existingMaterials[] = $material['material_id'];
                        }
                        
                        foreach ($allMaterials as $material): ?>
                        <option value="<?= $material['id'] ?>" <?= in_array($material['id'], $existingMaterials) ? 'data-existing="true"' : '' ?>>
                            <?= esc($material['name']) ?> (<?= esc($material['item_code']) ?>) - <?= ucfirst($material['unit']) ?>
                            <?= in_array($material['id'], $existingMaterials) ? ' - Already in warehouse' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="mt-1 text-xs text-gray-500" id="material_status"></p>
                </div>
                
                <div id="existing_material_fields" class="hidden">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-blue-800 mb-2">This material already exists in this warehouse. Use the Update Stock button in the inventory table to modify quantities.</p>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-gray-600">Current Stock:</span>
                                <span id="existing_current_stock" class="ml-2 font-medium"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Min Level:</span>
                                <span id="existing_min_level" class="ml-2 font-medium"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="new_material_fields">
                    <div>
                        <label for="current_quantity" class="block text-sm font-medium text-gray-700 mb-1">Initial Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="current_quantity" id="current_quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" step="0.01" required>
                    </div>
                    
                    <div class="mt-4">
                        <label for="minimum_quantity" class="block text-sm font-medium text-gray-700 mb-1">Minimum Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="minimum_quantity" id="minimum_quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" step="0.01" required>
                    </div>
                    
                    <div class="mt-4">
                        <label for="shelf_location" class="block text-sm font-medium text-gray-700 mb-1">Shelf Location</label>
                        <input type="text" name="shelf_location" id="shelf_location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button type="button" onclick="closeAddStockModal()" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors mr-2">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Add Stock
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Update Stock Modal -->
<div id="updateStockModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Update Material Stock</h3>
            <button type="button" onclick="closeUpdateStockModal()" class="text-gray-400 hover:text-gray-500">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="updateStockForm" action="" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="material_id" id="update_material_id">
            <div class="p-6 space-y-4">
                <div>
                    <p class="block text-sm font-medium text-gray-700 mb-1">Material</p>
                    <p id="update_material_name" class="text-gray-900 text-lg font-medium"></p>
                    <p class="text-sm text-gray-500">Current Stock: <span id="update_current_stock"></span></p>
                </div>
                
                <div>
                    <label for="movement_type_update" class="block text-sm font-medium text-gray-700 mb-1">Movement Type <span class="text-red-500">*</span></label>
                    <select name="movement_type" id="movement_type_update" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
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
                    <label for="quantity_update" class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="quantity_update" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0.01" step="0.01" required>
                </div>
                
                <div>
                    <label for="reference_no_update" class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                    <input type="text" name="reference_no" id="reference_no_update" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="notes_update" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" id="notes_update" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button type="button" onclick="closeUpdateStockModal()" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors mr-2">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Update Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Handle material selection in add stock modal
    const materialSelect = document.getElementById('material_id');
    const existingFields = document.getElementById('existing_material_fields');
    const newFields = document.getElementById('new_material_fields');
    const materialStatus = document.getElementById('material_status');
    const existingCurrentStock = document.getElementById('existing_current_stock');
    const existingMinLevel = document.getElementById('existing_min_level');
    
    // Store material data for quick lookup
    const materialData = {};
    <?php foreach ($materials as $material): ?>
    materialData[<?= $material['material_id'] ?>] = {
        current_quantity: <?= $material['current_quantity'] ?>,
        minimum_quantity: <?= $material['minimum_quantity'] ?>
    };
    <?php endforeach; ?>
    
    materialSelect.addEventListener('change', function() {
        const selectedValue = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const isExisting = selectedOption && selectedOption.getAttribute('data-existing') === 'true';
        
        if (selectedValue === '') {
            existingFields.classList.add('hidden');
            newFields.classList.remove('hidden');
            materialStatus.textContent = '';
        } else if (isExisting) {
            existingFields.classList.remove('hidden');
            newFields.classList.add('hidden');
            materialStatus.textContent = 'This material already exists in this warehouse. Use the Update Stock button in the inventory table to modify quantities.';
            materialStatus.className = 'mt-1 text-xs text-blue-600';
            
            // Show existing material details
            if (materialData[selectedValue]) {
                existingCurrentStock.textContent = materialData[selectedValue].current_quantity;
                existingMinLevel.textContent = materialData[selectedValue].minimum_quantity;
            }
        } else {
            existingFields.classList.add('hidden');
            newFields.classList.remove('hidden');
            materialStatus.textContent = 'This is a new material for this warehouse.';
            materialStatus.className = 'mt-1 text-xs text-green-600';
        }
    });
});

function openAddStockModal() {
    document.getElementById('addStockModal').classList.remove('hidden');
}

function closeAddStockModal() {
    document.getElementById('addStockModal').classList.add('hidden');
}

function openUpdateStockModal(materialId, materialName, currentStock) {
    document.getElementById('update_material_id').value = materialId;
    document.getElementById('update_material_name').textContent = materialName;
    document.getElementById('update_current_stock').textContent = currentStock;
    
    // Set form action
    document.getElementById('updateStockForm').action = '<?= base_url('admin/warehouses/update-stock/' . $warehouse['id']) ?>';
    
    document.getElementById('updateStockModal').classList.remove('hidden');
}

function closeUpdateStockModal() {
    document.getElementById('updateStockModal').classList.add('hidden');
}
</script>
<?= $this->endSection() ?>
