<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Stock Movement Report<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Stock Movement Report</h1>
            <p class="text-gray-600">
                <?= $startDate ? 'From ' . date('M j, Y', strtotime($startDate)) : '' ?> 
                <?= $endDate ? 'to ' . date('M j, Y', strtotime($endDate)) : '' ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <a href="<?= base_url('admin/materials/report') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Reports
            </a>
            <div class="dropdown relative">
                <button class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors" id="export-button">
                    <i data-lucide="download" class="w-4 h-4 mr-2"></i> Export
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-2"></i>
                </button>
                <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10" id="export-menu">
                    <div class="py-1">
                        <form action="<?= base_url('admin/materials/generate-report') ?>" method="post" id="export-pdf-form">
                            <input type="hidden" name="report_type" value="movement">
                            <input type="hidden" name="start_date" value="<?= $startDate ?>">
                            <input type="hidden" name="end_date" value="<?= $endDate ?>">
                            <input type="hidden" name="warehouse_id" value="<?= $warehouseId ?? '' ?>">
                            <input type="hidden" name="material_id" value="<?= $materialId ?? '' ?>">
                            <input type="hidden" name="format" value="pdf">
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="file-text" class="w-4 h-4 inline-block mr-2"></i> Export as PDF
                            </button>
                        </form>
                        
                        <form action="<?= base_url('admin/materials/generate-report') ?>" method="post" id="export-excel-form">
                            <input type="hidden" name="report_type" value="movement">
                            <input type="hidden" name="start_date" value="<?= $startDate ?>">
                            <input type="hidden" name="end_date" value="<?= $endDate ?>">
                            <input type="hidden" name="warehouse_id" value="<?= $warehouseId ?? '' ?>">
                            <input type="hidden" name="material_id" value="<?= $materialId ?? '' ?>">
                            <input type="hidden" name="format" value="excel">
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4 inline-block mr-2"></i> Export as Excel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Report filters summary -->
    <div class="bg-gray-50 rounded-lg border p-4 mb-6">
        <div class="flex flex-wrap gap-y-2">
            <div class="mr-6">
                <span class="text-sm text-gray-500">Date Range:</span>
                <p class="font-medium"><?= $startDate ? date('M j, Y', strtotime($startDate)) : 'All time' ?> to <?= $endDate ? date('M j, Y', strtotime($endDate)) : 'Present' ?></p>
            </div>
            
            <?php if (!empty($warehouse) && isset($warehouse['name'])): ?>
            <div class="mr-6">
                <span class="text-sm text-gray-500">Warehouse:</span>
                <p class="font-medium"><?= esc($warehouse['name']) ?></p>
            </div>
            <?php elseif(empty($warehouseId)): ?>
            <div class="mr-6">
                <span class="text-sm text-gray-500">Warehouse:</span>
                <p class="font-medium">All Warehouses</p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($material) && isset($material['name'])): ?>
            <div class="mr-6">
                <span class="text-sm text-gray-500">Material:</span>
                <p class="font-medium"><?= esc($material['name']) ?> (<?= esc($material['item_code']) ?>)</p>
            </div>
            <?php elseif(empty($materialId)): ?>
            <div class="mr-6">
                <span class="text-sm text-gray-500">Material:</span>
                <p class="font-medium">All Materials</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Stock Movement Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-fixed">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source/Destination</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($report)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No movement records found for the selected criteria</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($report as $movement): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('M j, Y H:i', strtotime($movement['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <?= esc($movement['material_name']) ?><br>
                                <span class="text-xs text-gray-500"><?= esc($movement['item_code']) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php 
                                $badgeClass = 'bg-gray-100 text-gray-800';
                                $icon = 'move';
                                
                                switch($movement['movement_type']) {
                                    case 'stock_in':
                                    case 'in_purchase':
                                    case 'in_return':
                                        $badgeClass = 'bg-green-100 text-green-800';
                                        $icon = 'arrow-down-right';
                                        break;
                                    case 'stock_out':
                                    case 'out_project':
                                        $badgeClass = 'bg-red-100 text-red-800';
                                        $icon = 'arrow-up-right';
                                        break;
                                    case 'stock_transfer':
                                        $badgeClass = 'bg-blue-100 text-blue-800';
                                        $icon = 'move-horizontal';
                                        break;
                                    case 'inventory_adjustment':
                                        $badgeClass = 'bg-amber-100 text-amber-800';
                                        $icon = 'clipboard-edit';
                                        break;
                                }
                                
                                // Convert movement type to human-readable format
                                $movementTypeText = ucwords(str_replace('_', ' ', $movement['movement_type']));
                                ?>
                                
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badgeClass ?>">
                                    <i data-lucide="<?= $icon ?>" class="w-3 h-3 mr-1"></i>
                                    <?= $movementTypeText ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?= number_format($movement['quantity'], 2) ?> <?= esc($movement['unit_of_measure'] ?? $movement['unit'] ?? '') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php if ($movement['movement_type'] == 'stock_transfer'): ?>
                                    <?= esc($movement['source_name'] ?? '') ?> &rarr; <?= esc($movement['destination_name'] ?? '') ?>
                                <?php elseif (in_array($movement['movement_type'], ['stock_in', 'in_purchase', 'in_return'])): ?>
                                    <?= esc($movement['destination_name'] ?? '') ?>
                                <?php else: ?>
                                    <?= esc($movement['source_name'] ?? '') ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $movement['project_name'] ?? 'N/A' ?>
                                <?php if (!empty($movement['project_code'])): ?>
                                    <span class="text-xs text-gray-400">(<?= esc($movement['project_code']) ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= esc($movement['performed_by_name'] ?? 'Unknown') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= esc($movement['notes'] ?? 'N/A') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= esc($movement['reference_number'] ?? '-') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Summary Statistics -->
    <?php if (!empty($report)): ?>
    <div class="mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Stock In -->
            <div class="bg-white p-4 rounded-lg border border-green-200">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-full mr-3">
                        <i data-lucide="arrow-down-right" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Stock In</p>
                        <p class="font-semibold text-xl text-gray-900">
                            <?php
                                $stockIn = array_reduce($report, function($sum, $item) {
                                    return $sum + (in_array($item['movement_type'], ['stock_in', 'in_purchase', 'in_return']) ? $item['quantity'] : 0);
                                }, 0);
                                echo number_format($stockIn, 2);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Stock Out -->
            <div class="bg-white p-4 rounded-lg border border-red-200">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-full mr-3">
                        <i data-lucide="arrow-up-right" class="w-5 h-5 text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Stock Out</p>
                        <p class="font-semibold text-xl text-gray-900">
                            <?php
                                $stockOut = array_reduce($report, function($sum, $item) {
                                    return $sum + (in_array($item['movement_type'], ['stock_out', 'out_project']) ? $item['quantity'] : 0);
                                }, 0);
                                echo number_format($stockOut, 2);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Total Transfers -->
            <div class="bg-white p-4 rounded-lg border border-blue-200">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-full mr-3">
                        <i data-lucide="move-horizontal" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Transfers</p>
                        <p class="font-semibold text-xl text-gray-900">
                            <?php
                                $transfers = array_reduce($report, function($sum, $item) {
                                    return $sum + ($item['movement_type'] == 'stock_transfer' ? 1 : 0);
                                }, 0);
                                echo number_format($transfers);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Total Movements -->
            <div class="bg-white p-4 rounded-lg border">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 rounded-full mr-3">
                        <i data-lucide="activity" class="w-5 h-5 text-gray-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Movements</p>
                        <p class="font-semibold text-xl text-gray-900">
                            <?= count($report) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Dropdown functionality
    const exportButton = document.getElementById('export-button');
    const exportMenu = document.getElementById('export-menu');
    
    if (exportButton && exportMenu) {
        exportButton.addEventListener('click', function(e) {
            e.preventDefault();
            exportMenu.classList.toggle('hidden');
        });
        
        // Close the dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!exportButton.contains(e.target)) {
                exportMenu.classList.add('hidden');
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
