<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $file['original_file_name'] ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.tab-button { @apply px-4 py-2 font-medium border-b-2 border-transparent text-gray-700 hover:text-gray-900 hover:border-gray-300 transition-colors cursor-pointer; }
.tab-button.active { @apply border-b-indigo-600 text-indigo-600; }
.tab-pane { display: none; }
.tab-pane.active { display: block; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white rounded-lg shadow-sm border p-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><i class="fas fa-file mr-2 text-indigo-600"></i><?= $file['original_file_name'] ?></h1>
            <p class="text-gray-600 text-sm mt-2">
                <i class="fas fa-calendar-alt mr-1"></i>Uploaded <?= date('M d, Y', strtotime($file['created_at'])) ?> | 
                <i class="fas fa-hdd mr-1"></i>Size: <?= formatBytes($file['file_size']) ?> | 
                <i class="fas fa-history mr-1"></i>Version: <?= $file['version_number'] ?>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('file-management/download/' . $file['id']) ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                <i class="fas fa-download mr-2"></i> Download
            </a>
            <a href="<?= base_url('file-management') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="flex space-x-8 border-b border-gray-200 px-6">
            <button class="tab-button active" data-tab="details">
                <i class="fas fa-info-circle mr-2"></i>Details
            </button>
            <button class="tab-button" data-tab="preview">
                <i class="fas fa-eye mr-2"></i>Preview
            </button>
            <button class="tab-button" data-tab="versions">
                <i class="fas fa-history mr-2"></i>Versions (<?= count($versions) ?>)
            </button>
            <button class="tab-button" data-tab="comments">
                <i class="fas fa-comments mr-2"></i>Comments (<?= count($comments) ?>)
            </button>
        </div>

        <!-- Tabs Content -->
        <div class="p-6">
            <!-- Details Tab -->
            <div id="details" class="tab-pane active space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">File Information</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between py-3 border-b border-gray-200">
                                    <span class="font-medium text-gray-700">File Name</span>
                                    <span class="text-gray-900"><?= $file['original_file_name'] ?></span>
                                </div>
                                <div class="flex justify-between py-3 border-b border-gray-200">
                                    <span class="font-medium text-gray-700">File Type</span>
                                    <span class="text-gray-900"><span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded text-sm font-medium"><?= strtoupper($file['file_type']) ?></span></span>
                                </div>
                                <div class="flex justify-between py-3 border-b border-gray-200">
                                    <span class="font-medium text-gray-700">File Size</span>
                                    <span class="text-gray-900"><?= formatBytes($file['file_size']) ?></span>
                                </div>
                                <div class="flex justify-between py-3 border-b border-gray-200">
                                    <span class="font-medium text-gray-700">MIME Type</span>
                                    <span class="text-gray-900 font-mono text-sm"><?= $file['mime_type'] ?></span>
                                </div>
                                <div class="flex justify-between py-3 border-b border-gray-200">
                                    <span class="font-medium text-gray-700">Description</span>
                                    <span class="text-gray-900"><?= $file['description'] ?: '<em class="text-gray-400">No description</em>' ?></span>
                                </div>
                                <div class="flex justify-between py-3 border-b border-gray-200">
                                    <span class="font-medium text-gray-700">Document Date</span>
                                    <span class="text-gray-900"><?= $file['document_date'] ? date('M d, Y', strtotime($file['document_date'])) : '<em class="text-gray-400">Not set</em>' ?></span>
                                </div>
                                <div class="flex justify-between py-3">
                                    <span class="font-medium text-gray-700">Expires At</span>
                                    <span class="text-gray-900"><?= $file['expires_at'] ? date('M d, Y', strtotime($file['expires_at'])) : '<em class="text-gray-400">No expiration</em>' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-4">
                        <!-- Tags -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-3">Tags</h4>
                            <?php if (count($tags) > 0): ?>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($tags as $tag): ?>
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium"><?= $tag['tag_name'] ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500 text-sm">No tags</p>
                            <?php endif; ?>
                        </div>

                        <!-- Metadata -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-3">Metadata</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Uploaded:</span>
                                    <span class="font-medium text-gray-900"><?= date('M d, Y H:i', strtotime($file['created_at'])) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Updated:</span>
                                    <span class="font-medium text-gray-900"><?= date('M d, Y H:i', strtotime($file['updated_at'])) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Tab -->
            <div id="preview" class="tab-pane space-y-6">
                <div class="bg-gray-50 rounded-lg p-12 border border-gray-200 text-center">
                    <div class="space-y-4">
                        <?php
                        $ext = strtolower($file['file_type'] ?? '');
                        $icon = 'fa-file';
                        $color = 'text-gray-400';
                        
                        if (in_array($ext, ['pdf'])) {
                            $icon = 'fa-file-pdf'; $color = 'text-red-600';
                        } elseif (in_array($ext, ['doc', 'docx', 'dot', 'dotx'])) {
                            $icon = 'fa-file-word'; $color = 'text-blue-600';
                        } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
                            $icon = 'fa-file-excel'; $color = 'text-green-600';
                        } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp'])) {
                            $icon = 'fa-file-image'; $color = 'text-purple-500';
                        } elseif (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
                            $icon = 'fa-file-archive'; $color = 'text-amber-600';
                        } elseif (in_array($ext, ['mp4', 'avi', 'mkv', 'mov'])) {
                            $icon = 'fa-file-video'; $color = 'text-red-500';
                        } elseif (in_array($ext, ['mp3', 'wav', 'flac'])) {
                            $icon = 'fa-file-audio'; $color = 'text-indigo-600';
                        }
                        ?>
                        <i class="fas <?= $icon ?> <?= $color ?> text-8xl"></i>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mt-4">Preview Not Available</h3>
                            <p class="text-gray-600 mt-2">This file type cannot be previewed in the browser.</p>
                            <p class="text-gray-500 text-sm mt-2">Use the Download button to view the file.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Versions Tab -->
            <div id="versions" class="tab-pane space-y-4">
                <?php if (count($versions) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Version</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Size</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Notes</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($versions as $version): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">v<?= $version['version_number'] ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-600"><?= formatBytes($version['file_size']) ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-600"><?= date('M d, Y H:i', strtotime($version['created_at'])) ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-600"><?= $version['change_description'] ?: '<em class="text-gray-400">No notes</em>' ?></td>
                                        <td class="px-4 py-3 text-sm">
                                            <a href="<?= base_url('file-management/download/' . $file['id']) ?>?version=<?= $version['version_number'] ?>" 
                                               class="text-indigo-600 hover:text-indigo-800 font-medium">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-lg p-8 text-center">
                        <p class="text-gray-500">No versions available</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Comments Tab -->
            <div id="comments" class="tab-pane space-y-6">
                <!-- Add Comment Form -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <form id="commentForm" class="space-y-3">
                        <?= csrf_field() ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Add a comment</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                      name="comment_text" placeholder="Your comment..." rows="3"></textarea>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                            Post Comment
                        </button>
                    </form>
                </div>

                <!-- Comments List -->
                <div class="space-y-4">
                    <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-semibold text-gray-900"><?= $comment['user']['name'] ?? 'Unknown User' ?></p>
                                        <p class="text-xs text-gray-500"><?= date('M d, Y H:i', strtotime($comment['created_at'])) ?></p>
                                    </div>
                                    <?php if ($comment['is_resolved']): ?>
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Resolved</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($comment['comment_text']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="bg-gray-50 rounded-lg p-8 text-center">
                            <p class="text-gray-500">No comments yet. Be the first to comment!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', function() {
        const tabName = this.getAttribute('data-tab');
        
        // Remove active class from all buttons and panes
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
        
        // Add active class to clicked button and corresponding pane
        this.classList.add('active');
        document.getElementById(tabName).classList.add('active');
    });
});

// Handle comment form submission
document.getElementById('commentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const fileId = <?= $file['id'] ?>;
    
    fetch('<?= base_url('file-management/comment/') ?>' + fileId, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="<?= csrf_token() ?>"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>

<?= $this->endSection() ?>
