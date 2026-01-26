<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= esc($budget['name']) ?></h1>
            <p class="text-gray-600 mt-1">Budget details and utilization tracking</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/accounting/job-budgets') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
            <a href="<?= base_url('admin/accounting/job-budgets/' . $budget['id'] . '/edit') ?>" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit Budget
            </a>
            <button type="button" onclick="updateActuals()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                Update Actuals
            </button>
        </div>
    </div>

    <!-- Budget Overview Card -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 rounded-t-lg">
            <h2 class="text-xl font-semibold flex items-center">
                <i data-lucide="file-text" class="w-6 h-6 mr-2"></i>
                Budget Overview
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column - Details -->
                <div class="space-y-4">
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="font-medium text-gray-700">Project:</span>
                        <span class="text-gray-900">
                            <span class="font-semibold"><?= esc($budget['project_code']) ?></span> - <?= esc($budget['project_name']) ?>
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="font-medium text-gray-700">Budget Period:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                            <?= ucfirst($budget['budget_period']) ?>
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="font-medium text-gray-700">Date Range:</span>
                        <span class="text-gray-900">
                            <?= date('M d, Y', strtotime($budget['start_date'])) ?> to <?= date('M d, Y', strtotime($budget['end_date'])) ?>
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="font-medium text-gray-700">Status:</span>
                        <span>
                            <?php
                            $statusColors = [
                                'draft' => 'gray',
                                'active' => 'green',
                                'completed' => 'blue',
                                'cancelled' => 'red'
                            ];
                            $color = $statusColors[$budget['status']] ?? 'gray';
                            ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800">
                                <?= ucfirst($budget['status']) ?>
                            </span>
                        </span>
                    </div>
                    <?php if ($budget['description']): ?>
                        <div class="py-2">
                            <span class="font-medium text-gray-700">Description:</span>
                            <p class="text-gray-900 mt-1"><?= esc($budget['description']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column - Financial Summary -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Summary</h3>
                    <?php
                    $utilization = $budget['total_budget'] > 0 ? ($budget['spent_amount'] / $budget['total_budget']) * 100 : 0;
                    $variance = $budget['spent_amount'] - $budget['total_budget'];
                    $variancePercent = $budget['total_budget'] > 0 ? ($variance / $budget['total_budget']) * 100 : 0;
                    ?>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Budget:</span>
                            <span class="text-lg font-bold text-blue-600">$<?= number_format($budget['total_budget'], 2) ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Allocated Budget:</span>
                            <span class="text-lg font-semibold text-gray-900">$<?= number_format($budget['allocated_budget'], 2) ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Spent Amount:</span>
                            <span class="text-lg font-bold text-orange-600">$<?= number_format($budget['spent_amount'], 2) ?></span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t-2 border-gray-300">
                            <span class="text-sm font-medium text-gray-700">Remaining Budget:</span>
                            <span class="text-xl font-bold <?= $budget['remaining_budget'] < 0 ? 'text-red-600' : 'text-green-600' ?>">
                                $<?= number_format($budget['remaining_budget'], 2) ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Variance:</span>
                            <span class="<?= $variance > 0 ? 'text-red-600' : 'text-green-600' ?> font-semibold">
                                <?= $variance > 0 ? '+' : '' ?>$<?= number_format(abs($variance), 2) ?>
                                (<?= $variance > 0 ? '+' : '' ?><?= number_format($variancePercent, 1) ?>%)
                                <?= $variance > 0 ? '↑' : '↓' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Utilization -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Budget Utilization</h3>
        </div>
        <div class="p-6">
            <div class="mb-2 flex justify-between text-sm">
                <span class="font-medium text-gray-700">Budget Usage: <?= number_format($utilization, 1) ?>%</span>
                <span class="text-gray-600">$<?= number_format($budget['spent_amount'], 2) ?> of $<?= number_format($budget['total_budget'], 2) ?></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-8 mb-4">
                <div class="h-8 rounded-full flex items-center justify-center text-sm font-semibold text-white transition-all <?= $utilization > 90 ? 'bg-red-500' : ($utilization > 75 ? 'bg-yellow-500' : 'bg-green-500') ?>"
                     style="width: <?= min($utilization, 100) ?>%">
                    <?= number_format($utilization, 1) ?>%
                </div>
            </div>

            <?php if ($utilization > 90): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i data-lucide="alert-triangle" class="w-5 h-5 mr-2 mt-0.5"></i>
                        <div>
                            <p class="font-medium">Warning: Critical Budget Level</p>
                            <p class="text-sm mt-1">Budget utilization is over 90%. Consider adjusting budget or reducing expenses.</p>
                        </div>
                    </div>
                </div>
            <?php elseif ($utilization > 75): ?>
                <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-2 mt-0.5"></i>
                        <div>
                            <p class="font-medium">Caution: High Budget Usage</p>
                            <p class="text-sm mt-1">Budget utilization is over 75%. Monitor spending closely.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Budget Line Items -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i data-lucide="list" class="w-5 h-5 mr-2"></i>
                Budget Line Items
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Budgeted</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actual</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Variance</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($budget['line_items'])): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i data-lucide="inbox" class="w-16 h-16 mx-auto text-gray-400 mb-4"></i>
                                <p class="text-gray-500 text-lg">No line items defined for this budget.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $totalBudgeted = 0;
                        $totalActual = 0;
                        foreach ($budget['line_items'] as $item):
                            $totalBudgeted += $item['budgeted_amount'];
                            $totalActual += $item['actual_amount'];
                            $itemVariance = $item['actual_amount'] - $item['budgeted_amount'];
                            $itemProgress = $item['budgeted_amount'] > 0 ? ($item['actual_amount'] / $item['budgeted_amount']) * 100 : 0;
                        ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?= esc($item['category_name']) ?></div>
                                    <div class="text-xs text-gray-500 mt-1"><?= ucfirst($item['budget_type']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($item['cost_code']): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?= esc($item['cost_code']) ?>
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1"><?= esc($item['cost_code_name']) ?></div>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-900"><?= esc($item['description']) ?></td>
                                <td class="px-6 py-4 text-right font-medium text-gray-900">$<?= number_format($item['budgeted_amount'], 2) ?></td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-semibold <?= $item['actual_amount'] > $item['budgeted_amount'] ? 'text-red-600' : 'text-gray-900' ?>">
                                        $<?= number_format($item['actual_amount'], 2) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-semibold <?= $itemVariance > 0 ? 'text-red-600' : 'text-green-600' ?>">
                                        <?= $itemVariance > 0 ? '+' : '' ?>$<?= number_format($itemVariance, 2) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-5">
                                            <div class="h-5 rounded-full flex items-center justify-center text-xs font-semibold text-white <?= $itemProgress > 100 ? 'bg-red-500' : ($itemProgress > 75 ? 'bg-yellow-500' : 'bg-green-500') ?>"
                                                 style="width: <?= min($itemProgress, 100) ?>%">
                                                <?= number_format($itemProgress, 0) ?>%
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- Totals Row -->
                        <tr class="bg-gray-100 font-bold border-t-2 border-gray-300">
                            <td colspan="3" class="px-6 py-4 text-right text-gray-900">TOTAL:</td>
                            <td class="px-6 py-4 text-right text-blue-600">$<?= number_format($totalBudgeted, 2) ?></td>
                            <td class="px-6 py-4 text-right text-orange-600">$<?= number_format($totalActual, 2) ?></td>
                            <td class="px-6 py-4 text-right <?= ($totalActual - $totalBudgeted) > 0 ? 'text-red-600' : 'text-green-600' ?>">
                                <?= ($totalActual - $totalBudgeted) > 0 ? '+' : '' ?>$<?= number_format($totalActual - $totalBudgeted, 2) ?>
                            </td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
lucide.createIcons();

function updateActuals() {
    if (confirm('This will update all actual amounts from job cost tracking. Continue?')) {
        fetch('<?= base_url('admin/accounting/job-budgets/' . $budget['id'] . '/update-actuals') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Budget actuals updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error updating budget actuals');
            console.error('Error:', error);
        });
    }
}
</script>
<?= $this->endSection() ?>
