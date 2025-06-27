<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Task Details - <?= esc($task['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Task Header -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= esc($task['title']) ?></h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Project: <a href="<?= base_url('admin/projects/view/' . $task['project_id']) ?>" class="text-blue-600 hover:text-blue-800"><?= esc($project['name']) ?></a>
                        (<?= esc($project['project_code']) ?>)
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="<?= base_url('admin/tasks') ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                    <a href="<?= base_url('admin/tasks/edit/' . $task['id']) ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <div class="relative">
                        <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium dropdown-toggle" onclick="toggleDropdown('actionsDropdown')">
                            <i class="fas fa-cog mr-2"></i>Actions
                        </button>
                        <div id="actionsDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border">
                            <a href="#" onclick="updateStatus('in_progress')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-play text-blue-600 mr-2"></i>Start Task
                            </a>
                            <a href="#" onclick="updateStatus('review')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-eye text-blue-500 mr-2"></i>Mark for Review
                            </a>
                            <a href="#" onclick="updateStatus('completed')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-check text-green-600 mr-2"></i>Mark Complete
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <a href="#" onclick="openModal('timeLogModal')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-clock mr-2"></i>Log Time
                            </a>
                            <a href="#" onclick="openModal('commentModal')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-comment mr-2"></i>Add Comment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Status:</span>
                                    <div>
                                        <?php
                                        $badgeClass = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                            'review' => 'bg-purple-100 text-purple-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ][$task['status']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $badgeClass ?>"><?= ucwords(str_replace('_', ' ', $task['status'])) ?></span>
                                    </div>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Priority:</span>
                                    <div>
                                        <?php
                                        $priorityClass = [
                                            'low' => 'bg-green-100 text-green-800',
                                            'medium' => 'bg-yellow-100 text-yellow-800',
                                            'high' => 'bg-red-100 text-red-800',
                                            'urgent' => 'bg-purple-100 text-purple-800'
                                        ][$task['priority']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $priorityClass ?>"><?= ucfirst($task['priority']) ?></span>
                                    </div>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Type:</span>
                                    <span class="text-gray-900"><?= ucfirst(str_replace('_', ' ', $task['task_type'])) ?></span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Assigned To:</span>
                                    <div>
                                        <?php if ($task['assigned_to']): ?>
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-2">
                                                    <?= strtoupper(substr($assigned_user['first_name'], 0, 1) . substr($assigned_user['last_name'], 0, 1)) ?>
                                                </div>
                                                <span class="text-gray-900"><?= esc($assigned_user['first_name'] . ' ' . $assigned_user['last_name']) ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-500">Unassigned</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Progress:</span>
                                    <div class="w-32">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $task['progress_percentage'] ?>%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600 mt-1"><?= $task['progress_percentage'] ?>%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Start Date:</span>
                                    <span class="text-gray-900"><?= $task['start_date'] ? date('M d, Y', strtotime($task['start_date'])) : 'Not set' ?></span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Due Date:</span>
                                    <div>
                                        <?php if ($task['due_date']): ?>
                                            <span class="text-gray-900"><?= date('M d, Y', strtotime($task['due_date'])) ?></span>
                                            <?php if (strtotime($task['due_date']) < time() && $task['status'] != 'completed'): ?>
                                                <br><small class="text-red-600"><i class="fas fa-exclamation-triangle"></i> Overdue</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-500">Not set</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Estimated Hours:</span>
                                    <span class="text-gray-900"><?= $task['estimated_hours'] ? number_format($task['estimated_hours'], 1) . ' hrs' : 'Not estimated' ?></span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Actual Hours:</span>
                                    <span class="text-gray-900"><?= $task['actual_hours'] ? number_format($task['actual_hours'], 1) . ' hrs' : 'Not logged' ?></span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Created:</span>
                                    <span class="text-gray-900"><?= date('M d, Y g:i A', strtotime($task['created_at'])) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Stats</h3>
                                <div class="grid grid-cols-2 gap-4 text-center">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-600 mb-1">Time Variance</h4>
                                        <?php 
                                        $variance = $task['estimated_hours'] && $task['actual_hours'] 
                                            ? (($task['actual_hours'] - $task['estimated_hours']) / $task['estimated_hours']) * 100 
                                            : 0;
                                        $varianceClass = $variance > 10 ? 'text-red-600' : ($variance < -10 ? 'text-green-600' : 'text-yellow-600');
                                        ?>
                                        <p class="text-xl font-bold <?= $varianceClass ?>"><?= $variance ? number_format($variance, 1) . '%' : 'N/A' ?></p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-600 mb-1">Comments</h4>
                                        <p class="text-xl font-bold text-blue-600"><?= count($comments) ?></p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-600 mb-1">Attachments</h4>
                                        <p class="text-xl font-bold text-gray-600"><?= count($attachments) ?></p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-600 mb-1">Dependencies</h4>
                                        <p class="text-xl font-bold text-blue-600"><?= count($dependencies) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($task['description']): ?>
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Description</h3>
                        <div class="prose max-w-none text-gray-700">
                            <?= nl2br(esc($task['description'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($task['tags']): ?>
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach (explode(',', $task['tags']) as $tag): ?>
                                <span class="inline-flex px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded-full"><?= esc(trim($tag)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('comments')" class="tab-button active border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600" id="comments-tab">
                    <i class="fas fa-comments mr-2"></i>Comments (<?= count($comments) ?>)
                </button>
                <button onclick="showTab('attachments')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" id="attachments-tab">
                    <i class="fas fa-paperclip mr-2"></i>Attachments (<?= count($attachments) ?>)
                </button>
                <button onclick="showTab('dependencies')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" id="dependencies-tab">
                    <i class="fas fa-link mr-2"></i>Dependencies (<?= count($dependencies) ?>)
                </button>
                <button onclick="showTab('activity')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" id="activity-tab">
                    <i class="fas fa-history mr-2"></i>Activity Log
                </button>
                <button onclick="showTab('time')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" id="time-tab">
                    <i class="fas fa-clock mr-2"></i>Time Tracking
                </button>
            </nav>
        </div>
        <div class="p-6">
            <!-- Comments Tab -->
            <div id="comments-content" class="tab-content">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Task Comments</h3>
                    <button type="button" onclick="openModal('commentModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>Add Comment
                    </button>
                </div>
                
                <div class="space-y-4">
                    <?php if (empty($comments)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-comments text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500 mb-4">No comments yet.</p>
                            <button type="button" onclick="openModal('commentModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Add First Comment
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">
                                        <?= strtoupper(substr($comment['first_name'], 0, 1) . substr($comment['last_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900"><?= esc($comment['first_name'] . ' ' . $comment['last_name']) ?></h4>
                                        <p class="text-sm text-gray-500"><?= date('M d, Y g:i A', strtotime($comment['created_at'])) ?></p>
                                    </div>
                                </div>
                                <?php if ($comment['user_id'] == session('user_id')): ?>
                                <button type="button" onclick="deleteComment(<?= $comment['id'] ?>)" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                            <div class="mt-3 text-gray-700">
                                <?= nl2br(esc($comment['comment'])) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Attachments Tab -->
            <div id="attachments-content" class="tab-content hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Task Attachments</h3>
                    <button type="button" onclick="openModal('attachmentModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>Upload File
                                </button>
                            </div>
                            
                            <?php if (empty($attachments)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-paperclip fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No attachments yet.</p>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#attachmentModal">
                                        Upload First File
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($attachments as $attachment): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="mb-2">
                                                    <?php
                                                    $ext = pathinfo($attachment['original_name'], PATHINFO_EXTENSION);
                                                    $iconClass = [
                                                        'pdf' => 'fas fa-file-pdf text-danger',
                                                        'doc' => 'fas fa-file-word text-primary',
                                                        'docx' => 'fas fa-file-word text-primary',
                                                        'xls' => 'fas fa-file-excel text-success',
                                                        'xlsx' => 'fas fa-file-excel text-success',
                                                        'ppt' => 'fas fa-file-powerpoint text-warning',
                                                        'pptx' => 'fas fa-file-powerpoint text-warning',
                                                        'jpg' => 'fas fa-file-image text-info',
                                                        'jpeg' => 'fas fa-file-image text-info',
                                                        'png' => 'fas fa-file-image text-info',
                                                        'gif' => 'fas fa-file-image text-info',
                                                        'zip' => 'fas fa-file-archive text-secondary',
                                                        'rar' => 'fas fa-file-archive text-secondary'
                                                    ][strtolower($ext)] ?? 'fas fa-file text-muted';
                                                    ?>
                                                    <i class="<?= $iconClass ?> fa-3x"></i>
                                                </div>
                                                <h6 class="card-title"><?= esc($attachment['original_name']) ?></h6>
                                                <p class="text-muted small"><?= formatBytes($attachment['file_size']) ?></p>
                                                <p class="text-muted small"><?= date('M d, Y', strtotime($attachment['uploaded_at'])) ?></p>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('admin/tasks/download/' . $attachment['id']) ?>" class="btn btn-primary">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                    <button type="button" class="btn btn-danger" onclick="deleteAttachment(<?= $attachment['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Dependencies Tab -->
                        <div class="tab-pane fade" id="dependencies" role="tabpanel">
                            <h5>Task Dependencies</h5>
                            
                            <?php if (!empty($dependencies)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Dependent Task</th>
                                            <th>Status</th>
                                            <th>Progress</th>
                                            <th>Due Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dependencies as $dep): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= base_url('admin/tasks/view/' . $dep['id']) ?>"><?= esc($dep['title']) ?></a>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'pending' => 'warning',
                                                    'in_progress' => 'primary',
                                                    'review' => 'info',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger'
                                                ][$dep['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $statusClass ?>"><?= ucwords(str_replace('_', ' ', $dep['status'])) ?></span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 15px;">
                                                    <div class="progress-bar" role="progressbar" style="width: <?= $dep['progress_percentage'] ?>%">
                                                        <?= $dep['progress_percentage'] ?>%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($dep['due_date']): ?>
                                                    <?= date('M d, Y', strtotime($dep['due_date'])) ?>
                                                    <?php if (strtotime($dep['due_date']) < time() && $dep['status'] != 'completed'): ?>
                                                        <br><small class="text-danger">Overdue</small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Not set</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/tasks/view/' . $dep['id']) ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-link fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No dependencies set for this task.</p>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Activity Log Tab -->
                        <div class="tab-pane fade" id="activity" role="tabpanel">
                            <h5>Activity Log</h5>
                            <div class="timeline">
                                <!-- Activity log items would go here -->
                                <div class="text-center py-4">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Activity logging feature coming soon...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Time Tracking Tab -->
                        <div class="tab-pane fade" id="time" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Time Tracking</h5>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#timeLogModal">
                                    <i class="fas fa-plus"></i> Log Time
                                </button>
                            </div>
                            
                            <div class="text-center py-4">
                                <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Time tracking feature coming soon...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Comment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/tasks/add-comment/' . $task['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Comment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Attachment Modal -->
<div class="modal fade" id="attachmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Attachment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/tasks/upload-attachment/' . $task['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="attachment">Select File</label>
                        <input type="file" class="form-control-file" id="attachment" name="attachment" required
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                        <small class="form-text text-muted">
                            Allowed file types: PDF, DOC, XLS, PPT, Images, ZIP. Max size: 10MB.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload File</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Time Log Modal -->
<div class="modal fade" id="timeLogModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Time</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/tasks/log-time/' . $task['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="hours">Hours Worked</label>
                        <input type="number" class="form-control" id="hours" name="hours" step="0.25" min="0.25" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Log Time</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    flex-shrink: 0;
}

.comment-item {
    background-color: #f8f9fa;
}
</style>

<script>
function updateStatus(status) {
    if (confirm('Are you sure you want to update the task status?')) {
        $.post('<?= base_url('admin/tasks/update-status/' . $task['id']) ?>', {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
            'status': status
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error updating task status');
            }
        }).fail(function() {
            alert('Error updating task status');
        });
    }
}

function deleteComment(commentId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        $.post('<?= base_url('admin/tasks/delete-comment') ?>/' + commentId, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error deleting comment');
            }
        }).fail(function() {
            alert('Error deleting comment');
        });
    }
}

function deleteAttachment(attachmentId) {
    if (confirm('Are you sure you want to delete this attachment?')) {
        $.post('<?= base_url('admin/tasks/delete-attachment') ?>/' + attachmentId, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error deleting attachment');
            }
        }).fail(function() {
            alert('Error deleting attachment');
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
