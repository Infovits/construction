<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Roles Management -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Roles & Permissions</h1>
            <p class="text-gray-600">Manage user roles and their permissions</p>
        </div>
        
        <?php if (hasPermission('roles.create')): ?>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/roles/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Add Role
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($roles)): ?>
        <div class="col-span-full">
            <div class="text-center py-12">
                <i data-lucide="shield-check" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                <p class="text-lg font-medium text-gray-900">No roles found</p>
                <p class="text-sm text-gray-500">Get started by creating your first role.</p>
                <?php if (hasPermission('roles.create')): ?>
                <a href="<?= base_url('admin/roles/create') ?>" class="inline-flex items-center mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Add Role
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <?php foreach ($roles as $role): ?>
        <div class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900"><?= esc($role['name']) ?></h3>
                    <p class="text-sm text-gray-500"><?= esc($role['slug']) ?></p>
                    <?php if ($role['is_system_role']): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-2">
                        System Role
                    </span>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-center space-x-2">
                    <?php if (hasPermission('roles.edit') && !$role['is_system_role']): ?>
                    <a href="<?= base_url('admin/roles/' . $role['id'] . '/edit') ?>" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (hasPermission('roles.create')): ?>
                    <a href="<?= base_url('admin/roles/' . $role['id'] . '/duplicate') ?>" class="text-green-600 hover:text-green-900" title="Duplicate">
                        <i data-lucide="copy" class="w-4 h-4"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (hasPermission('roles.delete') && !$role['is_system_role']): ?>
                    <button onclick="deleteRole(<?= $role['id'] ?>, '<?= esc($role['name']) ?>')" 
                            class="text-red-600 hover:text-red-900" title="Delete">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($role['description']): ?>
            <p class="text-sm text-gray-600 mb-4"><?= esc($role['description']) ?></p>
            <?php endif; ?>
            
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">
                    <i data-lucide="users" class="w-4 h-4 inline mr-1"></i>
                    <?= $role['user_count'] ?> users
                </span>
                
                <?php 
                $permissions = json_decode($role['permissions'], true) ?: [];
                $permissionCount = count($permissions);
                if (in_array('*', $permissions)) {
                    $permissionText = 'All permissions';
                } else {
                    $permissionText = $permissionCount . ' permissions';
                }
                ?>
                <span class="text-gray-500">
                    <i data-lucide="shield-check" class="w-4 h-4 inline mr-1"></i>
                    <?= $permissionText ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function deleteRole(id, name) {
    if (!confirm(`Are you sure you want to delete the role "${name}"? This action cannot be undone.`)) {
        return;
    }
    
    fetch(`/admin/roles/delete/${id}`, {
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
