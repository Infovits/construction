<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Stock Movement History<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Stock Movement History</h1>
                <p class="text-gray-600"><?= esc($material['name']) ?> (<?= esc($material['sku']) ?>)</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?= base_url('admin/materials/view/' . $material['id']) ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Material
                </a>
                <a href="<?= base_url('admin/materials/stock/' . $material['id']) ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Update Stock
                </a>
            </div>
        </div>
    </div>
    
    <!-- Stock Movement Filters -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <form action="<?= base_url('admin/materials/stock-history/' . $material['id']) ?>" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-1/4">
                    <label for="movement_type" class="block text-sm font-medium text-gray-700 mb-1">Movement Type</label>
                    <select name="movement_type" id="movement_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="in" <?= isset($_GET['movement_type']) && $_GET['movement_type'] === 'in' ? 'selected' : '' ?>>Stock In (All)</option>
                        <option value="out" <?= isset($_GET['movement_type']) && $_GET['movement_type'] === 'out' ? 'selected' : '' ?>>Stock Out (All)</option>
                        <option value="in_purchase" <?= isset($_GET['movement_type']) && $_GET['movement_type'] === 'in_purchase' ? 'selected' : '' ?>>Stock In - Purchase</option>
                        <option value="in_return" <?= isset($_GET['movement_type']) && $_GET['movement_type'] === 'in_return' ? 'selected' : '' ?>>Stock In - Return</option>
                        <option value="out_project" <?= isset($_GET['movement_type']) && $_GET['movement_type'] === 'out_project' ? 'selected' : '' ?>>Stock Out - Project</option>
                    </select>
                </div>
                <div class="w-full md:w-1/4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01') ?>">
                </div>
                <div class="w-full md:w-1/4">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d') ?>">
                </div>
                <div class="w-full md:w-1/4 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex-grow">
                        <i data-lucide="filter" class="w-4 h-4 inline mr-1"></i> Filter
                    </button>
                    <a href="<?= base_url('admin/materials/stock-history/' . $material['id']) ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i data-lucide="x" class="w-4 h-4 inline"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Material Summary Card -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row justify-between gap-6">
                <!-- Material Info -->
                <div class="flex items-center sm:w-1/3">
                    <div class="p-3 bg-blue-100 rounded-full mr-4">
                        <i data-lucide="package" class="w-8 h-8 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900"><?= esc($material['name']) ?></h3>
                        <p class="text-gray-500"><?= esc($material['sku']) ?> • <?= ucfirst($material['unit']) ?></p>
                    </div>
                </div>
                
                <!-- Stock Summary -->
                <div class="sm:w-2/3">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500 mb-1">Current Stock</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($material['current_stock']) ?> <span class="text-sm"><?= $material['unit'] ?></span></p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <p class="text-sm text-gray-500 mb-1">Total Stock In</p>
                            <p class="text-2xl font-bold text-green-700"><?= number_format($totalStockIn) ?> <span class="text-sm"><?= $material['unit'] ?></span></p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-lg">
                            <p class="text-sm text-gray-500 mb-1">Total Stock Out</p>
                            <p class="text-2xl font-bold text-red-700"><?= number_format($totalStockOut) ?> <span class="text-sm"><?= $material['unit'] ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Movement History Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Movement History</h3>
            
            <div class="flex items-center gap-2">
                <button id="exportPdfBtn" class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                    <i data-lucide="file-text" class="w-4 h-4 mr-1"></i> PDF
                </button>
                <button id="exportExcelBtn" class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-1"></i> Excel
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Movement Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Running Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($stockMovements)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No stock movements found matching your criteria</td>
                    </tr>
                    <?php else: ?>
                        <?php 
                        $runningBalance = $material['current_stock'];
                        // We need to reverse the array to show running balance correctly
                        $stockMovements = array_reverse($stockMovements);
                        ?>
                        <?php foreach ($stockMovements as $movement): ?>
                            <?php 
                            $movementType = explode('_', $movement['movement_type']);
                            $isStockIn = $movementType[0] === 'in';
                            $badgeColor = $isStockIn ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            
                            // Adjust running balance for display (going backwards in time)
                            if ($isStockIn) {
                                $runningBalance -= $movement['quantity'];
                            } else {
                                $runningBalance += $movement['quantity'];
                            }
                            
                            // Format movement type for display
                            $direction = $isStockIn ? 'Stock In' : 'Stock Out';
                            $type = ucfirst($movementType[1] ?? '');
                            ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('M d, Y H:i', strtotime($movement['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badgeColor ?>">
                                    <?= $direction ?>: <?= $type ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="font-medium <?= $isStockIn ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= $isStockIn ? '+' : '-' ?><?= number_format($movement['quantity'], 2) ?> <?= $material['unit'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?= number_format($runningBalance, 2) ?> <?= $material['unit'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $movement['reference_no'] ? esc($movement['reference_no']) : 'N/A' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if ($movement['project_id']): ?>
                                    <span class="text-blue-600">Project: <?= esc($movement['project_name'] ?? 'Unknown') ?></span>
                                <?php elseif ($movement['warehouse_id']): ?>
                                    <span>Warehouse: <?= esc($movement['warehouse_name'] ?? 'Unknown') ?></span>
                                <?php elseif ($movement['supplier_id']): ?>
                                    <span>Supplier: <?= esc($movement['supplier_name'] ?? 'Unknown') ?></span>
                                <?php else: ?>
                                    <?= $movement['notes'] ? esc($movement['notes']) : '-' ?>
                                <?php endif; ?>
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
        
        <!-- Pagination -->
        <?php if (!empty($pager)): ?>
        <div class="px-6 py-4 border-t border-gray-200">
            <?= $pager->links() ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Export functions
    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        const currentUrl = window.location.href;
        const exportUrl = currentUrl + (currentUrl.includes('?') ? '&' : '?') + 'export=pdf';
        window.open(exportUrl, '_blank');
    });
    
    document.getElementById('exportExcelBtn').addEventListener('click', function() {
        const currentUrl = window.location.href;
        const exportUrl = currentUrl + (currentUrl.includes('?') ? '&' : '?') + 'export=excel';
        window.open(exportUrl, '_blank');
    });
});
</script>
<?= $this->endSection() ?>
