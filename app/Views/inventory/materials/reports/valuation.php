<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Stock Valuation Report<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Stock Valuation Report</h1>
            <p class="text-gray-600">Current inventory valuation across all warehouses</p>
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
                            <input type="hidden" name="report_type" value="valuation">
                            <input type="hidden" name="format" value="pdf">
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="file-text" class="w-4 h-4 inline-block mr-2"></i> Export as PDF
                            </button>
                        </form>
                        
                        <form action="<?= base_url('admin/materials/generate-report') ?>" method="post" id="export-excel-form">
                            <input type="hidden" name="report_type" value="valuation">
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
    
    <!-- Valuation Summary -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Inventory Valuation Summary</h2>
        
        <?php if (empty($report)): ?>
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <i data-lucide="info" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">No Inventory Found</h3>
                <p class="text-gray-500 mt-1">There is no inventory data available for valuation.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Items -->
                <div class="bg-white p-5 rounded-lg border shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-100 rounded-full mr-4">
                            <i data-lucide="package" class="w-6 h-6 text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Items</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?= count($report) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Total Quantity -->
                <div class="bg-white p-5 rounded-lg border shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full mr-4">
                            <i data-lucide="scale" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Quantity</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?= number_format(array_sum(array_column($report, 'total_quantity')), 2) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Total Value -->
                <div class="bg-white p-5 rounded-lg border shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-amber-100 rounded-full mr-4">
                            <i data-lucide="dollar-sign" class="w-6 h-6 text-amber-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Inventory Value</p>
                            <p class="text-2xl font-bold text-gray-900">
                                MWK <?= number_format(array_sum(array_column($report, 'total_value')), 2) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Average Unit Cost -->
                <div class="bg-white p-5 rounded-lg border shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full mr-4">
                            <i data-lucide="trending-up" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Average Unit Cost</p>
                            <p class="text-2xl font-bold text-gray-900">
                                MWK <?= number_format(array_sum(array_column($report, 'total_value')) / array_sum(array_column($report, 'total_quantity')), 2) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Valuation Breakdown by Warehouse -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Valuation by Warehouse</h3>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items Count</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Quantity</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                    $warehouseTotals = [];
                                    foreach ($report as $item) {
                                        $warehouseName = $item['warehouse_name'];
                                        if (!isset($warehouseTotals[$warehouseName])) {
                                            $warehouseTotals[$warehouseName] = [
                                                'name' => $warehouseName,
                                                'items' => 0,
                                                'quantity' => 0,
                                                'value' => 0
                                            ];
                                        }
                                        $warehouseTotals[$warehouseName]['items']++;
                                        $warehouseTotals[$warehouseName]['quantity'] += $item['total_quantity'];
                                        $warehouseTotals[$warehouseName]['value'] += $item['total_value'];
                                    }
                                    
                                    $totalValue = array_sum(array_column($warehouseTotals, 'value'));
                                ?>
                                
                                <?php foreach ($warehouseTotals as $warehouse): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?= esc($warehouse['name']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $warehouse['items'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= number_format($warehouse['quantity'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                        MWK <?= number_format($warehouse['value'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php $percentage = ($totalValue > 0) ? ($warehouse['value'] / $totalValue) * 100 : 0; ?>
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: <?= $percentage ?>%"></div>
                                            </div>
                                            <span class="text-xs"><?= number_format($percentage, 1) ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Material Valuation Details -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Material Valuation Details</h3>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                    // Sort by total value descending
                                    usort($report, function($a, $b) {
                                        return $b['total_value'] - $a['total_value'];
                                    });
                                ?>
                                
                                <?php foreach ($report as $item): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= esc($item['material_name']) ?></div>
                                        <div class="text-xs text-gray-500"><?= esc($item['item_code']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= esc($item['category_name'] ?? 'Uncategorized') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= esc($item['warehouse_name']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        MWK <?= number_format($item['unit_cost'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= number_format($item['total_quantity'], 2) ?> <?= esc($item['unit']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                        MWK <?= number_format($item['total_value'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                            $statusText = 'Normal';
                                            $statusIcon = 'check-circle';
                                            
                                            if ($item['total_quantity'] == 0) {
                                                $statusClass = 'bg-red-100 text-red-800';
                                                $statusText = 'Out of Stock';
                                                $statusIcon = 'alert-circle';
                                            } elseif ($item['total_quantity'] <= $item['minimum_quantity']) {
                                                $statusClass = 'bg-amber-100 text-amber-800';
                                                $statusText = 'Low Stock';
                                                $statusIcon = 'alert-triangle';
                                            }
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                            <i data-lucide="<?= $statusIcon ?>" class="w-3 h-3 mr-1"></i>
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Top 5 Most Valuable Materials -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Top 5 Most Valuable Materials</h3>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <canvas id="topMaterialsChart" height="120"></canvas>
                </div>
            </div>
            
            <!-- Valuation Distribution Chart -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Valuation Distribution by Category</h3>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <canvas id="categoryDistributionChart" height="120"></canvas>
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
    
    // Top materials chart
    const topMaterials = <?= json_encode(array_slice($report, 0, 5)) ?>;
    createTopMaterialsChart(topMaterials);
    
    // Category distribution chart
    createCategoryDistributionChart(<?= json_encode($report) ?>);
    
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
    
    // Create top materials chart
    function createTopMaterialsChart(materials) {
        const ctx = document.getElementById('topMaterialsChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: materials.map(m => m.material_name),
                datasets: [{
                    label: 'Value (MWK)',
                    data: materials.map(m => m.total_value),
                    backgroundColor: [
                        'rgba(79, 70, 229, 0.6)',
                        'rgba(59, 130, 246, 0.6)',
                        'rgba(16, 185, 129, 0.6)',
                        'rgba(245, 158, 11, 0.6)',
                        'rgba(239, 68, 68, 0.6)'
                    ],
                    borderColor: [
                        'rgb(79, 70, 229)',
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'MWK' + context.parsed.x.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'MWK' + value;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Create category distribution chart
    function createCategoryDistributionChart(data) {
        // Group by category
        const categoryTotals = {};
        data.forEach(item => {
            const category = item.category_name || 'Uncategorized';
            if (!categoryTotals[category]) {
                categoryTotals[category] = 0;
            }
            categoryTotals[category] += item.total_value;
        });
        
        const labels = Object.keys(categoryTotals);
        const values = Object.values(categoryTotals);
        
        const ctx = document.getElementById('categoryDistributionChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: [
                        'rgba(79, 70, 229, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(147, 51, 234, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(34, 197, 94, 0.8)'
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
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: MWK${context.parsed.toFixed(2)} (${percentage}%)`;
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