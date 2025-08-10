<?php helper('currency'); ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Goods Receipt Note</h1>
            <p class="text-gray-600">Record receipt of goods from supplier delivery</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/goods-receipt') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- GRN Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form id="grnForm" action="<?= base_url('admin/goods-receipt/store') ?>" method="POST">
            <?= csrf_field() ?>
            
            <!-- Header Information -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Receipt Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- GRN Number -->
                    <div>
                        <label for="grn_number" class="block text-sm font-medium text-gray-700 mb-2">GRN Number</label>
                        <input type="text" id="grn_number" name="grn_number" 
                               value="<?= old('grn_number', $grn_number ?? '') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required readonly>
                    </div>

                    <!-- Purchase Order -->
                    <div>
                        <label for="purchase_order_id" class="block text-sm font-medium text-gray-700 mb-2">Purchase Order <span class="text-red-500">*</span></label>
                        <select id="purchase_order_id" name="purchase_order_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                required onchange="loadPurchaseOrderItems(this.value)">
                            <option value="">Select Purchase Order</option>
                            <?php if (isset($purchaseOrders)): ?>
                                <?php foreach ($purchaseOrders as $po): ?>
                                    <option value="<?= $po['id'] ?>" 
                                            data-supplier-id="<?= $po['supplier_id'] ?>"
                                            data-supplier-name="<?= esc($po['supplier_name']) ?>"
                                            <?= old('purchase_order_id') == $po['id'] ? 'selected' : '' ?>>
                                        PO #<?= esc($po['po_number']) ?> - <?= esc($po['supplier_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Warehouse -->
                    <div>
                        <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">Receiving Warehouse <span class="text-red-500">*</span></label>
                        <select id="warehouse_id" name="warehouse_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                required>
                            <option value="">Select Warehouse</option>
                            <?php if (isset($warehouses)): ?>
                                <?php foreach ($warehouses as $warehouse): ?>
                                    <option value="<?= $warehouse['id'] ?>" 
                                            <?= old('warehouse_id') == $warehouse['id'] ? 'selected' : '' ?>>
                                        <?= esc($warehouse['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Delivery Date -->
                    <div>
                        <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Delivery Date <span class="text-red-500">*</span></label>
                        <input type="date" id="delivery_date" name="delivery_date" 
                               value="<?= old('delivery_date', date('Y-m-d')) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                    </div>

                    <!-- Delivery Note Number -->
                    <div>
                        <label for="delivery_note_number" class="block text-sm font-medium text-gray-700 mb-2">Delivery Note Number</label>
                        <input type="text" id="delivery_note_number" name="delivery_note_number" 
                               value="<?= old('delivery_note_number') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Supplier's delivery note #">
                    </div>

                    <!-- Vehicle Number -->
                    <div>
                        <label for="vehicle_number" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Number</label>
                        <input type="text" id="vehicle_number" name="vehicle_number" 
                               value="<?= old('vehicle_number') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Vehicle registration">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Driver Name -->
                    <div>
                        <label for="driver_name" class="block text-sm font-medium text-gray-700 mb-2">Driver Name</label>
                        <input type="text" id="driver_name" name="driver_name" 
                               value="<?= old('driver_name') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Supplier (Auto-populated) -->
                    <div>
                        <label for="supplier_name" class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                        <input type="text" id="supplier_name" name="supplier_name" 
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg" 
                               readonly placeholder="Select Purchase Order first">
                        <input type="hidden" id="supplier_id" name="supplier_id">
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Delivery Items</h2>
                    <div class="text-sm text-gray-600">
                        Select a Purchase Order to load items automatically
                    </div>
                </div>

                <div id="itemsContainer">
                    <div class="overflow-x-auto">
                        <table class="w-full" id="itemsTable">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Material</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900 w-24">Ordered</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900 w-24">Delivered</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900 w-32">Batch/Lot</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900 w-32">Expiry Date</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900 w-40">Notes</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">
                                        <i data-lucide="package" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                                        <p>Select a Purchase Order to load items</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Reception Notes</label>
                        <textarea id="notes" name="notes" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Notes about the delivery condition, damages, etc."><?= old('notes') ?></textarea>
                    </div>

                    <!-- Summary -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Delivery Summary</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Items:</span>
                                <span id="totalItems" class="font-medium">0</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Items Delivered:</span>
                                <span id="itemsDelivered" class="font-medium text-green-600">0</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Partial/Missing:</span>
                                <span id="partialItems" class="font-medium text-yellow-600">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="p-6 bg-gray-50 rounded-b-lg">
                <div class="flex flex-col sm:flex-row gap-2 sm:justify-end">
                    <a href="<?= base_url('admin/goods-receipt') ?>" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" name="action" value="save_draft" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Save as Draft
                    </button>
                    <button type="submit" name="debug_mode" value="1" class="inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        <i data-lucide="bug" class="w-4 h-4 mr-2"></i>
                        Debug Data
                    </button>
                    <button type="submit" name="bypass_model" value="1" class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i data-lucide="zap" class="w-4 h-4 mr-2"></i>
                        Bypass Model
                    </button>
                    <button type="submit" name="action" value="create" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="package-check" class="w-4 h-4 mr-2"></i>
                        Create GRN
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-update supplier when PO is selected
    const poSelect = document.getElementById('purchase_order_id');
    if (poSelect.value) {
        loadPurchaseOrderItems(poSelect.value);
    }
});

