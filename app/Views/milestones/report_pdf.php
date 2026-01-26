<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - <?= esc($company_name) ?></title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        /* Header Styles */
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
            position: relative;
        }
        
        .company-logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 120px;
            height: 60px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #495057;
            font-size: 10px;
        }
        
        .header h1 {
            font-size: 22px;
            margin: 0 0 5px 0;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
        }
        
        .header h2 {
            font-size: 14px;
            margin: 0 0 5px 0;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .header p {
            font-size: 11px;
            color: #6c757d;
            margin: 0;
            font-weight: 500;
        }
        
        /* Meta Information */
        .report-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 10px;
            color: #495057;
            background: #f8f9fa;
            padding: 10px 15px;
            border-left: 4px solid #007bff;
            border-radius: 0 4px 4px 0;
        }
        
        .meta-item {
            display: flex;
            flex-direction: column;
        }
        
        .meta-label {
            font-weight: bold;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 9px;
        }
        
        .meta-value {
            font-weight: 500;
            color: #6c757d;
            margin-top: 2px;
        }
        
        /* Filters Section */
        .filters {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .filters h3 {
            margin: 0 0 12px 0;
            font-size: 13px;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #007bff;
            padding-bottom: 8px;
            display: inline-block;
        }
        
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .filter-item {
            background: white;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        .filter-label {
            font-size: 10px;
            color: #495057;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 4px;
        }
        
        .filter-value {
            font-size: 12px;
            color: #2c3e50;
            font-weight: 600;
            line-height: 1.4;
        }
        
        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px 15px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #007bff, #28a745);
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 5px;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 10px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        /* Table Styles */
        .table-container {
            margin-top: 25px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        
        th {
            background: linear-gradient(135deg, #343a40 0%, #495057 100%);
            color: white;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 12px 8px;
            text-align: left;
            border: 1px solid #495057;
            position: relative;
        }
        
        th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #007bff, #28a745);
        }
        
        td {
            padding: 10px 8px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            font-size: 10px;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e3f2fd;
        }
        
        /* Content Styles */
        .milestone-title {
            font-weight: 700;
            color: #2c3e50;
            font-size: 11px;
            line-height: 1.3;
        }
        
        .milestone-desc {
            font-size: 9px;
            color: #6c757d;
            margin-top: 4px;
            line-height: 1.4;
        }
        
        .project-name {
            font-weight: 700;
            color: #007bff;
            font-size: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .row-number {
            font-weight: 700;
            color: #2c3e50;
            text-align: center;
            width: 40px;
        }
        
        .status-not_started { background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%); color: #495057; }
        .status-pending { background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); color: #856404; border-color: #ffc107; }
        .status-in_progress { background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); color: #0c5460; border-color: #17a2b8; }
        .status-completed { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724; border-color: #28a745; }
        .status-on_hold { background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%); color: #7f5a00; border-color: #f39c12; }
        .status-cancelled { background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); color: #721c24; border-color: #dc3545; }
        
        .date-cell {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            color: #495057;
            font-weight: 600;
        }
        
        .progress-container {
            width: 100%;
            height: 14px;
            background-color: #e9ecef;
            border-radius: 7px;
            overflow: hidden;
            margin-bottom: 4px;
            border: 1px solid #dee2e6;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #007bff, #28a745);
            transition: width 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .progress-text {
            font-size: 9px;
            color: #6c757d;
            text-align: right;
            font-weight: 600;
        }
        
        .days-late {
            font-weight: 800;
            color: #dc3545;
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid rgba(220, 53, 69, 0.4);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        
        .on-time {
            font-weight: 800;
            color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid rgba(40, 167, 69, 0.4);
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        
        .not-set {
            color: #6c757d;
            font-style: italic;
            font-size: 10px;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #6c757d;
            text-align: center;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
        }
        
        .footer-title {
            font-size: 10px;
            color: #495057;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        
        .footer p {
            margin: 4px 0;
            font-size: 9px;
            color: #6c757d;
        }
        
        .confidential {
            margin-top: 10px;
            font-size: 8px;
            color: #adb5bd;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
        }
        
        /* No Data */
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
            font-style: italic;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
        }
        
        .no-data i {
            font-size: 24px;
            color: #6c757d;
            margin-bottom: 10px;
            display: block;
        }
        
        /* Page Break */
        .page-break {
            page-break-before: always;
        }
        
        /* Print Styles */
        @media print {
            body {
                font-size: 10px;
            }
            
            .header h1 {
                font-size: 18px;
            }
            
            .stat-number {
                font-size: 24px;
            }
            
            th, td {
                font-size: 9px;
                padding: 6px 4px;
            }
            
            .progress-container {
                height: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">
            <?= esc($company_name) ?>
        </div>
        <h1><?= esc($title) ?></h1>
        <h2>Comprehensive Milestone Analysis Report</h2>
        <p>Report Period: <?= date('F j, Y', strtotime($filters['date_from'])) ?> to <?= date('F j, Y', strtotime($filters['date_to'])) ?></p>
    </div>

    <div class="report-meta">
        <div class="meta-item">
            <span class="meta-label">Generated</span>
            <span class="meta-value"><?= $export_date ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Company</span>
            <span class="meta-value"><?= esc($company_name) ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Total Records</span>
            <span class="meta-value"><?= $stats['total'] ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Report Type</span>
            <span class="meta-value">Milestone Status Report</span>
        </div>
    </div>

    <?php if (!empty($filters['project_id'])): ?>
    <div class="filters">
        <h3>Report Filters Applied</h3>
        <div class="filter-grid">
            <div class="filter-item">
                <div class="filter-label">Project</div>
                <div class="filter-value">
                    <?php
                    $projectName = '';
                    foreach($projects as $project) {
                        if ($project['id'] == $filters['project_id']) {
                            $projectName = $project['name'];
                            break;
                        }
                    }
                    echo esc($projectName);
                    ?>
                </div>
            </div>
            <div class="filter-item">
                <div class="filter-label">Date Range</div>
                <div class="filter-value">
                    <?= date('F j, Y', strtotime($filters['date_from'])) ?> to <?= date('F j, Y', strtotime($filters['date_to'])) ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>


    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th style="width: 6%;">#</th>
                <th style="width: 28%;">Milestone Title</th>
                <th style="width: 20%;">Project Name</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 12%;">Due Date</th>
                <th style="width: 12%;">Completion Date</th>
                <th style="width: 10%;">Progress</th>
            </tr>
            </thead>
            <tbody>
                <?php if (empty($milestones)): ?>
                <tr>
                    <td colspan="7" class="no-data">
                        <i class="fas fa-chart-line"></i>
                        <div style="font-weight: 700; margin-top: 8px;">No milestones found</div>
                        <div style="font-size: 10px; margin-top: 4px; color: #6c757d;">
                            No milestones found for the selected criteria.
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                <?php $counter = 1; ?>
                <?php foreach ($milestones as $milestone): ?>
                <tr>
                    <td class="row-number"><?= $counter++ ?></td>
                    <td>
                        <div class="milestone-title"><?= esc($milestone['title']) ?></div>
                        <?php if ($milestone['description']): ?>
                        <div class="milestone-desc"><?= esc(substr($milestone['description'], 0, 100)) ?><?= strlen($milestone['description']) > 100 ? '...' : '' ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="project-name"><?= esc($milestone['project_name']) ?></div>
                    </td>
                    <td>
                        <?php
                        $statusClass = 'status-' . ($milestone['status'] ?: 'not_started');
                        ?>
                        <span class="status-badge <?= $statusClass ?>">
                            <?= ucwords(str_replace('_', ' ', $milestone['status'] ?: 'not_started')) ?>
                        </span>
                    </td>
                    <td class="date-cell">
                        <?php if ($milestone['planned_end_date']): ?>
                            <?= date('M d, Y', strtotime($milestone['planned_end_date'])) ?>
                        <?php else: ?>
                            <span class="not-set">Not set</span>
                        <?php endif; ?>
                    </td>
                    <td class="date-cell">
                        <?php if ($milestone['status'] === 'completed' && isset($milestone['actual_end_date']) && $milestone['actual_end_date'] && strtotime($milestone['actual_end_date']) > 0): ?>
                            <?= date('M d, Y', strtotime($milestone['actual_end_date'])) ?>
                        <?php else: ?>
                            <span class="not-set">Not completed</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="progress-container">
                            <div class="progress-bar" style="width: <?= $milestone['progress_percentage'] ?>%"></div>
                        </div>
                        <div class="progress-text"><?= $milestone['progress_percentage'] ?>%</div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div class="footer-title">Report Summary</div>
        <p><strong>Completion Rate:</strong> <?= $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0 ?>%</p>
        <p><strong>On-Time Performance:</strong> <?= $stats['total'] > 0 ? round((($stats['total'] - $stats['overdue']) / $stats['total']) * 100, 1) : 0 ?>%</p>
        <p>This report was automatically generated by the Construction Management System.</p>
        <p>For more information, please contact your system administrator.</p>
        <div class="confidential">Confidential - Internal Use Only</div>
    </div>
</body>
</html>
