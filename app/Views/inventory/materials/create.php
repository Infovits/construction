<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Add New Material<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add New Material</h1>
                <p class="text-gray-600">Add a new construction material to your inventory</p>
            </div>
            <div>
                <a href="<?php echo base_url(); ?>/admin/materials" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Materials
                </a>
            </div>
        </div>
    </div>

    <!-- Material Creation Form -->
    <div class="flex justify-center">
        <div class="w-full max-w-6xl">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Material Information</h3>
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

                    <form action="<?= base_url('admin/materials/store') ?>" method="POST" id="materialForm">
                        <?= csrf_field() ?>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Material Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('name') ?>" required>
                                </div>

                                <div>
                                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU/Item Code <span class="text-red-500">*</span></label>
                                    <input type="text" name="sku" id="sku" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('sku') ?>" required>
                                    <p class="mt-1 text-sm text-gray-500">Unique identifier for this material</p>
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea name="description" id="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="4"><?= old('description') ?></textarea>
                                </div>

                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                                    <select name="category_id" id="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                            <?= esc($category['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label for="barcode" class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                                    <input type="text" name="barcode" id="barcode" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('barcode') ?>">
                                    <p class="mt-1 text-sm text-gray-500">If known, enter the product barcode (UPC, EAN, etc.)</p>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Unit of Measure <span class="text-red-500">*</span></label>
                                        <select name="unit" id="unit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                            <option value="piece" <?= old('unit') === 'piece' ? 'selected' : '' ?>>Piece</option>
                                            <option value="kg" <?= old('unit') === 'kg' ? 'selected' : '' ?>>Kilogram</option>
                                            <option value="m" <?= old('unit') === 'm' ? 'selected' : '' ?>>Meter</option>
                                            <option value="m2" <?= old('unit') === 'm2' ? 'selected' : '' ?>>Square Meter</option>
                                            <option value="m3" <?= old('unit') === 'm3' ? 'selected' : '' ?>>Cubic Meter</option>
                                            <option value="l" <?= old('unit') === 'l' ? 'selected' : '' ?>>Liter</option>
                                            <option value="bag" <?= old('unit') === 'bag' ? 'selected' : '' ?>>Bag</option>
                                            <option value="roll" <?= old('unit') === 'roll' ? 'selected' : '' ?>>Roll</option>
                                            <option value="box" <?= old('unit') === 'box' ? 'selected' : '' ?>>Box</option>
                                            <option value="other" <?= old('unit') === 'other' ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">Unit Cost <span class="text-red-500">*</span></label>
                                        <input type="number" name="unit_cost" id="unit_cost" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" step="0.01" value="<?= old('unit_cost', 0) ?>" required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="min_stock_level" class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock Level</label>
                                        <input type="number" name="min_stock_level" id="min_stock_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" step="1" value="<?= old('min_stock_level', 10) ?>">
                                        <p class="mt-1 text-sm text-gray-500">Trigger low stock alerts below this level</p>
                                    </div>
                                    <div>
                                        <label for="reorder_quantity" class="block text-sm font-medium text-gray-700 mb-2">Reorder Quantity</label>
                                        <input type="number" name="reorder_quantity" id="reorder_quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" step="1" value="<?= old('reorder_quantity', 50) ?>">
                                    </div>
                                </div>

                                <div>
                                    <label for="primary_supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Primary Supplier</label>
                                    <select name="primary_supplier_id" id="primary_supplier_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Supplier</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?= $supplier['id'] ?>" <?= old('primary_supplier_id') == $supplier['id'] ? 'selected' : '' ?>>
                                            <?= esc($supplier['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= old('is_active', '1') ? 'checked' : '' ?>>
                                        <label for="is_active" class="ml-2 text-sm text-gray-700">
                                            Active Material
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_bulk" id="is_bulk" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= old('is_bulk') ? 'checked' : '' ?>>
                                        <label for="is_bulk" class="ml-2 text-sm text-gray-700">
                                            Bulk Material (Can be partially used)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <a href="<?= base_url('admin/materials') ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i data-lucide="x" class="w-4 h-4 mr-2"></i> Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i data-lucide="save" class="w-4 h-4 mr-2"></i> Save Material
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
