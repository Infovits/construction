<?php helper('currency'); ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Purchase Order</h1>
            <p class="text-gray-600">Create a new purchase order from material request or direct</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/purchase-orders') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Purchase Order Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form id="purchaseOrderForm" action="<?= base_url('admin/purchase-orders') ?>" method="POST">
            <?= csrf_field() ?>
            
            <!-- Header Information -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- PO Number -->
                    <div>
                        <label for="po_number" class="block text-sm font-medium text-gray-700 mb-2">PO Number</label>
                        <input type="text" id="po_number" name="po_number" 
                               value="<?= old('po_number', $po_number ?? '') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required readonly>
                    </div>

                    <!-- Material Request -->
                    <div>
                        <label for="material_request_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Material Request 
                            <span class="text-xs text-gray-500">(Auto-populates items)</span>
                        </label>
                        <select id="material_request_id" name="material_request_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Material Request (Optional)</option>
                            <?php if (isset($materialRequests)): ?>
                                <?php foreach ($materialRequests as $request): ?>
                                    <option value="<?= $request['id'] ?>" 
                                            <?= old('material_request_id') == $request['id'] ? 'selected' : '' ?>>
                                        <?= esc($request['request_number']) ?> - <?= esc($request['project_name'] ?? 'No Project') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Selecting a Material Request will auto-populate items with approved quantities and costs. You can modify quantities and add/remove items as needed.
                        </p>
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
                                            <?= old('supplier_id') == $supplier['id'] ? 'selected' : '' ?>>
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
                                            <?= old('project_id') == $project['id'] ? 'selected' : '' ?>>
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
                               value="<?= old('po_date', date('Y-m-d')) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                    </div>

                    <!-- Expected Delivery Date -->
                    <div>
                        <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Expected Delivery Date</label>
                        <input type="date" id="expected_delivery_date" name="expected_delivery_date" 
                               value="<?= old('expected_delivery_date') ?>" 
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
                                <!-- Items will be added dynamically -->
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
                                   value="<?= old('payment_terms', 'Net 30 days') ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Delivery Terms -->
                        <div class="mb-4">
                            <label for="delivery_terms" class="block text-sm font-medium text-gray-700 mb-2">Delivery Terms</label>
                            <input type="text" id="delivery_terms" name="delivery_terms" 
                                   value="<?= old('delivery_terms', 'FOB Destination') ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Totals</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span id="subtotalDisplay" class="font-medium">MWK 0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax Amount:</span>
                                <span id="taxAmountDisplay" class="font-medium">MWK 0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Freight Cost:</span>
                                <span id="freightCostDisplay" class="font-medium">MWK 0.00</span>
                            </div>
                            <hr>
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total Amount:</span>
                                <span id="totalAmountDisplay">MWK 0.00</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="tax_percentage" class="block text-sm font-medium text-gray-700 mb-2">Tax Percentage (%)</label>
                            <input type="number" id="tax_percentage" name="tax_percentage" step="0.01" min="0" max="100"
                                   value="<?= old('tax_percentage', '0.00') ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Enter tax percentage (e.g., 16 for 16%)</p>
                        </div>

                        <div class="mt-4">
                            <label for="freight_cost" class="block text-sm font-medium text-gray-700 mb-2">Freight Cost</label>
                            <input type="number" id="freight_cost" name="freight_cost" step="0.01" 
                                   value="<?= old('freight_cost', '0.00') ?>" 
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
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('notes') ?></textarea>
                    </div>
                    <div>
                        <label for="terms_conditions" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                        <textarea id="terms_conditions" name="terms_conditions" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('terms_conditions', 'All materials must meet specified standards and requirements.') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="p-6 bg-gray-50 rounded-b-lg">
                <div class="flex flex-col sm:flex-row gap-2 sm:justify-end">
                    <a href="<?= base_url('admin/purchase-orders') ?>" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" name="action" value="save_draft" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Save as Draft
                    </button>
                    <button type="submit" name="action" value="create" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                        Create Purchase Order
                    </button>
                </div>
            </div>

            <!-- Hidden fields -->
            <input type="hidden" id="subtotal" name="subtotal" value="0.00">
            <input type="hidden" id="total_amount" name="total_amount" value="0.00">
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
    let itemIndex = 0;
    let materialRequestItems = []; // Store current MR items for validation
    
    // Material Request selection change handler
    document.getElementById('material_request_id').addEventListener('change', function() {
        const materialRequestId = this.value;
        
        if (materialRequestId) {
            // Show loading indicator
            showLoadingMessage('Loading Material Request items...');
            
            // Fetch material request items via AJAX
            fetch(`<?= base_url('admin/purchase-orders/material-request-items/') ?>${materialRequestId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin' // Include cookies for session authentication
            })
                .then(response => response.json())
                .then(data => {
                    hideLoadingMessage();
                    
                    if (data.success) {
                        // Clear existing items
                        clearAllItems();
                        
                        // Auto-populate project if available
                        if (data.materialRequest.project_id) {
                            const projectSelect = document.getElementById('project_id');
                            projectSelect.value = data.materialRequest.project_id;
                        }
                        
                        // Store MR items for validation
                        materialRequestItems = data.items;
                        
                        // Populate items from Material Request
                        populateItemsFromMaterialRequest(data.items);
                        
                        // Show success message
                        showSuccessMessage(`Loaded ${data.items.length} items from Material Request ${data.materialRequest.request_number}`);
                        
                    } else {
                        showErrorMessage(data.error || 'Failed to load Material Request items');
                    }
                })
                .catch(error => {
                    hideLoadingMessage();
                    showErrorMessage('Error loading Material Request items');
                    console.error('Error:', error);
                });
        } else {
            // Clear items when no MR is selected
            clearAllItems();
            materialRequestItems = [];
            // Add one empty item
            addNewItem();
        }
    });
    
    // Add item functionality
    document.getElementById('addItemBtn').addEventListener('click', addNewItem);
    
    function addNewItem() {
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
    }
    
    function populateItemsFromMaterialRequest(items) {
        const tbody = document.getElementById('itemsTableBody');
        
        items.forEach(item => {
            addNewItem();
            const row = tbody.lastElementChild;
            
            // Set material selection
            const materialSelect = row.querySelector('.material-select');
            materialSelect.value = item.material_id;
            
            // Set quantity (use approved quantity as default, but allow modification)
            const quantityInput = row.querySelector('.quantity-input');
            quantityInput.value = parseFloat(item.quantity_approved).toFixed(3);
            quantityInput.setAttribute('data-max-approved', item.quantity_approved);
            quantityInput.setAttribute('data-mr-item-id', item.id);
            
            // Set unit cost (prefer current cost over estimated cost)
            const unitCostInput = row.querySelector('.unit-cost-input');
            const unitCost = item.current_unit_cost || item.estimated_unit_cost || 0;
            unitCostInput.value = parseFloat(unitCost).toFixed(2);
            
            // Add specification notes as tooltip/title if available
            if (item.specification_notes) {
                quantityInput.title = `Specification: ${item.specification_notes}`;
            }
            
            // Update item total
            updateItemTotal(row);
            
            // Add visual indicator that this is from MR
            row.classList.add('from-material-request');
            row.style.backgroundColor = '#f0f9ff'; // Light blue background
        });
        
        updateTotals();
    }
    
    function clearAllItems() {
        document.getElementById('itemsTableBody').innerHTML = '';
    }
    
    // Remove item functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            e.target.closest('tr').remove();
            updateTotals();
        }
    });
    
    // Update totals when inputs change
    document.addEventListener('input', function(e) {
        if (e.target.matches('.quantity-input, .unit-cost-input, #tax_percentage, #freight_cost')) {
            if (e.target.matches('.quantity-input, .unit-cost-input')) {
                updateItemTotal(e.target.closest('tr'));
                
                // Validate quantity doesn't exceed approved amount
                if (e.target.matches('.quantity-input')) {
                    validateQuantity(e.target);
                }
            }
            updateTotals();
        }
    });
    
    // Validate quantity against approved amount
    function validateQuantity(quantityInput) {
        const maxApproved = parseFloat(quantityInput.getAttribute('data-max-approved'));
        const currentQuantity = parseFloat(quantityInput.value);
        
        if (maxApproved && currentQuantity > maxApproved) {
            quantityInput.style.borderColor = '#ef4444';
            quantityInput.title = `Warning: Quantity exceeds approved amount of ${maxApproved}`;
            
            // Show warning message
            showWarningMessage(`Quantity for this item exceeds approved amount of ${maxApproved.toFixed(3)}`);
        } else {
            quantityInput.style.borderColor = '#d1d5db';
            
            // Clear any existing warning about approved quantity
            if (quantityInput.title.includes('Warning:')) {
                quantityInput.title = quantityInput.title.replace(/Warning:.*?(?=Specification:|$)/, '');
            }
        }
    }
    
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
        
        // Calculate tax based on percentage
        const taxPercentage = parseFloat(document.getElementById('tax_percentage').value) || 0;
        const taxAmount = (subtotal * taxPercentage) / 100;
        
        const freightCost = parseFloat(document.getElementById('freight_cost').value) || 0;
        const totalAmount = subtotal + taxAmount + freightCost;
        
        // Update displays
        document.getElementById('subtotalDisplay').textContent = 'MWK ' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('taxAmountDisplay').textContent = 'MWK ' + taxAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ` (${taxPercentage}%)`;
        document.getElementById('freightCostDisplay').textContent = 'MWK ' + freightCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('totalAmountDisplay').textContent = 'MWK ' + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        // Update hidden fields
        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('total_amount').value = totalAmount.toFixed(2);
    }
    
    // Message display functions
    function showLoadingMessage(message) {
        removeExistingMessages();
        const messageDiv = createMessageDiv('info', message);
        insertMessageDiv(messageDiv);
    }
    
    function showSuccessMessage(message) {
        removeExistingMessages();
        const messageDiv = createMessageDiv('success', message);
        insertMessageDiv(messageDiv);
        setTimeout(removeExistingMessages, 5000); // Auto-remove after 5 seconds
    }
    
    function showErrorMessage(message) {
        removeExistingMessages();
        const messageDiv = createMessageDiv('error', message);
        insertMessageDiv(messageDiv);
    }
    
    function showWarningMessage(message) {
        const messageDiv = createMessageDiv('warning', message);
        insertMessageDiv(messageDiv);
        setTimeout(() => messageDiv.remove(), 3000); // Auto-remove after 3 seconds
    }
    
    function hideLoadingMessage() {
        removeExistingMessages();
    }
    
    function createMessageDiv(type, message) {
        const div = document.createElement('div');
        div.className = `message-alert p-4 mb-4 rounded-lg ${getMessageClasses(type)}`;
        div.innerHTML = `
            <div class="flex items-center">
                <span class="mr-2">${getMessageIcon(type)}</span>
                <span>${message}</span>
                <button type="button" class="ml-auto text-gray-500 hover:text-gray-700" onclick="this.parentElement.parentElement.remove()">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        return div;
    }
    
    function getMessageClasses(type) {
        const classes = {
            'info': 'bg-blue-50 border border-blue-200 text-blue-700',
            'success': 'bg-green-50 border border-green-200 text-green-700',
            'error': 'bg-red-50 border border-red-200 text-red-700',
            'warning': 'bg-yellow-50 border border-yellow-200 text-yellow-700'
        };
        return classes[type] || classes['info'];
    }
    
    function getMessageIcon(type) {
        const icons = {
            'info': '<i data-lucide="info" class="w-4 h-4"></i>',
            'success': '<i data-lucide="check-circle" class="w-4 h-4"></i>',
            'error': '<i data-lucide="alert-circle" class="w-4 h-4"></i>',
            'warning': '<i data-lucide="alert-triangle" class="w-4 h-4"></i>'
        };
        return icons[type] || icons['info'];
    }
    
    function insertMessageDiv(messageDiv) {
        const form = document.getElementById('purchaseOrderForm');
        form.parentNode.insertBefore(messageDiv, form);
        
        // Reinitialize Lucide icons for the message
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
    
    function removeExistingMessages() {
        document.querySelectorAll('.message-alert').forEach(el => el.remove());
    }
    
    // Initialize with one empty item if no Material Request is pre-selected
    const materialRequestSelect = document.getElementById('material_request_id');
    const preSelectedMR = new URLSearchParams(window.location.search).get('material_request_id');
    
    if (preSelectedMR) {
        materialRequestSelect.value = preSelectedMR;
        materialRequestSelect.dispatchEvent(new Event('change'));
    } else if (!materialRequestSelect.value) {
        addNewItem();
    }
});
</script>

<style>
/* Visual styling for Material Request items */
.from-material-request {
    background-color: #f0f9ff !important;
    border-left: 3px solid #3b82f6;
}

.from-material-request .material-select {
    background-color: #e0f2fe;
}

.from-material-request .quantity-input[data-max-approved] {
    position: relative;
}

.from-material-request .quantity-input[data-max-approved]::after {
    content: " (max: " attr(data-max-approved) ")";
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    color: #6b7280;
    pointer-events: none;
}

/* Warning styling for quantities exceeding approved amounts */
.quantity-input[style*="border-color: #ef4444"] {
    background-color: #fef2f2;
    animation: shake 0.3s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Loading state */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.loading-spinner {
    border: 3px solid #f3f4f6;
    border-top: 3px solid #3b82f6;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<?= $this->endSection() ?>