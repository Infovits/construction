<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $file['original_file_name'] ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.tab-button { @apply px-4 py-3 font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300 transition-colors cursor-pointer text-sm; }
.tab-button.active { @apply border-b-indigo-600 text-indigo-600 bg-indigo-50; }
.tab-pane { display: none; }
.tab-pane.active { display: block; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <i class="fas fa-file text-indigo-600 text-xl"></i>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900"><?= $file['original_file_name'] ?></h1>
                </div>
                <div class="flex flex-wrap gap-4 text-sm text-gray-600 mt-4">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-calendar-alt"></i>
                        Uploaded <?= date('M d, Y', strtotime($file['created_at'])) ?>
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-hdd"></i>
                        <?= formatBytes($file['file_size']) ?>
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-code-branch"></i>
                        v<?= $file['version_number'] ?>
                    </span>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <?php if (!empty($canEdit)): ?>
                    <button type="button" onclick="document.getElementById('updateVersionModal').classList.remove('hidden')"
                            class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium text-sm shadow-sm hover:shadow">
                        <i class="fas fa-upload mr-2"></i> New Version
                    </button>
                <?php endif; ?>
                <a href="<?= base_url('file-management/download/' . $file['id']) ?>" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm shadow-sm hover:shadow">
                    <i class="fas fa-download mr-2"></i> Download
                </a>
                <?php if (!empty($canDelete)): ?>
                    <button type="button" onclick="deleteFile(<?= $file['id'] ?>)" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm shadow-sm hover:shadow">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                <?php endif; ?>
                <a href="<?= base_url('file-management') ?>" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-medium text-sm shadow-sm hover:shadow">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex space-x-1 border-b border-gray-200 px-6 bg-gray-50">
            <button class="tab-button active" data-tab="details">
                <i class="fas fa-info-circle mr-2"></i>Details
            </button>
            <button class="tab-button" data-tab="versions">
                <i class="fas fa-history mr-2"></i>Versions (<?= count($versions) ?>)
            </button>
            <button class="tab-button" data-tab="comments">
                <i class="fas fa-comments mr-2"></i>Comments (<?= count($comments) ?>)
            </button>
            <button class="tab-button" data-tab="activity">
                <i class="fas fa-clipboard-list mr-2"></i>Activity (<?= count($changeLogs) ?>)
            </button>
            <button class="tab-button" data-tab="access">
                <i class="fas fa-user-shield mr-2"></i>Access
            </button>
        </div>

        <!-- Tabs Content -->
        <div class="p-6">
            <!-- Details Tab -->
            <div id="details" class="tab-pane active space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-file-alt text-indigo-600"></i>File Information
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-200">
                                <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition">
                                    <span class="font-medium text-gray-700">File Name</span>
                                    <span class="text-gray-900 font-mono"><?= $file['original_file_name'] ?></span>
                                </div>
                                <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition">
                                    <span class="font-medium text-gray-700">File Type</span>
                                    <span class="inline-flex items-center gap-2">
                                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium"><?= strtoupper($file['file_type']) ?></span>
                                    </span>
                                </div>
                                <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition">
                                    <span class="font-medium text-gray-700">File Size</span>
                                    <span class="text-gray-900"><?= formatBytes($file['file_size']) ?></span>
                                </div>
                                <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition">
                                    <span class="font-medium text-gray-700">MIME Type</span>
                                    <span class="text-gray-900 font-mono text-sm"><?= $file['mime_type'] ?></span>
                                </div>
                                <div class="px-6 py-4 flex justify-between items-start hover:bg-gray-50 transition">
                                    <span class="font-medium text-gray-700">Description</span>
                                    <span class="text-gray-900 text-right"><?= $file['description'] ?: '<em class="text-gray-400">—</em>' ?></span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-4">
                        <!-- Tags -->
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-4 py-3 border-b border-gray-200">
                                <h4 class="font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-tags text-purple-600"></i>Tags
                                </h4>
                            </div>
                            <div class="p-4">
                                <?php if (count($tags) > 0): ?>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($tags as $tag): ?>
                                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium"><?= $tag['tag_name'] ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-gray-500 text-sm">No tags assigned</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Metadata -->
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-4 py-3 border-b border-gray-200">
                                <h4 class="font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-database text-blue-600"></i>Metadata
                                </h4>
                            </div>
                            <div class="p-4 space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Uploaded:</span>
                                    <span class="font-medium text-gray-900"><?= date('M d, Y', strtotime($file['created_at'])) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Time:</span>
                                    <span class="font-medium text-gray-900"><?= date('H:i:s', strtotime($file['created_at'])) ?></span>
                                </div>
                            </div>
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
                                <tr class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Version</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Size</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Notes</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($versions as $version): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">v<?= $version['version_number'] ?></span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600"><?= formatBytes($version['file_size']) ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-600"><?= date('M d, Y H:i', strtotime($version['created_at'])) ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-600"><?= $version['change_description'] ?: '<em class="text-gray-400">—</em>' ?></td>
                                        <td class="px-6 py-4 text-sm">
                                            <a href="<?= base_url('file-management/download/' . $file['id']) ?>?version=<?= $version['version_number'] ?>" 
                                               class="px-3 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-700 transition font-medium text-xs inline-flex items-center gap-1">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-lg p-8 text-center border border-gray-200">
                        <i class="fas fa-inbox text-gray-400 text-3xl mb-3 block"></i>
                        <p class="text-gray-500 font-medium">No versions available</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Comments Tab -->
            <div id="comments" class="tab-pane space-y-6">
                <!-- Add Comment Form -->
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 px-6 py-4 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-pen-to-square text-indigo-600"></i>Add Comment
                        </h4>
                    </div>
                    <div class="p-6">
                        <form id="commentForm" class="space-y-3">
                            <?= csrf_field() ?>
                            <div>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none" 
                                          name="comment_text" placeholder="Share your thoughts or feedback..." rows="3"></textarea>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm inline-flex items-center gap-2">
                                <i class="fas fa-paper-plane"></i>Post Comment
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Comments List -->
                <div class="space-y-4">
                    <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="bg-white rounded-lg p-5 border border-gray-200 hover:shadow-sm transition-shadow">
                                <div class="flex justify-between items-start gap-4 mb-3">
                                    <div>
                                        <p class="font-semibold text-gray-900"><?= $comment['user']['name'] ?? 'Unknown User' ?></p>
                                        <p class="text-xs text-gray-500"><?= date('M d, Y • H:i', strtotime($comment['created_at'])) ?></p>
                                    </div>
                                    <?php if ($comment['is_resolved']): ?>
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium flex items-center gap-1">
                                            <i class="fas fa-check-circle"></i>Resolved
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-gray-700 whitespace-pre-wrap text-sm"><?= htmlspecialchars($comment['comment_text']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="bg-gray-50 rounded-lg p-8 text-center border border-gray-200">
                            <i class="fas fa-comments text-gray-400 text-3xl mb-3 block"></i>
                            <p class="text-gray-600 font-medium">No comments yet</p>
                            <p class="text-gray-500 text-sm mt-1">Be the first to start a discussion!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Activity Tab -->
            <div id="activity" class="tab-pane space-y-4">
                <?php if (count($changeLogs) > 0): ?>
                    <div class="space-y-3">
                        <?php foreach ($changeLogs as $log): ?>
                            <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-sm transition-shadow">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <?php
                                        $icon = 'fa-circle-check';
                                        $color = 'bg-blue-100 text-blue-600';
                                        if ($log['action_type'] === 'uploaded') {
                                            $icon = 'fa-cloud-arrow-up'; $color = 'bg-green-100 text-green-600';
                                        } elseif ($log['action_type'] === 'updated') {
                                            $icon = 'fa-edit'; $color = 'bg-yellow-100 text-yellow-600';
                                        } elseif ($log['action_type'] === 'deleted') {
                                            $icon = 'fa-trash'; $color = 'bg-red-100 text-red-600';
                                        } elseif ($log['action_type'] === 'commented') {
                                            $icon = 'fa-comment'; $color = 'bg-purple-100 text-purple-600';
                                        } elseif ($log['action_type'] === 'shared') {
                                            $icon = 'fa-share'; $color = 'bg-indigo-100 text-indigo-600';
                                        }
                                        ?>
                                        <div class="p-2 rounded-lg <?= $color ?>">
                                            <i class="fas <?= $icon ?>"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">
                                            <?= ucfirst($log['action_type']) ?>
                                            <span class="font-normal text-gray-500">by</span>
                                            <?= $log['user']['name'] ?? 'Unknown User' ?>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1"><?= date('M d, Y • H:i', strtotime($log['created_at'])) ?></p>
                                        <?php if (!empty($log['action_description'])): ?>
                                            <p class="text-gray-700 mt-2 text-sm"><?= htmlspecialchars($log['action_description']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-lg p-8 text-center border border-gray-200">
                        <i class="fas fa-history text-gray-400 text-3xl mb-3 block"></i>
                        <p class="text-gray-600 font-medium">No activity yet</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Access Tab -->
            <div id="access" class="tab-pane space-y-6">
                <?php
                $usersById = [];
                foreach ($users as $u) {
                    $usersById[$u['id']] = $u;
                }

                $rolesById = [];
                foreach ($roles as $r) {
                    $rolesById[$r['id']] = $r;
                }
                ?>

                <?php if (!empty($canManage)): ?>
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-gray-200">
                            <h4 class="font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-plus-circle text-green-600"></i>Grant Access
                            </h4>
                        </div>
                        <div class="p-6">
                            <form id="accessForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                                <?= csrf_field() ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Type</label>
                                    <select name="target_type" id="accessTargetType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="user">User</option>
                                        <option value="role">Role</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                                    <select name="user_id" id="accessUserSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Select user</option>
                                        <?php foreach ($users as $u): ?>
                                            <option value="<?= $u['id'] ?>">
                                                <?= $u['first_name'] . ' ' . $u['last_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                    <select name="role_id" id="accessRoleSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 hidden">
                                        <option value="">Select role</option>
                                        <?php foreach ($roles as $r): ?>
                                            <option value="<?= $r['id'] ?>">
                                                <?= $r['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Access Level</label>
                                    <select name="access_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="view">View</option>
                                        <option value="edit">Edit</option>
                                        <option value="delete">Delete</option>
                                        <option value="manage">Manage</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Expiration</label>
                                    <input type="date" name="expires_at" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm">
                                        <i class="fas fa-share mr-2"></i>Grant
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-gray-600"></i>Current Access
                        </h4>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <?php if (count($accessList) > 0): ?>
                            <?php foreach ($accessList as $access): ?>
                                <?php $user = $usersById[$access['user_id']] ?? null; ?>
                                <?php $role = $rolesById[$access['role_id']] ?? null; ?>
                                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-indigo-100 rounded-lg">
                                            <i class="fas <?= $role ? 'fa-users' : 'fa-user' ?> text-indigo-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 text-sm">
                                                <?php if ($role): ?>
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold mr-2">ROLE</span><?= $role['name'] ?>
                                                <?php else: ?>
                                                    <?= $user ? ($user['first_name'] . ' ' . $user['last_name']) : 'Unknown User' ?>
                                                <?php endif; ?>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded"><?= ucfirst($access['access_type']) ?></span>
                                                <?= $access['expires_at'] ? ' • Expires ' . date('M d, Y', strtotime($access['expires_at'])) : ' • No expiration' ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php if (!empty($canManage)): ?>
                                        <button type="button" data-access-id="<?= $access['id'] ?>" class="revoke-access px-3 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 transition text-xs font-medium">
                                            <i class="fas fa-ban mr-1"></i>Revoke
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="px-6 py-8 text-center">
                                <i class="fas fa-lock-open text-gray-400 text-2xl mb-2 block"></i>
                                <p class="text-gray-600 font-medium">No access grants yet</p>
                                <p class="text-gray-500 text-sm mt-1">Only you can access this file</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Version Modal -->
<div class="fixed inset-0 z-50 hidden overflow-y-auto" id="updateVersionModal" aria-hidden="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-upload mr-2"></i>Upload New Version
                </h3>
                <form id="updateVersionForm" enctype="multipart/form-data" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select New File</label>
                        <input type="file" name="file" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Change Description</label>
                        <textarea name="change_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="What changed?"></textarea>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Update
                        </button>
                        <button type="button" onclick="document.getElementById('updateVersionModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
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

// Handle update version submission
document.getElementById('updateVersionForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const tokenInput = this.querySelector('input[name="<?= csrf_token() ?>"]');

    fetch('<?= base_url('file-management/updateVersion/' . $file['id']) ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': tokenInput?.value || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.csrfHash && tokenInput) {
            tokenInput.value = data.csrfHash;
        }
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});

// Handle access grant
document.getElementById('accessForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const tokenInput = this.querySelector('input[name="<?= csrf_token() ?>"]');

    fetch('<?= base_url('file-management/grantAccess/' . $file['id']) ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': tokenInput?.value || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.csrfHash && tokenInput) {
            tokenInput.value = data.csrfHash;
        }
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});

// Handle revoke access
document.querySelectorAll('.revoke-access').forEach(btn => {
    btn.addEventListener('click', function() {
        const accessId = this.getAttribute('data-access-id');
        if (!accessId) return;

        if (!confirm('Revoke access for this user?')) return;

        const csrfInput = document.querySelector('input[name="<?= csrf_token() ?>"]');

        fetch('<?= base_url('file-management/revokeAccess/') ?>' + accessId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfInput?.value || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.csrfHash && csrfInput) {
                csrfInput.value = data.csrfHash;
            }
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});

// Toggle access target type
document.getElementById('accessTargetType')?.addEventListener('change', function() {
    const userSelect = document.getElementById('accessUserSelect');
    const roleSelect = document.getElementById('accessRoleSelect');
    const target = this.value;

    if (target === 'role') {
        userSelect.classList.add('hidden');
        roleSelect.classList.remove('hidden');
        userSelect.value = '';
    } else {
        roleSelect.classList.add('hidden');
        userSelect.classList.remove('hidden');
        roleSelect.value = '';
    }
});

// Handle file deletion
function deleteFile(fileId) {
    const isArchived = <?= json_encode($file['is_archived'] == 1) ?>;
    const message = isArchived 
        ? 'Are you sure you want to permanently delete this file? This action cannot be undone.' 
        : 'Are you sure you want to delete this file? This will archive the file.';
    
    if (confirm(message)) {
        const csrfInput = document.querySelector('input[name="<?= csrf_token() ?>"]');
        const formData = new FormData();
        formData.append('<?= csrf_token() ?>', csrfInput?.value || '');
        
        fetch('<?= base_url('file-management/delete') ?>/' + fileId, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.csrfHash && csrfInput) {
                csrfInput.value = data.csrfHash;
            }
            if (data.success) {
                alert(data.message);
                // Redirect to main file-management page
                window.location.href = '<?= base_url('file-management') ?>';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            alert('Error: ' + error.message);
        });
    }
}

</script>

<?= $this->endSection() ?>
