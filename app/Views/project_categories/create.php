<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Project Category Create/Edit Page -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= isset($category) ? 'Edit Category' : 'Create New Category' ?></h1>
            <p class="text-gray-600">Configure project category details</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/project-categories') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Categories
            </a>
        </div>
    </div>

    <!-- Category Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form action="<?= isset($category) ? base_url('admin/project-categories/update/' . $category['id']) : base_url('admin/project-categories') ?>" method="post" class="p-6 space-y-6">
            <?= csrf_field() ?>
            
            <!-- Basic Information Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Category Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                        <input type="text" id="name" name="name" required
                               value="<?= old('name', isset($category) ? $category['name'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">Company *</label>
                        <select id="company_id" name="company_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Company</option>
                            <?php foreach($companies as $company): ?>
                                <option value="<?= $company['id'] ?>" 
                                        <?= old('company_id', isset($category) ? $category['company_id'] : '') == $company['id'] ? 'selected' : '' ?>>
                                    <?= esc($company['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('description', isset($category) ? $category['description'] : '') ?></textarea>
                </div>
            </div>

            <!-- Visual Settings Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Visual Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="color_code" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" id="color_code" name="color_code"
                                   value="<?= old('color_code', isset($category) ? $category['color_code'] : '#6366f1') ?>"
                                   class="w-12 h-10 border border-gray-300 rounded cursor-pointer">
                            <input type="text" id="color_text" 
                                   value="<?= old('color_code', isset($category) ? $category['color_code'] : '#6366f1') ?>"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   readonly>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">This color will be used to identify the category</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                       <?= old('is_active', isset($category) ? $category['is_active'] : 1) ? 'checked' : '' ?>
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Active Category
                                </label>
                            </div>
                            <p class="text-sm text-gray-500">Inactive categories won't be available for new projects</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Preview</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div id="preview-color" class="w-6 h-6 rounded-full" style="background-color: <?= old('color_code', isset($category) ? $category['color_code'] : '#6366f1') ?>"></div>
                        <div>
                            <div id="preview-name" class="font-medium text-gray-900"><?= old('name', isset($category) ? $category['name'] : 'Category Name') ?></div>
                            <div id="preview-description" class="text-sm text-gray-500"><?= old('description', isset($category) ? $category['description'] : 'Category description will appear here') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="border-t pt-6 flex flex-col sm:flex-row gap-3 justify-end">
                <a href="<?= base_url('admin/project-categories') ?>" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    <?= isset($category) ? 'Update Category' : 'Create Category' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameField = document.getElementById('name');
    const descriptionField = document.getElementById('description');
    const colorField = document.getElementById('color_code');
    const colorText = document.getElementById('color_text');
    
    const previewName = document.getElementById('preview-name');
    const previewDescription = document.getElementById('preview-description');
    const previewColor = document.getElementById('preview-color');

    // Update preview in real-time
    nameField.addEventListener('input', function() {
        previewName.textContent = this.value || 'Category Name';
    });

    descriptionField.addEventListener('input', function() {
        previewDescription.textContent = this.value || 'Category description will appear here';
    });

    colorField.addEventListener('input', function() {
        const color = this.value;
        colorText.value = color;
        previewColor.style.backgroundColor = color;
    });

    // Initialize Lucide icons
    lucide.createIcons();
});
</script>
<?= $this->endSection() ?>
