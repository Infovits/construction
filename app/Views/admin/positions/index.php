<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Positions Management -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Job Positions</h1>
            <p class="text-gray-600">Manage job positions across departments</p>
        </div>
        
        <?php if (hasPermission('positions.create')): ?>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/positions/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Add Position
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Positions Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employment Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary Range</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($positions)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i data-lucide="briefcase" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">No positions found</p>
                            <p class="text-sm">Get started by creating your first job position.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($positions as $position): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-medium text-gray-900"><?= esc($position['title']) ?></div>
                                <div class="text-sm text-gray-500">Code: <?= esc($position['code']) ?></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?= esc($position['department_name']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <?= ucfirst(str_replace('_', ' ', $position['employment_type'] ?? 'full_time')) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <?= $position['employee_count'] ?> employees
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?php if ($position['min_salary'] > 0 || $position['max_salary'] > 0): ?>
                                <?= format_currency($position['min_salary']) ?> - <?= format_currency($position['max_salary']) ?>
                            <?php else: ?>
                                <span class="text-gray-400">Not set</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $position['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $position['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <?php if (hasPermission('positions.edit')): ?>
                                <a href="<?= base_url('admin/positions/' . $position['id'] . '/edit') ?>" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <button onclick="toggleStatus(<?= $position['id'] ?>, <?= $position['is_active'] ? '1' : '0' ?>)" 
                                        class="text-amber-600 hover:text-amber-900" title="Toggle Status">
                                    <i data-lucide="power" class="w-4 h-4"></i>
                                </button>
                                <?php endif; ?>
                                <?php if (hasPermission('positions.delete')): ?>
                                <button onclick="deletePosition(<?= $position['id'] ?>, '<?= esc($position['title']) ?>')" 
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
function deletePosition(id, name) {
    if (!confirm(`Are you sure you want to delete the position "${name}"? This action cannot be undone.`)) {
        return;
    }
    
    fetch(`/admin/positions/delete/${id}`, {
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
    const action = currentStatus === '1' ? 'deactivate' : 'activate';
    
    if (!confirm(`Are you sure you want to ${action} this position?`)) {
        return;
    }
    
    fetch(`<?= base_url('admin/positions/toggle/') ?>${id}`, {
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
