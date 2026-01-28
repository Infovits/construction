<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Supplier Analysis Report<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Supplier Analysis Report</h1>
            <p class="text-gray-600">Comprehensive analysis of supplier performance and material relationships</p>
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
                            <input type="hidden" name="report_type" value="supplier">
                            <input type="hidden" name="start_date" value="<?= $startDate ?? '' ?>">
                            <input type="hidden" name="end_date" value="<?= $endDate ?? '' ?>">
                            <input type="hidden" name="supplier_id" value="<?= $supplierId ?? '' ?>">
                            <input type="hidden" name="format" value="pdf">
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="file-text" class="w-4 h-4 inline-block mr-2"></i> Export as PDF
                            </button>
                        </form>
                        
                        <form action="<?= base_url('admin/materials/generate-report') ?>" method="post" id="export-excel-form">
                            <input type="hidden" name="report_type" value="supplier">
                            <input type="hidden" name="start_date" value="<?= $startDate ?? '' ?>">
                            <input type="hidden" name="end_date" value="<?= $endDate ?? '' ?>">
                            <input type="hidden" name="supplier_id" value="<?= $supplierId ?? '' ?>">
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
                <p class="font-medium"><?= isset($startDate) && $startDate ? date('M j, Y', strtotime($startDate)) : 'All time' ?> to <?= isset($endDate) && $endDate ? date('M j, Y', strtotime($endDate)) : 'Present' ?></p>
            </div>
            
            <?php if (!empty($supplierId) && isset($suppliers[$supplierId])): ?>
            <div class="mr-6">
                <span class="text-sm text-gray-500">Supplier:</span>
                <p class="font-medium"><?= esc($suppliers[$supplierId]['name']) ?></p>
            </div>
            <?php else: ?>
            <div class="mr-6">
                <span class="text-sm text-gray-500">Supplier:</span>
                <p class="font-medium">All Suppliers</p>
            </div>
            <?php endif; ?>
            
            <div class="mr-6">
                <span class="text-sm text-gray-500">Total Suppliers:</span>
                <p class="font-medium"><?= count($suppliers) ?></p>
            </div>
        </div>
    </div>
    
    <!-- Supplier Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-full mr-3">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Suppliers</p>
                    <p class="font-semibold text-xl text-gray-900"><?= count($suppliers) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg border border-green-200">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-full mr-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Active Suppliers</p>
                    <p class="font-semibold text-xl text-gray-900"><?= count(array_filter($suppliers, function($s) { return $s['status'] === 'active'; })) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg border border-purple-200">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-full mr-3">
                    <i data-lucide="package" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Materials</p>
                    <p class="font-semibold text-xl text-gray-900"><?= array_sum(array_column($suppliers, 'material_count')) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg border border-yellow-200">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-full mr-3">
                    <i data-lucide="star" class="w-5 h-5 text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Avg. Rating</p>
                    <p class="font-semibold text-xl text-gray-900"><?= number_format(array_sum(array_column($suppliers, 'rating')) / count($suppliers), 1) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Distribution Chart -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Supplier Status Distribution</h3>
        <div class="chart-container" style="height: 300px;">
            <canvas id="supplierChart"></canvas>
        </div>
    </div>

    <!-- Supplier Details Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier Code</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materials</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Order</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($supplier['supplier_code']) ?></td>
                        <td class="px-6 py-4 text-sm">
                            <div class="font-medium text-gray-900"><?= esc($supplier['name']) ?></div>
                            <div class="text-xs text-gray-500">
                                <?= esc($supplier['supplier_type']) ?> • <?= esc($supplier['city']) ?>, <?= esc($supplier['country']) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($supplier['contact_person']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($supplier['email']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($supplier['phone']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $statusClass = 'bg-gray-100 text-gray-800';
                            if ($supplier['status'] === 'active') {
                                $statusClass = 'bg-green-100 text-green-800';
                            } elseif ($supplier['status'] === 'inactive') {
                                $statusClass = 'bg-red-100 text-red-800';
                            }
                            ?>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>">
                                <?= ucfirst($supplier['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                <?= $supplier['material_count'] ?? 0 ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <?= $supplier['rating'] ?? 'N/A' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= $supplier['last_order_date'] ? date('M j, Y', strtotime($supplier['last_order_date'])) : 'Never' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="<?= base_url('suppliers/view/' . $supplier['id']) ?>" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="<?= base_url('suppliers/edit/' . $supplier['id']) ?>" 
                                   class="text-yellow-600 hover:text-yellow-900">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Material-Supplier Relationships -->
    <?php if (!empty($supplierMaterials)): ?>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Material-Supplier Relationships</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Order Qty</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($supplierMaterials as $relationship): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900"><?= esc($relationship['name']) ?></div>
                            <div class="text-xs text-gray-500"><?= esc($relationship['item_code']) ?> • <?= esc($relationship['unit']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($relationship['supplier_name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= $relationship['unit_price'] ? 'MWK ' . number_format($relationship['unit_price'], 2) : 'N/A' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= $relationship['min_order_qty'] ? $relationship['min_order_qty'] . ' ' . esc($relationship['unit']) : 'N/A' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= $relationship['lead_time'] ? $relationship['lead_time'] . ' days' : 'N/A' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($relationship['notes']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Supplier Performance Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Suppliers by Material Count</h3>
            <div class="space-y-3">
                <?php 
                // Sort suppliers by material_count in descending order
                $sortedSuppliers = $suppliers;
                usort($sortedSuppliers, function($a, $b) {
                    return ($b['material_count'] ?? 0) - ($a['material_count'] ?? 0);
                });
                $topSuppliers = array_slice($sortedSuppliers, 0, 5);
                foreach ($topSuppliers as $supplier): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <div class="font-medium text-gray-900"><?= esc($supplier['name']) ?></div>
                        <div class="text-sm text-gray-500"><?= esc($supplier['supplier_code']) ?></div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-semibold text-blue-600"><?= $supplier['material_count'] ?? 0 ?></div>
                        <div class="text-xs text-gray-500">Materials</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Rated Suppliers</h3>
            <div class="space-y-3">
                <?php 
                // Sort suppliers by rating in descending order
                $sortedSuppliersByRating = $suppliers;
                usort($sortedSuppliersByRating, function($a, $b) {
                    return ($b['rating'] ?? 0) - ($a['rating'] ?? 0);
                });
                $topRated = array_slice($sortedSuppliersByRating, 0, 5);
                foreach ($topRated as $supplier): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <div class="font-medium text-gray-900"><?= esc($supplier['name']) ?></div>
                        <div class="text-sm text-gray-500"><?= esc($supplier['supplier_type']) ?></div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-semibold text-yellow-600"><?= $supplier['rating'] ?? 'N/A' ?></div>
                        <div class="text-xs text-gray-500">Rating</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Report Footer -->
    <div class="bg-gray-50 rounded-lg p-4 text-center text-gray-500">
        <p>Report generated on <?= date('M j, Y \a\t h:i A') ?> • Data source: Construction Management System</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Supplier Status Distribution Chart
    const ctx = document.getElementById('supplierChart').getContext('2d');
    const supplierData = <?= json_encode([
        'active' => count(array_filter($suppliers, function($s) { return $s['status'] === 'active'; })),
        'inactive' => count(array_filter($suppliers, function($s) { return $s['status'] === 'inactive'; })),
        'pending' => count(array_filter($suppliers, function($s) { return $s['status'] === 'pending'; }))
    ]) ?>;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Inactive', 'Pending'],
            datasets: [{
                data: [supplierData.active, supplierData.inactive, supplierData.pending],
                backgroundColor: ['#22c55e', '#ef4444', '#eab308'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return `${context.label}: ${context.raw} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

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
