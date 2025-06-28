<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Warehouses<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Warehouses</h1>
                <p class="text-gray-600">Manage warehouse locations and material storage</p>
            </div>
            <div>
                <button type="button" onclick="openAddWarehouseModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Warehouse
                </button>
            </div>
        </div>
    </div>

    <!-- Warehouses List -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <?php if (empty($warehouses)): ?>
            <div class="lg:col-span-3 bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                <div class="flex flex-col items-center">
                    <div class="p-4 bg-blue-100 rounded-full mb-4">
                        <i data-lucide="warehouse" class="w-12 h-12 text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No Warehouses Found</h3>
                    <p class="text-gray-500 mb-6">You haven't added any warehouses yet.</p>
                    <button type="button" onclick="openAddWarehouseModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i> Add Your First Warehouse
                    </button>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($warehouses as $warehouse): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900"><?= esc($warehouse['name']) ?></h3>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $warehouse['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $warehouse['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Location:</p>
                                <p class="font-medium"><?= esc($warehouse['location']) ?></p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Manager:</p>
                                <p class="font-medium"><?= esc($warehouse['manager_name'] ?? 'Unassigned') ?></p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Contact:</p>
                                <p class="font-medium"><?= esc($warehouse['contact_info'] ?? 'N/A') ?></p>
                            </div>
                            
                            <div class="pt-2 border-t border-gray-100">
                                <p class="text-sm text-gray-600">Inventory Stats:</p>
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    <div class="bg-blue-50 p-2 rounded">
                                        <p class="text-xs text-gray-600">Total Items</p>
                                        <p class="text-lg font-bold"><?= $warehouse['total_items'] ?? 0 ?></p>
                                    </div>
                                    <div class="bg-amber-50 p-2 rounded">
                                        <p class="text-xs text-gray-600">Low Stock</p>
                                        <p class="text-lg font-bold"><?= $warehouse['low_stock_count'] ?? 0 ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex justify-between">
                        <a href="<?= base_url('admin/warehouses/view/' . $warehouse['id']) ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i data-lucide="list" class="w-4 h-4 inline mr-1"></i> View Inventory
                        </a>
                        <div>
                            <button type="button" onclick="openEditWarehouseModal(<?= $warehouse['id'] ?>)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button type="button" onclick="confirmDeleteWarehouse(<?= $warehouse['id'] ?>, '<?= esc($warehouse['name']) ?>')" class="text-red-600 hover:text-red-900">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Add Warehouse Modal -->
<div id="addWarehouseModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Add New Warehouse</h3>
            <button type="button" onclick="closeAddWarehouseModal()" class="text-gray-400 hover:text-gray-500">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form action="<?= base_url('admin/warehouses/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="p-6 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Warehouse Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location <span class="text-red-500">*</span></label>
                    <input type="text" name="location" id="location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <p class="mt-1 text-xs text-gray-500">Address or description of where the warehouse is located</p>
                </div>
                
                <div>
                    <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-1">Manager</label>
                    <select name="manager_id" id="manager_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Manager (Optional)</option>
                        <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="contact_info" class="block text-sm font-medium text-gray-700 mb-1">Contact Information</label>
                    <input type="text" name="contact_info" id="contact_info" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Phone number, email, etc.</p>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked>
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active Warehouse</label>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button type="button" onclick="closeAddWarehouseModal()" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors mr-2">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Add Warehouse
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Warehouse Modal -->
<div id="editWarehouseModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Edit Warehouse</h3>
            <button type="button" onclick="closeEditWarehouseModal()" class="text-gray-400 hover:text-gray-500">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="editWarehouseForm" action="" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            
            <div class="p-6 space-y-4">
                <div>
                    <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Warehouse Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                
                <div>
                    <label for="edit_location" class="block text-sm font-medium text-gray-700 mb-1">Location <span class="text-red-500">*</span></label>
                    <input type="text" name="location" id="edit_location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <p class="mt-1 text-xs text-gray-500">Address or description of where the warehouse is located</p>
                </div>
                
                <div>
                    <label for="edit_manager_id" class="block text-sm font-medium text-gray-700 mb-1">Manager</label>
                    <select name="manager_id" id="edit_manager_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Manager (Optional)</option>
                        <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="edit_contact_info" class="block text-sm font-medium text-gray-700 mb-1">Contact Information</label>
                    <input type="text" name="contact_info" id="edit_contact_info" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Phone number, email, etc.</p>
                </div>
                
                <div>
                    <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="edit_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="edit_is_active" class="ml-2 text-sm text-gray-700">Active Warehouse</label>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button type="button" onclick="closeEditWarehouseModal()" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors mr-2">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Update Warehouse
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

function openAddWarehouseModal() {
    document.getElementById('addWarehouseModal').classList.remove('hidden');
}

function closeAddWarehouseModal() {
    document.getElementById('addWarehouseModal').classList.add('hidden');
}

function openEditWarehouseModal(id) {
    // Fetch warehouse data via AJAX
    fetch('<?= base_url('admin/warehouses/get') ?>/' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const warehouse = data.data;
                
                document.getElementById('edit_name').value = warehouse.name;
                document.getElementById('edit_location').value = warehouse.location;
                document.getElementById('edit_manager_id').value = warehouse.manager_id || '';
                document.getElementById('edit_contact_info').value = warehouse.contact_info || '';
                document.getElementById('edit_description').value = warehouse.description || '';
                document.getElementById('edit_is_active').checked = warehouse.is_active == 1;
                
                // Set form action URL
                document.getElementById('editWarehouseForm').action = '<?= base_url('admin/warehouses/update') ?>/' + id;
                
                // Show modal
                document.getElementById('editWarehouseModal').classList.remove('hidden');
            } else {
                alert('Error fetching warehouse data. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching warehouse data.');
        });
}

function closeEditWarehouseModal() {
    document.getElementById('editWarehouseModal').classList.add('hidden');
}

function confirmDeleteWarehouse(id, name) {
    if (confirm('Are you sure you want to delete the warehouse "' + name + '"? This will also delete all associated inventory records.')) {
        window.location.href = '<?= base_url('admin/warehouses/delete') ?>/' + id;
    }
}
</script>
<?= $this->endSection() ?>
