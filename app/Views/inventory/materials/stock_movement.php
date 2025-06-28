<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= esc($title) ?></h1>
            <nav class="flex mt-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="<?= base_url('admin/dashboard') ?>" class="text-gray-700 hover:text-blue-600">Dashboard</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                            <a href="<?= base_url('admin/materials') ?>" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">Materials</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                            <span class="ml-1 text-gray-500 md:ml-2">Stock Movement</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/materials') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back to Materials
            </a>
        </div>
    </div>

    <!-- Material Info Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2"><?= esc($material['name']) ?></h2>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-600">Item Code: <span class="font-medium text-gray-900"><?= esc($material['item_code']) ?></span></p>
                        <p class="text-sm text-gray-600">Current Stock: <span class="font-medium text-gray-900"><?= number_format($material['current_stock'] ?? 0, 2) ?> <?= esc($material['unit']) ?></span></p>
                        <p class="text-sm text-gray-600">Unit Cost: <span class="font-medium text-gray-900">$<?= number_format($material['unit_cost'] ?? 0, 2) ?></span></p>
                    </div>
                </div>
                <div>
                    <button type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors" onclick="openRecordMovementModal()">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>Record Movement
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Movement History -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Stock Movement History</h3>
        </div>
        <div class="p-6">
            <?php if (empty($movements)): ?>
                <div class="text-center py-12">
                    <i data-lucide="package" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-500 text-lg">No stock movements recorded for this material yet.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performed By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($movements as $movement): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?= date('M d, Y', strtotime($movement['created_at'])) ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?= date('H:i', strtotime($movement['created_at'])) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><?= esc($movement['reference_number']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $typeClass = '';
                                        switch($movement['movement_type']) {
                                            case 'in':
                                            case 'purchase':
                                                $typeClass = 'bg-green-100 text-green-800';
                                                break;
                                            case 'out':
                                            case 'project_usage':
                                                $typeClass = 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'transfer':
                                                $typeClass = 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'adjustment':
                                                $typeClass = 'bg-purple-100 text-purple-800';
                                                break;
                                            default:
                                                $typeClass = 'bg-gray-100 text-gray-800';
                                        }
                                        ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $typeClass ?>"><?= ucfirst(str_replace('_', ' ', $movement['movement_type'])) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= number_format($movement['quantity'], 2) ?> <?= esc($movement['unit']) ?></div>
                                        <?php if ($movement['unit_cost'] > 0): ?>
                                            <div class="text-xs text-gray-500">@ $<?= number_format($movement['unit_cost'], 2) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= $movement['source_warehouse_name'] ? esc($movement['source_warehouse_name']) : '<span class="text-gray-400">-</span>' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= $movement['destination_warehouse_name'] ? esc($movement['destination_warehouse_name']) : '<span class="text-gray-400">-</span>' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= $movement['project_name'] ? esc($movement['project_name']) : '<span class="text-gray-400">-</span>' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= $movement['task_name'] ? esc($movement['task_name']) : '<span class="text-gray-400">-</span>' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if ($movement['performer_first_name']): ?>
                                            <?= esc($movement['performer_first_name'] . ' ' . $movement['performer_last_name']) ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">System</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?= $movement['notes'] ? esc($movement['notes']) : '<span class="text-gray-400">-</span>' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Record Movement Modal -->
<div id="recordMovementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Record Stock Movement</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeRecordMovementModal()">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form action="<?= base_url('admin/materials/record-stock-movement/' . $material['id']) ?>" method="POST">
            <?= csrf_field() ?>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="movement_type" class="block text-sm font-medium text-gray-700 mb-2">Movement Type <span class="text-red-500">*</span></label>
                        <select name="movement_type" id="movement_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select Type</option>
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                            <option value="transfer">Transfer</option>
                            <option value="adjustment">Adjustment</option>
                            <option value="project_usage">Project Usage</option>
                        </select>
                    </div>
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" step="0.01" min="0.01" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">Unit Cost</label>
                        <input type="number" name="unit_cost" id="unit_cost" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" step="0.01" min="0" value="<?= $material['unit_cost'] ?? 0 ?>">
                    </div>
                    <div>
                        <label for="source_warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">Source Warehouse</label>
                        <select name="source_warehouse_id" id="source_warehouse_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Warehouse</option>
                            <?php foreach ($warehouses as $warehouse): ?>
                                <option value="<?= $warehouse['id'] ?>"><?= esc($warehouse['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4" id="destination_warehouse_row" style="display: none;">
                    <div>
                        <label for="destination_warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">Destination Warehouse</label>
                        <select name="destination_warehouse_id" id="destination_warehouse_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Warehouse</option>
                            <?php foreach ($warehouses as $warehouse): ?>
                                <option value="<?= $warehouse['id'] ?>"><?= esc($warehouse['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end px-6 py-3 bg-gray-50 border-t border-gray-200 space-x-3">
                <button type="button" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded-lg transition-colors" onclick="closeRecordMovementModal()">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">Record Movement</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();

    // Show/hide destination warehouse based on movement type
    const movementTypeSelect = document.getElementById('movement_type');
    const destinationWarehouseRow = document.getElementById('destination_warehouse_row');

    movementTypeSelect.addEventListener('change', function() {
        if (this.value === 'transfer') {
            destinationWarehouseRow.style.display = 'block';
        } else {
            destinationWarehouseRow.style.display = 'none';
        }
    });
});

// Modal functions
function openRecordMovementModal() {
    document.getElementById('recordMovementModal').classList.remove('hidden');
}

function closeRecordMovementModal() {
    document.getElementById('recordMovementModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('recordMovementModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRecordMovementModal();
    }
});
</script>

<?= $this->endSection() ?>
