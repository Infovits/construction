<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($task['title'] ?? 'Task Details') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Task Header -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= esc($task['title'] ?? 'Task') ?></h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Project: <a href="<?= base_url('admin/projects/' . ($task['project_id'] ?? '')) ?>" class="text-blue-600 hover:text-blue-800">
                            <?= esc($task['project_name'] ?? 'Unknown Project') ?>
                        </a>
                        (<?= esc($task['project_code'] ?? '') ?>)
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="<?= base_url('admin/tasks') ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                    <a href="<?= base_url('admin/tasks/' . ($task['id'] ?? '') . '/edit') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
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
                                        ][isset($task['status']) ? $task['status'] : 'pending'] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $badgeClass ?>">
                                            <?= ucwords(str_replace('_', ' ', isset($task['status']) ? $task['status'] : 'pending')) ?>
                                        </span>
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
                                        ][isset($task['priority']) ? $task['priority'] : 'medium'] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $priorityClass ?>">
                                            <?= ucfirst(isset($task['priority']) ? $task['priority'] : 'medium') ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Type:</span>
                                    <span class="text-gray-900">
                                        <?= isset($task['task_type']) ? ucfirst(str_replace('_', ' ', $task['task_type'])) : 'Task' ?>
                                    </span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Assigned To:</span>
                                    <div>
                                        <?php if (isset($task['assigned_to']) && $task['assigned_to']): ?>
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-2">
                                                    <?= strtoupper(substr($task['assigned_name'] ?? '', 0, 1)) ?>
                                                </div>
                                                <span class="text-gray-900"><?= esc($task['assigned_name'] ?? 'Unknown') ?></span>
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
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: <?= isset($task['progress_percentage']) ? $task['progress_percentage'] : 0 ?>%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600 mt-1">
                                            <?= isset($task['progress_percentage']) ? $task['progress_percentage'] : 0 ?>%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Start Date:</span>
                                    <span class="text-gray-900">
                                        <?= isset($task['start_date']) && $task['start_date'] ? date('M d, Y', strtotime($task['start_date'])) : 'Not set' ?>
                                    </span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Due Date:</span>
                                    <div>
                                        <?php if (isset($task['due_date']) && $task['due_date']): ?>
                                            <span class="text-gray-900"><?= date('M d, Y', strtotime($task['due_date'])) ?></span>
                                            <?php if (strtotime($task['due_date']) < time() && isset($task['status']) && $task['status'] != 'completed'): ?>
                                                <br><small class="text-red-600"><i class="fas fa-exclamation-triangle"></i> Overdue</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-500">Not set</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Estimated Hours:</span>
                                    <span class="text-gray-900">
                                        <?= isset($task['estimated_hours']) && $task['estimated_hours'] ? number_format($task['estimated_hours'], 1) . ' hrs' : 'Not estimated' ?>
                                    </span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Actual Hours:</span>
                                    <span class="text-gray-900">
                                        <?= isset($task['actual_hours']) && $task['actual_hours'] ? number_format($task['actual_hours'], 1) . ' hrs' : 'Not logged' ?>
                                    </span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Created:</span>
                                    <span class="text-gray-900">
                                        <?= isset($task['created_at']) ? date('M d, Y g:i A', strtotime($task['created_at'])) : 'Unknown' ?>
                                    </span>
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
                                        $variance = (isset($task['estimated_hours']) && isset($task['actual_hours']) && $task['estimated_hours'] && $task['actual_hours'])
                                            ? (($task['actual_hours'] - $task['estimated_hours']) / $task['estimated_hours']) * 100
                                            : 0;
                                        $varianceClass = $variance > 10 ? 'text-red-600' : ($variance < -10 ? 'text-green-600' : 'text-yellow-600');
                                        ?>
                                        <p class="text-xl font-bold <?= $varianceClass ?>">
                                            <?= $variance ? number_format($variance, 1) . '%' : 'N/A' ?>
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-600 mb-1">Comments</h4>
                                        <p class="text-xl font-bold text-blue-600"><?= count($comments ?? []) ?></p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-600 mb-1">Attachments</h4>
                                        <p class="text-xl font-bold text-gray-600"><?= count($attachments ?? []) ?></p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-600 mb-1">Dependencies</h4>
                                        <p class="text-xl font-bold text-blue-600"><?= count($dependencies ?? []) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($task['description']) && $task['description']): ?>
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Description</h3>
                        <div class="prose max-w-none text-gray-700">
                            <?= nl2br(esc($task['description'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($task['tags']) && $task['tags']): ?>
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach (explode(',', $task['tags']) as $tag): ?>
                                <span class="inline-flex px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded-full">
                                    <?= esc(trim($tag)) ?>
                                </span>
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
                    <i class="fas fa-comments mr-2"></i>Comments (<?= count($comments ?? []) ?>)
                </button>
                <button onclick="showTab('attachments')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" id="attachments-tab">
                    <i class="fas fa-paperclip mr-2"></i>Attachments (<?= count($attachments ?? []) ?>)
                </button>
                <button onclick="showTab('dependencies')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" id="dependencies-tab">
                    <i class="fas fa-link mr-2"></i>Dependencies (<?= count($dependencies ?? []) ?>)
                </button>
                <button onclick="showTab('activity')" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" id="activity-tab">
                    <i class="fas fa-history mr-2"></i>Activity Log
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
                    <?php if (empty($comments ?? [])): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-comments text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500 mb-4">No comments yet.</p>
                            <button type="button" onclick="openModal('commentModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Add First Comment
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach (($comments ?? []) as $comment): ?>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">
                                        <?= strtoupper(substr($comment['first_name'] ?? '', 0, 1) . substr($comment['last_name'] ?? '', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">
                                            <?= esc(($comment['first_name'] ?? '') . ' ' . ($comment['last_name'] ?? '')) ?>
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            <?= isset($comment['created_at']) ? date('M d, Y g:i A', strtotime($comment['created_at'])) : 'Unknown' ?>
                                        </p>
                                    </div>
                                </div>
                                <?php if (isset($comment['user_id']) && $comment['user_id'] == session('user_id')): ?>
                                <button type="button" onclick="deleteComment(<?= $comment['id'] ?? '' ?>)" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                            <div class="mt-3 text-gray-700">
                                <?= nl2br(esc($comment['comment'] ?? '')) ?>
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

                <?php if (empty($attachments ?? [])): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-paperclip text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 mb-4">No attachments yet.</p>
                        <button type="button" onclick="openModal('attachmentModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Upload First File
                        </button>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach (($attachments ?? []) as $attachment): ?>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <?php
                                    $ext = pathinfo($attachment['original_name'] ?? '', PATHINFO_EXTENSION);
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
                                    <i class="<?= $iconClass ?> fa-2x mr-3"></i>
                                    <div>
                                        <h4 class="font-medium text-gray-900 text-sm">
                                            <?= esc($attachment['original_name'] ?? 'Unknown File') ?>
                                        </h4>
                                        <p class="text-xs text-gray-500">
                                            <?= isset($attachment['file_size']) ? formatBytes($attachment['file_size']) : 'Unknown size' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <a href="<?= base_url('admin/tasks/download/' . ($attachment['id'] ?? '')) ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                                <button type="button" onclick="deleteAttachment(<?= $attachment['id'] ?? '' ?>)" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Dependencies Tab -->
            <div id="dependencies-content" class="tab-content hidden">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Task Dependencies</h3>

                <?php if (empty($dependencies ?? [])): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-link text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 mb-4">No dependencies set for this task.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach (($dependencies ?? []) as $dep): ?>
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">
                                            <a href="<?= base_url('admin/tasks/' . ($dep['id'] ?? '')) ?>" class="hover:text-indigo-600">
                                                <?= esc($dep['title'] ?? 'Unknown Task') ?>
                                            </a>
                                        </h4>
                                        <div class="flex items-center space-x-4 mt-1 text-sm text-gray-500">
                                            <span>
                                                <?php
                                                $statusClass = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                                    'review' => 'bg-purple-100 text-purple-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800'
                                                ][isset($dep['status']) ? $dep['status'] : 'pending'] ?? 'bg-gray-100 text-gray-800';
                                                ?>
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>">
                                                    <?= ucwords(str_replace('_', ' ', isset($dep['status']) ? $dep['status'] : 'pending')) ?>
                                                </span>
                                            </span>
                                            <span>Progress: <?= isset($dep['progress_percentage']) ? $dep['progress_percentage'] : 0 ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Activity Log Tab -->
            <div id="activity-content" class="tab-content hidden">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Activity Log</h3>
                <div class="text-center py-12">
                    <i class="fas fa-history text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Activity logging coming soon</h3>
                    <p class="text-gray-500">Task activity will be displayed here.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comment Modal -->
<div id="commentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Add Comment</h3>
            <button type="button" onclick="closeModal('commentModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="<?= base_url('admin/tasks/add-comment/' . ($task['id'] ?? '')) ?>" method="post" class="p-6">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                <textarea id="comment" name="comment" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                          placeholder="Enter your comment here..." required></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('commentModal')"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Add Comment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Attachment Modal -->
<div id="attachmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Upload Attachment</h3>
            <button type="button" onclick="closeModal('attachmentModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="<?= base_url('admin/tasks/upload-attachment/' . ($task['id'] ?? '')) ?>" method="post" enctype="multipart/form-data" class="p-6">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">Select File</label>
                <input type="file" id="attachment" name="attachment" required
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-2 text-sm text-gray-500">
                    Allowed file types: PDF, DOC, XLS, PPT, Images, ZIP. Max size: 10MB.
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('attachmentModal')"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Upload File
                </button>
            </div>
        </form>
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
function showTab(tabName) {
    // Hide all tab contents
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.add('hidden'));

    // Remove active state from all tabs
    const tabs = document.querySelectorAll('.tab-button');
    tabs.forEach(tab => {
        tab.classList.remove('active', 'border-blue-500', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    const selectedContent = document.getElementById(tabName + '-content');
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }

    // Add active state to selected tab
    const selectedTab = document.getElementById(tabName + '-tab');
    if (selectedTab) {
        selectedTab.classList.add('active', 'border-blue-500', 'text-blue-600');
        selectedTab.classList.remove('border-transparent', 'text-gray-500');
    }
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        // Prevent body scrolling when modal is open
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        // Restore body scrolling
        document.body.style.overflow = 'auto';
    }
}

// Close modals when clicking outside or pressing Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('[id$="Modal"]:not(.hidden)');
        modals.forEach(modal => modal.classList.add('hidden'));
        document.body.style.overflow = 'auto';
    }
});

function deleteComment(commentId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        fetch('<?= base_url('admin/tasks/delete-comment') ?>/' + commentId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: '<?= csrf_token() ?>=<?= csrf_hash() ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting comment: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the comment');
        });
    }
}

function deleteAttachment(attachmentId) {
    if (confirm('Are you sure you want to delete this attachment?')) {
        fetch('<?= base_url('admin/tasks/delete-attachment') ?>/' + attachmentId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: '<?= csrf_token() ?>=<?= csrf_hash() ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting attachment: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the attachment');
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
