<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Analytics</h1>
        <p class="text-gray-600 mt-1">Track system performance and user engagement metrics</p>
    </div>

    <!-- Time Period Filter -->
    <div class="flex gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">This Week</button>
        <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">This Month</button>
        <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Last 90 Days</button>
        <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Custom Range</button>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
        <!-- Messages Sent -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Messages Sent</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $messages_sent ?? 0 ?></p>
                    <p class="text-xs text-green-600 mt-3">
                        <i data-lucide="trending-up" class="w-3 h-3 inline"></i>
                        12% increase
                    </p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-lucide="send" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Conversations -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Active Conversations</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $active_conversations ?? 0 ?></p>
                    <p class="text-xs text-green-600 mt-3">
                        <i data-lucide="trending-up" class="w-3 h-3 inline"></i>
                        8% increase
                    </p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <i data-lucide="message-square" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
        </div>

        <!-- Task Completion -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Tasks Completed</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $tasks_completed ?? 0 ?></p>
                    <p class="text-xs text-green-600 mt-3">
                        <i data-lucide="trending-up" class="w-3 h-3 inline"></i>
                        5% increase
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="check-circle-2" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Milestones Completed -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Milestones Completed</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $milestones_completed ?? 0 ?></p>
                    <p class="text-xs text-green-600 mt-3">
                        <i data-lucide="trending-up" class="w-3 h-3 inline"></i>
                        2% increase
                    </p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <i data-lucide="flag" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>

        <!-- Task Completion Rate -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Task Completion Rate</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $task_completion_rate ?? 0 ?>%</p>
                    <p class="text-xs text-blue-600 mt-3">
                        <i data-lucide="percent" class="w-3 h-3 inline"></i>
                        Overall rate
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="bar-chart-3" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Milestone Completion Rate -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Milestone Completion Rate</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $milestone_completion_rate ?? 0 ?>%</p>
                    <p class="text-xs text-red-600 mt-3">
                        <i data-lucide="percent" class="w-3 h-3 inline"></i>
                        Overall rate
                    </p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <i data-lucide="flag" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Messages Timeline -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="line-chart" class="w-5 h-5 mr-2 text-indigo-600"></i>
                Messages Over Time
            </h2>
            <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center text-gray-500">
                <div class="text-center">
                    <i data-lucide="bar-chart-3" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                    <p>Chart visualization placeholder</p>
                    <p class="text-xs text-gray-400 mt-1">Data will be populated here</p>
                </div>
            </div>
        </div>

        <!-- Task Distribution -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="pie-chart" class="w-5 h-5 mr-2 text-green-600"></i>
                Task Status Distribution
            </h2>
            <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center text-gray-500">
                <div class="text-center">
                    <i data-lucide="pie-chart" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                    <p>Chart visualization placeholder</p>
                    <p class="text-xs text-gray-400 mt-1">Data will be populated here</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Most Active Users -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="award" class="w-5 h-5 mr-2 text-yellow-600"></i>
                Most Active Users
            </h2>
            <div class="space-y-3">
                <?php if (!empty($top_users)): ?>
                    <?php foreach ($top_users as $index => $user): ?>
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-medium">
                                    <?= $index + 1 ?>
                                </span>
                                <div>
                                    <p class="font-medium text-gray-900"><?= esc($user['name'] ?? 'Unknown') ?></p>
                                    <p class="text-xs text-gray-500"><?= $user['messages_count'] ?? 0 ?> messages</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-600"><?= $user['engagement_score'] ?? 0 ?>%</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <p>No user data available yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Project Progress -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="briefcase" class="w-5 h-5 mr-2 text-blue-600"></i>
                Project Progress
            </h2>
            <div class="space-y-4">
                <?php if (!empty($project_progress)): ?>
                    <?php foreach ($project_progress as $project): ?>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-medium text-gray-900 text-sm"><?= esc($project['name'] ?? 'Project') ?></p>
                                <span class="text-xs font-semibold text-gray-600"><?= $project['progress'] ?? 0 ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $project['progress'] ?? 0 ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <p>No project data available yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Milestone Progress -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="flag" class="w-5 h-5 mr-2 text-red-600"></i>
                Milestone Progress
            </h2>
            <div class="space-y-4">
                <?php if (!empty($milestone_progress)): ?>
                    <?php foreach ($milestone_progress as $milestone): ?>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-medium text-gray-900 text-sm"><?= esc($milestone['name'] ?? 'Milestone') ?></p>
                                <span class="text-xs font-semibold text-gray-600"><?= $milestone['progress'] ?? 0 ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full" style="width: <?= $milestone['progress'] ?? 0 ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <p>No milestone data available yet</p>
                    </div>
                <?php endif; ?>
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
