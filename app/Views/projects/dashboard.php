<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Project Dashboard - <?= esc($project['name']) ?><?= $this->endSection() ?>

<?= $this->section('head') ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= esc($project['name']) ?></h1>
            <p class="text-gray-600"><?= esc($project['project_code']) ?> | <?= esc($project['client_name'] ?? 'No Client') ?></p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/projects/gantt/' . $project['id']) ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="bar-chart-3" class="w-4 h-4 mr-2"></i>
                Gantt Chart
            </a>
            <a href="<?= base_url('admin/projects/team/' . $project['id']) ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                Team (<?= count($project['team_members'] ?? []) ?>)
            </a>
            <a href="<?= base_url('admin/projects/edit/' . $project['id']) ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit Project
            </a>
        </div>
    </div>

    <!-- Project Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">Progress</p>
                    <p class="text-2xl font-bold text-gray-900"><?= round($project['progress_percentage'] ?? 0, 1) ?>%</p>
                    <div class="mt-2 bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: <?= $project['progress_percentage'] ?? 0 ?>%"></div>
                    </div>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wide">Budget Status</p>
                    <p class="text-2xl font-bold text-gray-900"><?= round($budget_tracking['budget_utilization'] ?? 0, 1) ?>%</p>
                    <p class="text-sm text-gray-500">
                        <?= number_format($budget_tracking['actual_cost'] ?? 0) ?> / <?= number_format($budget_tracking['estimated_budget'] ?? 0) ?>
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Tasks</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?= $task_summary['completed_tasks'] ?? 0 ?>/<?= $task_summary['total_tasks'] ?? 0 ?>
                    </p>
                    <p class="text-sm text-gray-500">Completed</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-square" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wide">Milestones</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?= $milestone_progress['completed'] ?? 0 ?>/<?= $milestone_progress['total'] ?? 0 ?>
                    </p>
                    <p class="text-sm text-gray-500"><?= round($milestone_progress['completion_rate'] ?? 0, 1) ?>% Complete</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="flag" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Task Status Distribution Chart -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Task Status Distribution</h3>
                </div>
                <div class="p-6">
                    <div class="relative h-64">
                        <canvas id="taskStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Budget Tracking Chart -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Budget vs Actual Spending</h3>
                </div>
                <div class="p-6">
                    <div class="relative h-64">
                        <canvas id="budgetChart"></canvas>
                    </div>
                    <div class="mt-6 grid grid-cols-3 gap-4 text-center">
                        <div class="border-r border-gray-200">
                            <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">Estimated Budget</p>
                            <p class="text-lg font-bold text-gray-900">
                                <?= number_format($budget_tracking['estimated_budget'] ?? 0) ?> <?= $project['currency'] ?? 'USD' ?>
                            </p>
                        </div>
                        <div class="border-r border-gray-200">
                            <p class="text-xs font-semibold text-green-600 uppercase tracking-wide">Actual Cost</p>
                            <p class="text-lg font-bold text-gray-900">
                                <?= number_format($budget_tracking['actual_cost'] ?? 0) ?> <?= $project['currency'] ?? 'USD' ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold <?= ($budget_tracking['budget_variance'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' ?> uppercase tracking-wide">
                                <?= ($budget_tracking['budget_variance'] ?? 0) >= 0 ? 'Under Budget' : 'Over Budget' ?>
                            </p>
                            <p class="text-lg font-bold text-gray-900">
                                <?= number_format(abs($budget_tracking['budget_variance'] ?? 0)) ?> <?= $project['currency'] ?? 'USD' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
                </div>
                <div class="p-6">
                    <?php if (!empty($recent_activities)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recent_activities as $activity): ?>
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-2 h-2 bg-indigo-600 rounded-full mt-2"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900"><?= esc($activity['activity']) ?></h4>
                                    <p class="text-sm text-gray-500"><?= esc($activity['user']) ?></p>
                                    <p class="text-xs text-gray-400"><?= date('M d, Y H:i', strtotime($activity['timestamp'])) ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i data-lucide="clock" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                            <p class="text-gray-500">No recent activities.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column (1/3 width) -->
        <div class="space-y-6">
            <!-- Project Information -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Project Details</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= getStatusBadgeClass($project['status'] ?? 'active', 'project') ?>">
                            <?= ucfirst(str_replace('_', ' ', $project['status'] ?? 'Active')) ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Priority:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= getPriorityBadgeClass($project['priority'] ?? 'medium') ?>">
                            <?= ucfirst($project['priority'] ?? 'Medium') ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Start Date:</span>
                        <span class="text-sm text-gray-900"><?= date('M d, Y', strtotime($project['start_date'] ?? date('Y-m-d'))) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">End Date:</span>
                        <span class="text-sm text-gray-900"><?= date('M d, Y', strtotime($project['planned_end_date'] ?? date('Y-m-d'))) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Manager:</span>
                        <span class="text-sm text-gray-900"><?= esc($project['project_manager_name'] ?? 'Unassigned') ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Supervisor:</span>
                        <span class="text-sm text-gray-900"><?= esc($project['site_supervisor_name'] ?? 'Unassigned') ?></span>
                    </div>
                </div>
            </div>

            <!-- Overdue Tasks Alert -->
            <?php if (!empty($overdue_tasks)): ?>
            <div class="bg-white rounded-lg shadow-sm border border-l-4 border-l-red-500">
                <div class="p-6 bg-red-50 border-b">
                    <h3 class="text-lg font-semibold text-red-800 flex items-center">
                        <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
                        Overdue Tasks (<?= count($overdue_tasks) ?>)
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <?php foreach (array_slice($overdue_tasks, 0, 5) as $task): ?>
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900"><?= esc($task['title']) ?></h4>
                            <p class="text-xs text-red-600">Due: <?= date('M d, Y', strtotime($task['planned_end_date'])) ?></p>
                        </div>
                        <a href="<?= base_url('admin/tasks/' . $task['id']) ?>" class="ml-2 text-xs text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($overdue_tasks) > 5): ?>
                    <div class="text-center pt-4 border-t">
                        <a href="<?= base_url('admin/tasks?project=' . $project['id'] . '&status=overdue') ?>" class="text-sm text-red-600 hover:text-red-800 font-medium">
                            View All Overdue Tasks
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Upcoming Milestones -->
            <?php if (!empty($upcoming_milestones)): ?>
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Upcoming Milestones</h3>
                </div>
                <div class="p-6 space-y-4">
                    <?php foreach ($upcoming_milestones as $milestone): ?>
                    <div class="space-y-2">
                        <div class="flex justify-between items-start">
                            <h4 class="text-sm font-medium text-gray-900"><?= esc($milestone['title']) ?></h4>
                        </div>
                        <p class="text-xs text-gray-500">Due: <?= date('M d, Y', strtotime($milestone['planned_end_date'])) ?></p>
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: <?= $milestone['progress_percentage'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Team Summary -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Team Summary</h3>
                </div>
                <div class="p-6">
                    <div class="text-center mb-4">
                        <div class="text-3xl font-bold text-gray-900"><?= $team_stats['total_members'] ?? 0 ?></div>
                        <div class="text-sm text-gray-500">Total Team Members</div>
                    </div>
                    <?php if (!empty($team_stats['role_distribution'])): ?>
                    <div class="space-y-2">
                        <?php foreach ($team_stats['role_distribution'] as $role): ?>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600"><?= esc($role['role']) ?></span>
                            <span class="font-medium text-gray-900"><?= $role['count'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="mt-4">
                        <a href="<?= base_url('admin/projects/team/' . $project['id']) ?>" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                            <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                            Manage Team
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Task Status Distribution Chart
const taskCtx = document.getElementById('taskStatusChart').getContext('2d');
const taskStatusChart = new Chart(taskCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'In Progress', 'Not Started', 'On Hold'],
        datasets: [{
            data: [
                <?= $task_summary['completed_tasks'] ?? 0 ?>,
                <?= $task_summary['in_progress_tasks'] ?? 0 ?>,
                <?= $task_summary['not_started_tasks'] ?? 0 ?>,
                <?= $task_summary['on_hold_tasks'] ?? 0 ?>
            ],
            backgroundColor: [
                '#10B981', // green
                '#3B82F6', // blue
                '#6B7280', // gray
                '#F59E0B'  // yellow
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            }
        }
    }
});

// Budget Chart
const budgetCtx = document.getElementById('budgetChart').getContext('2d');
const budgetChart = new Chart(budgetCtx, {
    type: 'bar',
    data: {
        labels: ['Budget vs Actual'],
        datasets: [
            {
                label: 'Estimated Budget',
                data: [<?= $budget_tracking['estimated_budget'] ?? 0 ?>],
                backgroundColor: '#3B82F6',
                borderColor: '#2563EB',
                borderWidth: 1
            },
            {
                label: 'Actual Cost',
                data: [<?= $budget_tracking['actual_cost'] ?? 0 ?>],
                backgroundColor: '#10B981',
                borderColor: '#059669',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat().format(value);
                    }
                }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
