<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Edit Delivery - <?= esc($delivery['reference_number']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Delivery</h1>
                <p class="text-gray-600">Reference: <?= esc($delivery['reference_number']) ?> | Supplier: <?= esc($supplier['name']) ?></p>
            </div>
            <div class="flex gap-2">
                <a href="<?= base_url('admin/suppliers/view/' . $supplier['id']) ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Supplier
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <?php if(session('success')): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
        <p><?= session('success') ?></p>
    </div>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if(session('error')): ?>
    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
        <p><?= session('error') ?></p>
    </div>
    <?php endif; ?>

    <!-- Edit Delivery Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="<?= base_url('admin/suppliers/update-delivery/' . $delivery['id']) ?>" method="POST">
            <?= csrf_field() ?>
            <?= method_field('POST') ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Delivery Date</label>
                    <input type="date" name="delivery_date" id="delivery_date" 
                           value="<?= old('delivery_date', $delivery['delivery_date']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                </div>
                <div>
                    <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference #</label>
                    <input type="text" name="reference_number" id="reference_number" 
                           value="<?= old('reference_number', $delivery['reference_number']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="material_id" class="block text-sm font-medium text-gray-700 mb-2">Material</label>
                    <select name="material_id" id="material_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">Select a material</option>
                        <?php foreach($supplierMaterials as $material): ?>
                            <option value="<?= $material['id'] ?>" <?= ($delivery['material_id'] == $material['id']) ? 'selected' : '' ?>>
                                <?= esc($material['name']) ?> (<?= esc($material['item_code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">Receiving Warehouse</label>
                    <select name="warehouse_id" id="warehouse_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">Select warehouse</option>
                        <?php foreach($warehouses as $warehouse): ?>
                            <option value="<?= $warehouse['id'] ?>" <?= ($delivery['warehouse_id'] == $warehouse['id']) ? 'selected' : '' ?>>
                                <?= esc($warehouse['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <input type="number" step="0.01" min="0" name="quantity" id="quantity" 
                           value="<?= old('quantity', $delivery['quantity']) ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                </div>
                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 py-2 border border-gray-300 bg-gray-50 text-gray-500 rounded-l-lg">
                            MWK
                        </span>
                        <input type="number" step="0.01" min="0" name="unit_price" id="unit_price" 
                               value="<?= old('unit_price', $delivery['unit_price']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Delivery Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="received" <?= ($delivery['status'] == 'received') ? 'selected' : '' ?>>Received</option>
                        <option value="partial" <?= ($delivery['status'] == 'partial') ? 'selected' : '' ?>>Partial</option>
                        <option value="pending" <?= ($delivery['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="cancelled" <?= ($delivery['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 py-2 border border-gray-300 bg-gray-50 text-gray-500 rounded-l-lg">
                            MWK
                        </span>
                        <input type="text" name="total_amount" id="total_amount" 
                               value="<?= number_format($delivery['total_amount'], 2) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-r-lg bg-gray-100" readonly>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"><?= old('notes', $delivery['notes']) ?></textarea>
            </div>
            
            <div class="flex justify-end gap-3">
                <a href="<?= base_url('admin/suppliers/view/' . $supplier['id']) ?>" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Update Delivery
                </button>
            </div>
        </form>
    </div>

    <!-- Current Delivery Information -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Delivery Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <span class="text-sm text-gray-500">Material</span>
                <p class="text-sm font-medium text-gray-900"><?= esc($delivery['material_name']) ?></p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Quantity</span>
                <p class="text-sm font-medium text-gray-900"><?= number_format($delivery['quantity'], 2) ?> <?= esc($delivery['unit']) ?></p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Unit Price</span>
                <p class="text-sm font-medium text-gray-900">MWK <?= number_format($delivery['unit_price'], 2) ?></p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Total Amount</span>
                <p class="text-sm font-medium text-gray-900">MWK <?= number_format($delivery['total_amount'], 2) ?></p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Warehouse</span>
                <p class="text-sm font-medium text-gray-900"><?= esc($delivery['warehouse_name']) ?></p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Status</span>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                    <?php 
                        switch ($delivery['status']) {
                            case 'received': echo 'bg-green-100 text-green-800'; break;
                            case 'partial': echo 'bg-yellow-100 text-yellow-800'; break;
                            case 'pending': echo 'bg-blue-100 text-blue-800'; break;
                            case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                            default: echo 'bg-gray-100 text-gray-800';
                        }
                    ?>">
                    <?= ucfirst($delivery['status']) ?>
                </span>
            </div>
            <div>
                <span class="text-sm text-gray-500">Delivery Date</span>
                <p class="text-sm font-medium text-gray-900"><?= date('M d, Y', strtotime($delivery['delivery_date'])) ?></p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Created By</span>
                <p class="text-sm font-medium text-gray-900"><?= esc($delivery['created_by_name'] ?? 'Unknown') ?></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Calculate total amount when quantity or unit price changes
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unit_price');
    const totalAmountInput = document.getElementById('total_amount');
    
    function calculateTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const total = quantity * unitPrice;
        totalAmountInput.value = total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    
    quantityInput.addEventListener('input', calculateTotal);
    unitPriceInput.addEventListener('input', calculateTotal);
});

// Load supplier materials and warehouses via AJAX
document.addEventListener('DOMContentLoaded', function() {
    const supplierId = <?= $supplier['id'] ?>;
    const materialSelect = document.getElementById('material_id');
    const warehouseSelect = document.getElementById('warehouse_id');
    
    // Load supplier materials
    fetch('<?= base_url('admin/suppliers/get-materials/') ?>' + supplierId)
        .then(response => response.json())
        .then(data => {
            materialSelect.innerHTML = '<option value="">Select a material</option>';
            data.forEach(material => {
                const option = document.createElement('option');
                option.value = material.id;
                option.textContent = material.name + ' (' + material.item_code + ')';
                if (material.id == <?= $delivery['material_id'] ?>) {
                    option.selected = true;
                }
                materialSelect.appendChild(option);
            });
        });
    
    // Load warehouses
    fetch('<?= base_url('admin/warehouses/get-json') ?>')
        .then(response => response.json())
        .then(data => {
            warehouseSelect.innerHTML = '<option value="">Select warehouse</option>';
            data.forEach(warehouse => {
                const option = document.createElement('option');
                option.value = warehouse.id;
                option.textContent = warehouse.name;
                if (warehouse.id == <?= $delivery['warehouse_id'] ?>) {
                    option.selected = true;
                }
                warehouseSelect.appendChild(option);
            });
        });
});
</script>
<?= $this->endSection() ?>