<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Suppliers<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Suppliers</h1>
                <p class="text-gray-600">Manage material suppliers and delivery records</p>
            </div>
            <div>
                <a href="<?= base_url('admin/suppliers/new') ?>" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Supplier
                </a>
            </div>
        </div>
    </div>

    <!-- Suppliers List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Material Suppliers</h3>
            
            <div class="flex items-center gap-2">
                <form class="flex items-center" action="<?= base_url('admin/suppliers') ?>" method="GET">
                    <input type="search" name="search" placeholder="Search suppliers..." class="text-sm px-3 py-1 border border-gray-300 rounded-lg focus:ring-1 focus:ring-purple-500 focus:border-purple-500" value="<?= $search ?? '' ?>">
                    <button type="submit" class="ml-2 text-purple-600 hover:text-purple-800">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materials</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($suppliers)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            <?php if (!empty($search)): ?>
                                No suppliers found matching your search criteria
                            <?php else: ?>
                                No suppliers have been added yet
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900"><?= esc($supplier['name']) ?></div>
                                <div class="text-xs text-gray-500"><?= esc($supplier['address']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?= esc($supplier['contact_person']) ?></div>
                                <div class="text-xs text-gray-500"><?= esc($supplier['phone']) ?></div>
                                <div class="text-xs text-blue-500"><?= esc($supplier['email']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900"><?= $supplier['material_count'] ?? 0 ?> materials</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= !empty($supplier['last_order_date']) ? date('M d, Y', strtotime($supplier['last_order_date'])) : 'No orders' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                switch($supplier['status']) {
                                    case 'active':
                                        $statusClass = 'bg-green-100 text-green-800';
                                        $statusText = 'Active';
                                        break;
                                    case 'inactive':
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        $statusText = 'Inactive';
                                        break;
                                    case 'blacklisted':
                                        $statusClass = 'bg-red-100 text-red-800';
                                        $statusText = 'Blacklisted';
                                        break;
                                    default:
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        $statusText = 'Unknown';
                                }
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= base_url('admin/suppliers/' . $supplier['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="<?= base_url('admin/suppliers/edit/' . $supplier['id']) ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <button type="button" onclick="confirmDelete(<?= $supplier['id'] ?>, '<?= esc($supplier['name']) ?>')" class="text-red-600 hover:text-red-900">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
});

function confirmDelete(id, name) {
    if (confirm('Are you sure you want to delete supplier "' + name + '"? This may affect materials and purchase orders linked to this supplier.')) {
        window.location.href = '<?= base_url('admin/suppliers/delete') ?>/' + id;
    }
}
</script>
<?= $this->endSection() ?>
