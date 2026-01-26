<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Goods Receipt Note</h1>
            <p class="text-gray-600">GRN Number: <?= esc($grn['grn_number']) ?></p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <a href="<?= base_url('admin/goods-receipt/' . $grn['id']) ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="eye" class="w-4 h-4 mr-2"></i> View
            </a>
            <a href="<?= base_url('admin/goods-receipt') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
            <?php
            switch($grn['status']) {
                case 'pending_inspection':
                    echo 'bg-yellow-100 text-yellow-800';
                    break;
                case 'accepted':
                    echo 'bg-green-100 text-green-800';
                    break;
                case 'rejected':
                    echo 'bg-red-100 text-red-800';
                    break;
                default:
                    echo 'bg-gray-100 text-gray-800';
            }
            ?>">
            Status: <?= ucfirst(str_replace('_', ' ', $grn['status'])) ?>
        </span>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="<?= base_url('admin/goods-receipt/' . $grn['id']) ?>" method="post" id="goods-receipt-form">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">

            <div class="p-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">Warehouse</label>
                        <select name="warehouse_id" id="warehouse_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Warehouse</option>
                            <?php foreach ($warehouses as $warehouse): ?>
                                <option value="<?= $warehouse['id'] ?>" <?= $grn['warehouse_id'] == $warehouse['id'] ? 'selected' : '' ?>>
                                    <?= esc($warehouse['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Delivery Date</label>
                        <input type="date" name="delivery_date" id="delivery_date"
                               value="<?= $grn['delivery_date'] ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>

                    <div>
                        <label for="delivery_note_number" class="block text-sm font-medium text-gray-700 mb-2">Delivery Note Number</label>
                        <input type="text" name="delivery_note_number" id="delivery_note_number"
                               value="<?= esc($grn['delivery_note_number']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="freight_cost" class="block text-sm font-medium text-gray-700 mb-2">Freight Cost</label>
                        <input type="number" step="0.01" name="freight_cost" id="freight_cost"
                               value="<?= $grn['freight_cost'] ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="vehicle_number" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Number</label>
                        <input type="text" name="vehicle_number" id="vehicle_number"
                               value="<?= esc($grn['vehicle_number']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="driver_name" class="block text-sm font-medium text-gray-700 mb-2">Driver Name</label>
                        <input type="text" name="driver_name" id="driver_name"
                               value="<?= esc($grn['driver_name']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Additional notes..."><?= esc($grn['notes']) ?></textarea>
                </div>

                <!-- Items Section -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Received Items</h3>

                    <div id="items-container">
                        <?php if (!empty($grn['items'])): ?>
                            <?php foreach ($grn['items'] as $index => $item): ?>
                                <div class="item-row bg-gray-50 p-4 rounded-lg mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Material</label>
                                            <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                                                <?= esc($item['material_name']) ?> (<?= esc($item['item_code']) ?>)
                                            </div>
                                            <input type="hidden" name="items[<?= $item['id'] ?>][material_id]" value="<?= $item['material_id'] ?>">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Delivered</label>
                                            <input type="number" step="0.01" name="items[<?= $item['id'] ?>][quantity_delivered]"
                                                   value="<?= $item['quantity_delivered'] ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit Cost</label>
                                            <input type="number" step="0.01" name="items[<?= $item['id'] ?>][unit_cost]"
                                                   value="<?= $item['unit_cost'] ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                                            <input type="text" name="items[<?= $item['id'] ?>][batch_number]"
                                                   value="<?= esc($item['batch_number']) ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                                            <input type="date" name="items[<?= $item['id'] ?>][expiry_date]"
                                                   value="<?= $item['expiry_date'] ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Item Notes</label>
                                        <textarea name="items[<?= $item['id'] ?>][notes]" rows="2"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                  placeholder="Item-specific notes..."><?= esc($item['notes']) ?></textarea>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500">No items found for this goods receipt.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="<?= base_url('admin/goods-receipt') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Update Goods Receipt
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('goods-receipt-form');

    form.addEventListener('submit', function(e) {
        const quantityInputs = form.querySelectorAll('input[name*="[quantity_delivered]"]');
        const costInputs = form.querySelectorAll('input[name*="[unit_cost]"]');

        let isValid = true;

        quantityInputs.forEach(input => {
            if (!input.value || parseFloat(input.value) <= 0) {
                input.classList.add('border-red-500');
                isValid = false;
            } else {
                input.classList.remove('border-red-500');
            }
        });

        costInputs.forEach(input => {
            if (!input.value || parseFloat(input.value) < 0) {
                input.classList.add('border-red-500');
                isValid = false;
            } else {
                input.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please ensure all quantities are greater than 0 and costs are not negative.');
        }
    });
});
</script>

<?= $this->endSection() ?>
