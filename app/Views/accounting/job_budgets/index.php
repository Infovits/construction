<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                <p><?= session()->getFlashdata('success') ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
            <div class="flex items-center">
                <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                <p><?= session()->getFlashdata('error') ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg">
            <div class="flex items-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
                <p><?= $error_message ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Budgets</p>
                    <h3 class="text-3xl font-bold mt-2"><?= number_format($stats['total_budgets']) ?></h3>
                </div>
                <div class="bg-blue-400 bg-opacity-30 p-3 rounded-lg">
                    <i data-lucide="file-text" class="w-8 h-8"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Budgeted</p>
                    <h3 class="text-3xl font-bold mt-2">$<?= number_format($stats['total_budgeted'], 2) ?></h3>
                </div>
                <div class="bg-green-400 bg-opacity-30 p-3 rounded-lg">
                    <i data-lucide="dollar-sign" class="w-8 h-8"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Total Spent</p>
                    <h3 class="text-3xl font-bold mt-2">$<?= number_format($stats['total_spent'], 2) ?></h3>
                </div>
                <div class="bg-orange-400 bg-opacity-30 p-3 rounded-lg">
                    <i data-lucide="receipt" class="w-8 h-8"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-<?= $stats['utilization_percentage'] > 90 ? 'red' : 'purple' ?>-500 to-<?= $stats['utilization_percentage'] > 90 ? 'red' : 'purple' ?>-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-<?= $stats['utilization_percentage'] > 90 ? 'red' : 'purple' ?>-100 text-sm font-medium">Utilization</p>
                    <h3 class="text-3xl font-bold mt-2"><?= number_format($stats['utilization_percentage'], 1) ?>%</h3>
                </div>
                <div class="bg-<?= $stats['utilization_percentage'] > 90 ? 'red' : 'purple' ?>-400 bg-opacity-30 p-3 rounded-lg">
                    <i data-lucide="pie-chart" class="w-8 h-8"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-lg">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i data-lucide="calculator" class="w-6 h-6 mr-2 text-blue-600"></i>
                        Job Budgets
                    </h2>
                    <p class="text-gray-600 text-sm mt-1">Manage project budgets and track utilization</p>
                </div>
                <a href="<?= base_url('admin/accounting/job-budgets/create') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Create Budget
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <form method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Projects</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>" <?= $filters['project_id'] == $project['id'] ? 'selected' : '' ?>>
                                <?= esc($project['project_code']) ?> - <?= esc($project['project_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <?php foreach ($budgetStatuses as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $filters['status'] == $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Period</label>
                    <select name="budget_period" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Periods</option>
                        <?php foreach ($budgetPeriods as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $filters['budget_period'] == $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="<?= esc($filters['search']) ?>" placeholder="Search budgets..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Budget</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Spent</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Utilization</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($budgets)): ?>
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <i data-lucide="inbox" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                                <p class="text-gray-500 text-lg">No budgets found</p>
                                <p class="text-gray-400 mt-2">Create your first budget to get started</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($budgets as $budget):
                            $utilization = $budget['total_budget'] > 0 ? ($budget['spent_amount'] / $budget['total_budget']) * 100 : 0;
                            $statusColors = [
                                'draft' => 'gray',
                                'active' => 'green',
                                'completed' => 'blue',
                                'cancelled' => 'red'
                            ];
                            $color = $statusColors[$budget['status']] ?? 'gray';
                        ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?= esc($budget['name']) ?></div>
                                    <?php if ($budget['description']): ?>
                                        <div class="text-sm text-gray-500 mt-1"><?= esc(substr($budget['description'], 0, 50)) ?><?= strlen($budget['description']) > 50 ? '...' : '' ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?= esc($budget['project_code']) ?></div>
                                    <div class="text-sm text-gray-500"><?= esc($budget['project_name']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><?= ucfirst($budget['budget_period']) ?></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?= date('M d, Y', strtotime($budget['start_date'])) ?><br>
                                    to <?= date('M d, Y', strtotime($budget['end_date'])) ?>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900">$<?= number_format($budget['total_budget'], 2) ?></td>
                                <td class="px-6 py-4 text-right text-gray-900">$<?= number_format($budget['spent_amount'], 2) ?></td>
                                <td class="px-6 py-4">
                                    <div class="w-full bg-gray-200 rounded-full h-6">
                                        <div class="h-6 rounded-full flex items-center justify-center text-xs font-semibold text-white <?= $utilization > 90 ? 'bg-red-500' : ($utilization > 75 ? 'bg-yellow-500' : 'bg-green-500') ?>"
                                             style="width: <?= min($utilization, 100) ?>%">
                                            <?= number_format($utilization, 0) ?>%
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800">
                                        <?= ucfirst($budget['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="<?= base_url('admin/accounting/job-budgets/' . $budget['id']) ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="<?= base_url('admin/accounting/job-budgets/' . $budget['id'] . '/edit') ?>" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <button onclick="deleteBudget(<?= $budget['id'] ?>)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
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

<script>
lucide.createIcons();

function deleteBudget(id) {
    if (confirm('Are you sure you want to delete this budget? This action cannot be undone.')) {
        fetch('<?= base_url('admin/accounting/job-budgets') ?>/' + id, {
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
            alert('Error deleting budget');
            console.error('Error:', error);
        });
    }
}
</script>
<?= $this->endSection() ?>
