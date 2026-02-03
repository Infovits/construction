<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= esc($report_title ?? 'Report') ?></h1>
            <p class="text-gray-600 mt-1">Generated: <?= date('Y-m-d H:i:s') ?></p>
        </div>
        <a href="<?= base_url('admin/reports') ?>" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Reports
        </a>
    </div>

    <!-- Report Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border shadow-sm p-4">
            <p class="text-gray-600 text-sm font-medium">Report Type</p>
            <p class="text-2xl font-bold text-gray-900 mt-2"><?= esc($report_type ?? 'N/A') ?></p>
        </div>
        <div class="bg-white rounded-lg border shadow-sm p-4">
            <p class="text-gray-600 text-sm font-medium">Date Range</p>
            <p class="text-2xl font-bold text-gray-900 mt-2"><?= esc($date_range ?? 'N/A') ?></p>
        </div>
        <div class="bg-white rounded-lg border shadow-sm p-4">
            <p class="text-gray-600 text-sm font-medium">Total Records</p>
            <p class="text-2xl font-bold text-gray-900 mt-2"><?= count($report_data ?? []) ?></p>
        </div>
        <div class="bg-white rounded-lg border shadow-sm p-4">
            <p class="text-gray-600 text-sm font-medium">Generated At</p>
            <p class="text-lg font-bold text-gray-900 mt-2"><?= date('H:i:s') ?></p>
        </div>
    </div>

    <!-- Export Options -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Export Report</h2>
        <div class="flex flex-wrap gap-3">
            <form method="POST" action="<?= base_url('admin/reports/generate') ?>" style="display:inline;">
                <?= csrf_field() ?>
                <input type="hidden" name="report_type" value="<?= esc($report_type ?? '') ?>">
                <input type="hidden" name="date_range" value="<?= esc($date_range ?? '') ?>">
                <input type="hidden" name="department" value="<?= esc($department ?? '') ?>">
                <input type="hidden" name="format" value="pdf">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium flex items-center gap-2">
                    <i data-lucide="file-pdf" class="w-4 h-4"></i>
                    Download as PDF
                </button>
            </form>
            
            <form method="POST" action="<?= base_url('admin/reports/generate') ?>" style="display:inline;">
                <?= csrf_field() ?>
                <input type="hidden" name="report_type" value="<?= esc($report_type ?? '') ?>">
                <input type="hidden" name="date_range" value="<?= esc($date_range ?? '') ?>">
                <input type="hidden" name="department" value="<?= esc($department ?? '') ?>">
                <input type="hidden" name="format" value="excel">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium flex items-center gap-2">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    Download as Excel
                </button>
            </form>
            
            <form method="POST" action="<?= base_url('admin/reports/generate') ?>" style="display:inline;">
                <?= csrf_field() ?>
                <input type="hidden" name="report_type" value="<?= esc($report_type ?? '') ?>">
                <input type="hidden" name="date_range" value="<?= esc($date_range ?? '') ?>">
                <input type="hidden" name="department" value="<?= esc($department ?? '') ?>">
                <input type="hidden" name="format" value="csv">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    Download as CSV
                </button>
            </form>
            
            <a href="<?= base_url('admin/reports') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Generate Another Report
            </a>
        </div>
    </div>

    <!-- Report Data Table -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Report Details</h2>
        
        <?php if (!empty($report_data)): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <?php if ($report_type === 'messaging_activity'): ?>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Message ID</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Created At</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Sender</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Content</th>
                            <?php elseif ($report_type === 'project_performance'): ?>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Project Name</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Created</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Due Date</th>
                            <?php elseif ($report_type === 'task_summary'): ?>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Task Title</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Assigned To</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Due Date</th>
                            <?php elseif ($report_type === 'user_engagement'): ?>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">User Name</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Message Count</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Engagement Level</th>
                            <?php elseif ($report_type === 'client_summary'): ?>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Client Name</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Email</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Phone</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                            <?php elseif ($report_type === 'supplier_summary'): ?>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Supplier Name</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Contact</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Category</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                            <?php elseif ($report_type === 'material_usage'): ?>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Material Name</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Category</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Quantity</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Unit</th>
                            <?php elseif ($report_type === 'purchase_orders'): ?>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">PO Number</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Supplier</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Total Amount</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                            <?php elseif ($report_type === 'warehouse_inventory'): ?>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Warehouse Name</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Location</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Capacity</th>
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($report_data as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <?php if ($report_type === 'messaging_activity'): ?>
                                    <td class="py-3 px-4 text-sm text-gray-900"><?= esc($item['id'] ?? '') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['created_at'] ?? '') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['sender_id'] ?? '') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc(substr($item['body'] ?? '', 0, 50)) ?>...</td>
                                <?php elseif ($report_type === 'project_performance'): ?>
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900"><?= esc($item['name'] ?? '') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><?= esc($item['status'] ?? '') ?></span></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc(date('Y-m-d', strtotime($item['created_at'] ?? ''))) ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc(date('Y-m-d', strtotime($item['end_date'] ?? ''))) ?></td>
                                <?php elseif ($report_type === 'task_summary'): ?>
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900"><?= esc($item['title'] ?? '') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><?= esc($item['status'] ?? '') ?></span></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['assigned_to'] ?? 'N/A') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc(date('Y-m-d', strtotime($item['planned_end_date'] ?? ''))) ?></td>
                                <?php elseif ($report_type === 'user_engagement'): ?>
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900"><?= esc($item['name'] ?? '') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= ($item['message_count'] ?? 0) ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        <?php 
                                        $level = $item['engagement_level'] ?? 'Low';
                                        $color = $level === 'High' ? 'bg-green-100 text-green-800' : ($level === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                        ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $color ?>"><?= esc($level) ?></span>
                                    </td>
                                <?php elseif ($report_type === 'client_summary'): ?>
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900"><?= esc($item['name'] ?? '') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['email'] ?? 'N/A') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['phone'] ?? 'N/A') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><?= esc($item['status'] ?? 'Active') ?></span></td>
                                <?php elseif ($report_type === 'supplier_summary'): ?>
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900"><?= esc($item['name'] ?? '') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['contact_person'] ?? 'N/A') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['category'] ?? 'N/A') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><?= esc($item['status'] ?? 'Active') ?></span></td>
                                <?php elseif ($report_type === 'material_usage'): ?>
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900"><?= esc($item['name'] ?? '') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['category'] ?? 'N/A') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['quantity'] ?? '0') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['unit'] ?? 'pcs') ?></td>
                                <?php elseif ($report_type === 'purchase_orders'): ?>
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900"><?= esc($item['po_number'] ?? '') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['supplier_id'] ?? 'N/A') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc(number_format($item['total_amount'] ?? 0, 2)) ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><?= esc($item['status'] ?? 'Pending') ?></span></td>
                                <?php elseif ($report_type === 'warehouse_inventory'): ?>
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900"><?= esc($item['name'] ?? '') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['location'] ?? 'N/A') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><?= esc($item['capacity'] ?? '0') ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-600"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><?= esc($item['status'] ?? 'Active') ?></span></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8">
                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 text-gray-300"></i>
                <p class="text-gray-500">No data available for this report</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
<?= $this->endSection() ?>
