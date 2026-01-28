<?php
/**
 * PDF Template for Quality Inspections Report
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
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #ffebee;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-passed {
            background-color: #27ae60;
            color: white;
        }
        
        .status-failed {
            background-color: #e74c3c;
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
        
        .quality-indicators {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            padding: 15px;
            background-color: #fff3e0;
            border-radius: 4px;
            border-left: 4px solid #f39c12;
        }
        
        .indicator-box {
            text-align: center;
        }
        
        .indicator-number {
            font-size: 20px;
            font-weight: bold;
            color: #e74c3c;
        }
        
        .indicator-label {
            font-size: 11px;
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
            <p><strong>Report Type:</strong> Quality Inspections Report</p>
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
                <div class="stat-label">Total Inspections</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= count(array_filter($reportData, function($item) { return $item['status'] === 'passed'; })) ?></div>
                <div class="stat-label">Passed</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= count(array_filter($reportData, function($item) { return $item['status'] === 'failed'; })) ?></div>
                <div class="stat-label">Failed</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= count(array_filter($reportData, function($item) { return $item['status'] === 'pending'; })) ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        <!-- Quality Indicators -->
        <div class="quality-indicators">
            <div class="indicator-box">
                <div class="indicator-number"><?= array_sum(array_column($reportData, 'items_passed')) ?></div>
                <div class="indicator-label">Items Passed</div>
            </div>
            <div class="indicator-box">
                <div class="indicator-number"><?= array_sum(array_column($reportData, 'items_failed')) ?></div>
                <div class="indicator-label">Items Failed</div>
            </div>
            <div class="indicator-box">
                <div class="indicator-number"><?= round((array_sum(array_column($reportData, 'items_passed')) / max(array_sum(array_column($reportData, 'items_passed')) + array_sum(array_column($reportData, 'items_failed')), 1)) * 100, 2) ?>%</div>
                <div class="indicator-label">Pass Rate</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Inspection #</th>
                    <th>Date</th>
                    <th>GRN Reference</th>
                    <th>Inspector</th>
                    <th>Status</th>
                    <th>Items Passed</th>
                    <th>Items Failed</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData as $item): ?>
                    <tr>
                        <td><?= $item['inspection_number'] ?></td>
                        <td><?= date('Y-m-d', strtotime($item['inspection_date'])) ?></td>
                        <td><?= $item['grn_number'] ?></td>
                        <td><?= $item['inspector_name'] ?></td>
                        <td>
                            <span class="status-badge 
                                <?= $item['status'] === 'passed' ? 'status-passed' :
                                   ($item['status'] === 'failed' ? 'status-failed' :
                                   'status-pending') ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </td>
                        <td><?= $item['items_passed'] ?? 0 ?></td>
                        <td><?= $item['items_failed'] ?? 0 ?></td>
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
            <p>No quality inspections found for the selected criteria.</p>
        </div>
    <?php endif; ?>
</body>
</html>