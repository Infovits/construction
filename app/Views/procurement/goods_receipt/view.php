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
            <button onclick="printPreview()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                Print Preview
            </button>
            <button onclick="printReceipt()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                Print
            </button>
            <button id="exportPdfBtn" onclick="exportToPDF()" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                <span id="exportText">Export PDF</span>
            </button>
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

<!-- Print Preview Modal -->
<div id="printPreviewModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-auto">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold">Print Preview - GRN #<?= esc($grn['grn_number']) ?></h3>
                <div class="flex gap-2">
                    <button onclick="printFromPreview()" class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                        Print
                    </button>
                    <button onclick="closePreview()" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <div id="printContent" class="p-6">
                <!-- Print content will be inserted here -->
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .print-hidden {
        display: none !important;
    }
    
    body {
        background: white !important;
        font-size: 12px;
    }
    
    .print-only {
        display: block !important;
    }
    
    .bg-white {
        background: white !important;
    }
    
    .shadow-sm, .border {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
    }
    
    .rounded-lg {
        border-radius: 0 !important;
    }
    
    /* Print-specific styles */
    .print-header {
        background: #667eea !important;
        color: white !important;
        padding: 20px !important;
        text-align: center !important;
        margin-bottom: 20px !important;
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
    
    .print-section {
        margin-bottom: 20px !important;
        page-break-inside: avoid !important;
    }
    
    .print-table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    
    .print-table th, .print-table td {
        border: 1px solid #ddd !important;
        padding: 8px !important;
        text-align: left !important;
    }
    
    .print-table th {
        background: #74b9ff !important;
        color: white !important;
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
}

.receipt-template {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    background: white;
}

.receipt-header {
    background: #667eea;
    color: white;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
}

.receipt-title {
    font-size: 24px;
    font-weight: bold;
    margin: 0;
}

.receipt-subtitle {
    font-size: 14px;
    margin-top: 5px;
}

.receipt-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.receipt-info-box {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 8px;
}

.receipt-info-title {
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}

.receipt-info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.receipt-info-label {
    font-weight: bold;
    color: #666;
}

.receipt-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.receipt-table th,
.receipt-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

.receipt-table th {
    background: #74b9ff;
    color: white;
    font-weight: bold;
}

.receipt-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.receipt-signatures {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px;
    margin-top: 40px;
    border-top: 1px solid #ddd;
    padding-top: 20px;
}

.receipt-signature-box {
    text-align: center;
    border: 1px solid #ddd;
    padding: 30px 20px;
}

.receipt-signature-title {
    font-weight: bold;
    margin-bottom: 30px;
}

.receipt-signature-line {
    border-bottom: 1px solid #333;
    margin-bottom: 5px;
}

.receipt-footer {
    text-align: center;
    margin-top: 30px;
    font-size: 10px;
    color: #666;
}
</style>

<!-- Load html2pdf.js library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Fallback CDN if first one fails -->
<script>
if (typeof html2pdf === 'undefined') {
    document.write('<script src="https://unpkg.com/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"><\/script>');
}
</script>

<script>
// Simple print functions that work
function printPreview() {
    var content = generateSimpleReceipt();
    console.log('Generated content length:', content.length);
    console.log('Generated content preview:', content.substring(0, 300));
    
    document.getElementById('printPreviewModal').style.display = 'block';
    document.getElementById('printContent').innerHTML = content;
    
    // Add premium styles to the preview modal
    var existingStyle = document.getElementById('premium-preview-styles');
    if (!existingStyle) {
        var style = document.createElement('style');
        style.id = 'premium-preview-styles';
        style.innerHTML = getPremiumReceiptStyles();
        document.head.appendChild(style);
    }
    
    console.log('Preview modal opened, content loaded');
}

function closePreview() {
    document.getElementById('printPreviewModal').style.display = 'none';
}

function printFromPreview() {
    // Create a clone of the print content for printing
    var originalContent = document.getElementById('printContent');
    var printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write('<html><head><title>GRN Receipt</title>');
    printWindow.document.write('<meta charset="UTF-8">');
    printWindow.document.write('<style>' + getPremiumReceiptStyles() + '</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(originalContent.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    
    // Wait for styles to load before printing
    setTimeout(function() {
        printWindow.print();
    }, 500);
}

function printReceipt() {
    window.print();
}

function exportToPDF() {
    // Check if html2pdf library is loaded
    if (typeof html2pdf === 'undefined') {
        // Fallback to print dialog if library not available
        console.warn('html2pdf library not available, falling back to print');
        printFromPreview();
        return;
    }
    
    // Show loading state
    var exportBtn = document.getElementById('exportPdfBtn');
    var exportText = document.getElementById('exportText');
    var originalText = exportText.innerHTML;
    
    exportBtn.disabled = true;
    exportBtn.classList.add('opacity-50', 'cursor-not-allowed');
    exportText.innerHTML = 'Generating PDF...';
    
    // Always generate fresh content for PDF (don't rely on preview modal)
    var tempDiv = document.createElement('div');
    tempDiv.style.position = 'fixed';
    tempDiv.style.top = '0';
    tempDiv.style.left = '0';
    tempDiv.style.width = '800px';
    tempDiv.style.backgroundColor = 'white';
    tempDiv.style.padding = '20px';
    tempDiv.style.zIndex = '9999';
    tempDiv.style.visibility = 'visible';
    tempDiv.style.opacity = '1';
    
    // Generate fresh receipt content
    var receiptContent = generateSimpleReceipt();
    console.log('Fresh PDF Content length:', receiptContent.length);
    console.log('Fresh PDF Content preview:', receiptContent.substring(0, 300));
    
    tempDiv.innerHTML = receiptContent;
    document.body.appendChild(tempDiv);
    
    // Add the premium styles if not already present
    var existingStyle = document.getElementById('premium-preview-styles');
    if (!existingStyle) {
        var style = document.createElement('style');
        style.id = 'premium-preview-styles';
        style.innerHTML = getPremiumReceiptStyles();
        document.head.appendChild(style);
    }
    
    // Configure PDF options
    var opt = {
        margin: 10,
        filename: 'GRN-<?= esc($grn['grn_number']) ?>-<?= date('Y-m-d') ?>.pdf',
        image: { 
            type: 'jpeg', 
            quality: 0.98
        },
        html2canvas: { 
            scale: 1,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: true
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait'
        }
    };
    
    // Wait for styles to apply, then generate PDF
    setTimeout(function() {
        // Hide the temp div from user view while keeping it renderable
        tempDiv.style.zIndex = '-9999';
        tempDiv.style.pointerEvents = 'none';
        
        console.log('Generating PDF from element:', tempDiv);
        console.log('Element visible:', tempDiv.offsetHeight > 0);
        
        html2pdf().set(opt).from(tempDiv).save().then(function() {
            // Clean up temporary element
            if (document.body.contains(tempDiv)) {
                document.body.removeChild(tempDiv);
            }
            // Reset loading state
            exportBtn.disabled = false;
            exportBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            exportText.innerHTML = originalText;
            console.log('PDF generated successfully');
        }).catch(function(error) {
            console.error('PDF generation failed:', error);
            // Clean up temporary element
            if (document.body.contains(tempDiv)) {
                document.body.removeChild(tempDiv);
            }
            // Reset loading state on error
            exportBtn.disabled = false;
            exportBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            exportText.innerHTML = originalText;
            alert('Failed to generate PDF: ' + error.message);
        });
    }, 1500);
}

function generateSimpleReceipt() {
    var html = '<div class="invoice-container">';
    
    // Header section matching your invoice design
    html += '<div class="header">';
    html += '<div class="logo">';
    html += '<div class="logo-icon">C</div>';
    html += '<span class="logo-text">Construction</span>';
    html += '<span class="logo-subtext">Management</span>';
    html += '</div>';
    html += '<h1 class="invoice-title">Goods Receipt Note</h1>';
    html += '<div class="invoice-details">';
    html += '<p>GRN No: <?= esc($grn['grn_number']) ?></p>';
    html += '<p>Receipt Date: <?= date('d M, Y', strtotime($grn['delivery_date'])) ?></p>';
    html += '<p>Status: <?= ucfirst(str_replace('_', ' ', $grn['status'])) ?></p>';
    html += '</div>';
    html += '</div>';
    
    // Billing section (Receipt To / Receipt From)
    html += '<div class="billing-section">';
    html += '<div class="billing-info">';
    html += '<h3>Received From:</h3>';
    html += '<p class="name"><?= esc($grn['supplier_name']) ?></p>';
    <?php if (!empty($grn['delivery_note_number'])): ?>
    html += '<p>Delivery Note: <?= esc($grn['delivery_note_number']) ?></p>';
    <?php endif; ?>
    <?php if (!empty($grn['vehicle_number'])): ?>
    html += '<p>Vehicle: <?= esc($grn['vehicle_number']) ?></p>';
    <?php endif; ?>
    <?php if (!empty($grn['driver_name'])): ?>
    html += '<p>Driver: <?= esc($grn['driver_name']) ?></p>';
    <?php endif; ?>
    html += '</div>';
    html += '<div class="billing-info">';
    html += '<h3>Received At:</h3>';
    html += '<p class="name"><?= esc($grn['warehouse_name']) ?></p>';
    html += '<p>Purchase Order: PO #<?= esc($grn['po_number']) ?></p>';
    html += '<p>Received By: <?= esc($grn['received_by_name']) ?></p>';
    html += '<p>Date: <?= date('d M, Y', strtotime($grn['delivery_date'])) ?></p>';
    html += '</div>';
    html += '</div>';
    
    // Items table matching your invoice design
    html += '<table class="items-table">';
    html += '<thead>';
    html += '<tr>';
    html += '<th>No.</th>';
    html += '<th>Material Description</th>';
    html += '<th>Ordered</th>';
    html += '<th>Delivered</th>';
    html += '<th>Accepted</th>';
    html += '<th>Quality</th>';
    html += '<th>Unit Cost</th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';
    
    <?php if (isset($grnItems) && !empty($grnItems)): ?>
        <?php $counter = 1; ?>
        <?php foreach ($grnItems as $item): ?>
            html += '<tr>';
            html += '<td><?= $counter ?></td>';
            html += '<td>';
            html += '<strong><?= esc($item['material_name']) ?></strong><br>';
            html += '<small style="color: #666;">Code: <?= esc($item['material_code']) ?></small>';
            html += '</td>';
            html += '<td><?= number_format($item['quantity_ordered'], 2) ?> <?= esc($item['material_unit']) ?></td>';
            html += '<td><?= number_format($item['quantity_delivered'], 2) ?> <?= esc($item['material_unit']) ?></td>';
            html += '<td><?= number_format($item['quantity_accepted'], 2) ?> <?= esc($item['material_unit']) ?></td>';
            html += '<td>';
            html += '<span style="';
            html += 'padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase;';
            <?php if ($item['quality_status'] === 'passed'): ?>
            html += 'background: #d1fae5; color: #065f46;';
            <?php elseif ($item['quality_status'] === 'failed'): ?>
            html += 'background: #fee2e2; color: #991b1b;';
            <?php elseif ($item['quality_status'] === 'pending'): ?>
            html += 'background: #fef3c7; color: #92400e;';
            <?php else: ?>
            html += 'background: #fed7aa; color: #9a3412;';
            <?php endif; ?>
            html += '"><?= ucfirst($item['quality_status']) ?></span>';
            html += '</td>';
            html += '<td><?= format_currency($item['unit_cost'] ?? 0) ?></td>';
            html += '</tr>';
            <?php $counter++; ?>
        <?php endforeach; ?>
    <?php else: ?>
        html += '<tr><td colspan="7" style="text-align: center; color: #666; font-style: italic;">No items found</td></tr>';
    <?php endif; ?>
    
    html += '</tbody>';
    html += '</table>';
    
    // Totals section (if total value is available)
    <?php if (!empty($grn['total_received_value'])): ?>
    html += '<div class="totals-section">';
    html += '<div class="total-row grand-total">';
    html += '<span class="total-label">Total Received Value:</span>';
    html += '<span class="total-amount"><?= format_currency($grn['total_received_value']) ?></span>';
    html += '</div>';
    html += '</div>';
    <?php endif; ?>
    
    // Footer section with signatures (matching your footer style)
    html += '<div class="footer-section">';
    html += '<div class="signature-card">';
    html += '<div class="signature-title">RECEIVER</div>';
    html += '<div class="signature-area"></div>';
    html += '<div class="signature-name"><?= esc($grn['received_by_name']) ?></div>';
    html += '<div class="signature-label">Name & Signature</div>';
    html += '</div>';
    
    html += '<div class="signature-card">';
    html += '<div class="signature-title">QUALITY INSPECTOR</div>';
    html += '<div class="signature-area"></div>';
    html += '<div class="signature-name">_________________</div>';
    html += '<div class="signature-label">Name & Signature</div>';
    html += '</div>';
    
    html += '<div class="signature-card">';
    html += '<div class="signature-title">WAREHOUSE MANAGER</div>';
    html += '<div class="signature-area"></div>';
    html += '<div class="signature-name">_________________</div>';
    html += '<div class="signature-label">Name & Signature</div>';
    html += '</div>';
    html += '</div>';
    
    // Premium Footer
    html += '<div class="copyright">';
    html += '<p>© <?= date('Y') ?> Construction Management System. Generated on <?= date('F j, Y \a\t g:i A') ?></p>';
    html += '</div>';
    
    html += '</div>';
    
    return html;
}

function getPremiumReceiptStyles() {
    return `
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .logo-icon {
            background: #007bff;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .logo-subtext {
            font-size: 12px;
            color: #666;
            margin-left: 4px;
        }

        .invoice-title {
            font-size: 36px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .invoice-details {
            color: #666;
            font-size: 14px;
        }

        .billing-section {
            display: flex;
            justify-content: space-between;
            margin: 40px 0;
            gap: 40px;
        }

        .billing-info {
            flex: 1;
        }

        .billing-info h3 {
            color: #007bff;
            font-size: 14px;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .billing-info p {
            margin-bottom: 5px;
            color: #666;
            font-size: 14px;
        }

        .billing-info .name {
            color: #333;
            font-weight: 600;
            font-size: 16px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .items-table th {
            background: #e3f2fd;
            color: #333;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }

        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .items-table tr:hover {
            background: #f8f9fa;
        }

        .totals-section {
            margin-top: 20px;
            text-align: right;
        }

        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .total-label {
            width: 120px;
            text-align: right;
            margin-right: 20px;
            color: #666;
        }

        .total-amount {
            width: 100px;
            text-align: right;
            color: #333;
        }

        .grand-total {
            border-top: 2px solid #007bff;
            padding-top: 10px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 16px;
        }

        .grand-total .total-label,
        .grand-total .total-amount {
            color: #007bff;
            font-weight: bold;
        }

        .footer-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            gap: 40px;
        }

        .contact-info,
        .payment-info {
            flex: 1;
        }

        .footer-section h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .footer-section p {
            margin-bottom: 5px;
            color: #666;
            font-size: 13px;
        }

        .footer-section .icon {
            color: #007bff;
            margin-right: 8px;
        }

        .notice {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            border: 2px solid #007bff;
            border-radius: 4px;
            color: #666;
            font-size: 12px;
        }

        .copyright {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 11px;
        }

        .copyright a {
            color: #007bff;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .invoice-container {
                padding: 20px;
            }
            
            .billing-section,
            .footer-section {
                flex-direction: column;
                gap: 20px;
            }
            
            .items-table {
                font-size: 12px;
            }
            
            .items-table th,
            .items-table td {
                padding: 10px 5px;
            }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background: #ffffff;
        }
        
        .premium-receipt {
            max-width: 210mm;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        }
        
        /* Premium Header */
        .premium-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .premium-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50px, -50px);
        }
        
        .company-branding {
            display: flex;
            align-items: center;
            z-index: 1;
        }
        
        .company-logo {
            font-size: 48px;
            margin-right: 20px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }
        
        .company-name {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .company-tagline {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .document-info {
            text-align: right;
            z-index: 1;
        }
        
        .document-title {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .document-number {
            font-size: 18px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 10px;
            backdrop-filter: blur(10px);
        }
        
        .document-status {
            font-size: 14px;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 15px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending-inspection { background: rgba(251, 191, 36, 0.3); }
        .status-accepted { background: rgba(34, 197, 94, 0.3); }
        .status-rejected { background: rgba(239, 68, 68, 0.3); }
        
        /* Premium Info Section */
        .premium-info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 40px;
            background: #fafbfc;
        }
        
        .info-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
        }
        
        .primary-card { border-left-color: #667eea; }
        .secondary-card { border-left-color: #764ba2; }
        
        .card-header {
            background: linear-gradient(90deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 16px 20px;
            font-weight: 600;
            font-size: 14px;
            color: #475569;
            letter-spacing: 0.5px;
        }
        
        .card-content {
            padding: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .label {
            font-weight: 500;
            color: #64748b;
            font-size: 14px;
        }
        
        .value {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }
        
        .value.highlight {
            color: #667eea;
            font-weight: 700;
        }
        
        .total-value {
            background: linear-gradient(90deg, #f0f9ff 0%, #e0f2fe 100%);
            margin: 15px -20px -20px -20px;
            padding: 20px;
            border-top: 2px solid #0ea5e9;
        }
        
        .total-value .value.amount {
            font-size: 18px;
            font-weight: 700;
            color: #0369a1;
        }
        
        /* Premium Table Section */
        .premium-table-section {
            padding: 40px;
        }
        
        .section-header {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
            position: relative;
        }
        
        .section-header::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 60px;
            height: 3px;
            background: #764ba2;
        }
        
        .premium-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .premium-table thead tr {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .premium-table th {
            padding: 18px 16px;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }
        
        .premium-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background-color 0.2s ease;
        }
        
        .premium-table tbody tr:hover {
            background: #f8fafc;
        }
        
        .premium-table tbody tr:nth-child(even) {
            background: #fafbfc;
        }
        
        .premium-table td {
            padding: 16px;
            font-size: 14px;
        }
        
        .material-cell .material-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }
        
        .material-cell .material-code {
            font-size: 12px;
            color: #64748b;
            font-family: 'Monaco', 'Consolas', monospace;
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }
        
        .quantity-cell {
            font-weight: 600;
            color: #1e293b;
        }
        
        .quantity-cell .unit {
            font-size: 12px;
            color: #64748b;
            font-weight: 400;
        }
        
        .cost-cell {
            font-weight: 700;
            color: #059669;
            font-size: 15px;
        }
        
        .quality-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .quality-pending { background: #fef3c7; color: #92400e; }
        .quality-passed { background: #d1fae5; color: #065f46; }
        .quality-failed { background: #fee2e2; color: #991b1b; }
        .quality-conditional { background: #fed7aa; color: #9a3412; }
        
        .no-data {
            text-align: center;
            color: #64748b;
            font-style: italic;
            padding: 40px !important;
        }
        
        /* Premium Notes Section */
        .premium-notes-section {
            padding: 0 40px 40px 40px;
        }
        
        .notes-content {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            font-size: 14px;
            line-height: 1.7;
            color: #475569;
            border-left: 4px solid #667eea;
        }
        
        /* Premium Signatures */
        .premium-signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            padding: 40px;
            background: #fafbfc;
            border-top: 1px solid #e2e8f0;
        }
        
        .signature-card {
            background: white;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 2px solid #f1f5f9;
        }
        
        .signature-title {
            font-weight: 700;
            color: #1e293b;
            font-size: 12px;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }
        
        .signature-area {
            height: 60px;
            border-bottom: 2px solid #667eea;
            margin: 20px 0;
            position: relative;
        }
        
        .signature-name {
            font-weight: 600;
            color: #475569;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .signature-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Premium Footer */
        .premium-footer {
            padding: 30px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .footer-line {
            width: 100px;
            height: 2px;
            background: rgba(255, 255, 255, 0.5);
            margin: 0 auto 15px auto;
        }
        
        .footer-text {
            font-size: 12px;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .footer-disclaimer {
            font-size: 10px;
            opacity: 0.8;
            font-style: italic;
        }
        
        /* Print Optimizations */
        @media print {
            .premium-receipt {
                box-shadow: none;
                max-width: 100%;
            }
            
            .premium-header::before {
                display: none;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
    `;
}

// Hide modal when clicking outside
document.addEventListener('click', function(e) {
    var modal = document.getElementById('printPreviewModal');
    if (e.target === modal) {
        closePreview();
    }
});
</script>

<?= $this->endSection() ?>