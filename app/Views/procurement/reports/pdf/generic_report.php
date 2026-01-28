<?php
/**
 * Generic PDF Template for Procurement Reports
 * This template is used when a specific template doesn't exist for a report type
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?> - <?= date('Y-m-d') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 12px;
        }
        
        .report-info {
            margin-bottom: 20px;
            font-size: 12px;
        }
        
        .report-info p {
            margin: 2px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .summary-table {
            margin-top: 30px;
            border: 2px solid #333;
        }
        
        .summary-table th {
            background-color: #333;
            color: white;
            font-size: 12px;
        }
        
        .summary-table td {
            font-weight: bold;
            text-align: right;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= esc($title) ?></h1>
        <p>Generated on <?= esc($generated_date) ?></p>
        <p>Company: <?= esc($company_name ?? 'Construction Management System') ?></p>
    </div>

    <?php if (isset($reportData) && !empty($reportData)): ?>
        <?php if ($title === 'Procurement Summary Report'): ?>
            <!-- Summary Report Layout -->
            <div class="report-info">
                <p><strong>Report Type:</strong> Summary Report</p>
                <p><strong>Generated:</strong> <?= esc($generated_date) ?></p>
            </div>

            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Pending</th>
                        <th>Completed/Approved</th>
                        <th>Rejected/Failed</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($reportData['material_requests'])): ?>
                        <tr>
                            <td>Material Requests</td>
                            <td><?= $reportData['material_requests']['total'] ?? 0 ?></td>
                            <td><?= $reportData['material_requests']['pending'] ?? 0 ?></td>
                            <td><?= $reportData['material_requests']['approved'] ?? 0 ?></td>
                            <td><?= $reportData['material_requests']['rejected'] ?? 0 ?></td>
                            <td>-</td>
                        </tr>
                    <?php endif; ?>

                    <?php if (isset($reportData['purchase_orders'])): ?>
                        <tr>
                            <td>Purchase Orders</td>
                            <td><?= $reportData['purchase_orders']['total'] ?? 0 ?></td>
                            <td><?= $reportData['purchase_orders']['pending'] ?? 0 ?></td>
                            <td><?= $reportData['purchase_orders']['completed'] ?? 0 ?></td>
                            <td>-</td>
                            <td><?= number_format($reportData['purchase_orders']['total_value'] ?? 0, 2) ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (isset($reportData['goods_receipt'])): ?>
                        <tr>
                            <td>Goods Receipt</td>
                            <td><?= $reportData['goods_receipt']['total'] ?? 0 ?></td>
                            <td><?= $reportData['goods_receipt']['pending'] ?? 0 ?></td>
                            <td><?= $reportData['goods_receipt']['completed'] ?? 0 ?></td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    <?php endif; ?>

                    <?php if (isset($reportData['quality_inspections'])): ?>
                        <tr>
                            <td>Quality Inspections</td>
                            <td><?= $reportData['quality_inspections']['total'] ?? 0 ?></td>
                            <td><?= $reportData['quality_inspections']['pending'] ?? 0 ?></td>
                            <td><?= $reportData['quality_inspections']['passed'] ?? 0 ?></td>
                            <td><?= $reportData['quality_inspections']['failed'] ?? 0 ?></td>
                            <td>-</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php else: ?>
            <!-- Detailed Report Layout -->
            <div class="report-info">
                <p><strong>Report Type:</strong> <?= esc($title) ?></p>
                <p><strong>Generated:</strong> <?= esc($generated_date) ?></p>
                <?php if (isset($filters)): ?>
                    <?php if (!empty($filters['date_from'])): ?>
                        <p><strong>Date Range:</strong> <?= esc($filters['date_from']) ?> to <?= esc($filters['date_to'] ?? 'Present') ?></p>
                    <?php endif; ?>
                    <?php if (!empty($filters['supplier_id'])): ?>
                        <p><strong>Supplier:</strong> <?= esc($filters['supplier_id']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($filters['project_id'])): ?>
                        <p><strong>Project:</strong> <?= esc($filters['project_id']) ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <table>
                <thead>
                    <?php if ($title === 'Material Requests Report'): ?>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Project</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Priority</th>
                        </tr>
                    <?php elseif ($title === 'Purchase Orders Report'): ?>
                        <tr>
                            <th>PO #</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Total Value</th>
                            <th>Items</th>
                        </tr>
                    <?php elseif ($title === 'Goods Receipt Report'): ?>
                        <tr>
                            <th>GRN #</th>
                            <th>Date</th>
                            <th>PO Reference</th>
                            <th>Supplier</th>
                            <th>Received By</th>
                            <th>Items</th>
                            <th>Status</th>
                        </tr>
                    <?php elseif ($title === 'Quality Inspections Report'): ?>
                        <tr>
                            <th>Inspection #</th>
                            <th>Date</th>
                            <th>GRN Reference</th>
                            <th>Inspector</th>
                            <th>Status</th>
                            <th>Items Passed</th>
                            <th>Items Failed</th>
                        </tr>
                    <?php endif; ?>
                </thead>
                <tbody>
                    <?php foreach ($reportData as $item): ?>
                        <tr>
                            <?php if ($title === 'Material Requests Report'): ?>
                                <td><?= $item['id'] ?></td>
                                <td><?= date('Y-m-d', strtotime($item['request_date'])) ?></td>
                                <td><?= $item['project_name'] ?? 'N/A' ?></td>
                                <td><?= trim(($item['requester_first_name'] ?? '') . ' ' . ($item['requester_last_name'] ?? '')) ?: 'N/A' ?></td>
                                <td><?= $item['status'] ?></td>
                                <td><?= $item['item_count'] ?? 0 ?></td>
                                <td><?= $item['priority'] ?></td>
                            <?php elseif ($title === 'Purchase Orders Report'): ?>
                                <td><?= $item['po_number'] ?></td>
                                <td><?= date('Y-m-d', strtotime($item['po_date'])) ?></td>
                                <td><?= $item['supplier_name'] ?></td>
                                <td><?= $item['project_name'] ?? 'N/A' ?></td>
                                <td><?= $item['status'] ?></td>
                                <td><?= number_format($item['total_amount'], 2) ?></td>
                                <td><?= $item['item_count'] ?? 0 ?></td>
                            <?php elseif ($title === 'Goods Receipt Report'): ?>
                                <td><?= $item['grn_number'] ?></td>
                                <td><?= date('Y-m-d', strtotime($item['delivery_date'])) ?></td>
                                <td><?= $item['po_number'] ?></td>
                                <td><?= $item['supplier_name'] ?></td>
                                <td><?= $item['received_by_name'] ?></td>
                                <td><?= $item['item_count'] ?? 0 ?></td>
                                <td><?= $item['status'] ?></td>
                            <?php elseif ($title === 'Quality Inspections Report'): ?>
                                <td><?= $item['inspection_number'] ?></td>
                                <td><?= date('Y-m-d', strtotime($item['inspection_date'])) ?></td>
                                <td><?= $item['grn_number'] ?></td>
                                <td><?= $item['inspector_name'] ?></td>
                                <td><?= $item['status'] ?></td>
                                <td><?= $item['items_passed'] ?? 0 ?></td>
                                <td><?= $item['items_failed'] ?? 0 ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php else: ?>
        <div style="text-align: center; margin-top: 50px; color: #666;">
            <h3>No Data Available</h3>
            <p>No records found for the selected criteria.</p>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>Report generated by Construction Management System</p>
        <p>Page 1</p>
    </div>
</body>
</html>