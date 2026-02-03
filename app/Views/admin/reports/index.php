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
        <form class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option>Select Report Type</option>
                        <option>Messaging Activity</option>
                        <option>Project Performance</option>
                        <option>Task Summary</option>
                        <option>User Engagement</option>
                        <option>Financial Summary</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option>This Week</option>
                        <option>This Month</option>
                        <option>Last 90 Days</option>
                        <option>This Year</option>
                        <option>Custom Range</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option>All Departments</option>
                        <option>Sales</option>
                        <option>Operations</option>
                        <option>Engineering</option>
                        <option>Support</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option>PDF</option>
                        <option>Excel</option>
                        <option>CSV</option>
                        <option>HTML</option>
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

    <!-- Recent Reports -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i data-lucide="history" class="w-5 h-5 mr-2 text-indigo-600"></i>
                Recent Reports
            </h2>
            <select class="px-3 py-1 border border-gray-300 rounded-lg text-sm bg-white">
                <option>All Reports</option>
                <option>This Month</option>
                <option>Last Month</option>
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-gray-200">
                    <tr>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Report Name</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Type</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Date Range</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Generated</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                        <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (!empty($recent_reports)): ?>
                        <?php foreach ($recent_reports as $report): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-900"><?= esc($report['name'] ?? 'Report') ?></div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-sm text-gray-600"><?= esc($report['type'] ?? 'N/A') ?></span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-sm text-gray-600"><?= esc($report['date_range'] ?? 'N/A') ?></span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-sm text-gray-600"><?= isset($report['created_at']) ? format_datetime($report['created_at']) : 'N/A' ?></span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Complete
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium" title="View">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium" title="Download">
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                        </a>
                                        <a href="#" class="text-red-600 hover:text-red-800 text-sm font-medium" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 text-gray-300"></i>
                                <p>No reports generated yet</p>
                                <p class="text-sm mt-1">Generate your first report using the form above</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scheduled Reports -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i data-lucide="clock" class="w-5 h-5 mr-2 text-purple-600"></i>
                Scheduled Reports
            </h2>
            <button class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium">
                <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i> Schedule
            </button>
        </div>
        <div class="space-y-3">
            <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-medium text-gray-900">Weekly Activity Report</h3>
                        <p class="text-sm text-gray-600 mt-1">Sent every Monday at 9:00 AM</p>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Active</span>
                </div>
            </div>
            <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-medium text-gray-900">Monthly Performance Report</h3>
                        <p class="text-sm text-gray-600 mt-1">Sent on the 1st of each month at 8:00 AM</p>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Active</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
<?= $this->endSection() ?>
