<?php helper('currency'); ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Purchase Order #<?= esc($purchaseOrder['po_number']) ?></h1>
            <div class="flex items-center space-x-4 mt-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    <?php
                    switch($purchaseOrder['status']) {
                        case 'draft': echo 'bg-gray-100 text-gray-800'; break;
                        case 'sent': echo 'bg-blue-100 text-blue-800'; break;
                        case 'acknowledged': echo 'bg-green-100 text-green-800'; break;
                        case 'partially_received': echo 'bg-yellow-100 text-yellow-800'; break;
                        case 'completed': echo 'bg-green-100 text-green-800'; break;
                        case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                        default: echo 'bg-gray-100 text-gray-800'; break;
                    }
                    ?>">
                    <?= ucfirst(str_replace('_', ' ', $purchaseOrder['status'])) ?>
                </span>
                <span class="text-gray-600">Created <?= date('M j, Y', strtotime($purchaseOrder['created_at'])) ?></span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/purchase-orders') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
            
            <?php if ($purchaseOrder['status'] === 'draft'): ?>
                <a href="<?= base_url('admin/purchase-orders/' . $purchaseOrder['id'] . '/edit') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                    Edit
                </a>
                <button onclick="approvePO(<?= $purchaseOrder['id'] ?>)" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                    Approve & Send
                </button>
            <?php endif; ?>
            
            <?php if (in_array($purchaseOrder['status'], ['acknowledged', 'partially_received'])): ?>
                <a href="<?= base_url('admin/goods-receipt/create?purchase_order_id=' . $purchaseOrder['id']) ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="package" class="w-4 h-4 mr-2"></i>
                    Receive Goods
                </a>
            <?php endif; ?>
            
            <?php if ($purchaseOrder['status'] === 'sent'): ?>
                <button onclick="acknowledgePO(<?= $purchaseOrder['id'] ?>)" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    <i data-lucide="handshake" class="w-4 h-4 mr-2"></i>
                    Mark Acknowledged
                </button>
            <?php endif; ?>
            
            <?php if (!in_array($purchaseOrder['status'], ['completed', 'cancelled'])): ?>
                <button onclick="cancelPO(<?= $purchaseOrder['id'] ?>)" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                    Cancel
                </button>
            <?php endif; ?>
            
            <?php if ($purchaseOrder['status'] === 'draft'): ?>
                <button onclick="deletePO(<?= $purchaseOrder['id'] ?>)" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                    Delete
                </button>
            <?php endif; ?>
            
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                Print
            </button>
        </div>
    </div>

    <!-- PO Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">PO Number:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($purchaseOrder['po_number']) ?></span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">PO Date:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= date('M j, Y', strtotime($purchaseOrder['po_date'])) ?></span>
                </div>
                <?php if (!empty($purchaseOrder['expected_delivery_date'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Expected Delivery:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= date('M j, Y', strtotime($purchaseOrder['expected_delivery_date'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($purchaseOrder['material_request_number'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Material Request:</span>
                    <a href="<?= base_url('admin/material-requests/' . $purchaseOrder['material_request_id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800 ml-2">
                        <?= esc($purchaseOrder['material_request_number']) ?>
                    </a>
                </div>
                <?php endif; ?>
                <?php if (!empty($purchaseOrder['project_name'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Project:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($purchaseOrder['project_name']) ?></span>
                </div>
                <?php endif; ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Created By:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($purchaseOrder['created_by_name']) ?></span>
                </div>
            </div>
        </div>

        <!-- Supplier Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Supplier Information</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Supplier:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($purchaseOrder['supplier_name']) ?></span>
                </div>
                <?php if (!empty($purchaseOrder['supplier_email'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Email:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($purchaseOrder['supplier_email']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($purchaseOrder['supplier_phone'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Phone:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($purchaseOrder['supplier_phone']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($purchaseOrder['supplier_address'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Address:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($purchaseOrder['supplier_address']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Ordered</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Received</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (isset($purchaseOrderItems) && !empty($purchaseOrderItems)): ?>
                        <?php foreach ($purchaseOrderItems as $item): ?>
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
                                    <?= number_format($item['quantity_received'], 3) ?> <?= esc($item['material_unit']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= format_currency($item['unit_cost']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= format_currency($item['total_cost']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php
                                        $receivedPercentage = $item['quantity_ordered'] > 0 ? ($item['quantity_received'] / $item['quantity_ordered']) * 100 : 0;
                                        if ($receivedPercentage == 0): echo 'bg-gray-100 text-gray-800'; 
                                        elseif ($receivedPercentage < 100): echo 'bg-yellow-100 text-yellow-800';
                                        else: echo 'bg-green-100 text-green-800'; endif;
                                        ?>">
                                        <?php
                                        if ($receivedPercentage == 0): echo 'Pending';
                                        elseif ($receivedPercentage < 100): echo 'Partial (' . round($receivedPercentage) . '%)';
                                        else: echo 'Complete'; endif;
                                        ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Terms & Conditions -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Terms & Conditions</h2>
            <div class="space-y-3">
                <?php if (!empty($purchaseOrder['payment_terms'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Payment Terms:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($purchaseOrder['payment_terms']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($purchaseOrder['delivery_terms'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Delivery Terms:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($purchaseOrder['delivery_terms']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($purchaseOrder['terms_conditions'])): ?>
                <div class="mt-4">
                    <span class="text-sm font-medium text-gray-500">Additional Terms:</span>
                    <p class="text-sm text-gray-900 mt-1"><?= nl2br(esc($purchaseOrder['terms_conditions'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Order Totals -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Totals</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium"><?= format_currency($purchaseOrder['subtotal']) ?></span>
                </div>
                <?php if ($purchaseOrder['tax_amount'] > 0): ?>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tax Amount:</span>
                    <span class="font-medium"><?= format_currency($purchaseOrder['tax_amount']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($purchaseOrder['freight_cost'] > 0): ?>
                <div class="flex justify-between">
                    <span class="text-gray-600">Freight Cost:</span>
                    <span class="font-medium"><?= format_currency($purchaseOrder['freight_cost']) ?></span>
                </div>
                <?php endif; ?>
                <hr class="my-2">
                <div class="flex justify-between text-lg font-bold">
                    <span>Total Amount:</span>
                    <span><?= format_currency($purchaseOrder['total_amount']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes -->
    <?php if (!empty($purchaseOrder['notes'])): ?>
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes</h2>
        <p class="text-gray-700"><?= nl2br(esc($purchaseOrder['notes'])) ?></p>
    </div>
    <?php endif; ?>

    <!-- Activity Timeline -->
    <?php if (isset($timeline) && !empty($timeline)): ?>
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Activity Timeline</h2>
        <div class="space-y-4">
            <?php foreach ($timeline as $activity): ?>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-2 h-2 mt-2 bg-indigo-500 rounded-full"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900"><?= esc($activity['description']) ?></p>
                        <p class="text-sm text-gray-500"><?= date('M j, Y g:i A', strtotime($activity['created_at'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Related Documents -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Related Documents</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Goods Receipt Notes -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Goods Receipt Notes</h3>
                <?php if (isset($goodsReceiptNotes) && !empty($goodsReceiptNotes)): ?>
                    <ul class="space-y-1">
                        <?php foreach ($goodsReceiptNotes as $grn): ?>
                            <li>
                                <a href="<?= base_url('admin/goods-receipt/' . $grn['id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    GRN #<?= esc($grn['grn_number']) ?>
                                </a>
                                <span class="text-xs text-gray-500 ml-1">(<?= date('M j', strtotime($grn['delivery_date'])) ?>)</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-sm text-gray-500">No goods receipts yet</p>
                <?php endif; ?>
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .bg-white {
        background: white !important;
    }
    
    .shadow-sm, .border {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
    }
}
</style>

<script>
function approvePO(id) {
    if (confirm('Are you sure you want to approve and send this purchase order to the supplier?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= base_url('admin/purchase-orders/') ?>${id}/approve`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= csrf_token() ?>';
        csrfInput.value = '<?= csrf_hash() ?>';
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function acknowledgePO(id) {
    if (confirm('Mark this purchase order as acknowledged by the supplier?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= base_url('admin/purchase-orders/') ?>${id}/acknowledge`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= csrf_token() ?>';
        csrfInput.value = '<?= csrf_hash() ?>';
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function cancelPO(id) {
    if (confirm('Are you sure you want to cancel this purchase order? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= base_url('admin/purchase-orders/') ?>${id}/cancel`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= csrf_token() ?>';
        csrfInput.value = '<?= csrf_hash() ?>';
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function deletePO(id) {
    if (confirm('Are you sure you want to delete this purchase order? This action cannot be undone.')) {
        const baseUrl = `<?= base_url('admin/purchase-orders/') ?>`;
        const actionUrl = `${baseUrl}${id}/delete`;
        
        console.log('Delete PO function called with ID:', id);
        console.log('Base URL:', baseUrl);
        console.log('Action URL:', actionUrl);
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = actionUrl;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= csrf_token() ?>';
        csrfInput.value = '<?= csrf_hash() ?>';
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        console.log('Submitting form to:', actionUrl);
        form.submit();
    }
}

lucide.createIcons();
</script>

<?= $this->endSection() ?>