<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= esc($title) ?></h1>
            <nav class="flex mt-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="<?= base_url('admin/dashboard') ?>" class="text-gray-700 hover:text-blue-600">Dashboard</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                            <a href="<?= base_url('admin/warehouses') ?>" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">Warehouses</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                            <span class="ml-1 text-gray-500 md:ml-2">Edit Warehouse</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/warehouses') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>Back to Warehouses
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Success Message -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?= esc(session()->getFlashdata('success')) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Edit Warehouse Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Warehouse Information</h2>
        </div>
        
        <form action="<?= base_url('admin/warehouses/' . $warehouse['id']) ?>" method="POST" class="p-6">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Warehouse Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Warehouse Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('name', $warehouse['name'] ?? '') ?>" required>
                    <p class="mt-1 text-xs text-gray-500">Enter a descriptive name for the warehouse</p>
                </div>

                <!-- Warehouse Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Warehouse Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('code', $warehouse['code'] ?? '') ?>" required>
                    <p class="mt-1 text-xs text-gray-500">Unique identifier for this warehouse (e.g., WH001)</p>
                </div>

                <!-- Warehouse Type -->
                <div>
                    <label for="warehouse_type" class="block text-sm font-medium text-gray-700 mb-2">Warehouse Type</label>
                    <select name="warehouse_type" id="warehouse_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="main" <?= old('warehouse_type', $warehouse['warehouse_type'] ?? 'main') === 'main' ? 'selected' : '' ?>>Main Warehouse</option>
                        <option value="site" <?= old('warehouse_type', $warehouse['warehouse_type'] ?? 'main') === 'site' ? 'selected' : '' ?>>Site Storage</option>
                        <option value="temporary" <?= old('warehouse_type', $warehouse['warehouse_type'] ?? 'main') === 'temporary' ? 'selected' : '' ?>>Temporary Storage</option>
                    </select>
                </div>

                <!-- Manager -->
                <div>
                    <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-2">Manager</label>
                    <select name="manager_id" id="manager_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Manager (Optional)</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= old('manager_id', $warehouse['manager_id'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" id="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('phone', $warehouse['phone'] ?? '') ?>">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('email', $warehouse['email'] ?? '') ?>">
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Capacity</label>
                    <input type="number" name="capacity" id="capacity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" step="0.01" min="0" value="<?= old('capacity', $warehouse['capacity'] ?? '') ?>">
                    <p class="mt-1 text-xs text-gray-500">Storage capacity in square meters</p>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="active" <?= old('status', $warehouse['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= old('status', $warehouse['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="maintenance" <?= old('status', $warehouse['status'] ?? 'active') === 'maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                    </select>
                </div>
            </div>

            <!-- Address Section -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" id="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= old('address', $warehouse['address'] ?? '') ?></textarea>
                    </div>

                    <!-- City -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" name="city" id="city" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('city', $warehouse['city'] ?? '') ?>">
                    </div>

                    <!-- State -->
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State/Region</label>
                        <input type="text" name="state" id="state" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('state', $warehouse['state'] ?? '') ?>">
                    </div>

                    <!-- Country -->
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <input type="text" name="country" id="country" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?= old('country', $warehouse['country'] ?? 'Malawi') ?>">
                    </div>
                </div>
            </div>

            <!-- Project Association -->
            <div class="mt-6">
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="is_project_site" id="is_project_site" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" <?= old('is_project_site', $warehouse['is_project_site'] ?? false) ? 'checked' : '' ?>>
                    <label for="is_project_site" class="ml-2 text-sm text-gray-700">This is a project site warehouse</label>
                </div>
                
                <div id="project_selection" class="<?= old('is_project_site', $warehouse['is_project_site'] ?? false) ? '' : 'hidden' ?>">
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Associated Project</label>
                    <select name="project_id" id="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>" <?= old('project_id', $warehouse['project_id'] ?? '') == $project['id'] ? 'selected' : '' ?>>
                                <?= esc($project['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Additional notes about this warehouse..."><?= old('notes', $warehouse['notes'] ?? '') ?></textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="<?= base_url('admin/warehouses') ?>" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>Update Warehouse
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Show/hide project selection based on checkbox
    const isProjectSiteCheckbox = document.getElementById('is_project_site');
    const projectSelection = document.getElementById('project_selection');
    
    isProjectSiteCheckbox.addEventListener('change', function() {
        if (this.checked) {
            projectSelection.classList.remove('hidden');
        } else {
            projectSelection.classList.add('hidden');
            document.getElementById('project_id').value = '';
        }
    });
});
</script>

<?= $this->endSection() ?>