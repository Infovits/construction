<?php helper('currency'); ?>
<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Goods Receipt Note #<?= esc($grn['grn_number']) ?></h1>
            <div class="flex items-center space-x-4 mt-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    <?php
                    switch($grn['status']) {
                        case 'pending_inspection': echo 'bg-yellow-100 text-yellow-800'; break;
                        case 'partially_accepted': echo 'bg-orange-100 text-orange-800'; break;
                        case 'accepted': echo 'bg-green-100 text-green-800'; break;
                        case 'rejected': echo 'bg-red-100 text-red-800'; break;
                        default: echo 'bg-gray-100 text-gray-800'; break;
                    }
                    ?>">
                    <?= ucfirst(str_replace('_', ' ', $grn['status'])) ?>
                </span>
                <span class="text-gray-600">Delivered <?= date('M j, Y', strtotime($grn['delivery_date'])) ?></span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/goods-receipt') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
            <?php if ($grn['status'] === 'pending_inspection'): ?>
            <a href="<?= base_url('admin/goods-receipt/' . $grn['id'] . '/edit') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit
            </a>
            <?php endif; ?>
            <a href="<?= base_url('admin/goods-receipt/' . $grn['id'] . '/pdf') ?>" 
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
               target="_blank">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                Export PDF
            </a>
        </div>
    </div>

    <!-- GRN Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Delivery Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Delivery Information</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">GRN Number:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($grn['grn_number']) ?></span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Purchase Order:</span>
                    <a href="<?= base_url('admin/purchase-orders/' . $grn['purchase_order_id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800 ml-2">
                        PO #<?= esc($grn['po_number']) ?>
                    </a>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Delivery Date:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= date('M j, Y', strtotime($grn['delivery_date'])) ?></span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Warehouse:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($grn['warehouse_name']) ?></span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Received By:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($grn['received_by_name']) ?></span>
                </div>
                <?php if (!empty($grn['delivery_note_number'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Delivery Note:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($grn['delivery_note_number']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Supplier & Transport -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Supplier & Transport</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Supplier:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($grn['supplier_name']) ?></span>
                </div>
                <?php if (!empty($grn['vehicle_number'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Vehicle Number:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($grn['vehicle_number']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($grn['driver_name'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Driver Name:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($grn['driver_name']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($grn['total_received_value'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Total Value:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= format_currency($grn['total_received_value']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Received Items -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Received Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordered</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivered</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accepted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Expiry</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quality Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (isset($grnItems) && !empty($grnItems)): ?>
                        <?php foreach ($grnItems as $item): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= esc($item['material_name']) ?></div>
                                        <div class="text-sm text-gray-500"><?= esc($item['material_code']) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= number_format($item['quantity_ordered'], 3) ?> <?= esc($item['material_unit']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= number_format($item['quantity_delivered'], 3) ?> <?= esc($item['material_unit']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="<?= $item['quantity_accepted'] > 0 ? 'text-green-600 font-medium' : 'text-gray-500' ?>">
                                        <?= number_format($item['quantity_accepted'], 3) ?> <?= esc($item['material_unit']) ?>
                                    </span>
                                    <?php if ($item['quantity_rejected'] > 0): ?>
                                        <div class="text-red-600 text-xs">
                                            Rejected: <?= number_format($item['quantity_rejected'], 3) ?> <?= esc($item['material_unit']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if (!empty($item['batch_number'])): ?>
                                        <div class="text-sm"><?= esc($item['batch_number']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['expiry_date'])): ?>
                                        <div class="text-xs text-gray-500">Exp: <?= date('M j, Y', strtotime($item['expiry_date'])) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php
                                        switch($item['quality_status']) {
                                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'passed': echo 'bg-green-100 text-green-800'; break;
                                            case 'failed': echo 'bg-red-100 text-red-800'; break;
                                            case 'conditional': echo 'bg-orange-100 text-orange-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800'; break;
                                        }
                                        ?>">
                                        <?= ucfirst($item['quality_status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if ($item['quality_status'] === 'pending'): ?>
                                        <a href="<?= base_url('admin/quality-inspections/create?grn_item_id=' . $item['id']) ?>" 
                                           class="text-indigo-600 hover:text-indigo-900" title="Create Quality Inspection">
                                            <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                                        </a>
                                    <?php elseif (isset($item['quality_inspection_id'])): ?>
                                        <a href="<?= base_url('admin/quality-inspections/' . $item['quality_inspection_id']) ?>" 
                                           class="text-blue-600 hover:text-blue-900" title="View Quality Inspection">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notes and Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Notes -->
        <?php if (!empty($grn['notes'])): ?>
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Reception Notes</h2>
            <p class="text-gray-700"><?= nl2br(esc($grn['notes'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Delivery Summary -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Delivery Summary</h2>
            <div class="space-y-3">
                <?php
                $totalItems = count($grnItems ?? []);
                $fullyDelivered = 0;
                $partiallyDelivered = 0;
                $pendingInspection = 0;
                $qualityPassed = 0;
                
                if (isset($grnItems)) {
                    foreach ($grnItems as $item) {
                        if ($item['quantity_delivered'] >= $item['quantity_ordered']) {
                            $fullyDelivered++;
                        } elseif ($item['quantity_delivered'] > 0) {
                            $partiallyDelivered++;
                        }
                        
                        if ($item['quality_status'] === 'pending') {
                            $pendingInspection++;
                        } elseif ($item['quality_status'] === 'passed') {
                            $qualityPassed++;
                        }
                    }
                }
                ?>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Total Items:</span>
                    <span class="font-medium"><?= $totalItems ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Fully Delivered:</span>
                    <span class="font-medium text-green-600"><?= $fullyDelivered ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Partially Delivered:</span>
                    <span class="font-medium text-yellow-600"><?= $partiallyDelivered ?></span>
                </div>
                <hr>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Pending Inspection:</span>
                    <span class="font-medium text-yellow-600"><?= $pendingInspection ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Quality Approved:</span>
                    <span class="font-medium text-green-600"><?= $qualityPassed ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Documents -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Related Documents</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Purchase Order -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Purchase Order</h3>
                <a href="<?= base_url('admin/purchase-orders/' . $grn['purchase_order_id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800">
                    PO #<?= esc($grn['po_number']) ?>
                </a>
                <p class="text-xs text-gray-500 mt-1">View original order details</p>
            </div>

            <!-- Quality Inspections -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Quality Inspections</h3>
                <?php if (isset($qualityInspections) && !empty($qualityInspections)): ?>
                    <ul class="space-y-1">
                        <?php foreach ($qualityInspections as $qi): ?>
                            <li>
                                <a href="<?= base_url('admin/quality-inspections/' . $qi['id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    QI #<?= esc($qi['inspection_number']) ?>
                                </a>
                                <span class="text-xs <?= $qi['status'] === 'passed' ? 'text-green-600' : ($qi['status'] === 'failed' ? 'text-red-600' : 'text-yellow-600') ?> ml-1">
                                    (<?= ucfirst($qi['status']) ?>)
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-sm text-gray-500">No inspections yet</p>
                    <?php if ($grn['status'] === 'pending_inspection'): ?>
                        <a href="<?= base_url('admin/quality-inspections/create?grn_id=' . $grn['id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800 block mt-1">
                            Create Inspection
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Stock Movements -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Stock Movements</h3>
                <?php if (isset($stockMovements) && !empty($stockMovements)): ?>
                    <ul class="space-y-1">
                        <?php foreach ($stockMovements as $movement): ?>
                            <li class="text-sm">
                                <span class="text-gray-900"><?= ucfirst($movement['movement_type']) ?>:</span>
                                <span class="text-gray-600"><?= number_format($movement['quantity'], 2) ?> <?= esc($movement['material_unit']) ?></span>
                                <span class="text-xs text-gray-500 block"><?= date('M j', strtotime($movement['created_at'])) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-sm text-gray-500">No stock movements yet</p>
                    <p class="text-xs text-gray-400 mt-1">Items will move to stock after quality approval</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
