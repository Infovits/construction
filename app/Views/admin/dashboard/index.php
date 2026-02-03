<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-1">Welcome back, <?= esc(session('user_name') ?? 'User') ?></p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Messages Card -->
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-lg p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-indigo-600 text-sm font-medium">Unread Messages</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $unreadMessageCount ?></p>
                    <a href="<?= base_url('admin/messages') ?>" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium mt-3 inline-block">View Inbox →</a>
                </div>
                <div class="p-3 bg-indigo-600 rounded-lg">
                    <i data-lucide="message-circle" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Notifications Card -->
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-lg p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-orange-600 text-sm font-medium">Unread Notifications</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $unreadNotificationCount ?></p>
                    <a href="<?= base_url('admin/notifications') ?>" class="text-orange-600 hover:text-orange-800 text-xs font-medium mt-3 inline-block">View All →</a>
                </div>
                <div class="p-3 bg-orange-600 rounded-lg">
                    <i data-lucide="bell" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Projects Card -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-green-600 text-sm font-medium">Active Projects</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $project_stats['active'] ?? 0 ?></p>
                    <a href="<?= base_url('admin/projects') ?>" class="text-green-600 hover:text-green-800 text-xs font-medium mt-3 inline-block">View Projects →</a>
                </div>
                <div class="p-3 bg-green-600 rounded-lg">
                    <i data-lucide="briefcase" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Tasks Card -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-purple-600 text-sm font-medium">In Progress Tasks</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $task_stats['in_progress'] ?? 0 ?></p>
                    <a href="<?= base_url('admin/tasks') ?>" class="text-purple-600 hover:text-purple-800 text-xs font-medium mt-3 inline-block">View Tasks →</a>
                </div>
                <div class="p-3 bg-purple-600 rounded-lg">
                    <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Milestones Card -->
        <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-lg p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-red-600 text-sm font-medium">In Progress Milestones</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= $milestone_stats['in_progress'] ?? 0 ?></p>
                    <a href="<?= base_url('admin/milestones') ?>" class="text-red-600 hover:text-red-800 text-xs font-medium mt-3 inline-block">View Milestones →</a>
                </div>
                <div class="p-3 bg-red-600 rounded-lg">
                    <i data-lucide="flag" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Conversations (Left) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i data-lucide="message-square" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Recent Conversations
                    </h2>
                    <a href="<?= base_url('admin/messages') ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
                </div>
                <?php if (empty($recentConversations)): ?>
                    <div class="text-center py-12">
                        <i data-lucide="message-circle" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                        <p class="text-gray-500 mb-4">No conversations yet</p>
                        <a href="<?= base_url('admin/messages/new') ?>" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                            Start a Conversation
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recentConversations as $conversation): ?>
                            <a href="<?= base_url('admin/messages/' . $conversation['id']) ?>" class="block p-4 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900"><?= esc($conversation['participant_names']) ?></h3>
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-1"><?= esc($conversation['last_message'] ?? '(No messages yet)') ?></p>
                                    </div>
                                    <?php if ((int)$conversation['unread_count'] > 0): ?>
                                        <span class="ml-3 inline-flex items-center px-2 py-1 bg-indigo-600 text-white text-xs font-medium rounded-full flex-shrink-0">
                                            <?= $conversation['unread_count'] ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Notifications (Right) -->
        <div>
            <div class="bg-white rounded-lg border shadow-sm p-6 h-full">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i data-lucide="bell" class="w-5 h-5 mr-2 text-orange-600"></i>
                        Notifications
                    </h2>
                    <a href="<?= base_url('admin/notifications') ?>" class="text-orange-600 hover:text-orange-800 text-sm font-medium">All</a>
                </div>
                <?php if (empty($recentNotifications)): ?>
                    <div class="text-center py-8">
                        <i data-lucide="inbox" class="w-10 h-10 text-gray-300 mx-auto mb-2"></i>
                        <p class="text-gray-500 text-sm">No notifications</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($recentNotifications as $notification): ?>
                            <?php
                                $link = '#';
                                if ($notification['related_type'] === 'conversation' && !empty($notification['related_id'])) {
                                    $link = base_url('admin/messages/' . $notification['related_id']);
                                }
                            ?>
                            <a href="<?= esc($link) ?>" class="block p-3 rounded-lg hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900"><?= esc($notification['title']) ?></p>
                                        <p class="text-xs text-gray-500 mt-1"><?= format_datetime($notification['created_at']) ?></p>
                                    </div>
                                    <?php if ((int)$notification['is_read'] === 0): ?>
                                        <span class="w-2 h-2 bg-indigo-600 rounded-full mt-1.5 flex-shrink-0"></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bottom Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Task Summary -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="list-check" class="w-4 h-4 mr-2 text-blue-600"></i>
                Task Summary
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Tasks</span>
                    <span class="text-lg font-bold text-gray-900"><?= $task_stats['total'] ?? 0 ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Completed</span>
                    <span class="text-lg font-bold text-green-600"><?= $task_stats['completed'] ?? 0 ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">In Progress</span>
                    <span class="text-lg font-bold text-purple-600"><?= $task_stats['in_progress'] ?? 0 ?></span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t">
                    <span class="text-sm text-gray-600">Overdue</span>
                    <span class="text-lg font-bold text-red-600"><?= $task_stats['overdue'] ?? 0 ?></span>
                </div>
            </div>
        </div>

        <!-- User Stats -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="users" class="w-4 h-4 mr-2 text-green-600"></i>
                User Statistics
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Users</span>
                    <span class="text-lg font-bold text-gray-900"><?= $user_stats['total'] ?? 0 ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Active</span>
                    <span class="text-lg font-bold text-green-600"><?= $user_stats['active'] ?? 0 ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Inactive</span>
                    <span class="text-lg font-bold text-gray-500"><?= $user_stats['inactive'] ?? 0 ?></span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t">
                    <span class="text-sm text-gray-600">New This Month</span>
                    <span class="text-lg font-bold text-blue-600"><?= $user_stats['new_this_month'] ?? 0 ?></span>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                <i data-lucide="activity" class="w-4 h-4 mr-2 text-green-600"></i>
                System Status
            </h3>
            <div class="space-y-3">
                <div class="flex items-center">
                    <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                    <span class="text-sm text-gray-600">Messaging: <strong class="text-green-600">Online</strong></span>
                </div>
                <div class="flex items-center">
                    <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                    <span class="text-sm text-gray-600">Notifications: <strong class="text-green-600">Online</strong></span>
                </div>
                <div class="flex items-center">
                    <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                    <span class="text-sm text-gray-600">Database: <strong class="text-green-600">Online</strong></span>
                </div>
                <div class="flex items-center pt-2 border-t mt-3">
                    <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                    <span class="text-sm text-gray-600">API: <strong class="text-green-600">Operational</strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Additional Dashboard Content -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 md:gap-8">
        <!-- Left Column -->
        <div class="xl:col-span-2 space-y-6 md:space-y-8">
            <!-- Project Statistics -->
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between mb-4 md:mb-6">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800">Project Growth</h3>
                    <span class="text-xs md:text-sm text-gray-600">Last 12 months</span>
                </div>
                
                <div class="h-48 md:h-64">
                    <canvas id="clientChart"></canvas>
                </div>
                
                <div class="mt-4 grid grid-cols-3 gap-4 pt-4 border-t">
                    <div class="text-center">
                        <p class="text-xs text-gray-600">Total</p>
                        <p class="text-lg font-bold text-gray-900"><?= $project_stats['total'] ?? 0 ?></p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-600">Active</p>
                        <p class="text-lg font-bold text-green-600"><?= $project_stats['active'] ?? 0 ?></p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-600">Completed</p>
                        <p class="text-lg font-bold text-blue-600"><?= $project_stats['completed'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

                        <!-- Bottom Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <!-- Site Health -->
                            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                                <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-2">Site Health</h3>
                                <p class="text-xs md:text-sm text-gray-600 mb-4 md:mb-6">Check your site health score</p>
                                
                                <div class="relative w-24 h-24 md:w-32 md:h-32 mx-auto mb-4">
                                    <canvas id="siteHealthChart"></canvas>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-xl md:text-2xl font-bold text-gray-800"><?= $site_health['score'] ?>%</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-3 h-3 bg-indigo-500 rounded-full"></div>
                                            <span class="text-xs md:text-sm text-gray-600">Project Health</span>
                                        </div>
                                        <span class="text-xs md:text-sm font-medium"><?= $site_health['project_health'] ?>%</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                            <span class="text-xs md:text-sm text-gray-600">Task Completion</span>
                                        </div>
                                        <span class="text-xs md:text-sm font-medium"><?= $site_health['task_health'] ?>%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Task Completion Rate -->
                            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                                <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-2">Task Completion</h3>
                                <p class="text-xs md:text-sm text-gray-600 mb-4 md:mb-6">Overall task completion rate</p>
                                
                                <div class="relative w-24 h-24 md:w-32 md:h-32 mx-auto mb-4">
                                    <canvas id="taskCompletionChart"></canvas>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center">
                                            <?php 
                                            $completionRate = $task_stats['total'] > 0 ? round(($task_stats['completed'] / $task_stats['total']) * 100) : 0;
                                            ?>
                                            <div class="text-xl md:text-2xl font-bold text-gray-800"><?= $completionRate ?>%</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-center">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-xs md:text-sm text-gray-600"><?= $task_stats['completed'] ?> of <?= $task_stats['total'] ?> tasks</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Daily Tasks -->
                    <div class="space-y-4 md:space-y-6">
                        <!-- Quick Actions -->
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-4 md:p-6 text-white">
                            <h3 class="text-base md:text-lg font-semibold mb-2">Quick Actions</h3>
                            <p class="text-xs md:text-sm opacity-90 mb-4">Manage your projects efficiently</p>
                            <div class="space-y-2">
                                <a href="<?= base_url('admin/projects/create') ?>" class="block bg-white text-indigo-600 px-3 md:px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors text-sm text-center">
                                    <i data-lucide="plus" class="w-4 h-4 inline-block mr-1"></i> New Project
                                </a>
                                <a href="<?= base_url('admin/tasks/create') ?>" class="block bg-indigo-500 text-white px-3 md:px-4 py-2 rounded-lg font-medium hover:bg-indigo-400 transition-colors text-sm text-center">
                                    <i data-lucide="check-square" class="w-4 h-4 inline-block mr-1"></i> New Task
                                </a>
                            </div>
                        </div>

                        <!-- Daily Tasks -->
                        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-2">Today's Tasks</h3>
                            <p class="text-xs md:text-sm text-gray-600 mb-4 md:mb-6">Your tasks for today</p>
                            
                            <?php if (empty($daily_tasks)): ?>
                                <div class="text-center py-8">
                                    <i data-lucide="check-circle" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                                    <p class="text-gray-500 text-sm">No tasks scheduled for today</p>
                                </div>
                            <?php else: ?>
                                <div class="space-y-2 md:space-y-3">
                                    <?php 
                                    $colors = ['indigo', 'yellow', 'green', 'purple', 'red', 'blue'];
                                    $colorIndex = 0;
                                    foreach ($daily_tasks as $task): 
                                        $color = $colors[$colorIndex % count($colors)];
                                        $colorIndex++;
                                        $statusColor = $task['status'] === 'in_progress' ? $color . '-600' : 'gray-400';
                                    ?>
                                        <div class="bg-<?= $color ?>-500 rounded-lg p-2 md:p-3">
                                            <div class="text-white font-medium text-sm md:text-base"><?= esc($task['title']) ?></div>
                                            <div class="text-<?= $color ?>-200 text-xs md:text-sm">
                                                <?= esc($task['project_name']) ?>
                                                <?php if ($task['planned_start_date']): ?>
                                                    • <?= date('h:i A', strtotime($task['planned_start_date'])) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Daily Milestones -->
                        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-2">Today's Milestones</h3>
                            <p class="text-xs md:text-sm text-gray-600 mb-4 md:mb-6">Your milestones for today</p>
                            
                            <?php if (empty($daily_milestones)): ?>
                                <div class="text-center py-8">
                                    <i data-lucide="flag" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                                    <p class="text-gray-500 text-sm">No milestones scheduled for today</p>
                                </div>
                            <?php else: ?>
                                <div class="space-y-2 md:space-y-3">
                                    <?php 
                                    $milestoneColors = ['red', 'pink', 'rose', 'orange', 'amber'];
                                    $milestoneIndex = 0;
                                    foreach ($daily_milestones as $milestone): 
                                        $color = $milestoneColors[$milestoneIndex % count($milestoneColors)];
                                        $milestoneIndex++;
                                    ?>
                                        <div class="bg-<?= $color ?>-600 rounded-lg p-2 md:p-3 border-l-4 border-<?= $color ?>-800">
                                            <div class="text-white font-semibold text-sm md:text-base flex items-center">
                                                <i data-lucide="flag" class="w-4 h-4 mr-2"></i>
                                                <?= esc($milestone['title']) ?>
                                            </div>
                                            <div class="text-<?= $color ?>-100 text-xs md:text-sm mt-1">
                                                <?= esc($milestone['project_name']) ?>
                                                <?php if ($milestone['planned_start_date']): ?>
                                                    • <?= date('h:i A', strtotime($milestone['planned_start_date'])) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const clientChartCanvas = document.getElementById('clientChart');
    if (clientChartCanvas) {
        const clientStats = <?= json_encode($client_stats) ?>;
        new Chart(clientChartCanvas, {
            type: 'line',
            data: {
                labels: clientStats.map(item => item.month),
                datasets: [{
                    label: 'Projects Created',
                    data: clientStats.map(item => item.count),
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }
    const siteHealthCanvas = document.getElementById('siteHealthChart');
    if (siteHealthCanvas) {
        const siteHealth = <?= json_encode($site_health) ?>;
        new Chart(siteHealthCanvas, {
            type: 'doughnut',
            data: { datasets: [{ data: [siteHealth.score, 100 - siteHealth.score], backgroundColor: ['#4F46E5', '#E5E7EB'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: true, cutout: '75%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
        });
    }
    const taskCompletionCanvas = document.getElementById('taskCompletionChart');
    if (taskCompletionCanvas) {
        const completionRate = <?= $task_stats['total'] > 0 ? round(($task_stats['completed'] / $task_stats['total']) * 100) : 0 ?>;
        new Chart(taskCompletionCanvas, {
            type: 'doughnut',
            data: { datasets: [{ data: [completionRate, 100 - completionRate], backgroundColor: ['#10B981', '#E5E7EB'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: true, cutout: '75%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
        });
    }
});
</script>
<?= $this->endSection() ?>
