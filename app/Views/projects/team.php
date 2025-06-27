<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Team Management - <?= esc($project['name']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Team Management</h1>
            <p class="text-gray-600">Project: <?= esc($project['name']) ?> (<?= esc($project['project_code']) ?>)</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/projects/view/' . $project['id']) ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Project
            </a>
            <button type="button" onclick="openAddMemberModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                Add Member
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Team Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">Total Members</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($team_members) ?></p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wide">Active Members</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count(array_filter($team_members, function($m) { return !isset($m['removed_at']) || $m['removed_at'] === null; })) ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-check" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Different Roles</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count(array_unique(array_column($team_members, 'role'))) ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="briefcase" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wide">Inactive Members</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count(array_filter($team_members, function($m) { return isset($m['removed_at']) && $m['removed_at'] !== null; })) ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-x" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members Table -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Team Members</h3>
            
            <?php if (!empty($team_members)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($team_members as $member): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold text-sm">
                                                <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= esc($member['first_name'] . ' ' . $member['last_name']) ?>
                                            </div>
                                            <div class="flex gap-1 mt-1">
                                                <?php if (isset($project['project_manager_id']) && $member['user_id'] == $project['project_manager_id']): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        Project Manager
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (isset($project['site_supervisor_id']) && $member['user_id'] == $project['site_supervisor_id']): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Site Supervisor
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <?= esc($member['role']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?= esc($member['email']) ?></div>
                                    <div class="text-sm text-gray-500"><?= esc($member['phone'] ?? 'Not provided') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M d, Y', strtotime($member['assigned_at'] ?? date('Y-m-d'))) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!isset($member['removed_at']) || $member['removed_at'] === null): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                            Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button type="button" onclick="editMember(<?= $member['id'] ?>, '<?= esc($member['role']) ?>', <?= (!isset($member['removed_at']) || $member['removed_at'] === null) ? 'true' : 'false' ?>)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                        <button type="button" onclick="removeMember(<?= $member['id'] ?>, '<?= esc($member['first_name'] . ' ' . $member['last_name']) ?>')" class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i data-lucide="users" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No team members assigned</h3>
                    <p class="text-gray-500 mb-4">Get started by adding the first team member to this project.</p>
                    <button type="button" onclick="openAddMemberModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                        Add First Member
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div id="addMemberModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md bg-white rounded-lg shadow-lg">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add Team Member</h3>
                <button type="button" onclick="closeAddMemberModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="<?= base_url('admin/projects/team/add/' . $project['id']) ?>" method="post" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Select User *</label>
                    <select id="user_id" name="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Choose a user...</option>
                        <?php foreach ($available_users as $user): ?>
                            <option value="<?= $user['id'] ?>">
                                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> (<?= esc($user['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select id="role" name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Select role...</option>
                        <option value="Project Manager">Project Manager</option>
                        <option value="Site Supervisor">Site Supervisor</option>
                        <option value="Architect">Architect</option>
                        <option value="Engineer">Engineer</option>
                        <option value="Contractor">Contractor</option>
                        <option value="Foreman">Foreman</option>
                        <option value="Worker">Worker</option>
                        <option value="Quality Inspector">Quality Inspector</option>
                        <option value="Safety Officer">Safety Officer</option>
                        <option value="Consultant">Consultant</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div id="customRoleGroup" class="hidden">
                    <label for="custom_role" class="block text-sm font-medium text-gray-700 mb-1">Custom Role</label>
                    <input type="text" id="custom_role" name="custom_role" placeholder="Enter custom role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">Active member</label>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddMemberModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Add Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Member Modal -->
<div id="editMemberModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md bg-white rounded-lg shadow-lg">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit Team Member</h3>
                <button type="button" onclick="closeEditMemberModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="editMemberForm" method="post" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label for="edit_role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select id="edit_role" name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="Project Manager">Project Manager</option>
                        <option value="Site Supervisor">Site Supervisor</option>
                        <option value="Architect">Architect</option>
                        <option value="Engineer">Engineer</option>
                        <option value="Contractor">Contractor</option>
                        <option value="Foreman">Foreman</option>
                        <option value="Worker">Worker</option>
                        <option value="Quality Inspector">Quality Inspector</option>
                        <option value="Safety Officer">Safety Officer</option>
                        <option value="Consultant">Consultant</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="edit_is_active" class="ml-2 block text-sm text-gray-700">Active member</label>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditMemberModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Update Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Modal functions
function openAddMemberModal() {
    document.getElementById('addMemberModal').classList.remove('hidden');
}

function closeAddMemberModal() {
    document.getElementById('addMemberModal').classList.add('hidden');
}

function openEditMemberModal() {
    document.getElementById('editMemberModal').classList.remove('hidden');
}

function closeEditMemberModal() {
    document.getElementById('editMemberModal').classList.add('hidden');
}

// Handle custom role field visibility
document.getElementById('role').addEventListener('change', function() {
    const customRoleGroup = document.getElementById('customRoleGroup');
    const customRoleInput = document.getElementById('custom_role');
    
    if (this.value === 'Other') {
        customRoleGroup.classList.remove('hidden');
        customRoleInput.setAttribute('required', 'required');
    } else {
        customRoleGroup.classList.add('hidden');
        customRoleInput.removeAttribute('required');
    }
});

function editMember(memberId, currentRole, isActive) {
    document.getElementById('edit_role').value = currentRole;
    document.getElementById('edit_is_active').checked = isActive;
    document.getElementById('editMemberForm').action = '<?= base_url('admin/projects/team/update/' . $project['id']) ?>/' + memberId;
    openEditMemberModal();
}

function removeMember(memberId, memberName) {
    if (confirm('Are you sure you want to remove ' + memberName + ' from this project?')) {
        fetch('<?= base_url('admin/projects/team/remove/' . $project['id']) ?>/' + memberId, {
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
                alert('Error removing team member: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing team member');
        });
    }
}

// Close modals when clicking outside
document.getElementById('addMemberModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddMemberModal();
    }
});

document.getElementById('editMemberModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditMemberModal();
    }
});
</script>
<?= $this->endSection() ?>
