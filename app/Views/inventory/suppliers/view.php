<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Supplier Details - <?= esc($supplier['name']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= esc($supplier['name']) ?></h1>
                <p class="text-gray-600"><?= $supplier['supplier_type'] ?> supplier | <?= $supplier['status'] == 'active' ? 'Active' : 'Inactive' ?></p>
            </div>
            <div class="flex gap-2">
                <a href="<?= base_url('admin/suppliers') ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Suppliers
                </a>
                <a href="<?= base_url('admin/suppliers/edit/' . $supplier['id']) ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i> Edit
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

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Supplier Information Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-200">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Supplier Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <h4 class="text-xs uppercase tracking-wider font-semibold text-gray-500">Contact Info</h4>
                        <div class="mt-2 space-y-1">
                            <?php if(!empty($supplier['contact_person'])): ?>
                            <p class="text-sm text-gray-700"><span class="font-medium">Contact:</span> <?= esc($supplier['contact_person']) ?></p>
                            <?php endif; ?>
                            
                            <?php if(!empty($supplier['phone'])): ?>
                            <p class="text-sm text-gray-700"><span class="font-medium">Phone:</span> <?= esc($supplier['phone']) ?></p>
                            <?php endif; ?>
                            
                            <?php if(!empty($supplier['mobile'])): ?>
                            <p class="text-sm text-gray-700"><span class="font-medium">Mobile:</span> <?= esc($supplier['mobile']) ?></p>
                            <?php endif; ?>
                            
                            <?php if(!empty($supplier['email'])): ?>
                            <p class="text-sm text-gray-700"><span class="font-medium">Email:</span> <a href="mailto:<?= esc($supplier['email']) ?>" class="text-blue-600 hover:text-blue-800"><?= esc($supplier['email']) ?></a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-xs uppercase tracking-wider font-semibold text-gray-500">Address</h4>
                        <div class="mt-2">
                            <p class="text-sm text-gray-700">
                                <?php
                                $addressParts = [];
                                if (!empty($supplier['address'])) $addressParts[] = $supplier['address'];
                                if (!empty($supplier['city'])) $addressParts[] = $supplier['city'];
                                if (!empty($supplier['state'])) $addressParts[] = $supplier['state'];
                                if (!empty($supplier['country'])) $addressParts[] = $supplier['country'];
                                
                                echo implode(', ', $addressParts);
                                ?>
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-xs uppercase tracking-wider font-semibold text-gray-500">Business Details</h4>
                        <div class="mt-2 space-y-1">
                            <?php if(!empty($supplier['supplier_code'])): ?>
                            <p class="text-sm text-gray-700"><span class="font-medium">Supplier Code:</span> <?= esc($supplier['supplier_code']) ?></p>
                            <?php endif; ?>
                            
                            <?php if(!empty($supplier['tax_number'])): ?>
                            <p class="text-sm text-gray-700"><span class="font-medium">Tax Number:</span> <?= esc($supplier['tax_number']) ?></p>
                            <?php endif; ?>
                            
                            <?php if(!empty($supplier['payment_terms'])): ?>
                            <p class="text-sm text-gray-700"><span class="font-medium">Payment Terms:</span> <?= esc($supplier['payment_terms']) ?></p>
                            <?php endif; ?>
                            
                            <?php if(!empty($supplier['credit_limit'])): ?>
                            <p class="text-sm text-gray-700"><span class="font-medium">Credit Limit:</span> $<?= number_format($supplier['credit_limit'], 2) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if(!empty($supplier['notes'])): ?>
                    <div>
                        <h4 class="text-xs uppercase tracking-wider font-semibold text-gray-500">Notes</h4>
                        <div class="mt-2">
                            <p class="text-sm text-gray-700"><?= nl2br(esc($supplier['notes'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Supplier Rating -->
                    <div>
                        <h4 class="text-xs uppercase tracking-wider font-semibold text-gray-500">Supplier Rating</h4>
                        <div class="mt-2">
                            <div class="flex items-center">
                                <?php 
                                $rating = $supplier['rating'] ?? 0;
                                for($i = 1; $i <= 5; $i++): 
                                ?>
                                <button type="button" onclick="rateSupplier(<?= $i ?>)" class="w-6 h-6 text-<?= ($i <= $rating) ? 'yellow' : 'gray' ?>-400 focus:outline-none">
                                    <i data-lucide="star" class="w-6 h-6 fill-current"></i>
                                </button>
                                <?php endfor; ?>
                                <span class="ml-2 text-sm text-gray-600"><?= $rating ?> out of 5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="ratingForm" action="<?= base_url('admin/suppliers/rate/' . $supplier['id']) ?>" method="POST" class="hidden">
                <?= csrf_field() ?>
                <input type="hidden" name="rating" id="ratingInput" value="<?= $supplier['rating'] ?? 0 ?>">
            </form>
        </div>

        <!-- Right Side Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Materials Supplied -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Materials Supplied</h3>
                    <a href="#" onclick="showAddMaterialModal()" class="text-purple-600 hover:text-purple-800 text-sm flex items-center">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Add Material
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Order</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($materials)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No materials have been linked to this supplier yet
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($materials as $material): ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?= esc($material['name']) ?></div>
                                        <div class="text-xs text-gray-500"><?= esc($material['sku']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        $<?= number_format($material['unit_price'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $material['lead_time'] ?? '-' ?> days
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $material['min_order_qty'] ?? '-' ?> <?= esc($material['unit_of_measure']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button type="button" onclick="editSupplierMaterial(<?= $material['id'] ?>)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                        <button type="button" onclick="removeSupplierMaterial(<?= $material['id'] ?>)" class="text-red-600 hover:text-red-900">
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

            <!-- Recent Deliveries -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Deliveries</h3>
                    <a href="#" onclick="showAddDeliveryModal()" class="text-purple-600 hover:text-purple-800 text-sm flex items-center">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Record Delivery
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($deliveries)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No deliveries have been recorded for this supplier yet
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($deliveries as $delivery): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M d, Y', strtotime($delivery['delivery_date'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="#" onclick="viewDelivery(<?= $delivery['id'] ?>)" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                            <?= esc($delivery['reference_number']) ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900"><?= esc($delivery['material_name']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        <?= number_format($delivery['quantity'], 2) ?> <?= esc($delivery['unit_of_measure']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                        $<?= number_format($delivery['total_amount'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Material Modal (hidden by default) -->
    <div id="addMaterialModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" aria-modal="true" role="dialog">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add Material to Supplier</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeAddMaterialModal()">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="<?= base_url('admin/suppliers/add_material/' . $supplier['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="space-y-4">
                    <div>
                        <label for="material_id" class="block text-sm font-medium text-gray-700 mb-2">Material</label>
                        <select name="material_id" id="material_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                            <option value="">Select a material</option>
                            <!-- Materials will be populated from controller -->
                        </select>
                    </div>
                    
                    <div>
                        <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">$</span>
                            </div>
                            <input type="number" step="0.01" min="0" name="unit_price" id="unit_price" class="w-full pl-8 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="min_order_qty" class="block text-sm font-medium text-gray-700 mb-2">Min Order Qty</label>
                            <input type="number" step="1" min="0" name="min_order_qty" id="min_order_qty" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="lead_time" class="block text-sm font-medium text-gray-700 mb-2">Lead Time (days)</label>
                            <input type="number" step="1" min="0" name="lead_time" id="lead_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                    </div>
                </div>
                
                <div class="mt-5 flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" onclick="closeAddMaterialModal()">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Delivery Modal (hidden by default) -->
    <div id="addDeliveryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" aria-modal="true" role="dialog">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Record New Delivery</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeAddDeliveryModal()">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="<?= base_url('admin/suppliers/record_delivery/' . $supplier['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Delivery Date</label>
                            <input type="date" name="delivery_date" id="delivery_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div>
                            <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference #</label>
                            <input type="text" name="reference_number" id="reference_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="material_id" class="block text-sm font-medium text-gray-700 mb-2">Material</label>
                        <select name="material_id" id="delivery_material_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                            <option value="">Select a material</option>
                            <!-- Supplier materials will be populated from controller -->
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <input type="number" step="0.01" min="0" name="quantity" id="quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        </div>
                        <div>
                            <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">$</span>
                                </div>
                                <input type="number" step="0.01" min="0" name="unit_price" id="delivery_unit_price" class="w-full pl-8 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">Receiving Warehouse</label>
                        <select name="warehouse_id" id="warehouse_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                            <option value="">Select warehouse</option>
                            <!-- Warehouses will be populated from controller -->
                        </select>
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Delivery Status</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                            <option value="received">Received</option>
                            <option value="partial">Partial</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="delivery_notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                    </div>
                </div>
                
                <div class="mt-5 flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" onclick="closeAddDeliveryModal()">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Record Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Initialize star icons for rating
    const stars = document.querySelectorAll('[data-lucide="star"]');
    stars.forEach(star => {
        if (star.closest('button') && 
            parseInt(star.closest('button').getAttribute('onclick').match(/\d+/)[0]) <= <?= $supplier['rating'] ?? 0 ?>) {
            star.classList.add('fill-current');
        }
    });
});

function rateSupplier(rating) {
    document.getElementById('ratingInput').value = rating;
    document.getElementById('ratingForm').submit();
}

function showAddMaterialModal() {
    document.getElementById('addMaterialModal').classList.remove('hidden');
    
    // Load materials via AJAX
    fetch('<?= base_url('admin/materials/get-json') ?>')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('material_id');
            select.innerHTML = '<option value="">Select a material</option>';
            
            data.forEach(material => {
                const option = document.createElement('option');
                option.value = material.id;
                option.textContent = `${material.name} (${material.sku})`;
                select.appendChild(option);
            });
        });
}

function closeAddMaterialModal() {
    document.getElementById('addMaterialModal').classList.add('hidden');
}

function showAddDeliveryModal() {
    document.getElementById('addDeliveryModal').classList.remove('hidden');
    
    // Load supplier's materials via AJAX
    fetch('<?= base_url('admin/suppliers/get-materials/' . $supplier['id']) ?>')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('delivery_material_id');
            select.innerHTML = '<option value="">Select a material</option>';
            
            data.forEach(material => {
                const option = document.createElement('option');
                option.value = material.id;
                option.textContent = material.name;
                option.dataset.price = material.unit_price;
                select.appendChild(option);
            });
        });
    
    // Load warehouses via AJAX
    fetch('<?= base_url('admin/warehouses/get-json') ?>')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('warehouse_id');
            select.innerHTML = '<option value="">Select warehouse</option>';
            
            data.forEach(warehouse => {
                const option = document.createElement('option');
                option.value = warehouse.id;
                option.textContent = warehouse.name;
                select.appendChild(option);
            });
        });
    
    // Set unit price when material changes
    document.getElementById('delivery_material_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.dataset.price) {
            document.getElementById('delivery_unit_price').value = selectedOption.dataset.price;
        }
    });
}

function closeAddDeliveryModal() {
    document.getElementById('addDeliveryModal').classList.add('hidden');
}

function editSupplierMaterial(materialId) {
    // Implement via AJAX or redirect
    window.location.href = `<?= base_url('admin/suppliers/edit_material/' . $supplier['id']) ?>/${materialId}`;
}

function removeSupplierMaterial(materialId) {
    if (confirm('Are you sure you want to remove this material from this supplier?')) {
        window.location.href = `<?= base_url('admin/suppliers/remove_material/' . $supplier['id']) ?>/${materialId}`;
    }
}

function viewDelivery(deliveryId) {
    window.location.href = `<?= base_url('admin/suppliers/delivery') ?>/${deliveryId}`;
}
</script>
<?= $this->endSection() ?>
