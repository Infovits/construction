<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= $reportTitle ?></h1>
            <p class="text-gray-600">Generated on <?= date('F d, Y') ?></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('admin/procurement/reports') ?>" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Reports
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                Print Report
            </button>
            <a href="<?= base_url('admin/procurement/reports/export/excel/' . urlencode($reportTitle)) ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i>
                Export to Excel
            </a>
            <a href="<?= base_url('admin/procurement/reports/export/pdf/' . urlencode($reportTitle)) ?>" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                Export to PDF
            </a>
        </div>
    </div>

    <!-- Report Content -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <?php if (isset($reportData) && !empty($reportData)): ?>
            <?php if ($reportTitle === 'Procurement Summary Report'): ?>
                <!-- Summary Report -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Material Requests Summary -->
                    <div class="bg-blue-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-blue-700 mb-4">Material Requests</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-semibold"><?= $reportData['material_requests']['total'] ?? 0 ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Pending:</span>
                                <span class="font-semibold"><?= $reportData['material_requests']['pending'] ?? 0 ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Approved:</span>
                                <span class="font-semibold"><?= $reportData['material_requests']['approved'] ?? 0 ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Rejected:</span>
                                <span class="font-semibold"><?= $reportData['material_requests']['rejected'] ?? 0 ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Orders Summary -->
                    <div class="bg-green-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-green-700 mb-4">Purchase Orders</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-semibold"><?= $reportData['purchase_orders']['total'] ?? 0 ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Pending:</span>
                                <span class="font-semibold"><?= $reportData['purchase_orders']['pending'] ?? 0 ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Completed:</span>
                                <span class="font-semibold"><?= $reportData['purchase_orders']['completed'] ?? 0 ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Value:</span>
                                <span class="font-semibold">K<?= number_format($reportData['purchase_orders']['total_value'] ?? 0, 2) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Goods Receipt Summary -->
                    <div class="bg-yellow-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-yellow-700 mb-4">Goods Receipt</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-semibold"><?= $reportData['goods_receipt']['total'] ?? 0 ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Completed:</span>
                                <span class="font-semibold"><?= $reportData['goods_receipt']['completed'] ?? 0 ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Pending:</span>
                                <span class="font-semibold"><?= $reportData['goods_receipt']['pending'] ?? 0 ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Items:</span>
                                <span class="font-semibold"><?= $reportData['goods_receipt']['total_items'] ?? 0 ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quality Inspections Summary -->
                    <div class="bg-purple-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-purple-700 mb-4">Quality Inspections</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-semibold"><?= $reportData['quality_inspections']['total'] ?? 0 ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">passed:</span>
                                <span class="font-semibold"><?= $reportData['quality_inspections']['passed'] ?? 0 ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">failed:</span>
                                <span class="font-semibold"><?= $reportData['quality_inspections']['failed'] ?? 0 ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Pending:</span>
                                <span class="font-semibold"><?= $reportData['quality_inspections']['pending'] ?? 0 ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Detailed Report Tables -->
                <div class="overflow-x-auto">
                    <table id="reportTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <?php if ($reportTitle === 'Material Requests Report'): ?>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested By</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                <?php elseif ($reportTitle === 'Purchase Orders Report'): ?>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO #</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                <?php elseif ($reportTitle === 'Goods Receipt Report'): ?>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GRN #</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Reference</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received By</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <?php elseif ($reportTitle === 'Quality Inspections Report'): ?>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inspection #</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GRN Reference</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inspector</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items passed</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items failed</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($reportData as $item): ?>
                                <tr>
                                    <?php if ($reportTitle === 'Material Requests Report'): ?>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['id'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('Y-m-d', strtotime($item['request_date'])) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['project_name'] ?? 'N/A' ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= trim(($item['requester_first_name'] ?? '') . ' ' . ($item['requester_last_name'] ?? '')) ?: 'N/A' ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                <?= $item['status'] === 'approved' ? 'bg-green-100 text-green-800' :
                                                   ($item['status'] === 'pending_approval' ? 'bg-yellow-100 text-yellow-800' :
                                                   'bg-red-100 text-red-800') ?>">
                                                <?= $item['status'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['item_count'] ?? 0 ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['priority'] ?></td>
                                    <?php elseif ($reportTitle === 'Purchase Orders Report'): ?>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['po_number'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('Y-m-d', strtotime($item['po_date'])) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['supplier_name'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['project_name'] ?? 'N/A' ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                <?= $item['status'] === 'completed' ? 'bg-green-100 text-green-800' :
                                                   ($item['status'] === 'pending_approval' ? 'bg-yellow-100 text-yellow-800' :
                                                   'bg-blue-100 text-blue-800') ?>">
                                                <?= $item['status'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">K<?= number_format($item['total_amount'], 2) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['item_count'] ?? 0 ?></td>
                                    <?php elseif ($reportTitle === 'Goods Receipt Report'): ?>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['grn_number'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('Y-m-d', strtotime($item['delivery_date'])) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['po_number'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['supplier_name'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['received_by_name'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['item_count'] ?? 0 ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                <?= $item['status'] === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                                <?= $item['status'] ?>
                                            </span>
                                        </td>
                                    <?php elseif ($reportTitle === 'Quality Inspections Report'): ?>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['inspection_number'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('Y-m-d', strtotime($item['inspection_date'])) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['grn_number'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['inspector_name'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                <?= $item['status'] === 'passed' ? 'bg-green-100 text-green-800' :
                                                   ($item['status'] === 'failed' ? 'bg-red-100 text-red-800' :
                                                   'bg-yellow-100 text-yellow-800') ?>">
                                                <?= $item['status'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['items_passed'] ?? 0 ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $item['items_failed'] ?? 0 ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-8">
                <p class="text-gray-500">No data available for the selected report criteria.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Function to export table to Excel
function exportTableToExcel(tableID, filename = '') {
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

    // Specify file name
    filename = filename ? filename + '.xls' : 'report.xls';

    // Create download link element
    downloadLink = document.createElement("a");

    document.body.appendChild(downloadLink);

    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        // Create a link to the file
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

        // Setting the file name
        downloadLink.download = filename;

        //triggering the function
        downloadLink.click();
    }
}

// Initialize Lucide icons
lucide.createIcons();
</script>

<?= $this->endSection() ?>
