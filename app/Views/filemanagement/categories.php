<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>File Categories<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-tags mr-2"></i>File Categories</h1>
            <p class="text-gray-600 mt-1">Manage file categories for better organization</p>
        </div>
        <div class="flex gap-2">
            <button type="button" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors" onclick="openAddModal()">
                <i class="fas fa-plus mr-2"></i> Add Category
            </button>
            <a href="<?= base_url('file-management') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Files
            </a>
        </div>
    </div>

    <!-- Alerts -->
    <div id="alertContainer"></div>

    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Color</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (count($categories) > 0): ?>
                        <?php foreach ($categories as $category): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= esc($category['name']) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?= esc($category['description'] ?? 'No description') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded" style="background-color: <?= esc($category['color_code']) ?>"></div>
                                        <span class="text-xs text-gray-600"><?= esc($category['color_code']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($category['is_active']): ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= date('M d, Y', strtotime($category['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button onclick='editCategory(<?= json_encode($category) ?>)' class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button onclick="deleteCategory(<?= $category['id'] ?>)" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No categories found. <button onclick="openAddModal()" class="text-indigo-600 hover:text-indigo-800 font-semibold">Create your first category</button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="fixed inset-0 z-50 hidden overflow-y-auto" id="categoryModal">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modalTitle">
                    <i class="fas fa-tag mr-2"></i>Add Category
                </h3>
                <form id="categoryForm">
                    <?= csrf_field() ?>
                    <input type="hidden" id="categoryId" name="category_id">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-600">*</span></label>
                            <input type="text" id="categoryName" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Blueprints, Permits, Contracts">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="categoryDescription" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional description"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <input type="color" id="categoryColor" name="color_code" value="#6366f1" class="w-20 h-10 border border-gray-300 rounded-lg cursor-pointer">
                        </div>

                        <div id="statusField" class="hidden">
                            <label class="flex items-center">
                                <input type="checkbox" id="categoryActive" name="is_active" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="button" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const csrfName = '<?= csrf_token() ?>';
let csrfHash = '<?= csrf_hash() ?>';
let isEdit = false;

function openAddModal() {
    isEdit = false;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-tag mr-2"></i>Add Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('statusField').classList.add('hidden');
    document.getElementById('categoryModal').classList.remove('hidden');
}

function editCategory(category) {
    isEdit = true;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit mr-2"></i>Edit Category';
    document.getElementById('categoryId').value = category.id;
    document.getElementById('categoryName').value = category.name;
    document.getElementById('categoryDescription').value = category.description || '';
    document.getElementById('categoryColor').value = category.color_code;
    document.getElementById('categoryActive').checked = category.is_active == 1;
    document.getElementById('statusField').classList.remove('hidden');
    document.getElementById('categoryModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.set(csrfName, csrfHash);
    
    const categoryId = document.getElementById('categoryId').value;
    const url = isEdit 
        ? '<?= base_url('file-management/categories/update') ?>/' + categoryId
        : '<?= base_url('file-management/categories/store') ?>';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.csrfHash) csrfHash = data.csrfHash;
        
        showAlert(data.success ? 'success' : 'error', data.message);
        
        if (data.success) {
            closeModal();
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred. Please try again.');
    });
});

function deleteCategory(categoryId) {
    if (!confirm('Are you sure you want to delete this category?')) return;
    
    const formData = new FormData();
    formData.set(csrfName, csrfHash);
    
    fetch('<?= base_url('file-management/categories/delete') ?>/' + categoryId, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.csrfHash) csrfHash = data.csrfHash;
        
        showAlert(data.success ? 'success' : 'error', data.message);
        
        if (data.success) {
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred. Please try again.');
    });
}

function showAlert(type, message) {
    const alertClass = type === 'success' 
        ? 'bg-green-50 border-green-200 text-green-800' 
        : 'bg-red-50 border-red-200 text-red-800';
    
    const alertHtml = `
        <div class="${alertClass} border px-4 py-3 rounded-lg flex justify-between items-center">
            <span>${message}</span>
            <button type="button" class="text-current hover:opacity-75 text-xl" onclick="this.parentElement.remove()">&times;</button>
        </div>
    `;
    
    document.getElementById('alertContainer').innerHTML = alertHtml;
    
    setTimeout(() => {
        document.getElementById('alertContainer').innerHTML = '';
    }, 5000);
}
</script>

<?= $this->endSection() ?>
