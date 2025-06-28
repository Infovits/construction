<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Material Categories<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Material Categories</h1>
                <p class="text-gray-600">Manage and organize material categories for inventory</p>
            </div>
            <div>
                <button type="button" onclick="openAddCategoryModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Category
                </button>
            </div>
        </div>
    </div>

    <!-- Categories List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Material Categories</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materials</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No categories found</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= esc($category['name']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 line-clamp-2"><?= esc($category['description'] ?: 'No description') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= $category['material_count'] ?? 0 ?> materials</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $category['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                    <?= $category['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button" onclick="openEditCategoryModal(<?= $category['id'] ?>, '<?= esc($category['name']) ?>', '<?= esc(addslashes($category['description'] ?? '')) ?>', <?= $category['is_active'] ? 'true' : 'false' ?>)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button type="button" onclick="confirmDeleteCategory(<?= $category['id'] ?>, '<?= esc($category['name']) ?>')" class="text-red-600 hover:text-red-900">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if (!empty($pager)): ?>
        <div class="px-6 py-3 border-t border-gray-200">
            <?= $pager->links() ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Add New Category</h3>
            <button type="button" onclick="closeAddCategoryModal()" class="text-gray-400 hover:text-gray-500">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form action="<?= base_url('admin/material-categories/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="p-6 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" checked>
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active Category</label>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button type="button" onclick="closeAddCategoryModal()" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors mr-2">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Add Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Edit Category</h3>
            <button type="button" onclick="closeEditCategoryModal()" class="text-gray-400 hover:text-gray-500">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="editCategoryForm" action="<?= base_url('admin/material-categories/update') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="id" id="edit_id">
            
            <div class="p-6 space-y-4">
                <div>
                    <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Category Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                
                <div>
                    <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="edit_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="edit_is_active" class="ml-2 text-sm text-gray-700">Active Category</label>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button type="button" onclick="closeEditCategoryModal()" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors mr-2">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
});

function openAddCategoryModal() {
    document.getElementById('addCategoryModal').classList.remove('hidden');
}

function closeAddCategoryModal() {
    document.getElementById('addCategoryModal').classList.add('hidden');
    document.getElementById('name').value = '';
    document.getElementById('description').value = '';
    document.getElementById('is_active').checked = true;
}

function openEditCategoryModal(id, name, description, isActive) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_is_active').checked = isActive;
    
    // Update form action
    document.getElementById('editCategoryForm').action = '<?= base_url('admin/material-categories/update') ?>/' + id;
    
    document.getElementById('editCategoryModal').classList.remove('hidden');
}

function closeEditCategoryModal() {
    document.getElementById('editCategoryModal').classList.add('hidden');
}

function confirmDeleteCategory(id, name) {
    if (confirm('Are you sure you want to delete the category "' + name + '"? This may affect materials assigned to this category.')) {
        window.location.href = '<?= base_url('admin/material-categories/delete') ?>/' + id;
    }
}
</script>
<?= $this->endSection() ?>
