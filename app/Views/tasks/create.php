<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Create New Task<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Task</h1>
                <p class="text-gray-600">Add a new task to your project</p>
            </div>
            <div>
                <a href="<?php echo base_url(); ?>/admin/tasks" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Tasks
                </a>
            </div>
        </div>
    </div>

    <!-- Task Creation Form -->
    <div class="flex justify-center">
        <div class="w-full max-w-6xl">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Task Information</h3>
                </div>
                <div class="p-6">
                    <?php if (session('errors')): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/tasks/store') ?>" method="POST" id="taskForm">
                        <?= csrf_field() ?>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div>
                                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project <span class="text-red-500">*</span></label>
                                    <select name="project_id" id="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required onchange="updateTaskCode()">
                                        <option value="">Select Project</option>
                                        <?php foreach ($projects as $project): ?>
                                        <option value="<?= $project['id'] ?>" <?= $selected_project == $project['id'] ? 'selected' : '' ?>>
                                            <?= esc($project['name']) ?> (<?= esc($project['project_code']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label for="task_code" class="block text-sm font-medium text-gray-700 mb-2">Task Code</label>
                                    <input type="text" name="task_code" id="task_code" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg" value="<?= old('task_code', $task_code) ?>" readonly>
                                    <p class="mt-1 text-sm text-gray-500">Auto-generated based on selected project</p>
                                </div>

                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Task Title <span class="text-red-500">*</span></label>
                                    <input type="text" name="title" id="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('title') ?>" required>
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea name="description" id="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="4"><?= old('description') ?></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="task_type" class="block text-sm font-medium text-gray-700 mb-2">Task Type <span class="text-red-500">*</span></label>
                                        <select name="task_type" id="task_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                            <option value="task" <?= old('task_type') === 'task' ? 'selected' : '' ?>>Regular Task</option>
                                            <option value="milestone" <?= old('task_type') === 'milestone' ? 'selected' : '' ?>>Milestone</option>
                                            <option value="subtask" <?= old('task_type') === 'subtask' ? 'selected' : '' ?>>Subtask</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority <span class="text-red-500">*</span></label>
                                        <select name="priority" id="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                            <option value="low" <?= old('priority') === 'low' ? 'selected' : '' ?>>Low</option>
                                            <option value="medium" <?= old('priority', 'medium') === 'medium' ? 'selected' : '' ?>>Medium</option>
                                            <option value="high" <?= old('priority') === 'high' ? 'selected' : '' ?>>High</option>
                                            <option value="critical" <?= old('priority') === 'critical' ? 'selected' : '' ?>>Critical</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Assign To</label>
                                    <select name="assigned_to" id="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select User</option>
                                        <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= old('assigned_to') == $user['id'] ? 'selected' : '' ?>>
                                            <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                            <?php if (!empty($user['employee_id'])): ?>
                                            (<?= esc($user['employee_id']) ?>)
                                            <?php endif; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="planned_start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="planned_start_date" id="planned_start_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('planned_start_date', date('Y-m-d')) ?>" required>
                                    </div>
                                    <div>
                                        <label for="planned_end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="planned_end_date" id="planned_end_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('planned_end_date') ?>" required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="estimated_hours" class="block text-sm font-medium text-gray-700 mb-2">Estimated Hours</label>
                                        <input type="number" name="estimated_hours" id="estimated_hours" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" step="0.5" value="<?= old('estimated_hours', 0) ?>">
                                    </div>
                                    <div>
                                        <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-2">Estimated Cost</label>
                                        <input type="number" name="estimated_cost" id="estimated_cost" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" step="0.01" value="<?= old('estimated_cost', 0) ?>">
                                    </div>
                                </div>

                                <div>
                                    <label for="parent_task_id" class="block text-sm font-medium text-gray-700 mb-2">Parent Task</label>
                                    <select name="parent_task_id" id="parent_task_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">No Parent Task</option>
                                        <!-- Will be populated based on selected project -->
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">Select a parent task if this is a subtask</p>
                                </div>

                                <div>
                                    <label for="depends_on" class="block text-sm font-medium text-gray-700 mb-2">Dependencies</label>
                                    <select name="depends_on[]" id="depends_on" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" multiple>
                                        <!-- Will be populated based on selected project -->
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">Hold Ctrl/Cmd to select multiple dependencies</p>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_critical_path" id="is_critical_path" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= old('is_critical_path') ? 'checked' : '' ?>>
                                        <label for="is_critical_path" class="ml-2 text-sm text-gray-700">
                                            Critical Path Task
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="checkbox" name="requires_approval" id="requires_approval" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= old('requires_approval') ? 'checked' : '' ?>>
                                        <label for="requires_approval" class="ml-2 text-sm text-gray-700">
                                            Requires Approval
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_billable" id="is_billable" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= old('is_billable') ? 'checked' : '' ?>>
                                        <label for="is_billable" class="ml-2 text-sm text-gray-700">
                                            Billable Task
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <a href="<?= base_url('admin/tasks') ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i> Cancel
                                </a>
                                <div class="flex gap-3">
                                    <button type="button" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 transition-colors" onclick="saveAsDraft()">
                                        <i class="fas fa-save mr-2"></i> Save as Draft
                                    </button>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i> Create Task
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum end date based on start date
    document.getElementById('planned_start_date').addEventListener('change', function() {
        document.getElementById('planned_end_date').min = this.value;
    });

    // Initialize with current start date
    const startDateInput = document.getElementById('planned_start_date');
    if (startDateInput.value) {
        document.getElementById('planned_end_date').min = startDateInput.value;
    }

    // Load project tasks when project is selected
    document.getElementById('project_id').addEventListener('change', function() {
        loadProjectTasks(this.value);
        updateTaskCode();
    });

    // Initial load if project is pre-selected
    const projectSelect = document.getElementById('project_id');
    if (projectSelect.value) {
        loadProjectTasks(projectSelect.value);
    }
});

