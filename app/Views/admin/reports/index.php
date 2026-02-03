<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Reports</h1>
        <p class="text-gray-600 mt-1">Generate and manage system reports</p>
    </div>

    <!-- Generate New Report -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Generate New Report</h2>
        <form method="POST" action="<?= base_url('admin/reports/generate') ?>" class="space-y-4">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                    <select name="report_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                        <option value="">Select Report Type</option>
                        <option value="messaging_activity">Messaging Activity</option>
                        <option value="project_performance">Project Performance</option>
                        <option value="task_summary">Task Summary</option>
                        <option value="user_engagement">User Engagement</option>
                        <option value="client_summary">Client Summary</option>
                        <option value="supplier_summary">Supplier Summary</option>
                        <option value="material_usage">Material Usage</option>
                        <option value="purchase_orders">Purchase Orders</option>
                        <option value="warehouse_inventory">Warehouse Inventory</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <select name="date_range" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                        <option value="">Select Date Range</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="last_90_days">Last 90 Days</option>
                        <option value="this_year">This Year</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Departments</option>
                        <option value="sales">Sales</option>
                        <option value="operations">Operations</option>
                        <option value="engineering">Engineering</option>
                        <option value="support">Support</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                    <select name="format" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                        <option value="">Select Format</option>
                        <option value="view">View Report</option>
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                        <option value="csv">CSV</option>
                        <option value="html">HTML</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    Generate Report
                </button>
                <button type="reset" class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Reset
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
<?= $this->endSection() ?>
