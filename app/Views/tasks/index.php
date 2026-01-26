<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Tasks<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h1 class="text-3xl font-bold text-gray-900">Tasks</h1>
            <div class="flex flex-wrap gap-2">
                <a href="<?php  echo base_url() ; ?>/admin/tasks/create" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i> New Task
                </a>
                <a href="<?php  echo base_url() ; ?>/admin/tasks/calendar" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 border border-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50 transition-colors">
                    <i class="fas fa-calendar mr-2"></i> Calendar View
                </a>
                <a href="<?= base_url('admin/tasks/my-tasks') ?>" class="inline-flex items-center px-4 py-2 bg-white text-green-600 border border-green-600 text-sm font-medium rounded-lg hover:bg-green-50 transition-colors">
                    <i class="fas fa-user mr-2"></i> My Tasks
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
                        <a href="<?= base_url('admin/tasks') ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-600 border border-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">All Tasks</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto" id="tasksTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($tasks as $task): ?>
                        <tr class="<?= getTaskRowClass($task) === 'table-warning' ? 'bg-yellow-50' : (getTaskRowClass($task) === 'table-success' ? 'bg-green-50' : '') ?> hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <?php if ($task['task_type'] === 'milestone'): ?>
                                    <i class="fas fa-flag text-red-500 mr-3"></i>
                                    <?php elseif ($task['is_critical_path']): ?>
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
                                    <?php if ($task['planned_end_date'] < date('Y-m-d') && $task['status'] !== 'completed'): ?>
                                    <div class="text-red-600 flex items-center mt-1">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> 
                                        <?= floor((time() - strtotime($task['planned_end_date'])) / 86400) ?> days overdue
                                    </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                <span class="text-gray-500">No due date</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="relative inline-block text-left">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="menu-button-<?= $task['id'] ?>" onclick="toggleDropdown(<?= $task['id'] ?>)">
                                        Actions
                                        <i class="fas fa-chevron-down ml-2"></i>
                                    </button>
                                    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden z-10" id="dropdown-<?= $task['id'] ?>">
                                        <div class="py-1">
                                            <a href="<?= base_url('admin/tasks/' . $task['id']) ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-eye mr-3"></i> View Details
                                            </a>
                                            <a href="<?= base_url('admin/tasks/' . $task['id'] . '/edit') ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit mr-3"></i> Edit
                                            </a>
                                            <hr class="my-1 border-gray-200">
                                            <?php if ($task['status'] !== 'completed'): ?>
                                            <a href="#" onclick="updateTaskStatus(<?= $task['id'] ?>, 'in_progress')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-play mr-3"></i> Start Task
                                            </a>
                                            <a href="#" onclick="updateTaskStatus(<?= $task['id'] ?>, 'completed')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-check mr-3"></i> Mark Complete
                                            </a>
                                            <?php endif; ?>
                                            <form method="POST" action="<?= base_url('admin/tasks/' . $task['id']) ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100 w-full text-left">
                                                    <i class="fas fa-trash mr-3"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                <?= $pager->links() ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Status Update Modal -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="statusUpdateModal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Task Status</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="statusUpdateForm">
                <div class="mb-4">
                    <label for="task_status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="task_status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="not_started">Not Started</option>
                        <option value="in_progress">In Progress</option>
                        <option value="review">Under Review</option>
                        <option value="completed">Completed</option>
                        <option value="on_hold">On Hold</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label for="task_progress" class="block text-sm font-medium text-gray-700 mb-2">Progress (%)</label>
                    <input type="number" id="task_progress" name="progress_percentage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" max="100" value="0">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentTaskId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#tasksTable').DataTable({
        "pageLength": 25,
        "order": [[ 6, "asc" ]], // Sort by due date
        "columnDefs": [
            { "orderable": false, "targets": [7] } // Disable sorting for actions column
        ]
    });

    // Status update form
    document.getElementById('statusUpdateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(`<?= base_url('admin/tasks') ?>/${currentTaskId}/status`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating task status.');
        });
    });

    // Close modal when clicking outside
    document.getElementById('statusUpdateModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
});

function toggleDropdown(taskId) {
    const dropdown = document.getElementById(`dropdown-${taskId}`);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== `dropdown-${taskId}`) {
            d.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('[id^="menu-button-"]') && !e.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
            d.classList.add('hidden');
        });
    }
});

function updateTaskStatus(taskId, status = null) {
    currentTaskId = taskId;
    
    if (status) {
        // Quick status update
        const formData = new FormData();
        formData.append('status', status);
        formData.append('progress_percentage', status === 'completed' ? 100 : 0);
        
        fetch(`<?= base_url('admin/tasks') ?>/${taskId}/status`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating task status.');
        });
    } else {
        // Open modal for detailed update
        document.getElementById('statusUpdateModal').classList.remove('hidden');
    }
}

function closeModal() {
    document.getElementById('statusUpdateModal').classList.add('hidden');
}

</script>

<?= $this->endSection() ?>