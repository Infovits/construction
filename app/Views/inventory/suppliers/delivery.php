<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Delivery Details - <?= esc($delivery['reference_number']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Delivery Details</h1>
                <p class="text-gray-600">Reference #: <?= esc($delivery['reference_number']) ?></p>
            </div>
            <div class="flex gap-2">
                <a href="<?= base_url('admin/suppliers/view/' . $delivery['supplier_id']) ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Supplier
                </a>
                <a href="#" onclick="printDelivery()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Print
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Delivery Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-200">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Delivery Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-6">
                                <h4 class="text-xs uppercase tracking-wider font-semibold text-gray-500 mb-2">Delivery Details</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Reference Number:</span>
                                        <span class="text-sm font-medium text-gray-900"><?= esc($delivery['reference_number']) ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Delivery Date:</span>
                                        <span class="text-sm font-medium text-gray-900"><?= date('F j, Y', strtotime($delivery['delivery_date'])) ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Status:</span>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                                switch ($delivery['status']) {
                                                    case 'received': echo 'bg-green-100 text-green-800'; break;
                                                    case 'partial': echo 'bg-yellow-100 text-yellow-800'; break;
                                                    case 'pending': echo 'bg-blue-100 text-blue-800'; break;
                                                    case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                                    default: echo 'bg-gray-100 text-gray-800';
                                                }
                                            ?>">
                                            <?= ucfirst($delivery['status']) ?>
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Received By:</span>
                                        <span class="text-sm font-medium text-gray-900"><?= esc($delivery['received_by_name']) ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Receiving Warehouse:</span>
                                        <span class="text-sm font-medium text-gray-900"><?= esc($delivery['warehouse_name']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-xs uppercase tracking-wider font-semibold text-gray-500 mb-2">Material Information</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Material:</span>
                                        <span class="text-sm font-medium text-gray-900"><?= esc($delivery['material_name']) ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">SKU:</span>
                                        <span class="text-sm font-medium text-gray-900"><?= esc($delivery['material_sku']) ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Quantity:</span>
                                        <span class="text-sm font-medium text-gray-900"><?= number_format($delivery['quantity'], 2) ?> <?= esc($delivery['unit_of_measure']) ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Unit Price:</span>
                                        <span class="text-sm font-medium text-gray-900">$<?= number_format($delivery['unit_price'], 2) ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Total Amount:</span>
                                        <span class="text-sm font-medium text-gray-900">$<?= number_format($delivery['total_amount'], 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-6">
                                <h4 class="text-xs uppercase tracking-wider font-semibold text-gray-500 mb-2">Supplier Information</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Supplier Name:</span>
                                        <a href="<?= base_url('admin/suppliers/view/' . $delivery['supplier_id']) ?>" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                            <?= esc($delivery['supplier_name']) ?>
                                        </a>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Contact Person:</span>
                                        <span class="text-sm font-medium text-gray-900"><?= esc($delivery['contact_person']) ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Phone:</span>
                                        <span class="text-sm font-medium text-gray-900"><?= esc($delivery['phone']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($delivery['notes'])): ?>
                            <div>
                                <h4 class="text-xs uppercase tracking-wider font-semibold text-gray-500 mb-2">Notes</h4>
                                <p class="text-sm text-gray-700 p-3 bg-gray-50 rounded-lg"><?= nl2br(esc($delivery['notes'])) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mt-6">
                                <h4 class="text-xs uppercase tracking-wider font-semibold text-gray-500 mb-2">Timestamps</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Created:</span>
                                        <span class="text-sm font-medium text-gray-900"><?= date('M j, Y g:i A', strtotime($delivery['created_at'])) ?></span>
                                    </div>
                                    <?php if (!empty($delivery['updated_at']) && $delivery['updated_at'] != $delivery['created_at']): ?>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Last Updated:</span>
                                        <span class="text-sm font-medium text-gray-900"><?= date('M j, Y g:i A', strtotime($delivery['updated_at'])) ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-200">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
                </div>
                <div class="p-6 space-y-4">
                    <?php if ($delivery['status'] !== 'received'): ?>
                    <a href="<?= base_url('admin/suppliers/update-delivery-status/' . $delivery['id'] . '/received') ?>" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> Mark as Received
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($delivery['status'] !== 'cancelled'): ?>
                    <a href="<?= base_url('admin/suppliers/update-delivery-status/' . $delivery['id'] . '/cancelled') ?>" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Cancel Delivery
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('admin/materials/view/' . $delivery['material_id']) ?>" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="package" class="w-4 h-4 mr-2"></i> View Material Details
                    </a>
                    
                    <a href="<?= base_url('admin/warehouses/view/' . $delivery['warehouse_id']) ?>" class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i data-lucide="home" class="w-4 h-4 mr-2"></i> View Warehouse
                    </a>
                    
                    <!-- Generate Barcode Labels Button -->
                    <a href="<?= base_url('admin/materials/generate-labels/' . $delivery['material_id'] . '/' . $delivery['quantity']) ?>" target="_blank" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i data-lucide="tag" class="w-4 h-4 mr-2"></i> Generate Labels
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
});

function printDelivery() {
    window.print();
}
</script>

<!-- Print Styles (Only applied when printing) -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    .container, .container * {
        visibility: visible;
    }
    
    .container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    a[href]:after {
        content: none !important;
    }
    
    /* Hide action panel when printing */
    .lg\\:col-span-1 {
        display: none;
    }
    
    /* Make the delivery info full width */
    .lg\\:col-span-2 {
        grid-column: span 3 / span 3;
    }
}
</style>
<?= $this->endSection() ?>
