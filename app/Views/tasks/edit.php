<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= isset($task) ? 'Edit Task' : 'Create New Task' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900"><?= isset($task) ? 'Edit Task' : 'Create New Task' ?></h1>
            <a href="<?= base_url('admin/tasks') ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Tasks
            </a>
        </div>
        <div class="p-6">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <form action="<?= isset($task) ? base_url('admin/tasks/' . $task['id']) : base_url('admin/tasks/store') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Basic Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Task Title *</label>
                            <input type="text" id="title" name="title"
                                   value="<?= old('title', isset($task) && isset($task['title']) ? $task['title'] : '') ?>" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= old('description', isset($task) && isset($task['description']) ? $task['description'] : '') ?></textarea>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project *</label>
                            <select id="project_id" name="project_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Project</option>
                                <?php foreach($projects as $project): ?>
                                    <option value="<?= $project['id'] ?>"
                                            <?= old('project_id', isset($task) && isset($task['project_id']) ? $task['project_id'] : ($_GET['project_id'] ?? '')) == $project['id'] ? 'selected' : '' ?>>
                                        <?= esc($project['name']) ?> (<?= esc($project['project_code']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="parent_task_id" class="block text-sm font-medium text-gray-700 mb-2">Parent Task</label>
                            <select id="parent_task_id" name="parent_task_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">No Parent Task</option>
                                <!-- Will be populated dynamically -->
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending" <?= old('status', isset($task) && isset($task['status']) ? $task['status'] : 'pending') == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="in_progress" <?= old('status', isset($task) && isset($task['status']) ? $task['status'] : 'pending') == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="review" <?= old('status', isset($task) && isset($task['status']) ? $task['status'] : 'pending') == 'review' ? 'selected' : '' ?>>Under Review</option>
                            <option value="completed" <?= old('status', isset($task) && isset($task['status']) ? $task['status'] : 'pending') == 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= old('status', isset($task) && isset($task['status']) ? $task['status'] : 'pending') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select id="priority" name="priority"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="low" <?= old('priority', isset($task) && isset($task['priority']) ? $task['priority'] : 'medium') == 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= old('priority', isset($task) && isset($task['priority']) ? $task['priority'] : 'medium') == 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= old('priority', isset($task) && isset($task['priority']) ? $task['priority'] : 'medium') == 'high' ? 'selected' : '' ?>>High</option>
                            <option value="urgent" <?= old('priority', isset($task) && isset($task['priority']) ? $task['priority'] : 'medium') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label for="task_type" class="block text-sm font-medium text-gray-700 mb-2">Task Type</label>
                        <select id="task_type" name="task_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="task" <?= old('task_type', isset($task) && isset($task['task_type']) ? $task['task_type'] : 'task') == 'task' ? 'selected' : '' ?>>Regular Task</option>
                            <option value="milestone" <?= old('task_type', isset($task) && isset($task['task_type']) ? $task['task_type'] : 'task') == 'milestone' ? 'selected' : '' ?>>Milestone</option>
                        </select>
                    </div>

                    <div>
                        <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-2">Progress (%)</label>
                        <input type="number" id="progress_percentage" name="progress_percentage"
                               min="0" max="100" value="<?= old('progress_percentage', isset($task) && isset($task['progress_percentage']) ? $task['progress_percentage'] : 0) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Assignment & Timeline -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Assignment & Timeline</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                            <select id="assigned_to" name="assigned_to"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Unassigned</option>
                                <?php foreach($users as $user): ?>
                                    <option value="<?= $user['id'] ?>"
                                            <?= old('assigned_to', isset($task) && isset($task['assigned_to']) ? $task['assigned_to'] : '') == $user['id'] ? 'selected' : '' ?>>
                                        <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="planned_start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" id="planned_start_date" name="planned_start_date"
                                   value="<?= old('planned_start_date', isset($task) && isset($task['planned_start_date']) ? $task['planned_start_date'] : '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="planned_end_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                            <input type="date" id="planned_end_date" name="planned_end_date"
                                   value="<?= old('planned_end_date', isset($task) && isset($task['planned_end_date']) ? $task['planned_end_date'] : '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Effort Estimation -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Effort Estimation</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="estimated_hours" class="block text-sm font-medium text-gray-700 mb-2">Estimated Hours</label>
                            <input type="number" id="estimated_hours" name="estimated_hours"
                                   step="0.5" min="0" value="<?= old('estimated_hours', isset($task) && isset($task['estimated_hours']) ? $task['estimated_hours'] : '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="actual_hours" class="block text-sm font-medium text-gray-700 mb-2">Actual Hours</label>
                            <input type="number" id="actual_hours" name="actual_hours"
                                   step="0.5" min="0" value="<?= old('actual_hours', isset($task) && isset($task['actual_hours']) ? $task['actual_hours'] : '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-2">Estimated Cost</label>
                            <input type="number" id="estimated_cost" name="estimated_cost"
                                   step="0.01" min="0" value="<?= old('estimated_cost', isset($task) && isset($task['estimated_cost']) ? $task['estimated_cost'] : '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Dependencies -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Dependencies</h2>
                    <div>
                        <label for="dependency_tasks" class="block text-sm font-medium text-gray-700 mb-2">Dependent Tasks</label>
                        <select id="dependency_tasks" name="dependency_tasks[]" multiple
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" style="height: 120px;">
                            <!-- Will be populated dynamically based on project selection -->
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Select tasks that must be completed before this task can start.</p>
                    </div>
                </div>

                <!-- File Attachments -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Attachments</h2>
                    <div>
                        <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">Upload Files</label>
                        <input type="file" id="attachments" name="attachments[]" multiple
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">
                            Allowed file types: PDF, DOC, XLS, PPT, Images, ZIP. Max size: 10MB per file.
                        </p>

                        <?php if (isset($task) && !empty($task_attachments)): ?>
                        <div class="mt-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Existing Attachments</h3>
                            <div class="space-y-2">
                                <?php foreach ($task_attachments as $attachment): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-file text-gray-500 mr-3"></i>
                                        <div>
                                            <a href="<?= base_url('admin/tasks/download/' . $attachment['id']) ?>" target="_blank" 
                                               class="text-blue-600 hover:text-blue-800 font-medium">
                                                <?= esc($attachment['original_name']) ?>
                                            </a>
                                            <p class="text-sm text-gray-500"><?= formatBytes($attachment['file_size'] ?? 0) ?></p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeAttachment(<?= $attachment['id'] ?>)" 
                                            class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tags -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Additional Information</h2>
                    <div class="space-y-6">
                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                            <input type="text" id="tags" name="tags"
                                   value="<?= old('tags', isset($task) && isset($task['tags']) ? $task['tags'] : '') ?>"
                                   placeholder="Enter tags separated by commas"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-sm text-gray-500 mt-1">e.g., construction, electrical, safety</p>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= old('notes', isset($task) && isset($task['notes']) ? $task['notes'] : '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="<?= base_url('admin/tasks') ?>" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-save mr-2"></i><?= isset($task) ? 'Update Task' : 'Create Task' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load parent tasks and dependencies when project changes
    const projectSelect = document.getElementById('project_id');
    const parentTaskSelect = document.getElementById('parent_task_id');
    const dependencyTasksSelect = document.getElementById('dependency_tasks');

    projectSelect.addEventListener('change', function() {
        const projectId = this.value;
        if (projectId) {
            loadProjectTasks(projectId);
        } else {
            parentTaskSelect.innerHTML = '<option value="">No Parent Task</option>';
            dependencyTasksSelect.innerHTML = '';
        }
    });

    // Load tasks on page load if project is pre-selected
    if (projectSelect.value) {
        loadProjectTasks(projectSelect.value);
    }

    // Validate dates
    const startDateInput = document.getElementById('planned_start_date');
    const dueDateInput = document.getElementById('planned_end_date');
    
    function validateDates() {
        const startDate = new Date(startDateInput.value);
        const dueDate = new Date(dueDateInput.value);
        
        if (startDate && dueDate && startDate > dueDate) {
            alert('Due date must be after start date');
            dueDateInput.value = '';
        }
    }

    startDateInput.addEventListener('change', validateDates);
    dueDateInput.addEventListener('change', validateDates);

    // Auto-calculate progress based on status
    const statusSelect = document.getElementById('status');
    const progressInput = document.getElementById('progress_percentage');
    
    statusSelect.addEventListener('change', function() {
        const status = this.value;
        
        if (status === 'completed' && progressInput.value < 100) {
            progressInput.value = 100;
        } else if (status === 'pending' && progressInput.value > 0) {
            progressInput.value = 0;
        } else if (status === 'in_progress' && progressInput.value == 0) {
            progressInput.value = 25;
        }
    });
});

