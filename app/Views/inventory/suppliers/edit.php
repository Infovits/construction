<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Edit Supplier<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Supplier</h1>
                <p class="text-gray-600">Update supplier information and preferences</p>
            </div>
            <div>
                <a href="<?php echo base_url(); ?>/admin/suppliers" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Suppliers
                </a>
            </div>
        </div>
    </div>

    <!-- Supplier Edit Form -->
    <div class="flex justify-center">
        <div class="w-full max-w-4xl">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Supplier Information</h3>
                </div>
                <div class="p-6">
                    <?php if (session('errors')): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/suppliers/update/' . $supplier['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Supplier Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('name', $supplier['name']) ?>" required>
                                </div>

                                <div>
                                    <label for="supplier_code" class="block text-sm font-medium text-gray-700 mb-2">Supplier Code <span class="text-red-500">*</span></label>
                                    <input type="text" name="supplier_code" id="supplier_code" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('supplier_code', $supplier['supplier_code']) ?>" required>
                                    <p class="mt-1 text-xs text-gray-500">Unique identifier for this supplier</p>
                                </div>

                                <div>
                                    <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                                    <input type="text" name="contact_person" id="contact_person" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('contact_person', $supplier['contact_person']) ?>">
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" name="phone" id="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('phone', $supplier['phone']) ?>">
                                </div>

                                <div>
                                    <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                                    <input type="tel" name="mobile" id="mobile" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('mobile', $supplier['mobile']) ?>">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" name="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('email', $supplier['email']) ?>">
                                </div>

                                <div>
                                    <label for="tax_number" class="block text-sm font-medium text-gray-700 mb-2">Tax Number</label>
                                    <input type="text" name="tax_number" id="tax_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('tax_number', $supplier['tax_number']) ?>">
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                        <option value="active" <?= old('status', $supplier['status']) == 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= old('status', $supplier['status']) == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        <option value="blacklisted" <?= old('status', $supplier['status']) == 'blacklisted' ? 'selected' : '' ?>>Blacklisted</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <textarea name="address" id="address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"><?= old('address', $supplier['address']) ?></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                        <input type="text" name="city" id="city" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('city', $supplier['city']) ?>">
                                    </div>
                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State/Province</label>
                                        <input type="text" name="state" id="state" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('state', $supplier['state']) ?>">
                                    </div>
                                </div>

                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                    <input type="text" name="country" id="country" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('country', $supplier['country']) ?>">
                                </div>

                                <div>
                                    <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                                    <input type="text" name="payment_terms" id="payment_terms" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('payment_terms', $supplier['payment_terms']) ?>">
                                    <p class="mt-1 text-xs text-gray-500">E.g., Net 30, COD, etc.</p>
                                </div>

                                <div>
                                    <label for="credit_limit" class="block text-sm font-medium text-gray-700 mb-2">Credit Limit</label>
                                    <div class="flex">
                                        <span class="inline-flex items-center px-3 py-2 border border-gray-300 bg-gray-50 text-gray-500 rounded-l-lg">
                                            MWK
                                        </span>
                                        <input type="number" step="0.01" min="0" name="credit_limit" id="credit_limit" class="w-full px-3 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('credit_limit', $supplier['credit_limit']) ?>">
                                    </div>
                                </div>

                                <div>
                                    <label for="supplier_type" class="block text-sm font-medium text-gray-700 mb-2">Supplier Type</label>
                                    <select name="supplier_type" id="supplier_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                        <option value="material" <?= old('supplier_type', $supplier['supplier_type']) == 'material' ? 'selected' : '' ?>>Materials</option>
                                        <option value="equipment" <?= old('supplier_type', $supplier['supplier_type']) == 'equipment' ? 'selected' : '' ?>>Equipment</option>
                                        <option value="service" <?= old('supplier_type', $supplier['supplier_type']) == 'service' ? 'selected' : '' ?>>Services</option>
                                        <option value="mixed" <?= old('supplier_type', $supplier['supplier_type']) == 'mixed' ? 'selected' : '' ?>>Mixed</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                    <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"><?= old('notes', $supplier['notes']) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <a href="<?= base_url('admin/suppliers') ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i data-lucide="x" class="w-4 h-4 mr-2"></i> Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Update Supplier
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
});
</script>
<?= $this->endSection() ?>
