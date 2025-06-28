<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Material Reports<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Material Reports</h1>
                <p class="text-gray-600">Generate reports on material usage, stock movements, and valuation</p>
            </div>
            <div>
                <a href="<?php echo base_url(); ?>/admin/materials" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Materials
                </a>
            </div>
        </div>
    </div>

    <!-- Report Options -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <!-- Stock Valuation Report Card -->
        <div class="bg-white rounded-lg shadow-sm border border-indigo-200 hover:border-indigo-300 transition-colors">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-indigo-100 rounded-full">
                        <i data-lucide="bar-chart-4" class="w-8 h-8 text-indigo-600"></i>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium text-indigo-800 bg-indigo-100 rounded-full">Most Popular</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Stock Valuation Report</h3>
                <p class="text-gray-600 mb-6">Generate a comprehensive report of current inventory valuation across all warehouses.</p>
                
                <form action="<?= base_url('admin/materials/generate-report') ?>" method="post" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="report_type" value="valuation">
                    
                    <div>
                        <label for="category_id_1" class="block text-sm font-medium text-gray-700 mb-1">Category (Optional)</label>
                        <select name="category_id" id="category_id_1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="warehouse_id_1" class="block text-sm font-medium text-gray-700 mb-1">Warehouse (Optional)</label>
                        <select name="warehouse_id" id="warehouse_id_1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Warehouses</option>
                            <?php foreach ($warehouses as $warehouse): ?>
                            <option value="<?= $warehouse['id'] ?>"><?= esc($warehouse['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="format_1" class="block text-sm font-medium text-gray-700 mb-1">Report Format</label>
                        <select name="format" id="format_1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="html">HTML (View in Browser)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Generate Report
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Stock Movement Report Card -->
        <div class="bg-white rounded-lg shadow-sm border border-blue-200 hover:border-blue-300 transition-colors">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i data-lucide="trending-up" class="w-8 h-8 text-blue-600"></i>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Stock Movement Report</h3>
                <p class="text-gray-600 mb-6">Track all material movements (in/out) within a specific date range.</p>
                
                <form action="<?= base_url('admin/materials/generate-report') ?>" method="post" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="report_type" value="movement">
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="start_date_2" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date" id="start_date_2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= date('Y-m-01') ?>" required>
                        </div>
                        <div>
                            <label for="end_date_2" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date" id="end_date_2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="material_id_2" class="block text-sm font-medium text-gray-700 mb-1">Material (Optional)</label>
                        <select name="material_id" id="material_id_2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Materials</option>
                            <?php foreach ($materials as $material): ?>
                            <option value="<?= $material['id'] ?>"><?= esc($material['name']) ?> (<?= esc($material['sku']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="movement_type_2" class="block text-sm font-medium text-gray-700 mb-1">Movement Type (Optional)</label>
                        <select name="movement_type" id="movement_type_2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Movements</option>
                            <option value="in">Stock In (All)</option>
                            <option value="out">Stock Out (All)</option>
                            <option value="in_purchase">Stock In - Purchase</option>
                            <option value="in_return">Stock In - Return</option>
                            <option value="out_project">Stock Out - Project</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="format_2" class="block text-sm font-medium text-gray-700 mb-1">Report Format</label>
                        <select name="format" id="format_2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="html">HTML (View in Browser)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Generate Report
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Project Material Usage Report Card -->
        <div class="bg-white rounded-lg shadow-sm border border-green-200 hover:border-green-300 transition-colors">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i data-lucide="building" class="w-8 h-8 text-green-600"></i>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Project Material Usage</h3>
                <p class="text-gray-600 mb-6">Analyze material consumption by project for budgeting and cost analysis.</p>
                
                <form action="<?= base_url('admin/materials/generate-report') ?>" method="post" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="report_type" value="project_usage">
                    
                    <div>
                        <label for="project_id_3" class="block text-sm font-medium text-gray-700 mb-1">Project <span class="text-red-500">*</span></label>
                        <select name="project_id" id="project_id_3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                            <option value="">Select Project</option>
                            <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>"><?= esc($project['name']) ?> (<?= esc($project['project_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="start_date_3" class="block text-sm font-medium text-gray-700 mb-1">Start Date (Optional)</label>
                            <input type="date" name="start_date" id="start_date_3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="end_date_3" class="block text-sm font-medium text-gray-700 mb-1">End Date (Optional)</label>
                            <input type="date" name="end_date" id="end_date_3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    
                    <div>
                        <label for="category_id_3" class="block text-sm font-medium text-gray-700 mb-1">Material Category (Optional)</label>
                        <select name="category_id" id="category_id_3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="format_3" class="block text-sm font-medium text-gray-700 mb-1">Report Format</label>
                        <select name="format" id="format_3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="html">HTML (View in Browser)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Generate Report
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Low Stock Alert Report Card -->
        <div class="bg-white rounded-lg shadow-sm border border-amber-200 hover:border-amber-300 transition-colors">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-amber-100 rounded-full">
                        <i data-lucide="alert-triangle" class="w-8 h-8 text-amber-600"></i>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium text-amber-800 bg-amber-100 rounded-full">Important</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Low Stock Alert Report</h3>
                <p class="text-gray-600 mb-6">Identify all materials that have fallen below their minimum stock levels.</p>
                
                <form action="<?= base_url('admin/materials/generate-report') ?>" method="post" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="report_type" value="low_stock">
                    
                    <div>
                        <label for="category_id_4" class="block text-sm font-medium text-gray-700 mb-1">Category (Optional)</label>
                        <select name="category_id" id="category_id_4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="threshold_4" class="block text-sm font-medium text-gray-700 mb-1">Threshold Percentage (Optional)</label>
                        <select name="threshold" id="threshold_4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="0">At or Below Minimum Level</option>
                            <option value="10">10% Above Minimum Level</option>
                            <option value="20">20% Above Minimum Level</option>
                            <option value="50">50% Above Minimum Level</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Shows materials that will soon reach minimum level</p>
                    </div>
                    
                    <div>
                        <label for="format_4" class="block text-sm font-medium text-gray-700 mb-1">Report Format</label>
                        <select name="format" id="format_4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="html">HTML (View in Browser)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Generate Report
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Supplier Analysis Report Card -->
        <div class="bg-white rounded-lg shadow-sm border border-purple-200 hover:border-purple-300 transition-colors">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i data-lucide="truck" class="w-8 h-8 text-purple-600"></i>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Supplier Analysis Report</h3>
                <p class="text-gray-600 mb-6">Analyze material purchases by supplier for cost optimization.</p>
                
                <form action="<?= base_url('admin/materials/generate-report') ?>" method="post" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="report_type" value="supplier">
                    
                    <div>
                        <label for="supplier_id_5" class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                        <select name="supplier_id" id="supplier_id_5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                            <option value="">Select Supplier</option>
                            <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier['id'] ?>"><?= esc($supplier['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="start_date_5" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date" id="start_date_5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= date('Y-01-01') ?>" required>
                        </div>
                        <div>
                            <label for="end_date_5" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date" id="end_date_5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="format_5" class="block text-sm font-medium text-gray-700 mb-1">Report Format</label>
                        <select name="format" id="format_5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="html">HTML (View in Browser)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Generate Report
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Material Cost Trend Report Card -->
        <div class="bg-white rounded-lg shadow-sm border border-cyan-200 hover:border-cyan-300 transition-colors">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-cyan-100 rounded-full">
                        <i data-lucide="line-chart" class="w-8 h-8 text-cyan-600"></i>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Cost Trend Analysis</h3>
                <p class="text-gray-600 mb-6">Track material cost changes over time to identify price trends.</p>
                
                <form action="<?= base_url('admin/materials/generate-report') ?>" method="post" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="report_type" value="cost_trend">
                    
                    <div>
                        <label for="material_id_6" class="block text-sm font-medium text-gray-700 mb-1">Material <span class="text-red-500">*</span></label>
                        <select name="material_id" id="material_id_6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" required>
                            <option value="">Select Material</option>
                            <?php foreach ($materials as $material): ?>
                            <option value="<?= $material['id'] ?>"><?= esc($material['name']) ?> (<?= esc($material['sku']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="date_range_6" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                        <select name="date_range" id="date_range_6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                            <option value="30">Last 30 Days</option>
                            <option value="90">Last 3 Months</option>
                            <option value="180">Last 6 Months</option>
                            <option value="365" selected>Last 12 Months</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    
                    <div id="custom_date_range_6" class="grid grid-cols-2 gap-3 hidden">
                        <div>
                            <label for="start_date_6" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date" id="start_date_6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        </div>
                        <div>
                            <label for="end_date_6" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date" id="end_date_6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    
                    <div>
                        <label for="format_6" class="block text-sm font-medium text-gray-700 mb-1">Report Format</label>
                        <select name="format" id="format_6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="html">HTML (View in Browser)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Generate Report
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Custom date range toggling
    const dateRangeSelect = document.getElementById('date_range_6');
    const customDateRange = document.getElementById('custom_date_range_6');
    
    if (dateRangeSelect && customDateRange) {
        dateRangeSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.classList.remove('hidden');
            } else {
                customDateRange.classList.add('hidden');
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
