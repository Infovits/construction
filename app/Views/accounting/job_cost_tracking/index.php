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
                    <p class="text-blue-100 text-sm font-medium">Total Entries</p>
                    <h3 class="text-3xl font-bold mt-2"><?= number_format($stats['total_entries']) ?></h3>
                </div>
                <div class="bg-blue-400 bg-opacity-30 p-3 rounded-lg">
                    <i data-lucide="list" class="w-8 h-8"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Cost</p>
                    <h3 class="text-3xl font-bold mt-2">$<?= number_format($stats['total_cost'], 2) ?></h3>
                </div>
                <div class="bg-green-400 bg-opacity-30 p-3 rounded-lg">
                    <i data-lucide="dollar-sign" class="w-8 h-8"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-cyan-100 text-sm font-medium">Average Cost</p>
                    <h3 class="text-3xl font-bold mt-2">$<?= number_format($stats['avg_cost'], 2) ?></h3>
                </div>
                <div class="bg-cyan-400 bg-opacity-30 p-3 rounded-lg">
                    <i data-lucide="calculator" class="w-8 h-8"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Categories</p>
                    <h3 class="text-3xl font-bold mt-2"><?= count($stats['by_category']) ?></h3>
                </div>
                <div class="bg-orange-400 bg-opacity-30 p-3 rounded-lg">
                    <i data-lucide="tags" class="w-8 h-8"></i>
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
                        <i data-lucide="trending-up" class="w-6 h-6 mr-2 text-blue-600"></i>
                        Job Cost Tracking
                    </h2>
                    <p class="text-gray-600 text-sm mt-1">Track and manage project costs</p>
                </div>
                <div class="flex gap-2">
                    <a href="<?= base_url('admin/accounting/job-budgets') ?>" class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                        <i data-lucide="calculator" class="w-4 h-4 mr-2"></i>
                        View Budgets
                    </a>
                    <a href="<?= base_url('admin/accounting/job-cost-tracking/create') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Add Job Cost Entry
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Projects</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>" <?= $filters['project_id'] == $project['id'] ? 'selected' : '' ?>>
                                <?= esc($project['project_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cost Code</label>
                    <select name="cost_code_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Cost Codes</option>
                        <?php foreach ($costCodes as $costCode): ?>
                            <option value="<?= $costCode['id'] ?>" <?= $filters['cost_code_id'] == $costCode['id'] ? 'selected' : '' ?>>
                                <?= esc($costCode['code']) ?> - <?= esc($costCode['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="cost_category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        <?php foreach ($costCategories as $key => $category): ?>
                            <option value="<?= $key ?>" <?= $filters['cost_category'] == $key ? 'selected' : '' ?>>
                                <?= $category ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" name="date_from" value="<?= $filters['date_from'] ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" name="date_to" value="<?= $filters['date_to'] ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="<?= esc($filters['search']) ?>" placeholder="Search..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                        Filter
                    </button>
                    <a href="<?= base_url('admin/accounting/job-cost-tracking') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <?php if (empty($jobCosts)): ?>
                <div class="text-center py-12">
                    <i data-lucide="trending-up" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                    <h5 class="text-gray-500 text-lg font-medium">No job cost entries found</h5>
                    <p class="text-gray-400 mt-2">Start tracking job costs by adding your first entry.</p>
                    <a href="<?= base_url('admin/accounting/job-cost-tracking/create') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors mt-4">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Add Job Cost Entry
                    </a>
                </div>
            <?php else: ?>
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($jobCosts as $jobCost):
                            $categoryColors = [
                                'actual' => 'green',
                                'estimated' => 'blue',
                                'budgeted' => 'yellow'
                            ];
                            $color = $categoryColors[$jobCost['cost_category']] ?? 'gray';
                        ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?= date('M j, Y', strtotime($jobCost['cost_date'])) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?= esc($jobCost['project_name']) ?></div>
                                    <div class="text-sm text-gray-500"><?= esc($jobCost['project_number']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <?= esc($jobCost['cost_code']) ?>
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1"><?= esc($jobCost['cost_code_name']) ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?= esc($jobCost['description']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800">
                                        <?= ucfirst($jobCost['cost_category']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-900"><?= number_format($jobCost['quantity'], 2) ?></td>
                                <td class="px-6 py-4 text-right text-sm text-gray-900">$<?= number_format($jobCost['unit_cost'], 2) ?></td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">$<?= number_format($jobCost['total_cost'], 2) ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?= esc($jobCost['vendor_supplier']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="<?= base_url('admin/accounting/job-cost-tracking/' . $jobCost['id']) ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="<?= base_url('admin/accounting/job-cost-tracking/' . $jobCost['id'] . '/edit') ?>" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <button onclick="deleteJobCost(<?= $jobCost['id'] ?>)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
lucide.createIcons();

function deleteJobCost(id) {
    if (confirm('Are you sure you want to delete this job cost entry?')) {
        fetch(`<?= base_url('admin/accounting/job-cost-tracking') ?>/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
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
            alert('An error occurred while deleting the job cost entry.');
        });
    }
}
</script>
<?= $this->endSection() ?>
