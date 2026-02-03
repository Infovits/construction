<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">System Overview</h1>
            <p class="text-gray-600 mt-1">Comprehensive view of all system modules and real-time statistics</p>
        </div>
    </div>

    <!-- System Health Status -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i data-lucide="activity" class="w-5 h-5 mr-2 text-green-600"></i>
            System Health Status
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 border-l-4 border-green-500 bg-green-50 rounded">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Overall Status</p>
                        <p class="text-2xl font-bold text-green-600 mt-1"><?= esc($system_health['status'] ?? 'Operational') ?></p>
                    </div>
                    <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
                </div>
            </div>
            <div class="p-4 border-l-4 border-blue-500 bg-blue-50 rounded">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Uptime</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1"><?= $system_health['uptime'] ?? 99.8 ?>%</p>
                    </div>
                    <i data-lucide="trending-up" class="w-8 h-8 text-blue-600"></i>
                </div>
            </div>
            <div class="p-4 border-l-4 border-purple-500 bg-purple-50 rounded">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Response Time</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1"><?= $system_health['response_time'] ?? 145 ?>ms</p>
                    </div>
                    <i data-lucide="zap" class="w-8 h-8 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- HR & USERS MODULE -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center pb-4 border-b">
            <i data-lucide="users" class="w-5 h-5 mr-2 text-indigo-600"></i>
            Human Resources & Users
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-indigo-50 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?= $total_users ?? 0 ?></p>
                    </div>
                    <i data-lucide="users-2" class="w-12 h-12 text-indigo-600 opacity-20"></i>
                </div>
                <div class="mt-4 pt-4 border-t border-indigo-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Active:</span>
                        <span class="font-semibold text-green-600"><?= $active_users ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between text-sm mt-2">
                        <span class="text-gray-600">Inactive:</span>
                        <span class="font-semibold text-red-600"><?= $inactive_users ?? 0 ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PROJECT MANAGEMENT MODULE -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center pb-4 border-b">
            <i data-lucide="briefcase" class="w-5 h-5 mr-2 text-green-600"></i>
            Project Management
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-green-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Total Projects</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $total_projects ?? 0 ?></p>
                <div class="mt-4 pt-4 border-t border-green-200 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-600">Active:</span><span class="font-semibold text-blue-600"><?= $active_projects ?? 0 ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Completed:</span><span class="font-semibold text-green-600"><?= $completed_projects ?? 0 ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-600">On Hold:</span><span class="font-semibold text-amber-600"><?= $on_hold_projects ?? 0 ?></span></div>
                </div>
            </div>

            <div class="bg-orange-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Tasks</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $task_stats['total'] ?? 0 ?></p>
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: <?= $task_stats['completion_rate'] ?? 0 ?>%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-2"><?= $task_stats['completion_rate'] ?? 0 ?>% Completed</p>
                </div>
                <div class="mt-4 pt-4 border-t border-orange-200 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-600">In Progress:</span><span class="font-semibold text-blue-600"><?= $task_stats['in_progress'] ?? 0 ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Overdue:</span><span class="font-semibold text-red-600"><?= $task_stats['overdue'] ?? 0 ?></span></div>
                </div>
            </div>

            <div class="bg-red-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Milestones</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $milestone_stats['total'] ?? 0 ?></p>
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width: <?= $milestone_stats['completion_rate'] ?? 0 ?>%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-2"><?= $milestone_stats['completion_rate'] ?? 0 ?>% Completed</p>
                </div>
                <div class="mt-4 pt-4 border-t border-red-200 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-600">Completed:</span><span class="font-semibold text-green-600"><?= $milestone_stats['completed'] ?? 0 ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-600">In Progress:</span><span class="font-semibold text-blue-600"><?= $milestone_stats['in_progress'] ?? 0 ?></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- COMMUNICATIONS MODULE -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center pb-4 border-b">
            <i data-lucide="message-circle" class="w-5 h-5 mr-2 text-purple-600"></i>
            Communications
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-purple-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Conversations</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $total_conversations ?? 0 ?></p>
                <div class="mt-4 pt-4 border-t border-purple-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Active:</span>
                        <span class="font-semibold text-blue-600"><?= $active_conversations ?? 0 ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Total Messages</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $total_messages ?? 0 ?></p>
                <p class="text-xs text-gray-600 mt-4">Across all conversations</p>
            </div>
        </div>
    </div>

    <!-- CLIENT & SUPPLIER MODULE -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center pb-4 border-b">
            <i data-lucide="building" class="w-5 h-5 mr-2 text-cyan-600"></i>
            Clients & Suppliers
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-cyan-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Clients</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $client_stats['total'] ?? 0 ?></p>
                <div class="mt-4 pt-4 border-t border-cyan-200 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-600">Active:</span><span class="font-semibold text-green-600"><?= $client_stats['active'] ?? 0 ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Inactive:</span><span class="font-semibold text-red-600"><?= $client_stats['inactive'] ?? 0 ?></span></div>
                </div>
            </div>

            <div class="bg-teal-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Suppliers</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $supplier_stats['total'] ?? 0 ?></p>
                <div class="mt-4 pt-4 border-t border-teal-200 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-600">Active:</span><span class="font-semibold text-green-600"><?= $supplier_stats['active'] ?? 0 ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Inactive:</span><span class="font-semibold text-red-600"><?= $supplier_stats['inactive'] ?? 0 ?></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- INVENTORY MODULE -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center pb-4 border-b">
            <i data-lucide="package" class="w-5 h-5 mr-2 text-amber-600"></i>
            Inventory Management
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-amber-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Total Materials</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $inventory_stats['total'] ?? 0 ?></p>
                <p class="text-xs text-gray-600 mt-4">In all warehouses</p>
            </div>

            <div class="bg-orange-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Low Stock</p>
                <p class="text-3xl font-bold text-orange-600 mt-2"><?= $inventory_stats['low_stock'] ?? 0 ?></p>
                <p class="text-xs text-gray-600 mt-4">Items below threshold</p>
            </div>

            <div class="bg-red-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Out of Stock</p>
                <p class="text-3xl font-bold text-red-600 mt-2"><?= $inventory_stats['out_of_stock'] ?? 0 ?></p>
                <p class="text-xs text-gray-600 mt-4">Require immediate ordering</p>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-sky-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Warehouses</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $warehouse_stats['total'] ?? 0 ?></p>
                <div class="mt-4 pt-4 border-t border-sky-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Active:</span>
                        <span class="font-semibold text-green-600"><?= $warehouse_stats['active'] ?? 0 ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PROCUREMENT MODULE -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center pb-4 border-b">
            <i data-lucide="shopping-cart" class="w-5 h-5 mr-2 text-fuchsia-600"></i>
            Procurement
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-fuchsia-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Total POs</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $purchase_order_stats['total'] ?? 0 ?></p>
                <p class="text-xs text-gray-600 mt-4">Purchase Orders</p>
            </div>

            <div class="bg-pink-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Pending</p>
                <p class="text-3xl font-bold text-pink-600 mt-2"><?= $purchase_order_stats['pending'] ?? 0 ?></p>
                <p class="text-xs text-gray-600 mt-4">Awaiting approval</p>
            </div>

            <div class="bg-blue-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Approved</p>
                <p class="text-3xl font-bold text-blue-600 mt-2"><?= $purchase_order_stats['approved'] ?? 0 ?></p>
                <p class="text-xs text-gray-600 mt-4">Ready for delivery</p>
            </div>

            <div class="bg-green-50 rounded-lg p-6">
                <p class="text-gray-600 text-sm font-medium">Delivered</p>
                <p class="text-3xl font-bold text-green-600 mt-2"><?= $purchase_order_stats['delivered'] ?? 0 ?></p>
                <p class="text-xs text-gray-600 mt-4">Completed orders</p>
            </div>
        </div>
    </div>

    <!-- Resource Usage -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="database" class="w-5 h-5 mr-2 text-blue-600"></i>
                Storage & Resources
            </h2>
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Database Size</span>
                        <span class="text-sm font-semibold text-gray-900"><?= $storage_info['database_size'] ?? '2.4' ?> GB / <?= $storage_info['database_limit'] ?? '10' ?> GB</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?= round(($storage_info['database_size'] / $storage_info['database_limit']) * 100) ?>%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">File Storage</span>
                        <span class="text-sm font-semibold text-gray-900"><?= round($storage_info['file_storage'] / 1024, 2) ?? '0.8' ?> GB / <?= round($storage_info['file_limit'] / 1024, 2) ?? '5' ?> GB</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: <?= round(($storage_info['file_storage'] / $storage_info['file_limit']) * 100) ?>%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Memory Usage</span>
                        <span class="text-sm font-semibold text-gray-900"><?= $storage_info['memory_usage'] ?? '512' ?> MB / <?= $storage_info['memory_limit'] ?? '2048' ?> MB</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: <?= round(($storage_info['memory_usage'] / $storage_info['memory_limit']) * 100) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="shield" class="w-5 h-5 mr-2 text-green-600"></i>
                Security Status
            </h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                    <div>
                        <p class="text-sm font-medium text-green-900">SSL Certificate</p>
                        <p class="text-xs text-green-700">Valid until <?= $security_status['ssl_expires'] ?? '2027-02-03' ?></p>
                    </div>
                    <?php if ($security_status['ssl_valid'] ?? true): ?>
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-green-600"></i>
                    <?php else: ?>
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                    <?php endif; ?>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                    <div>
                        <p class="text-sm font-medium text-green-900">Backups</p>
                        <p class="text-xs text-green-700">Last backup: <?= date('M d, Y \a\t H:i A', strtotime($security_status['last_backup'] ?? date('Y-m-d H:i:s'))) ?></p>
                    </div>
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-green-600"></i>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                    <div>
                        <p class="text-sm font-medium text-green-900">Security Scan</p>
                        <p class="text-xs text-green-700"><?= $security_status['security_scan'] ?? 'No threats detected' ?></p>
                    </div>
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i data-lucide="history" class="w-5 h-5 mr-2 text-indigo-600"></i>
            Recent System Activity
        </h2>
        <div class="space-y-3">
            <?php if (!empty($recent_activity)): ?>
                <?php foreach ($recent_activity as $activity): ?>
                    <div class="flex items-start gap-3 py-3 border-b border-gray-200 last:border-0">
                        <div class="w-2 h-2 <?php 
                            echo match($activity['type'] ?? 'default') {
                                'user' => 'bg-blue-600',
                                'project' => 'bg-green-600',
                                'task' => 'bg-purple-600',
                                'system' => 'bg-orange-600',
                                default => 'bg-gray-600'
                            };
                        ?> rounded-full mt-2 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900"><?= esc($activity['title'] ?? '') ?></p>
                            <p class="text-xs text-gray-500"><?= esc($activity['description'] ?? '') ?></p>
                        </div>
                        <p class="text-xs text-gray-500 flex-shrink-0"><?= time_elapsed_string($activity['time'] ?? date('Y-m-d H:i:s')) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <p>No recent activity yet</p>
                </div>
            <?php endif; ?>
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
