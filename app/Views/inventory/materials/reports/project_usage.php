<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Project Material Usage Report<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Project Material Usage Report</h1>
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
                            <input type="hidden" name="report_type" value="project_usage">
                            <input type="hidden" name="start_date" value="<?= isset($startDate) ? $startDate : '' ?>">
                            <input type="hidden" name="end_date" value="<?= isset($endDate) ? $endDate : '' ?>">
                            <input type="hidden" name="project_id" value="<?= isset($projectId) ? $projectId : '' ?>">
                            <input type="hidden" name="category_id" value="<?= isset($categoryId) ? $categoryId : '' ?>">
                            <input type="hidden" name="format" value="pdf">
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="file-text" class="w-4 h-4 inline-block mr-2"></i> Export as PDF
                            </button>
                        </form>
                        
                        <form action="<?= base_url('admin/materials/generate-report') ?>" method="post" id="export-excel-form">
                            <input type="hidden" name="report_type" value="project_usage">
                            <input type="hidden" name="start_date" value="<?= isset($startDate) ? $startDate : '' ?>">
                            <input type="hidden" name="end_date" value="<?= isset($endDate) ? $endDate : '' ?>">
                            <input type="hidden" name="project_id" value="<?= isset($projectId) ? $projectId : '' ?>">
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
            <?php if (!empty($project) && isset($project['name'])): ?>
            <div class="mr-6">
                <span class="text-sm text-gray-500">Project:</span>
                <p class="font-medium"><?= esc($project['name']) ?> (<?= esc($project['project_code']) ?>)</p>
            </div>
            <?php endif; ?>
            
            <div class="mr-6">
                <span class="text-sm text-gray-500">Date Range:</span>
                <p class="font-medium"><?= isset($startDate) && $startDate ? date('M j, Y', strtotime($startDate)) : 'Start of project' ?> to <?= isset($endDate) && $endDate ? date('M j, Y', strtotime($endDate)) : 'Present' ?></p>
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
    
    <!-- Material Usage Summary -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Material Usage Summary</h2>
        
        <?php if (empty($report)): ?>
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <i data-lucide="info" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">No Material Usage Found</h3>
                <p class="text-gray-500 mt-1">There are no material usage records matching your criteria for this project.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Materials Used -->
                <div class="bg-white p-5 rounded-lg border shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-100 rounded-full mr-4">
                            <i data-lucide="package" class="w-6 h-6 text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Materials Used</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?= count(array_unique(array_column($report, 'material_id'))) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Total Material Cost -->
                <div class="bg-white p-5 rounded-lg border shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full mr-4">
                            <i data-lucide="wallet" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Material Cost</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php
                                    $totalCost = array_reduce($report, function($sum, $item) {
                                        return $sum + ($item['quantity'] * $item['unit_cost']);
                                    }, 0);
                                    echo '₱' . number_format($totalCost, 2);
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Total Quantity Units -->
                <div class="bg-white p-5 rounded-lg border shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-amber-100 rounded-full mr-4">
                            <i data-lucide="scale" class="w-6 h-6 text-amber-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Most Used Unit Type</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php
                                    $units = array_count_values(array_column($report, 'unit'));
                                    echo !empty($units) ? array_keys($units)[array_search(max($units), $units)] : 'N/A';
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Material Usage Table -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Material Usage Details</h3>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Used</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Per Unit</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage Distribution</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                    // Reorganize data by material
                                    $materialUsage = [];
                                    $totalUsageCost = 0;
                                    
                                    foreach ($report as $usage) {
                                        $materialId = $usage['material_id'];
                                        $cost = $usage['quantity'] * $usage['unit_cost'];
                                        $totalUsageCost += $cost;
                                        
                                        if (!isset($materialUsage[$materialId])) {
                                            $materialUsage[$materialId] = [
                                                'name' => $usage['material_name'],
                                                'sku' => $usage['item_code'],
                                                'category' => $usage['category_name'] ?? 'Uncategorized',
                                                'unit' => $usage['unit'],
                                                'quantity' => $usage['quantity'],
                                                'cost_per_unit' => $usage['unit_cost'],
                                                'total_cost' => $cost
                                            ];
                                        } else {
                                            $materialUsage[$materialId]['quantity'] += $usage['quantity'];
                                            $materialUsage[$materialId]['total_cost'] += $cost;
                                        }
                                    }
                                    
                                    // Sort by total cost - highest first
                                    usort($materialUsage, function($a, $b) {
                                        return $b['total_cost'] - $a['total_cost'];
                                    });
                                ?>
                                
                                <?php foreach ($materialUsage as $usage): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= esc($usage['name']) ?></div>
                                        <div class="text-xs text-gray-500"><?= esc($usage['sku']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= esc($usage['category']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= number_format($usage['quantity'], 2) ?> <?= esc($usage['unit']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₱<?= number_format($usage['cost_per_unit'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                        ₱<?= number_format($usage['total_cost'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php $percentage = ($totalUsageCost > 0) ? ($usage['total_cost'] / $totalUsageCost) * 100 : 0; ?>
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
            
            <!-- Usage Timeline Chart -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Usage Timeline</h3>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <canvas id="usageTimelineChart" height="100"></canvas>
                </div>
            </div>
            
            <!-- Top 5 Materials by Cost -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Top Materials by Cost</h3>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <canvas id="topMaterialsChart" height="120"></canvas>
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
    
    // Timeline chart data preparation
    const timelineData = prepareTimelineData();
    createTimelineChart(timelineData);
    
    // Top materials chart
    const topMaterials = <?= json_encode(array_slice($materialUsage, 0, 5)) ?>;
    createTopMaterialsChart(topMaterials);
    
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
    
    // Helper function to prepare timeline data
    function prepareTimelineData() {
        // This would be different based on your actual data structure
        // Here's a simplified version that groups usage by month
        const rawData = <?= json_encode($report) ?>;
        const timeData = {};
        
        rawData.forEach(usage => {
            const date = new Date(usage.created_at);
            const monthYear = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
            
            if (!timeData[monthYear]) {
                timeData[monthYear] = {
                    cost: 0,
                    quantity: 0
                };
            }
            
            timeData[monthYear].cost += usage.quantity * usage.unit_cost;
            timeData[monthYear].quantity += parseFloat(usage.quantity);
        });
        
        // Sort by date
        const sortedLabels = Object.keys(timeData).sort();
        const costData = sortedLabels.map(date => timeData[date].cost);
        const quantityData = sortedLabels.map(date => timeData[date].quantity);
        
        // Format labels for display (e.g., "Jan 2023")
        const formattedLabels = sortedLabels.map(dateStr => {
            const [year, month] = dateStr.split('-');
            const date = new Date(year, month - 1);
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        });
        
        return {
            labels: formattedLabels,
            cost: costData,
            quantity: quantityData
        };
    }
    
    // Create timeline chart
    function createTimelineChart(data) {
        const ctx = document.getElementById('usageTimelineChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Total Cost (₱)',
                        data: data.cost,
                        borderColor: '#4F46E5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        yAxisID: 'y',
                        fill: true,
                        tension: 0.1
                    },
                    {
                        label: 'Quantity Used',
                        data: data.quantity,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.0)',
                        borderDash: [5, 5],
                        yAxisID: 'y1',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Cost (₱)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: 'Quantity'
                        }
                    }
                }
            }
        });
    }
    
    // Create top materials chart
    function createTopMaterialsChart(materials) {
        const ctx = document.getElementById('topMaterialsChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: materials.map(m => m.name),
                datasets: [{
                    label: 'Cost (₱)',
                    data: materials.map(m => m.total_cost),
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
                                return `₱${context.parsed.x.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value;
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
