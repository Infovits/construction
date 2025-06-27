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

            <form action="<?= isset($task) ? base_url('admin/tasks/update/' . $task['id']) : base_url('admin/tasks/store') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Basic Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Task Title *</label>
                            <input type="text" id="title" name="title" 
                                   value="<?= old('title', isset($task) ? $task['title'] : '') ?>" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= old('description', isset($task) ? $task['description'] : '') ?></textarea>
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
                                            <?= old('project_id', isset($task) ? $task['project_id'] : ($_GET['project_id'] ?? '')) == $project['id'] ? 'selected' : '' ?>>
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
                            <option value="pending" <?= old('status', isset($task) ? $task['status'] : 'pending') == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="in_progress" <?= old('status', isset($task) ? $task['status'] : 'pending') == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="review" <?= old('status', isset($task) ? $task['status'] : 'pending') == 'review' ? 'selected' : '' ?>>Under Review</option>
                            <option value="completed" <?= old('status', isset($task) ? $task['status'] : 'pending') == 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= old('status', isset($task) ? $task['status'] : 'pending') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select id="priority" name="priority"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="low" <?= old('priority', isset($task) ? $task['priority'] : 'medium') == 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= old('priority', isset($task) ? $task['priority'] : 'medium') == 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= old('priority', isset($task) ? $task['priority'] : 'medium') == 'high' ? 'selected' : '' ?>>High</option>
                            <option value="urgent" <?= old('priority', isset($task) ? $task['priority'] : 'medium') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label for="task_type" class="block text-sm font-medium text-gray-700 mb-2">Task Type</label>
                        <select id="task_type" name="task_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="task" <?= old('task_type', isset($task) ? $task['task_type'] : 'task') == 'task' ? 'selected' : '' ?>>Regular Task</option>
                            <option value="milestone" <?= old('task_type', isset($task) ? $task['task_type'] : 'task') == 'milestone' ? 'selected' : '' ?>>Milestone</option>
                        </select>
                    </div>

                    <div>
                        <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-2">Progress (%)</label>
                        <input type="number" id="progress_percentage" name="progress_percentage" 
                               min="0" max="100" value="<?= old('progress_percentage', isset($task) ? $task['progress_percentage'] : 0) ?>"
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
                                            <?= old('assigned_to', isset($task) ? $task['assigned_to'] : '') == $user['id'] ? 'selected' : '' ?>>
                                        <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" id="start_date" name="start_date" 
                                   value="<?= old('start_date', isset($task) ? $task['start_date'] : '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                            <input type="date" id="due_date" name="due_date" 
                                   value="<?= old('due_date', isset($task) ? $task['due_date'] : '') ?>"
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
                                   step="0.5" min="0" value="<?= old('estimated_hours', isset($task) ? $task['estimated_hours'] : '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="actual_hours" class="block text-sm font-medium text-gray-700 mb-2">Actual Hours</label>
                            <input type="number" id="actual_hours" name="actual_hours" 
                                   step="0.5" min="0" value="<?= old('actual_hours', isset($task) ? $task['actual_hours'] : '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-2">Estimated Cost</label>
                            <input type="number" id="estimated_cost" name="estimated_cost" 
                                   step="0.01" min="0" value="<?= old('estimated_cost', isset($task) ? $task['estimated_cost'] : '') ?>"
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
                                    <input type="file" class="form-control-file" id="attachments" name="attachments[]" multiple 
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                                    <small class="form-text text-muted">
                                        Allowed file types: PDF, DOC, XLS, PPT, Images, ZIP. Max size: 10MB per file.
                                    </small>
                                </div>

                                <?php if (isset($task) && !empty($task_attachments)): ?>
                                <div class="mt-3">
                                    <h6>Existing Attachments</h6>
                                    <ul class="list-unstyled">
                                        <?php foreach ($task_attachments as $attachment): ?>
                                        <li class="mb-2">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <i class="fas fa-file mr-2"></i>
                                                    <a href="<?= base_url('admin/tasks/download/' . $attachment['id']) ?>" target="_blank">
                                                        <?= esc($attachment['original_name']) ?>
                                                    </a>
                                                    <small class="text-muted">(<?= formatBytes($attachment['file_size']) ?>)</small>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeAttachment(<?= $attachment['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tags">Tags</label>
                                    <input type="text" class="form-control" id="tags" name="tags" 
                                           value="<?= old('tags', isset($task) ? $task['tags'] : '') ?>"
                                           placeholder="Enter tags separated by commas">
                                    <small class="form-text text-muted">e.g., construction, electrical, safety</small>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Additional Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes', isset($task) ? $task['notes'] : '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> <?= isset($task) ? 'Update Task' : 'Create Task' ?>
                                    </button>
                                    <a href="<?= base_url('admin/tasks') ?>" class="btn btn-secondary ml-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <?php if (isset($task)): ?>
                                    <a href="<?= base_url('admin/tasks/view/' . $task['id']) ?>" class="btn btn-info ml-2">
                                        <i class="fas fa-eye"></i> View Task
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Load parent tasks and dependencies when project changes
    $('#project_id').on('change', function() {
        var projectId = $(this).val();
        if (projectId) {
            loadProjectTasks(projectId);
        } else {
            $('#parent_task_id, #dependency_tasks').empty().append('<option value="">No tasks available</option>');
        }
    });

    // Load tasks on page load if project is pre-selected
    if ($('#project_id').val()) {
        loadProjectTasks($('#project_id').val());
    }

    // Validate dates
    $('#start_date, #due_date').on('change', function() {
        var startDate = new Date($('#start_date').val());
        var dueDate = new Date($('#due_date').val());
        
        if (startDate && dueDate && startDate > dueDate) {
            alert('Due date must be after start date');
            $(this).val('');
        }
    });

    // Auto-calculate progress based on status
    $('#status').on('change', function() {
        var status = $(this).val();
        var progressField = $('#progress_percentage');
        
        if (status === 'completed' && progressField.val() < 100) {
            progressField.val(100);
        } else if (status === 'pending' && progressField.val() > 0) {
            progressField.val(0);
        } else if (status === 'in_progress' && progressField.val() == 0) {
            progressField.val(25);
        }
    });
});

