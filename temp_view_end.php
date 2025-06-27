                </button>
            </div>
            
            <div class="space-y-4">
                <?php if (empty($attachments)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-paperclip text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 mb-4">No attachments uploaded yet.</p>
                        <button type="button" onclick="openModal('attachmentModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Upload First File
                        </button>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($attachments as $attachment): ?>
                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="mb-3">
                                <?php
                                $ext = pathinfo($attachment['original_name'], PATHINFO_EXTENSION);
                                $iconClass = [
                                    'pdf' => 'fas fa-file-pdf text-red-600',
                                    'doc' => 'fas fa-file-word text-blue-600',
                                    'docx' => 'fas fa-file-word text-blue-600',
                                    'xls' => 'fas fa-file-excel text-green-600',
                                    'xlsx' => 'fas fa-file-excel text-green-600',
                                    'ppt' => 'fas fa-file-powerpoint text-yellow-600',
                                    'pptx' => 'fas fa-file-powerpoint text-yellow-600',
                                    'jpg' => 'fas fa-file-image text-blue-500',
                                    'jpeg' => 'fas fa-file-image text-blue-500',
                                    'png' => 'fas fa-file-image text-blue-500',
                                    'gif' => 'fas fa-file-image text-blue-500',
                                    'zip' => 'fas fa-file-archive text-gray-600',
                                    'rar' => 'fas fa-file-archive text-gray-600'
                                ][strtolower($ext)] ?? 'fas fa-file text-gray-500';
                                ?>
                                <i class="<?= $iconClass ?> text-4xl"></i>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-1"><?= esc($attachment['original_name']) ?></h4>
                            <p class="text-sm text-gray-500 mb-1"><?= formatBytes($attachment['file_size'] ?? 0) ?></p>
                            <p class="text-sm text-gray-500 mb-3"><?= date('M d, Y', strtotime($attachment['uploaded_at'])) ?></p>
                            <div class="flex space-x-2 justify-center">
                                <a href="<?= base_url('admin/tasks/download/' . $attachment['id']) ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                                <button type="button" onclick="deleteAttachment(<?= $attachment['id'] ?>)" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
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
                
                <?php if (!empty($dependencies)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dependent Task</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($dependencies as $dep): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="<?= base_url('admin/tasks/view/' . $dep['id']) ?>" class="text-blue-600 hover:text-blue-800"><?= esc($dep['title']) ?></a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusClass = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'review' => 'bg-purple-100 text-purple-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ][$dep['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>"><?= ucwords(str_replace('_', ' ', $dep['status'])) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $dep['progress_percentage'] ?>%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600"><?= $dep['progress_percentage'] ?>%</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($dep['due_date']): ?>
                                        <?= date('M d, Y', strtotime($dep['due_date'])) ?>
                                        <?php if (strtotime($dep['due_date']) < time() && $dep['status'] != 'completed'): ?>
                                            <br><small class="text-red-600">Overdue</small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-500">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= base_url('admin/tasks/view/' . $dep['id']) ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-link text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No dependencies set for this task.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Activity Log Tab -->
            <div id="activity-content" class="tab-content hidden">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Activity Log</h3>
                <div class="text-center py-12">
                    <i class="fas fa-history text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Activity logging feature coming soon...</p>
                </div>
            </div>

            <!-- Time Tracking Tab -->
            <div id="time-content" class="tab-content hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Time Tracking</h3>
                    <button type="button" onclick="openModal('timeLogModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>Log Time
                    </button>
                </div>
                
                <div class="text-center py-12">
                    <i class="fas fa-clock text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Time tracking feature coming soon...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comment Modal -->
<div id="commentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add Comment</h3>
                <button onclick="closeModal('commentModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="<?= base_url('admin/tasks/add-comment/' . $task['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                    <textarea id="comment" name="comment" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('commentModal')" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Add Comment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Attachment Modal -->
<div id="attachmentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Upload Attachment</h3>
                <button onclick="closeModal('attachmentModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="<?= base_url('admin/tasks/upload-attachment/' . $task['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">Select File</label>
                    <input type="file" id="attachment" name="attachment" required
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">
                        Allowed file types: PDF, DOC, XLS, PPT, Images, ZIP. Max size: 10MB.
                    </p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('attachmentModal')" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Upload File</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Time Log Modal -->
<div id="timeLogModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Log Time</h3>
                <button onclick="closeModal('timeLogModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="<?= base_url('admin/tasks/log-time/' . $task['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label for="hours" class="block text-sm font-medium text-gray-700 mb-2">Hours Worked</label>
                    <input type="number" id="hours" name="hours" step="0.25" min="0.25" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="time_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="time_description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('timeLogModal')" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Log Time</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Add active class to selected tab button
    const activeButton = document.getElementById(tabName + '-tab');
    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
}

// Modal functionality
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Dropdown functionality
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('[id$="Dropdown"]');
    dropdowns.forEach(dropdown => {
        if (!dropdown.contains(event.target) && !event.target.closest('.dropdown-toggle')) {
            dropdown.classList.add('hidden');
        }
    });
});

function updateStatus(status) {
    if (confirm('Are you sure you want to update the task status?')) {
        fetch('<?= base_url('admin/tasks/update-status/' . $task['id']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                'status': status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating task status');
            }
        })
        .catch(error => {
            alert('Error updating task status');
        });
    }
}

function deleteComment(commentId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        fetch('<?= base_url('admin/tasks/delete-comment') ?>/' + commentId, {
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
                alert('Error deleting comment');
            }
        })
        .catch(error => {
            alert('Error deleting comment');
        });
    }
}

function deleteAttachment(attachmentId) {
    if (confirm('Are you sure you want to delete this attachment?')) {
        fetch('<?= base_url('admin/tasks/delete-attachment') ?>/' + attachmentId, {
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
                alert('Error deleting attachment');
            }
        })
        .catch(error => {
            alert('Error deleting attachment');
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
