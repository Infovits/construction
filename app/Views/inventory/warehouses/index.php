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
                <a href="<?= base_url('admin/warehouses/new') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Warehouse
                </a>
            </div>
        </div>
    </div>

    <!-- Warehouses List -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <?php if (empty($warehouses)): ?>
            <div class="lg:col-span-3 bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                <div class="flex flex-col items-center">
                <a href="<?= base_url('admin/warehouses/new') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="warehouse" class="w-12 h-12 text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No Warehouses Found</h3>
                    <p class="text-gray-500 mb-6">You haven't added any warehouses yet.</p>
                    <a href="<?= base_url('admin/warehouses/new') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Your First Warehouse
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($warehouses as $warehouse): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900"><?= esc($warehouse['name']) ?></h3>
                                <?php
                                $statusText = '';
                                $status = $warehouse['status'] ?? 'active';
                                switch($status) {
                                case 'active':
                                    $statusClass = 'bg-green-100 text-green-800';
                                    $statusText = 'Active';
                                    break;
                                case 'inactive':
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                    $statusText = 'Inactive';
                                    break;
                                case 'maintenance':
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                    $statusText = 'Maintenance';
                                    break;
                                default:
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                    $statusText = 'Unknown';
                            }
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                <?= $statusText ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Address:</p>
                                <p class="font-medium"><?= esc($warehouse['address'] ?? 'Not specified') ?></p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Manager:</p>
                                <?php
                                $managerName = '';
                                if (!empty($warehouse['first_name']) && !empty($warehouse['last_name'])) {
                                    $managerName = $warehouse['first_name'] . ' ' . $warehouse['last_name'];
                                } else {
                                    $managerName = 'Unassigned';
                                }
                                ?>
                                <p class="font-medium"><?= esc($managerName) ?></p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Contact:</p>
                                <p class="font-medium"><?= esc($warehouse['phone'] ?? $warehouse['email'] ?? 'N/A') ?></p>
                            </div>
                            
                            <div class="pt-2 border-t border-gray-100">
                                <p class="text-sm text-gray-600">Warehouse Details:</p>
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    <div class="bg-blue-50 p-2 rounded">
                                        <p class="text-xs text-gray-600">Type</p>
                                        <p class="text-sm font-medium"><?= ucfirst($warehouse['warehouse_type'] ?? 'main') ?></p>
                                    </div>
                                    <div class="bg-green-50 p-2 rounded">
                                        <p class="text-xs text-gray-600">Code</p>
                                        <p class="text-sm font-medium"><?= esc($warehouse['code'] ?? 'N/A') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex justify-between">
                        <a href="<?= base_url('admin/warehouses/' . $warehouse['id']) ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i data-lucide="list" class="w-4 h-4 inline mr-1"></i> View Inventory
                        </a>
                        <div>
                            <a href="<?= base_url('admin/warehouses/' . $warehouse['id'] . '/edit') ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
});

function confirmDeleteWarehouse(id, name) {
    if (confirm('Are you sure you want to delete the warehouse "' + name + '"? This will also delete all associated inventory records.')) {
        window.location.href = '<?= base_url('admin/warehouses/delete') ?>/' + id;
    }
}
</script>
<?= $this->endSection() ?>