function updateTaskCode() {
    const projectId = document.getElementById('project_id').value;
    if (!projectId) {
        document.getElementById('task_code').value = '';
        return;
    }

    // Generate task code (this would typically be done on the server)
    fetch(`<?= base_url('admin/tasks/generate-code') ?>?project_id=${projectId}`)
        .then(response => response.text())
        .then(taskCode => {
            document.getElementById('task_code').value = taskCode;
        })
        .catch(error => {
            console.error('Error generating task code:', error);
        });
}

function loadProjectTasks(projectId) {
    if (!projectId) {
        document.getElementById('parent_task_id').innerHTML = '<option value="">No Parent Task</option>';
        document.getElementById('depends_on').innerHTML = '';
        return;
    }

    // Load tasks for dependencies and parent task selection
    fetch(`<?= base_url('admin/tasks/by-project') ?>/${projectId}`)
        .then(response => response.json())
        .then(tasks => {
            // Update parent task options
            const parentSelect = document.getElementById('parent_task_id');
            parentSelect.innerHTML = '<option value="">No Parent Task</option>';
            
            // Update dependencies options
            const dependsSelect = document.getElementById('depends_on');
            dependsSelect.innerHTML = '';
            
            tasks.forEach(task => {
                // Add to parent task options (exclude milestones)
                if (task.task_type !== 'milestone') {
                    const parentOption = document.createElement('option');
                    parentOption.value = task.id;
                    parentOption.textContent = task.title;
                    parentSelect.appendChild(parentOption);
                }
                
                // Add to dependencies options
                const dependsOption = document.createElement('option');
                dependsOption.value = task.id;
                dependsOption.textContent = task.title + ' (' + task.task_code + ')';
                dependsSelect.appendChild(dependsOption);
            });
        })
        .catch(error => {
            console.error('Error loading project tasks:', error);
        });
}

function saveAsDraft() {
    // Add a hidden field to indicate this is a draft
    const form = document.getElementById('taskForm');
    const draftInput = document.createElement('input');
    draftInput.type = 'hidden';
    draftInput.name = 'save_as_draft';
    draftInput.value = '1';
    form.appendChild(draftInput);
    
    form.submit();
}

// Form validation
document.getElementById('taskForm').addEventListener('submit', function(e) {
    const startDate = new Date(document.getElementById('planned_start_date').value);
    const endDate = new Date(document.getElementById('planned_end_date').value);
    
    if (endDate < startDate) {
        e.preventDefault();
        alert('End date cannot be before start date.');
        return false;
    }
    
    const estimatedHours = parseFloat(document.getElementById('estimated_hours').value);
    const estimatedCost = parseFloat(document.getElementById('estimated_cost').value);
    
    if (estimatedHours < 0 || estimatedCost < 0) {
        e.preventDefault();
        alert('Estimated hours and cost cannot be negative.');
        return false;
    }
});
</script>

<?= $this->endSection() ?>