function loadPurchaseOrderItems(purchaseOrderId) {
    if (!purchaseOrderId) {
        document.getElementById('itemsTableBody').innerHTML = `
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-500">
                    <i data-lucide="package" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                    <p>Select a Purchase Order to load items</p>
                </td>
            </tr>
        `;
        lucide.createIcons();
        return;
    }

    // Update supplier info
    const poSelect = document.getElementById('purchase_order_id');
    const selectedOption = poSelect.options[poSelect.selectedIndex];
    
    document.getElementById('supplier_id').value = selectedOption.getAttribute('data-supplier-id') || '';
    document.getElementById('supplier_name').value = selectedOption.getAttribute('data-supplier-name') || '';

    // Load PO items via AJAX
    fetch(`<?= base_url('admin/purchase-orders/') ?>${purchaseOrderId}/items`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPOItems(data.items);
                updateSummary();
            } else {
                alert('Error loading purchase order items: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading purchase order items');
        });
}

function renderPOItems(items) {
    const tbody = document.getElementById('itemsTableBody');
    
    if (!items || items.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-500">
                    <p>No items found in this Purchase Order</p>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    items.forEach((item, index) => {
        const pendingQty = item.quantity_ordered - item.quantity_received;
        html += `
            <tr class="border-b border-gray-100">
                <td class="py-3 px-4">
                    <div>
                        <div class="text-sm font-medium text-gray-900">${escapeHtml(item.material_name)}</div>
                        <div class="text-sm text-gray-500">${escapeHtml(item.material_code || '')}</div>
                    </div>
                    <input type="hidden" name="items[${index}][purchase_order_item_id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][material_id]" value="${item.material_id}">
                    <input type="hidden" name="items[${index}][unit_cost]" value="${item.unit_cost}">
                </td>
                <td class="py-3 px-4">
                    <div class="text-sm">
                        <div class="font-medium">${parseFloat(item.quantity_ordered).toFixed(3)} ${escapeHtml(item.material_unit || '')}</div>
                        <div class="text-gray-500 text-xs">Pending: ${pendingQty.toFixed(3)}</div>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <input type="number" name="items[${index}][quantity_delivered]" 
                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 delivered-qty"
                           step="0.001" min="0" max="${pendingQty}" 
                           value="${pendingQty.toFixed(3)}"
                           onchange="updateSummary()">
                </td>
                <td class="py-3 px-4">
                    <input type="text" name="items[${index}][batch_number]" 
                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Batch/Lot">
                </td>
                <td class="py-3 px-4">
                    <input type="date" name="items[${index}][expiry_date]" 
                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </td>
                <td class="py-3 px-4">
                    <textarea name="items[${index}][notes]" rows="2"
                              class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Condition, damages, etc."></textarea>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
    updateSummary();
}

function updateSummary() {
    const deliveredQtyInputs = document.querySelectorAll('.delivered-qty');
    let totalItems = deliveredQtyInputs.length;
    let itemsDelivered = 0;
    let partialItems = 0;

    deliveredQtyInputs.forEach(input => {
        const row = input.closest('tr');
        const orderedQty = parseFloat(row.querySelector('input[name*="quantity_ordered"]')?.value) || 
                          parseFloat(row.querySelector('div').textContent.match(/[\d.]+/)?.[0]) || 0;
        const deliveredQty = parseFloat(input.value) || 0;

        if (deliveredQty > 0) {
            if (deliveredQty >= orderedQty) {
                itemsDelivered++;
            } else {
                partialItems++;
            }
        }
    });

    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('itemsDelivered').textContent = itemsDelivered;
    document.getElementById('partialItems').textContent = partialItems;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize Lucide icons
lucide.createIcons();
</script>

<?= $this->endSection() ?>