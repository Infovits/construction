<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Goods Receipt Notes</h1>
            <p class="text-gray-600">Manage goods receipt and delivery processing</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/goods-receipt/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">    
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                New Goods Receipt
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="pending_inspection" <?= ($filters['status'] ?? '') === 'pending_inspection' ? 'selected' : '' ?>>Pending Inspection</option>
                    <option value="partially_accepted" <?= ($filters['status'] ?? '') === 'partially_accepted' ? 'selected' : '' ?>>Partially Accepted</option>
                    <option value="accepted" <?= ($filters['status'] ?? '') === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                    <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <select name="supplier_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Suppliers</option>
                    <?php if (isset($suppliers)): ?>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier['id'] ?>" <?= ($filters['supplier_id'] ?? '') == $supplier['id'] ? 'selected' : '' ?>><?= esc($supplier['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <select name="warehouse_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Warehouses</option>
                    <?php if (isset($warehouses)): ?>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <option value="<?= $warehouse['id'] ?>" <?= ($filters['warehouse_id'] ?? '') == $warehouse['id'] ? 'selected' : '' ?>><?= esc($warehouse['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                    Filter
                </button>
                <a href="<?= base_url('admin/goods-receipt') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- GRNs Table -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Goods Receipt Notes</h2>
        </div>
        
        <?php if (empty($grns)): ?>
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="package-check" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Goods Receipt Notes Found</h3>
                <p class="text-gray-500 mb-4">Get started by creating your first goods receipt note.</p>
                <a href="<?= base_url('admin/goods-receipt/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Create Goods Receipt
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GRN Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($grns as $grn): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= esc($grn['grn_number']) ?></div>
                                    <div class="text-sm text-gray-500">Received by: <?= esc($grn['receiver_first_name'] ?? '') ?> <?= esc($grn['receiver_last_name'] ?? '') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($grn['po_number'])): ?>
                                        <a href="<?= base_url('admin/purchase-orders/' . $grn['purchase_order_id']) ?>" class="text-indigo-600 hover:text-indigo-900">
                                            <?= esc($grn['po_number']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-500">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= esc($grn['supplier_name'] ?? 'Unknown') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= esc($grn['warehouse_name'] ?? 'Unknown') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('M j, Y', strtotime($grn['delivery_date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php
                                        switch($grn['status']) {
                                            case 'pending_inspection': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'partially_accepted': echo 'bg-blue-100 text-blue-800'; break;
                                            case 'accepted': echo 'bg-green-100 text-green-800'; break;
                                            case 'rejected': echo 'bg-red-100 text-red-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800'; break;
                                        }
                                        ?>">
                                        <?= ucfirst(str_replace('_', ' ', $grn['status'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    MWK <?= number_format($grn['total_received_value'] ?? 0, 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="<?= base_url('admin/goods-receipt/' . $grn['id']) ?>" class="text-indigo-600 hover:text-indigo-900">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <?php if ($grn['status'] === 'pending_inspection'): ?>
                                            <a href="<?= base_url('admin/goods-receipt/' . $grn['id'] . '/edit') ?>" class="text-gray-600 hover:text-gray-900">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('admin/quality-inspections/create?grn_id=' . $grn['id']) ?>" class="text-green-600 hover:text-green-900" title="Create Quality Inspection">
                                            <i data-lucide="search" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
lucide.createIcons();
</script>

<?= $this->endSection() ?>
