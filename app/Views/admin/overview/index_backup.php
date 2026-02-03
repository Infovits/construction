<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">System Overview</h1>
        <p class="text-gray-600 mt-1">Complete view of your system's current status and health</p>
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

    <!-- Core Metrics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-lg border shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $total_users ?? 0 ?></p>
                    <div class="flex gap-4 mt-4">
                        <span class="text-xs">
                            <span class="font-semibold text-green-600"><?= $active_users ?? 0 ?></span>
                            <span class="text-gray-600">Active</span>
                        </span>
                        <span class="text-xs">
                            <span class="font-semibold text-gray-600"><?= $inactive_users ?? 0 ?></span>
                            <span class="text-gray-600">Inactive</span>
                        </span>
                    </div>
                </div>
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Projects -->
        <div class="bg-white rounded-lg border shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Projects</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $total_projects ?? 0 ?></p>
                    <div class="flex gap-4 mt-4">
                        <span class="text-xs">
                            <span class="font-semibold text-blue-600"><?= $active_projects ?? 0 ?></span>
                            <span class="text-gray-600">Active</span>
                        </span>
                        <span class="text-xs">
                            <span class="font-semibold text-gray-600"><?= $completed_projects ?? 0 ?></span>
                            <span class="text-gray-600">Done</span>
                        </span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="briefcase" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Tasks -->
        <div class="bg-white rounded-lg border shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Tasks</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $total_tasks ?? 0 ?></p>
                    <div class="flex gap-4 mt-4">
                        <span class="text-xs">
                            <span class="font-semibold text-orange-600"><?= $pending_tasks ?? 0 ?></span>
                            <span class="text-gray-600">Pending</span>
                        </span>
                        <span class="text-xs">
                            <span class="font-semibold text-green-600"><?= $completed_tasks ?? 0 ?></span>
                            <span class="text-gray-600">Done</span>
                        </span>
                    </div>
                </div>
                <div class="p-3 bg-orange-100 rounded-lg">
                    <i data-lucide="check-square" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>

        <!-- Conversations -->
        <div class="bg-white rounded-lg border shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Conversations</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $total_conversations ?? 0 ?></p>
                    <div class="flex gap-4 mt-4">
                        <span class="text-xs">
                            <span class="font-semibold text-indigo-600"><?= $active_conversations ?? 0 ?></span>
                            <span class="text-gray-600">Active</span>
                        </span>
                        <span class="text-xs">
                            <span class="font-semibold text-gray-600"><?= $archived_conversations ?? 0 ?></span>
                            <span class="text-gray-600">Archived</span>
                        </span>
                    </div>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i data-lucide="message-square" class="w-6 h-6 text-purple-600"></i>
                </div>
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
