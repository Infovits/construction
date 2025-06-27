<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- User Management Index Page -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
            <p class="text-gray-600">Manage users, roles, and permissions</p>
        </div>
        
        <?php if (hasPermission('users.create')): ?>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/users/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Add User
            </a>
            <button onclick="showBulkActions()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="settings" class="w-4 h-4 mr-2"></i>
                Bulk Actions
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">New This Month</p>
                    <p class="text-2xl font-bold text-indigo-600"><?= $userStats['new_this_month'] ?></p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="<?= esc($filters['search']) ?>" 
                       placeholder="Name, email, username..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="suspended" <?= $filters['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Roles</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= $filters['role'] == $role['id'] ? 'selected' : '' ?>>
                            <?= esc($role['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= $filters['department'] == $dept['id'] ? 'selected' : '' ?>>
                            <?= esc($dept['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                    Filter
                </button>
                <a href="<?= base_url('admin/users') ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    <i data-lucide="refresh-ccw" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Users List</h3>
            <div class="flex items-center gap-2">
                <button onclick="selectAll()" class="text-sm text-indigo-600 hover:text-indigo-800">Select All</button>
                <span class="text-gray-300">|</span>
                <button onclick="clearSelection()" class="text-sm text-gray-600 hover:text-gray-800">Clear</button>
                <?php if (hasPermission('users.view')): ?>
                <span class="text-gray-300">|</span>
                <a href="<?= base_url('admin/users/export?format=excel') ?>" class="text-sm text-green-600 hover:text-green-800">
                    <i data-lucide="download" class="w-4 h-4 inline mr-1"></i>
                    Export
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="users" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">No users found</p>
                                <p class="text-sm">Try adjusting your filters or add a new user.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                           value="<?= $user['id'] ?>">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                            <?php if ($user['profile_photo_url']): ?>
                                                <img src="<?= $user['profile_photo_url'] ?>" alt="" class="w-10 h-10 rounded-full">
                                            <?php else: ?>
                                                <span class="text-sm font-medium text-gray-600">
                                                    <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500"><?= esc($user['email']) ?></div>
                                            <?php if ($user['employee_id']): ?>
                                                <div class="text-xs text-gray-400">ID: <?= esc($user['employee_id']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($user['role_name']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            <?= esc($user['role_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">No role assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $user['department_name'] ? esc($user['department_name']) : '-' ?>
                                    <?php if ($user['position_title']): ?>
                                        <div class="text-xs text-gray-500"><?= esc($user['position_title']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($user['employment_status']): ?>
                                        <span class="capitalize"><?= esc(str_replace('_', ' ', $user['employment_status'])) ?></span>
                                        <?php if ($user['hire_date']): ?>
                                            <div class="text-xs text-gray-500">Since <?= date('M Y', strtotime($user['hire_date'])) ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'inactive' => 'bg-red-100 text-red-800',
                                        'suspended' => 'bg-yellow-100 text-yellow-800'
                                    ];
                                    $statusColor = $statusColors[$user['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColor ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <?php if (hasPermission('users.edit')): ?>
                                            <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" 
                                               class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if (hasPermission('users.edit') && $user['id'] != session('user_id')): ?>
                                            <button onclick="toggleUserStatus(<?= $user['id'] ?>, '<?= $user['status'] ?>')"
                                                    class="<?= $user['status'] === 'active' ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' ?> p-1 rounded hover:bg-gray-50">
                                                <i data-lucide="<?= $user['status'] === 'active' ? 'user-x' : 'user-check' ?>" class="w-4 h-4"></i>
                                            </button>
                                            
                                            <button onclick="resetPassword(<?= $user['id'] ?>)"
                                                    class="text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50"
                                                    title="Reset Password">
                                                <i data-lucide="key" class="w-4 h-4"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if (hasPermission('users.delete') && $user['id'] != session('user_id')): ?>
                                            <button onclick="deleteUser(<?= $user['id'] ?>, '<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>')"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div id="bulkActionsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold mb-4">Bulk Actions</h3>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600 mb-2">Selected: <span id="selectedCount">0</span> users</p>
            </div>
            <div class="space-y-2">
                <button onclick="performBulkAction('activate')" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Activate Selected Users
                </button>
                <button onclick="performBulkAction('deactivate')" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Deactivate Selected Users
                </button>
                <button onclick="performBulkAction('delete')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete Selected Users
                </button>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-6">
            <button onclick="hideBulkActions()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                Cancel
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
// User management JavaScript functions
let selectedUsers = [];

function selectAll() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
        if (!selectedUsers.includes(parseInt(checkbox.value))) {
            selectedUsers.push(parseInt(checkbox.value));
        }
    });
    
    selectAllCheckbox.checked = true;
    updateSelectedCount();
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    selectAllCheckbox.checked = false;
    selectedUsers = [];
    updateSelectedCount();
}

function updateSelectedCount() {
    document.getElementById('selectedCount').textContent = selectedUsers.length;
}

// Handle individual checkbox changes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const userId = parseInt(this.value);
            
            if (this.checked) {
                if (!selectedUsers.includes(userId)) {
                    selectedUsers.push(userId);
                }
            } else {
                const index = selectedUsers.indexOf(userId);
                if (index > -1) {
                    selectedUsers.splice(index, 1);
                }
            }
            
            // Update select all checkbox
            selectAllCheckbox.checked = selectedUsers.length === checkboxes.length;
            updateSelectedCount();
        });
    });
    
    selectAllCheckbox.addEventListener('change', function() {
        if (this.checked) {
            selectAll();
        } else {
            clearSelection();
        }
    });
});

function showBulkActions() {
    if (selectedUsers.length === 0) {
        alert('Please select at least one user');
        return;
    }
    document.getElementById('bulkActionsModal').classList.remove('hidden');
}

function hideBulkActions() {
    document.getElementById('bulkActionsModal').classList.add('hidden');
}

function performBulkAction(action) {
    if (selectedUsers.length === 0) {
        alert('No users selected');
        return;
    }
    
    const actionText = action === 'delete' ? 'delete' : action;
    if (!confirm(`Are you sure you want to ${actionText} ${selectedUsers.length} user(s)?`)) {
        return;
    }
    
    fetch('/admin/users/bulk-action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            action: action,
            user_ids: selectedUsers
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
    
    hideBulkActions();
}

function toggleUserStatus(userId, currentStatus) {
    const action = currentStatus === 'active' ? 'deactivate' : 'activate';
    const actionText = currentStatus === 'active' ? 'deactivate' : 'activate';
    
    if (!confirm(`Are you sure you want to ${actionText} this user?`)) {
        return;
    }
    
    fetch(`/admin/users/deactivate/${userId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
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
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function resetPassword(userId) {
    if (!confirm('Are you sure you want to reset this user\'s password?')) {
        return;
    }
    
    fetch(`/admin/users/reset-password/${userId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Password reset successfully. Temporary password: ${data.temp_password}`);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function deleteUser(userId, userName) {
    if (!confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
        return;
    }
    
    fetch(`/admin/users/delete/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
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
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

// Initialize Lucide icons
lucide.createIcons();
</script>
<?= $this->endSection() ?>