function loadProjectTasks(projectId) {
    fetch(`<?= base_url('admin/tasks/by-project') ?>/${projectId}`)
        .then(response => response.json())
        .then(data => {
            const parentSelect = document.getElementById('parent_task_id');
            const depSelect = document.getElementById('dependency_tasks');
            
            // Clear existing options
            parentSelect.innerHTML = '<option value="">No Parent Task</option>';
            depSelect.innerHTML = '';
            
            if (data.tasks && data.tasks.length > 0) {
                data.tasks.forEach(task => {
                    // Exclude current task from both dropdowns if editing
                    <?php if (isset($task)): ?>
                    if (task.id != <?= $task['id'] ?>) {
                    <?php endif; ?>
                        const parentOption = document.createElement('option');
                        parentOption.value = task.id;
                        parentOption.textContent = task.title;
                        parentSelect.appendChild(parentOption);
                        
                        const depOption = document.createElement('option');
                        depOption.value = task.id;
                        depOption.textContent = task.title;
                        depSelect.appendChild(depOption);
                    <?php if (isset($task)): ?>
                    }
                    <?php endif; ?>
                });
            }

            // Restore selected values if editing
            <?php if (isset($task)): ?>
            if ('<?= $task['parent_task_id'] ?>') {
                parentSelect.value = '<?= $task['parent_task_id'] ?>';
            }
            <?php endif; ?>
        })
        .catch(error => {
            console.error('Failed to load project tasks:', error);
        });
}

function removeAttachment(attachmentId) {
    if (confirm('Are you sure you want to remove this attachment?')) {
        fetch(`<?= base_url('admin/tasks/remove-attachment') ?>/${attachmentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error removing attachment: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error removing attachment');
        });
    }
}

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
</script>
<?= $this->endSection() ?>
