<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Job Budget</h1>
            <p class="text-gray-600 mt-1">Set up a new budget for project cost tracking</p>
        </div>
        <a href="<?= base_url('admin/accounting/job-budgets') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to List
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-lg">
        <form action="<?= base_url('admin/accounting/job-budgets') ?>" method="post" id="budgetForm" class="p-6">
            <?= csrf_field() ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                    <div class="flex items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-2 mt-0.5"></i>
                        <div>
                            <p class="font-medium">Please fix the following errors:</p>
                            <ul class="mt-2 ml-4 list-disc list-inside text-sm">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Basic Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Basic Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select name="project_id" id="project_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Project</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>" <?= old('project_id') == $project['id'] ? 'selected' : '' ?>>
                                    <?= esc($project['project_code']) ?> - <?= esc($project['project_name']) ?>
                                    (Est: $<?= number_format($project['estimated_budget'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Budget Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required value="<?= old('name') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">e.g., "Q1 2024 Budget" or "Foundation Phase Budget"</p>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= old('description') ?></textarea>
                </div>
            </div>

            <!-- Budget Period & Dates -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Budget Period</h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label for="budget_period" class="block text-sm font-medium text-gray-700 mb-2">
                            Period Type <span class="text-red-500">*</span>
                        </label>
                        <select name="budget_period" id="budget_period" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php foreach ($budgetPeriods as $value => $label): ?>
                                <option value="<?= $value ?>" <?= old('budget_period') == $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" required value="<?= old('start_date') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" id="end_date" required value="<?= old('end_date') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php foreach ($budgetStatuses as $value => $label): ?>
                                <option value="<?= $value ?>" <?= old('status', 'draft') == $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Total Budget -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Budget Amount</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="total_budget" class="block text-sm font-medium text-gray-700 mb-2">
                            Total Budget <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                            <input type="number" step="0.01" name="total_budget" id="total_budget" required value="<?= old('total_budget') ?>" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-lg w-full">
                            <div class="flex items-start">
                                <i data-lucide="info" class="w-5 h-5 mr-2 mt-0.5"></i>
                                <div>
                                    <p class="font-medium text-sm">Note:</p>
                                    <p class="text-xs mt-1">Allocated budget will be calculated from line items below.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Line Items -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Budget Line Items</h3>
                    <button type="button" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors" onclick="addLineItem()">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Add Line Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-300" id="lineItemsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-300">
                                    Category <span class="text-red-500">*</span>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-300">
                                    Cost Code
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-300">
                                    Description <span class="text-red-500">*</span>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-300">
                                    Amount <span class="text-red-500">*</span>
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-300">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody id="lineItemsBody" class="bg-white divide-y divide-gray-200">
                            <!-- Line items will be added here dynamically -->
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right font-semibold text-gray-900 border-t-2 border-gray-300">Total Allocated:</td>
                                <td class="px-4 py-3 font-bold text-blue-600 border-t-2 border-gray-300" id="totalAllocated">$0.00</td>
                                <td class="border-t-2 border-gray-300"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="<?= base_url('admin/accounting/job-budgets') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                    Create Budget
                </button>
            </div>
        </form>
    </div>
</div>

<script>
lucide.createIcons();

const budgetCategories = <?= json_encode($budgetCategories) ?>;
const costCodes = <?= json_encode($costCodes) ?>;

let lineItemIndex = 0;

function addLineItem() {
    const tbody = document.getElementById('lineItemsBody');
    const row = document.createElement('tr');
    row.id = 'lineItem_' + lineItemIndex;
    row.className = 'hover:bg-gray-50';

    row.innerHTML = `
        <td class="px-4 py-3">
            <select name="line_category_id[]" required onchange="updateTotal()" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Category</option>
                ${budgetCategories.map(cat => `<option value="${cat.id}">${cat.name} (${cat.budget_type})</option>`).join('')}
            </select>
        </td>
        <td class="px-4 py-3">
            <select name="line_cost_code_id[]" onchange="updateTotal()" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Cost Code (Optional)</option>
                ${costCodes.map(code => `<option value="${code.id}">${code.code} - ${code.name}</option>`).join('')}
            </select>
        </td>
        <td class="px-4 py-3">
            <input type="text" name="line_description[]" required class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </td>
        <td class="px-4 py-3">
            <input type="number" step="0.01" name="line_amount[]" required onchange="updateTotal()" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 line-amount">
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button" onclick="removeLineItem(${lineItemIndex})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </td>
    `;

    tbody.appendChild(row);
    lineItemIndex++;
    lucide.createIcons();
}

function removeLineItem(index) {
    const row = document.getElementById('lineItem_' + index);
    if (row) {
        row.remove();
        updateTotal();
    }
}

function updateTotal() {
    const amounts = document.querySelectorAll('.line-amount');
    let total = 0;

    amounts.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });

    document.getElementById('totalAllocated').textContent = '$' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Add at least one line item on page load
document.addEventListener('DOMContentLoaded', function() {
    addLineItem();
});
</script>
<?= $this->endSection() ?>
