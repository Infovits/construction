<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Departments Management -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Department Management</h1>
            <p class="text-gray-600">Manage organizational departments and their structure</p>
        </div>
        
        <?php if (hasPermission('departments.create')): ?>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/departments/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Add Department
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow-sm border">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="<?= esc($filters['search']) ?>" 
                       placeholder="Search by name, code..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                    Filter
                </button>
                <a href="<?= base_url('admin/departments') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Departments Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manager</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($departments)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i data-lucide="building-2" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">No departments found</p>
                            <p class="text-sm">Get started by creating your first department.</p>
                            <?php if (hasPermission('departments.create')): ?>
                            <a href="<?= base_url('admin/departments/create') ?>" class="inline-flex items-center mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                Add Department
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($departments as $department): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-medium text-gray-900"><?= esc($department['name']) ?></div>
                                <div class="text-sm text-gray-500">Code: <?= esc($department['code']) ?></div>
                                <?php if ($department['description']): ?>
                                <div class="text-sm text-gray-500 mt-1"><?= esc($department['description']) ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?= $department['manager_name'] ? esc($department['manager_name']) : '<span class="text-gray-400">No manager</span>' ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?= $department['parent_name'] ? esc($department['parent_name']) : '<span class="text-gray-400">Root department</span>' ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <?= $department['employee_count'] ?> employees
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?= $department['budget'] > 0 ? format_currency($department['budget']) : '<span class="text-gray-400">Not set</span>' ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $department['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= ucfirst($department['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <?php if (hasPermission('departments.edit')): ?>
                                <a href="<?= base_url('admin/departments/edit/' . $department['id']) ?>" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <button onclick="toggleStatus(<?= $department['id'] ?>, '<?= $department['status'] ?>')" 
                                        class="text-amber-600 hover:text-amber-900" title="Toggle Status">
                                    <i data-lucide="power" class="w-4 h-4"></i>
                                </button>
                                <?php endif; ?>
                                <?php if (hasPermission('departments.delete')): ?>
                                <button onclick="deleteDepartment(<?= $department['id'] ?>, '<?= esc($department['name']) ?>')" 
                                        class="text-red-600 hover:text-red-900" title="Delete">
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

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function deleteDepartment(id, name) {
    if (!confirm(`Are you sure you want to delete the department "${name}"? This action cannot be undone.`)) {
        return;
    }
    
    fetch(`/admin/departments/delete/${id}`, {
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

function toggleStatus(id, currentStatus) {
    const action = currentStatus === 'active' ? 'deactivate' : 'activate';
    
    if (!confirm(`Are you sure you want to ${action} this department?`)) {
        return;
    }
    
    fetch(`/admin/departments/toggle/${id}`, {
        method: 'PATCH',
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
