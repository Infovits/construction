<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create Purchase Order</h1>
        <div>
            <a href="<?= base_url('materials/low-stock-notifications') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Low Stock Items
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('materials/save-purchase-order') ?>" method="post" class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="w-full rounded-lg border-gray-300" required>
                    <option value="">Select Supplier</option>
                    <?php foreach ($suppliers as $supplier) : ?>
                        <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery Date</label>
                <input type="date" name="expected_delivery_date" id="expected_delivery_date" class="w-full rounded-lg border-gray-300" 
                       value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Shipping Address</label>
                <select name="shipping_address" id="shipping_address" class="w-full rounded-lg border-gray-300" required>
                    <option value="">Select Delivery Location</option>
                    <?php foreach ($warehouses as $warehouse) : ?>
                        <option value="<?= $warehouse['name'] . ' - ' . $warehouse['address'] ?>"><?= $warehouse['name'] . ' - ' . $warehouse['address'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="shipping_method" class="block text-sm font-medium text-gray-700 mb-1">Shipping Method</label>
                <select name="shipping_method" id="shipping_method" class="w-full rounded-lg border-gray-300" required>
                    <option value="Standard Delivery">Standard Delivery</option>
                    <option value="Express Delivery">Express Delivery</option>
                    <option value="Supplier Delivery">Supplier Delivery</option>
                    <option value="Pick Up">Pick Up</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-1">Payment Terms</label>
            <select name="payment_terms" id="payment_terms" class="w-full rounded-lg border-gray-300" required>
                <option value="Net 30">Net 30</option>
                <option value="Net 15">Net 15</option>
                <option value="Net 60">Net 60</option>
                <option value="Immediate Payment">Immediate Payment</option>
            </select>
        </div>

        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" id="notes" rows="3" class="w-full rounded-lg border-gray-300"></textarea>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Items</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Item</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Code</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Current Stock</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Reorder Level</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Order Quantity</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Unit Price</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Total</th>
                        </tr>
                    </thead>
                    <tbody id="orderItems">
                        <?php foreach ($materials as $index => $material) : ?>
                            <tr>
                                <td class="py-3 px-4 border-t">
                                    <input type="hidden" name="material_id[]" value="<?= $material['id'] ?>">
                                    <?= $material['name'] ?>
                                </td>
                                <td class="py-3 px-4 border-t"><?= $material['item_code'] ?></td>
                                <td class="py-3 px-4 border-t text-red-600">
                                    <?= $material['current_stock'] ?> <?= $material['unit'] ?>
                                </td>
                                <td class="py-3 px-4 border-t">
                                    <?= $material['reorder_level'] ?> <?= $material['unit'] ?>
                                </td>
                                <td class="py-3 px-4 border-t">
                                    <input type="number" name="quantity[]" class="w-24 rounded-lg border-gray-300 order-quantity" 
                                           value="<?= max($material['reorder_level'] * 2 - $material['current_stock'], 5) ?>" 
                                           min="1" step="0.01" required>
                                </td>
                                <td class="py-3 px-4 border-t">
                                    <input type="number" name="unit_price[]" class="w-24 rounded-lg border-gray-300 unit-price" 
                                           value="<?= $material['unit_cost'] ?>" min="0.01" step="0.01" required>
                                </td>
                                <td class="py-3 px-4 border-t">
                                    <span class="item-total"><?= number_format($material['unit_cost'] * max($material['reorder_level'] * 2 - $material['current_stock'], 5), 2) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="py-3 px-4 border-t"></td>
                            <td class="py-3 px-4 border-t font-semibold">Total:</td>
                            <td class="py-3 px-4 border-t font-semibold" id="orderTotal">0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-lucide="check" class="w-4 h-4 mr-1 inline"></i> Create Purchase Order
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calculate totals on page load
        calculateTotals();
        
        // Update totals when quantities or prices change
        document.querySelectorAll('.order-quantity, .unit-price').forEach(input => {
            input.addEventListener('input', calculateItemTotal);
        });
        
        function calculateItemTotal(e) {
            const row = e.target.closest('tr');
            const quantity = parseFloat(row.querySelector('.order-quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
            const total = quantity * unitPrice;
            
            row.querySelector('.item-total').textContent = total.toFixed(2);
            
            calculateTotals();
        }
        
        function calculateTotals() {
            let orderTotal = 0;
            
            document.querySelectorAll('.item-total').forEach(item => {
                orderTotal += parseFloat(item.textContent.replace(/,/g, '')) || 0;
            });
            
            document.getElementById('orderTotal').textContent = orderTotal.toFixed(2);
        }
    });
</script>
