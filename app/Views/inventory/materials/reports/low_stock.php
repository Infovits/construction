<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Low Stock Alert Report<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Low Stock Alert Report</h1>
            <p class="text-gray-600">
                <?= isset($startDate) && $startDate ? 'From ' . date('M j, Y', strtotime($startDate)) : '' ?> 
                <?= isset($endDate) && $endDate ? 'to ' . date('M j, Y', strtotime($endDate)) : '' ?>
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
                            <input type="hidden" name="report_type" value="low_stock">
                            <input type="hidden" name="start_date" value="<?= isset($startDate) ? $startDate : '' ?>">
                            <input type="hidden" name="end_date" value="<?= isset($endDate) ? $endDate : '' ?>">
                            <input type="hidden" name="warehouse_id" value="<?= isset($warehouseId) ? $warehouseId : '' ?>">
                            <input type="hidden" name="category_id" value="<?= isset($categoryId) ? $categoryId : '' ?>">
                            <input type="hidden" name="format" value="pdf">
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="file-text" class="w-4 h-4 inline-block mr-2"></i> Export as PDF
                            </button>
                        </form>
                        
                        <form action="<?= base_url('admin/materials/generate-report') ?>" method="post" id="export-excel-form">
                            <input type="hidden" name="report_type" value="low_stock">
                            <input type="hidden" name="start_date" value="<?= isset($startDate) ? $startDate : '' ?>">
                            <input type="hidden" name="end_date" value="<?= isset($endDate) ? $endDate : '' ?>">
                            <input type="hidden" name="warehouse_id" value="<?= isset($warehouseId) ? $warehouseId : '' ?>">
                            <input type="hidden" name="category_id" value="<?= isset($categoryId) ? $categoryId : '' ?>">
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
            <?php if (!empty($warehouse) && isset($warehouse['name'])): ?>
            <div class="mr-6">
                <span class="text-sm text-gray-500">Warehouse:</span>
                <p class="font-medium"><?= esc($warehouse['name']) ?></p>
            </div>
            <?php endif; ?>
            
            <div class="mr-6">
                <span class="text-sm text-gray-500">Date Range:</span>
                <p class="font-medium"><?= isset($startDate) && $startDate ? date('M j, Y', strtotime($startDate)) : 'All time' ?> to <?= isset($endDate) && $endDate ? date('M j, Y', strtotime($endDate)) : 'Present' ?></p>
            </div>
            
            <?php if (!empty($category) && isset($category['name'])): ?>
            <div class="mr-6">
                <span class="text-sm text-gray-500">Material Category:</span>
                <p class="font-medium"><?= esc($category['name']) ?></p>
            </div>
            <?php elseif(empty($categoryId)): ?>
            <div class="mr-6">
                <span class="text-sm text-gray-500">Material Category:</span>
                <p class="font-medium">All Categories</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Low Stock Summary -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Low Stock Summary</h2>
        
        <?php if (empty($report)): ?>
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <i data-lucide="check-circle" class="w-12 h-12 text-green-500 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">No Low Stock Items Found</h3>
                <p class="text-gray-500 mt-1">All materials are adequately stocked according to your threshold settings.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Critical Items -->
                <div class="bg-white p-5 rounded-lg border shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-full mr-4">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Critical Items</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?= count(array_filter($report, function($item) { return $item['current_quantity'] <= 0; })) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Low Stock Items -->
                <div class="bg-white p-5 rounded-lg border shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-amber-100 rounded-full mr-4">
                            <i data-lucide="alert-circle" class="w-6 h-6 text-amber-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Low Stock Items</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?= count(array_filter($report, function($item) { return $item['current_quantity'] > 0 && $item['current_quantity'] <= $item['minimum_stock']; })) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Total Value at Risk -->
                <div class="bg-white p-5 rounded-lg border shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-full mr-4">
                            <i data-lucide="dollar-sign" class="w-6 h-6 text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Value at Risk</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php
                                    $totalValue = array_reduce($report, function($sum, $item) {
                                        return $sum + ($item['current_quantity'] * $item['unit_cost']);
                                    }, 0);
                                    echo '₱' . number_format($totalValue, 2);
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Low Stock Items Table -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Low Stock Items</h3>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Minimum Level</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reorder Level</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action Required</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($report as $item): ?>
                                <tr class="<?= $item['current_quantity'] <= 0 ? 'bg-red-50' : ($item['current_quantity'] <= $item['minimum_stock'] ? 'bg-amber-50' : '') ?>">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= esc($item['name']) ?></div>
                                        <div class="text-xs text-gray-500"><?= esc($item['item_code']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= esc($item['category_name'] ?? 'Uncategorized') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                        <?= number_format($item['current_quantity'], 2) ?> <?= esc($item['unit']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= number_format($item['minimum_stock'], 2) ?> <?= esc($item['unit']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= number_format($item['reorder_level'], 2) ?> <?= esc($item['unit']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                            $status = '';
                                            $statusClass = '';
                                            if ($item['current_quantity'] <= 0) {
                                                $status = 'CRITICAL';
                                                $statusClass = 'bg-red-100 text-red-800';
                                            } elseif ($item['current_quantity'] <= $item['minimum_stock']) {
                                                $status = 'LOW STOCK';
                                                $statusClass = 'bg-amber-100 text-amber-800';
                                            } else {
                                                $status = 'OK';
                                                $statusClass = 'bg-green-100 text-green-800';
                                            }
                                        ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>">
                                            <?= $status ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php 
                                            $action = '';
                                            if ($item['current_quantity'] <= 0) {
                                                $action = 'Immediate reorder required';
                                            } elseif ($item['current_quantity'] <= $item['minimum_stock']) {
                                                $action = 'Reorder recommended';
                                            } else {
                                                $action = 'Monitor stock levels';
                                            }
                                        ?>
                                        <span class="text-sm"><?= $action ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Stock Level Distribution Chart -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Stock Level Distribution</h3>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <canvas id="stockLevelChart" height="100"></canvas>
                </div>
            </div>
            
            <!-- Reorder Recommendations -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Reorder Recommendations</h3>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recommended Order</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimated Cost</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($report as $item): ?>
                                <?php 
                                    // Calculate recommended order quantity
                                    $recommendedOrder = max(0, $item['reorder_level'] - $item['current_quantity']);
                                    $estimatedCost = $recommendedOrder * $item['unit_cost'];
                                ?>
                                <?php if ($recommendedOrder > 0): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($item['name']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= number_format($item['current_quantity'], 2) ?> <?= esc($item['unit']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold"><?= number_format($recommendedOrder, 2) ?> <?= esc($item['unit']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">₱<?= number_format($estimatedCost, 2) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $item['current_quantity'] <= 0 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' ?>">
                                            <?= $item['current_quantity'] <= 0 ? 'CRITICAL' : 'HIGH' ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    <?php if (!empty($report)): ?>
    
    // Create stock level distribution chart
    createStockLevelChart();
    
    <?php endif; ?>
    
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
    
    // Create stock level distribution chart
    function createStockLevelChart() {
        const ctx = document.getElementById('stockLevelChart').getContext('2d');
        
        // Count items by status
        const criticalCount = <?= count(array_filter($report, function($item) { return $item['current_quantity'] <= 0; })) ?>;
        const lowCount = <?= count(array_filter($report, function($item) { return $item['current_quantity'] > 0 && $item['current_quantity'] <= $item['minimum_stock']; })) ?>;
        const okCount = <?= count(array_filter($report, function($item) { return $item['current_quantity'] > $item['minimum_stock']; })) ?>;
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Critical (Out of Stock)', 'Low Stock', 'Adequate Stock'],
                datasets: [{
                    data: [criticalCount, lowCount, okCount],
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(16, 185, 129, 0.8)'
                    ],
                    borderColor: [
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(16, 185, 129)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>