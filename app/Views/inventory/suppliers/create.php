<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Add New Supplier<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add New Supplier</h1>
                <p class="text-gray-600">Register a new material supplier for inventory management</p>
            </div>
            <div>
                <a href="<?php echo base_url(); ?>/admin/suppliers" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Suppliers
                </a>
            </div>
        </div>
    </div>

    <!-- Supplier Creation Form -->
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

                    <form action="<?= base_url('admin/suppliers/create') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                 <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Supplier Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('name') ?>" required>
                                </div>
                                <div>
                                    <label for="supplier_code" class="block text-sm font-medium text-gray-700 mb-2">Supplier Code <span class="text-red-500">*</span></label>
                                    <input type="text" name="supplier_code" id="supplier_code" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('supplier_code') ?>" required>
                                    <p class="mt-1 text-xs text-gray-500">Unique identifier for this supplier (e.g., SUP001)</p>
                                </div>

                               

                                <div>
                                    <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                                    <input type="text" name="contact_person" id="contact_person" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('contact_person') ?>">
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                                    <input type="tel" name="phone" id="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('phone') ?>" required>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" name="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('email') ?>">
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                        <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        <option value="blacklisted" <?= old('status') === 'blacklisted' ? 'selected' : '' ?>>Blacklisted</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <textarea name="address" id="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"><?= old('address') ?></textarea>
                                </div>

                                <div>
                                    <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                                    <input type="url" name="website" id="website" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('website') ?>">
                                </div>

                                <div>
                                    <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                                    <input type="text" name="payment_terms" id="payment_terms" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="<?= old('payment_terms') ?>">
                                    <p class="mt-1 text-xs text-gray-500">E.g., Net 30, COD, etc.</p>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                    <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"><?= old('notes') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h4 class="text-base font-medium text-gray-800 mb-4">Material Categories Supplied (Optional)</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <?php foreach ($categories as $category): ?>
                                <div class="flex items-start">
                                    <input type="checkbox" name="categories[]" id="category_<?= $category['id'] ?>" value="<?= $category['id'] ?>" class="h-4 w-4 mt-1 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                    <label for="category_<?= $category['id'] ?>" class="ml-2 text-sm text-gray-700">
                                        <?= esc($category['name']) ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <a href="<?= base_url('admin/suppliers') ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i data-lucide="x" class="w-4 h-4 mr-2"></i> Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Save Supplier
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

    // Auto-generate supplier code based on name
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('supplier_code');

    nameInput.addEventListener('input', function() {
        if (!codeInput.value) { // Only auto-generate if code field is empty
            const name = this.value.trim();
            if (name) {
                // Generate code from first 3 letters of name + random number
                const prefix = name.substring(0, 3).toUpperCase().replace(/[^A-Z]/g, '');
                const randomNum = Math.floor(Math.random() * 999) + 1;
                const code = prefix + String(randomNum).padStart(3, '0');
                codeInput.value = code;
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
