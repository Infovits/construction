<?php
/**
 * PDF Template for Goods Receipt Report
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
            color: #2c3e50;
        }
        
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 12px;
        }
        
        .report-info {
            margin-bottom: 20px;
            font-size: 12px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
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
            background-color: #8e44ad;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f5eef8;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-completed {
            background-color: #27ae60;
            color: white;
        }
        
        .status-pending {
            background-color: #f39c12;
            color: white;
        }
        
        .summary-stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 15px;
            background-color: #ecf0f1;
            border-radius: 4px;
        }
        
        .stat-box {
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .company-logo {
            float: right;
            width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Company Logo" class="company-logo">
        <h1><?= esc($title) ?></h1>
        <p>Generated on <?= esc($generated_date) ?></p>
        <p>Construction Management System</p>
    </div>

    <?php if (isset($reportData) && !empty($reportData)): ?>
        <div class="report-info">
            <p><strong>Report Type:</strong> Goods Receipt Report</p>
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

        <!-- Summary Statistics -->
        <div class="summary-stats">
            <div class="stat-box">
                <div class="stat-number"><?= count($reportData) ?></div>
                <div class="stat-label">Total GRNs</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= count(array_filter($reportData, function($item) { return $item['status'] === 'completed'; })) ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= count(array_filter($reportData, function($item) { return $item['status'] === 'pending'; })) ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= array_sum(array_column($reportData, 'item_count')) ?></div>
                <div class="stat-label">Total Items</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>GRN #</th>
                    <th>Date</th>
                    <th>PO Reference</th>
                    <th>Supplier</th>
                    <th>Received By</th>
                    <th>Items</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData as $item): ?>
                    <tr>
                        <td><?= $item['grn_number'] ?></td>
                        <td><?= date('Y-m-d', strtotime($item['delivery_date'])) ?></td>
                        <td><?= $item['po_number'] ?></td>
                        <td><?= $item['supplier_name'] ?></td>
                        <td><?= $item['received_by_name'] ?></td>
                        <td><?= $item['item_count'] ?? 0 ?></td>
                        <td>
                            <span class="status-badge 
                                <?= $item['status'] === 'completed' ? 'status-completed' :
                                   'status-pending' ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="footer">
            <p>Report generated by Construction Management System</p>
            <p>Page 1</p>
        </div>
    <?php else: ?>
        <div style="text-align: center; margin-top: 50px; color: #666;">
            <h3>No Data Available</h3>
            <p>No goods receipt notes found for the selected criteria.</p>
        </div>
    <?php endif; ?>
</body>
</html>