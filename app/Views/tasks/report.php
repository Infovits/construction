<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Task Report<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Task Report</h1>
                <p class="text-gray-600 mt-1">Comprehensive analysis of all tasks across projects</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?= base_url('admin/tasks') ?>" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 border border-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Tasks
                </a>
                <a href="<?= base_url('admin/tasks/report/export/pdf') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-file-pdf mr-2"></i> Export PDF
                </a>
                <a href="<?= base_url('admin/tasks/report/export/excel') ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="project" class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                        <select name="project" id="project" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Projects</option>
                            <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>" <?= $filters['project'] == $project['id'] ? 'selected' : '' ?>>
                                <?= esc($project['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Statuses</option>
                            <option value="not_started" <?= $filters['status'] === 'not_started' ? 'selected' : '' ?>>Not Started</option>
                            <option value="in_progress" <?= $filters['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="review" <?= $filters['status'] === 'review' ? 'selected' : '' ?>>Under Review</option>
                            <option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="on_hold" <?= $filters['status'] === 'on_hold' ? 'selected' : '' ?>>On Hold</option>
                            <option value="cancelled" <?= $filters['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                        <select name="assigned_to" id="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Users</option>
                            <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= $filters['assigned_to'] == $user['id'] ? 'selected' : '' ?>>
                                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 border border-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50 transition-colors">
                            <i class="fas fa-filter mr-2"></i> Filter
                        </button>
                        <a href="<?= base_url('admin/tasks/report') ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-600 border border-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['total'] ?></p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-tasks text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-green-600"><?= $stats['completed'] ?></p>
                    <p class="text-xs text-gray-500 mt-1"><?= $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0 ?>%</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Overdue</p>
                    <p class="text-2xl font-bold text-red-600"><?= $stats['overdue'] ?></p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">In Progress</p>
                    <p class="text-2xl font-bold text-yellow-600"><?= $stats['in_progress'] ?></p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-spinner text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Task Details</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto" id="tasksTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Late</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($tasks)): ?>
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-chart-line text-4xl text-gray-300 mb-4"></i>
                                <div class="text-lg font-medium">No tasks found</div>
                                <div class="text-sm text-gray-400 mt-2">No tasks match the selected criteria.</div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php $counter = 1; ?>
                        <?php foreach ($tasks as $task): ?>
                        <?php if ($task['is_milestone']) continue; ?>
                        <tr class="<?= getTaskRowClass($task) === 'table-warning' ? 'bg-yellow-50' : (getTaskRowClass($task) === 'table-success' ? 'bg-green-50' : '') ?> hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?= $counter++ ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <?php if ($task['is_critical_path']): ?>
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                                    <?php else: ?>
                                    <i class="fas fa-tasks text-gray-400 mr-3"></i>
                                    <?php endif; ?>
                                    <div>
                                        <a href="<?= base_url('admin/tasks/' . $task['id']) ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                                            <?= esc($task['title']) ?>
                                        </a>
                                        <?php if (!empty($task['task_code'])): ?>
                                        <div class="text-sm text-gray-500"><?= esc($task['task_code']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="<?= base_url('admin/projects/' . $task['project_id']) ?>" class="text-blue-600 hover:text-blue-800">
                                    <?= esc($task['project_name']) ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($task['assigned_name']): ?>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8">
                                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                            <?= substr($task['assigned_name'], 0, 2) ?>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900"><?= esc($task['assigned_name']) ?></div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <span class="text-gray-500">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= getPriorityBadgeClass($task['priority']) === 'danger' ? 'bg-red-100 text-red-800' : (getPriorityBadgeClass($task['priority']) === 'warning' ? 'bg-yellow-100 text-yellow-800' : (getPriorityBadgeClass($task['priority']) === 'info' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) ?>">
                                    <?= ucfirst($task['priority']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= getStatusBadgeClass($task['status'], 'task') === 'success' ? 'bg-green-100 text-green-800' : (getStatusBadgeClass($task['status'], 'task') === 'warning' ? 'bg-yellow-100 text-yellow-800' : (getStatusBadgeClass($task['status'], 'task') === 'info' ? 'bg-blue-100 text-blue-800' : (getStatusBadgeClass($task['status'], 'task') === 'danger' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2.5 mr-3">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?= $task['progress_percentage'] ?>%"></div>
                                    </div>
                                    <span class="text-sm text-gray-900"><?= round($task['progress_percentage']) ?>%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if ($task['planned_end_date']): ?>
                                    <div class="text-gray-900"><?= date('M d, Y', strtotime($task['planned_end_date'])) ?></div>
                                <?php else: ?>
                                <span class="text-gray-500">No due date</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php
                                $daysLate = 0;
                                if ($task['planned_end_date'] && $task['status'] !== 'completed') {
                                    $dueDate = strtotime($task['planned_end_date']);
                                    $today = strtotime(date('Y-m-d'));
                                    if ($today > $dueDate) {
                                        $daysLate = floor(($today - $dueDate) / (60 * 60 * 24));
                                    }
                                } elseif ($task['actual_end_date'] && $task['planned_end_date']) {
                                    $dueDate = strtotime($task['planned_end_date']);
                                    $completedDate = strtotime($task['actual_end_date']);
                                    if ($completedDate > $dueDate) {
                                        $daysLate = floor(($completedDate - $dueDate) / (60 * 60 * 24));
                                    }
                                }
                                ?>
                                <?php if ($daysLate > 0): ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> <?= $daysLate ?> days
                                </span>
                                <?php elseif ($task['status'] === 'completed' && $daysLate <= 0): ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> On time
                                </span>
                                <?php else: ?>
                                <span class="text-gray-500">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#tasksTable').DataTable({
        "pageLength": 25,
        "order": [[ 7, "asc" ]], // Sort by due date
        "columnDefs": [
            { "orderable": false, "targets": [1, 2, 3, 4, 5, 6, 8] } // Disable sorting for specific columns
        ]
    });
});
</script>

<?= $this->endSection() ?>