function loadProjectTasks(projectId) {
    $.get('<?= base_url('admin/tasks/api/project-tasks') ?>/' + projectId)
        .done(function(data) {
            // Update parent task dropdown
            var parentSelect = $('#parent_task_id');
            parentSelect.empty().append('<option value="">No Parent Task</option>');
            
            // Update dependency tasks dropdown
            var depSelect = $('#dependency_tasks');
            depSelect.empty();
            
            if (data.tasks && data.tasks.length > 0) {
                $.each(data.tasks, function(index, task) {
                    // Exclude current task from both dropdowns if editing
                    <?php if (isset($task)): ?>
                    if (task.id != <?= $task['id'] ?>) {
                    <?php endif; ?>
                        parentSelect.append('<option value="' + task.id + '">' + task.title + '</option>');
                        depSelect.append('<option value="' + task.id + '">' + task.title + '</option>');
                    <?php if (isset($task)): ?>
                    }
                    <?php endif; ?>
                });
            }

            // Restore selected values if editing
            <?php if (isset($task)): ?>
            if ('<?= $task['parent_task_id'] ?>') {
                parentSelect.val('<?= $task['parent_task_id'] ?>');
            }
            // Restore dependency selections (would need to be passed from controller)
            <?php endif; ?>
        })
        .fail(function() {
            console.error('Failed to load project tasks');
        });
}

function removeAttachment(attachmentId) {
    if (confirm('Are you sure you want to remove this attachment?')) {
        $.post('<?= base_url('admin/tasks/remove-attachment') ?>/' + attachmentId, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        })
        .done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error removing attachment: ' + response.message);
            }
        })
        .fail(function() {
            alert('Error removing attachment');
        });
    }
}

<?php if (!function_exists('formatBytes')): ?>
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
<?php endif; ?>
</script>
<?= $this->endSection() ?>
