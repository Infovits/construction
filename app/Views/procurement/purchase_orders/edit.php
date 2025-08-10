<?php helper('currency'); ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Purchase Order</h1>
            <p class="text-gray-600">Edit Purchase Order #<?= esc($purchaseOrder['po_number']) ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/purchase-orders/' . $purchaseOrder['id']) ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Cancel
            </a>
        </div>
    </div>

    <!-- Purchase Order Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form id="purchaseOrderForm" action="<?= base_url('admin/purchase-orders/' . $purchaseOrder['id'] . '/update') ?>" method="POST">
            <?= csrf_field() ?>
            
            <!-- Header Information -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- PO Number -->
                    <div>
                        <label for="po_number" class="block text-sm font-medium text-gray-700 mb-2">PO Number</label>
                        <input type="text" id="po_number" name="po_number" 
                               value="<?= esc($purchaseOrder['po_number']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required readonly>
                    </div>

                    <!-- Supplier -->
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Supplier <span class="text-red-500">*</span></label>
                        <select id="supplier_id" name="supplier_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                required>
                            <option value="">Select Supplier</option>
                            <?php if (isset($suppliers)): ?>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= $supplier['id'] ?>" 
                                            <?= $purchaseOrder['supplier_id'] == $supplier['id'] ? 'selected' : '' ?>>
                                        <?= esc($supplier['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Project -->
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                        <select id="project_id" name="project_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Project (Optional)</option>
                            <?php if (isset($projects)): ?>
                                <?php foreach ($projects as $project): ?>
                                    <option value="<?= $project['id'] ?>" 
                                            <?= $purchaseOrder['project_id'] == $project['id'] ? 'selected' : '' ?>>
                                        <?= esc($project['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- PO Date -->
                    <div>
                        <label for="po_date" class="block text-sm font-medium text-gray-700 mb-2">PO Date <span class="text-red-500">*</span></label>
                        <input type="date" id="po_date" name="po_date" 
                               value="<?= $purchaseOrder['po_date'] ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                    </div>

                    <!-- Expected Delivery Date -->
                    <div>
                        <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Expected Delivery Date</label>
                        <input type="date" id="expected_delivery_date" name="expected_delivery_date" 
                               value="<?= $purchaseOrder['expected_delivery_date'] ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                    <button type="button" id="addItemBtn" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Add Item
                    </button>
                </div>

                <div id="itemsContainer">
                    <div class="overflow-x-auto">
                        <table class="w-full" id="itemsTable">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 font-medium text-gray-900">Material</th>
                                    <th class="text-left py-3 font-medium text-gray-900 w-24">Quantity</th>
                                    <th class="text-left py-3 font-medium text-gray-900 w-32">Unit Cost</th>
                                    <th class="text-left py-3 font-medium text-gray-900 w-32">Total</th>
                                    <th class="text-left py-3 font-medium text-gray-900 w-20">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <?php if (isset($purchaseOrder['items']) && !empty($purchaseOrder['items'])): ?>
                                    <?php foreach ($purchaseOrder['items'] as $index => $item): ?>
                                        <tr class="border-b border-gray-100">
                                            <td class="py-3">
                                                <select name="items[<?= $index ?>][material_id]" class="material-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                                    <option value="">Select Material</option>
                                                    <?php if (isset($materials)): ?>
                                                        <?php foreach ($materials as $material): ?>
                                                            <option value="<?= $material['id'] ?>" 
                                                                    data-unit-cost="<?= $material['unit_cost'] ?>" 
                                                                    data-unit="<?= $material['unit'] ?>"
                                                                    <?= $item['material_id'] == $material['id'] ? 'selected' : '' ?>>
                                                                <?= esc($material['name']) ?> (<?= esc($material['unit']) ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </td>
                                            <td class="py-3">
                                                <input type="number" name="items[<?= $index ?>][quantity_ordered]" 
                                                       value="<?= $item['quantity_ordered'] ?>"
                                                       class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                                       step="0.001" min="0" required>
                                            </td>
                                            <td class="py-3">
                                                <input type="number" name="items[<?= $index ?>][unit_cost]" 
                                                       value="<?= $item['unit_cost'] ?>"
                                                       class="unit-cost-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                                       step="0.01" min="0" required>
                                            </td>
                                            <td class="py-3">
                                                <span class="item-total font-medium">MWK <?= number_format($item['total_cost'], 2) ?></span>
                                            </td>
                                            <td class="py-3">
                                                <button type="button" class="remove-item text-red-600 hover:text-red-800">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Totals Section -->
            <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                        
                        <!-- Payment Terms -->
                        <div class="mb-4">
                            <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                            <input type="text" id="payment_terms" name="payment_terms" 
                                   value="<?= esc($purchaseOrder['payment_terms'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Delivery Terms -->
                        <div class="mb-4">
                            <label for="delivery_terms" class="block text-sm font-medium text-gray-700 mb-2">Delivery Terms</label>
                            <input type="text" id="delivery_terms" name="delivery_terms" 
                                   value="<?= esc($purchaseOrder['delivery_terms'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Currency -->
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                            <select id="currency" name="currency" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="MWK" <?= ($purchaseOrder['currency'] ?? 'MWK') === 'MWK' ? 'selected' : '' ?>>Malawi Kwacha (MWK)</option>
                                <option value="USD" <?= ($purchaseOrder['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>US Dollar (USD)</option>
                                <option value="EUR" <?= ($purchaseOrder['currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>Euro (EUR)</option>
                                <option value="GBP" <?= ($purchaseOrder['currency'] ?? '') === 'GBP' ? 'selected' : '' ?>>British Pound (GBP)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Totals</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span id="subtotalDisplay" class="font-medium">MWK <?= number_format($purchaseOrder['subtotal'], 2) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax Amount:</span>
                                <span id="taxAmountDisplay" class="font-medium">MWK <?= number_format($purchaseOrder['tax_amount'], 2) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Freight Cost:</span>
                                <span id="freightCostDisplay" class="font-medium">MWK <?= number_format($purchaseOrder['freight_cost'], 2) ?></span>
                            </div>
                            <hr class="my-2">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total Amount:</span>
                                <span id="totalAmountDisplay">MWK <?= number_format($purchaseOrder['total_amount'], 2) ?></span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="tax_amount" class="block text-sm font-medium text-gray-700 mb-2">Tax Amount</label>
                            <input type="number" id="tax_amount" name="tax_amount" step="0.01" 
                                   value="<?= $purchaseOrder['tax_amount'] ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="mt-4">
                            <label for="freight_cost" class="block text-sm font-medium text-gray-700 mb-2">Freight Cost</label>
                            <input type="number" id="freight_cost" name="freight_cost" step="0.01" 
                                   value="<?= $purchaseOrder['freight_cost'] ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea id="notes" name="notes" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= esc($purchaseOrder['notes'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label for="terms_conditions" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                        <textarea id="terms_conditions" name="terms_conditions" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= esc($purchaseOrder['terms_conditions'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="p-6 bg-gray-50 rounded-b-lg">
                <div class="flex flex-col sm:flex-row gap-2 sm:justify-end">
                    <a href="<?= base_url('admin/purchase-orders/' . $purchaseOrder['id']) ?>" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Update Purchase Order
                    </button>
                </div>
            </div>

            <!-- Hidden fields -->
            <input type="hidden" id="subtotal" name="subtotal" value="<?= $purchaseOrder['subtotal'] ?>">
            <input type="hidden" id="total_amount" name="total_amount" value="<?= $purchaseOrder['total_amount'] ?>">
        </form>
    </div>
</div>

<!-- Item Row Template -->
<template id="itemRowTemplate">
    <tr class="border-b border-gray-100">
        <td class="py-3">
            <select name="items[__INDEX__][material_id]" class="material-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                <option value="">Select Material</option>
                <?php if (isset($materials)): ?>
                    <?php foreach ($materials as $material): ?>
                        <option value="<?= $material['id'] ?>" data-unit-cost="<?= $material['unit_cost'] ?>" data-unit="<?= $material['unit'] ?>">
                            <?= esc($material['name']) ?> (<?= esc($material['unit']) ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </td>
        <td class="py-3">
            <input type="number" name="items[__INDEX__][quantity_ordered]" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" step="0.001" min="0" required>
        </td>
        <td class="py-3">
            <input type="number" name="items[__INDEX__][unit_cost]" class="unit-cost-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" step="0.01" min="0" required>
        </td>
        <td class="py-3">
            <span class="item-total font-medium">MWK 0.00</span>
        </td>
        <td class="py-3">
            <button type="button" class="remove-item text-red-600 hover:text-red-800">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = <?= isset($purchaseOrder['items']) ? count($purchaseOrder['items']) : 0 ?>;
    
    // Add item functionality
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const template = document.getElementById('itemRowTemplate');
        const tbody = document.getElementById('itemsTableBody');
        
        const clone = template.content.cloneNode(true);
        const html = clone.querySelector('tr').outerHTML.replace(/__INDEX__/g, itemIndex);
        
        tbody.insertAdjacentHTML('beforeend', html);
        itemIndex++;
        
        // Reinitialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        updateTotals();
    });
    
    // Remove item functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            e.target.closest('tr').remove();
            updateTotals();
        }
    });
    
    // Update totals when inputs change
    document.addEventListener('input', function(e) {
        if (e.target.matches('.quantity-input, .unit-cost-input, #tax_amount, #freight_cost')) {
            if (e.target.matches('.quantity-input, .unit-cost-input')) {
                updateItemTotal(e.target.closest('tr'));
            }
            updateTotals();
        }
    });
    
    // Auto-fill unit cost when material is selected
    document.addEventListener('change', function(e) {
        if (e.target.matches('.material-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const unitCost = selectedOption.getAttribute('data-unit-cost') || '0.00';
            const row = e.target.closest('tr');
            const unitCostInput = row.querySelector('.unit-cost-input');
            
            unitCostInput.value = parseFloat(unitCost).toFixed(2);
            updateItemTotal(row);
            updateTotals();
        }
    });
    
    function updateItemTotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const unitCost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
        const total = quantity * unitCost;
        
        row.querySelector('.item-total').textContent = 'MWK ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    
    function updateTotals() {
        let subtotal = 0;
        
        document.querySelectorAll('#itemsTableBody tr').forEach(function(row) {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const unitCost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
            subtotal += quantity * unitCost;
        });
        
        const taxAmount = parseFloat(document.getElementById('tax_amount').value) || 0;
        const freightCost = parseFloat(document.getElementById('freight_cost').value) || 0;
        const totalAmount = subtotal + taxAmount + freightCost;
        
        // Update displays
        document.getElementById('subtotalDisplay').textContent = 'MWK ' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('taxAmountDisplay').textContent = 'MWK ' + taxAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('freightCostDisplay').textContent = 'MWK ' + freightCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('totalAmountDisplay').textContent = 'MWK ' + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        // Update hidden fields
        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('total_amount').value = totalAmount.toFixed(2);
    }
    
    // Initialize totals for existing items
    document.querySelectorAll('#itemsTableBody tr').forEach(updateItemTotal);
    updateTotals();
});

// Initialize Lucide icons
lucide.createIcons();
</script>

<?= $this->endSection() ?>