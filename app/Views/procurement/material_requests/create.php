<?php helper('currency'); ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Material Request</h1>
            <p class="text-gray-600">Request materials for your project or department</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/material-requests') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Requests
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Request Details</h3>
        </div>

        <form method="POST" action="<?= base_url('admin/material-requests/store') ?>" class="p-6 space-y-6">
            <?= csrf_field() ?>
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Project (Optional)</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>"><?= esc($project['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select name="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Department (Optional)</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?= $department['id'] ?>"><?= esc($department['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Required Date</label>
                    <input type="date" name="required_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                    <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Priority</option>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Additional notes or requirements..."></textarea>
            </div>

            <!-- Material Items -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900">Requested Materials</h4>
                    <button type="button" onclick="addMaterialRow()" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Add Material
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Material</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Est. Unit Cost</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Est. Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Specifications</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="materialItems" class="divide-y divide-gray-200">
                            <!-- Material rows will be added here -->
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <div class="text-lg font-semibold text-gray-900">
                        Total Estimated Cost: <span id="totalCost">MWK 0.00</span>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="<?= base_url('admin/material-requests') ?>" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Create Request
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Material Row Template -->
<template id="materialRowTemplate">
    <tr class="material-row">
        <td class="px-4 py-3">
            <select name="items[INDEX][material_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Select Material</option>
                <?php foreach ($materials as $material): ?>
                    <option value="<?= $material['id'] ?>" data-unit-cost="<?= $material['unit_cost'] ?>" data-unit="<?= $material['unit'] ?>">
                        <?= esc($material['name']) ?> (<?= esc($material['item_code']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="px-4 py-3">
            <input type="number" name="items[INDEX][quantity_requested]" step="0.001" min="0.001" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 quantity-input"
                   onchange="calculateRowTotal(this)">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="items[INDEX][estimated_unit_cost]" step="0.01" min="0" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 unit-cost-input"
                   onchange="calculateRowTotal(this)">
        </td>
        <td class="px-4 py-3">
            <div class="text-sm font-medium text-gray-900 row-total">MWK 0.00</div>
        </td>
        <td class="px-4 py-3">
            <textarea name="items[INDEX][specification_notes]" rows="2" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Specifications..."></textarea>
        </td>
        <td class="px-4 py-3">
            <button type="button" onclick="removeMaterialRow(this)" class="text-red-600 hover:text-red-900">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </td>
    </tr>
</template>

<script>
let materialRowIndex = 0;

function addMaterialRow() {
    const template = document.getElementById('materialRowTemplate');
    const tbody = document.getElementById('materialItems');
    
    const newRow = template.content.cloneNode(true);
    
    // Replace INDEX placeholder with actual index
    const html = newRow.querySelector('tr').outerHTML.replace(/INDEX/g, materialRowIndex);
    tbody.insertAdjacentHTML('beforeend', html);
    
    materialRowIndex++;
    
    // Initialize Lucide icons for the new row
    lucide.createIcons();
}

function removeMaterialRow(button) {
    button.closest('tr').remove();
    calculateTotalCost();
}

function calculateRowTotal(input) {
    const row = input.closest('tr');
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const unitCost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
    const total = quantity * unitCost;
    
    row.querySelector('.row-total').textContent = 'MWK ' + total.toFixed(2);
    calculateTotalCost();
}

function calculateTotalCost() {
    let total = 0;
    document.querySelectorAll('.row-total').forEach(element => {
        const value = parseFloat(element.textContent.replace('MWK ', '')) || 0;
        total += value;
    });
    
    document.getElementById('totalCost').textContent = 'MWK ' + total.toFixed(2);
}

// Auto-populate unit cost when material is selected
document.addEventListener('change', function(e) {
    if (e.target.name && e.target.name.includes('[material_id]')) {
        const selectedOption = e.target.selectedOptions[0];
        const unitCost = selectedOption.getAttribute('data-unit-cost');
        
        if (unitCost) {
            const row = e.target.closest('tr');
            const unitCostInput = row.querySelector('.unit-cost-input');
            unitCostInput.value = unitCost;
            calculateRowTotal(unitCostInput);
        }
    }
});

// Add initial row
document.addEventListener('DOMContentLoaded', function() {
    addMaterialRow();
    lucide.createIcons();
});
</script>

<?= $this->endSection() ?>