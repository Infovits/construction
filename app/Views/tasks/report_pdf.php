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
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        
        /* Header Styles */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        
        .header-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header-subtitle {
            font-size: 12px;
            color: #666;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .header-meta {
            font-size: 10px;
            color: #888;
            font-weight: 500;
        }
        
        /* Report Info */
        .report-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }
        
        .info-item {
            text-align: center;
        }
        
        .info-label {
            font-size: 9px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            font-weight: 600;
        }
        
        .info-value {
            font-size: 11px;
            color: #333;
            font-weight: 700;
        }
        
        /* Table Styles */
        .table-container {
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        thead {
            background: #333;
            color: white;
        }
        
        th {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            border-bottom: 1px solid #444;
        }
        
        tbody tr {
            border-bottom: 1px solid #eee;
        }
        
        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        td {
            padding: 10px 8px;
            vertical-align: middle;
        }
        
        /* Content Styles */
        .task-title {
            font-weight: 700;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
        }
        
        .task-desc {
            font-size: 9px;
            color: #6c757d;
            margin-top: 2px;
            line-height: 1.3;
        }
        
        .project-name {
            font-weight: 700;
            color: #333;
            font-size: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .row-number {
            font-weight: 700;
            color: #6c757d;
            text-align: center;
            width: 30px;
            font-size: 9px;
        }
        
        /* Status Colors */
        .status-not_started { color: #6c757d; }
        .status-in_progress { color: #6c757d; }
        .status-review { color: #6c757d; }
        .status-completed { color: #6c757d; }
        .status-on_hold { color: #6c757d; }
        .status-cancelled { color: #6c757d; }
        
        /* Priority Colors */
        .priority-low { color: #6c757d; }
        .priority-medium { color: #6c757d; }
        .priority-high { color: #6c757d; }
        .priority-urgent { color: #6c757d; }
        
        .date-cell {
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            font-size: 9px;
            color: #495057;
            font-weight: 600;
        }
        
        .progress-container {
            width: 100%;
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 2px;
        }
        
        .progress-bar {
            height: 100%;
            background: #6c757d;
            transition: width 0.3s ease;
        }
        
        .progress-text {
            font-size: 8px;
            color: #6c757d;
            text-align: right;
            font-weight: 600;
        }
        
        .days-late {
            font-weight: 700;
            color: #6c757d;
            font-size: 8px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        
        .on-time {
            font-weight: 700;
            color: #6c757d;
            font-size: 8px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        
        .not-set {
            color: #6c757d;
            font-style: italic;
            font-size: 9px;
        }
        
        .critical-path {
            color: #6c757d;
            font-weight: 700;
            font-size: 8px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #6c757d;
            text-align: center;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            background: #f8f9fa;
            border-radius: 6px;
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
            margin: 2px 0;
            font-size: 9px;
            color: #6c757d;
        }
        
        .confidential {
            margin-top: 8px;
            font-size: 8px;
            color: #adb5bd;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-top: 1px solid #dee2e6;
            padding-top: 6px;
        }
        
        /* No Data */
        .no-data {
            text-align: center;
            padding: 25px 20px;
            color: #6c757d;
            font-style: italic;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px dashed #dee2e6;
        }
        
        .no-data i {
            font-size: 18px;
            color: #adb5bd;
            margin-bottom: 6px;
            display: block;
        }
        
        /* Print Styles */
        @media print {
            body {
                font-size: 11px;
            }
            
            .header-title {
                font-size: 16px;
            }
            
            th, td {
                font-size: 9px;
                padding: 6px 4px;
            }
            
            .progress-container {
                height: 6px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-title"><?= esc($title) ?></div>
        <div class="header-subtitle">Task Status Report</div>
        <div class="header-meta">Generated: <?= $export_date ?></div>
    </div>

    <div class="report-info">
        <div class="info-item">
            <div class="info-label">Report Date</div>
            <div class="info-value"><?= $export_date ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Company</div>
            <div class="info-value"><?= esc($company_name) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Report Type</div>
            <div class="info-value">Task Status Report</div>
        </div>
        <div class="info-item">
            <div class="info-label">Total Records</div>
            <div class="info-value"><?= $stats['total'] ?></div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th style="width: 6%;">#</th>
                <th style="width: 25%;">Task Title</th>
                <th style="width: 18%;">Project Name</th>
                <th style="width: 15%;">Assigned To</th>
                <th style="width: 10%;">Priority</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 10%;">Progress</th>
                <th style="width: 12%;">Due Date</th>
                <th style="width: 12%;">Days Late</th>
            </tr>
            </thead>
            <tbody>
                <?php if (empty($tasks)): ?>
                <tr>
                    <td colspan="9" class="no-data">
                        <i class="fas fa-chart-line"></i>
                        <div style="font-weight: 600; margin-top: 6px;">No tasks found</div>
                        <div style="font-size: 9px; margin-top: 3px; color: #6c757d;">
                            No tasks found for the selected criteria.
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                <?php $counter = 1; ?>
                <?php foreach ($tasks as $task): ?>
                <?php if ($task['is_milestone']) continue; ?>
                <tr>
                    <td class="row-number"><?= $counter++ ?></td>
                    <td>
                        <div class="task-title"><?= esc($task['title']) ?></div>
                        <?php if ($task['is_critical_path']): ?>
                        <div class="critical-path">Critical Path</div>
                        <?php endif; ?>
                        <?php if (!empty($task['task_code'])): ?>
                        <div class="task-desc"><?= esc($task['task_code']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="project-name"><?= esc($task['project_name']) ?></div>
                    </td>
                    <td>
                        <?php if ($task['assigned_name']): ?>
                            <div style="font-weight: 600; color: #333;"><?= esc($task['assigned_name']) ?></div>
                        <?php else: ?>
                            <span class="not-set">Unassigned</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $priorityClass = 'priority-' . $task['priority'];
                        ?>
                        <span class="status-badge <?= $priorityClass ?>">
                            <?= ucfirst($task['priority']) ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $statusClass = 'status-' . ($task['status'] ?: 'not_started');
                        ?>
                        <span class="status-badge <?= $statusClass ?>">
                            <?= ucwords(str_replace('_', ' ', $task['status'] ?: 'not_started')) ?>
                        </span>
                    </td>
                    <td>
                        <div class="progress-container">
                            <div class="progress-bar" style="width: <?= $task['progress_percentage'] ?>%"></div>
                        </div>
                        <div class="progress-text"><?= $task['progress_percentage'] ?>%</div>
                    </td>
                    <td class="date-cell">
                        <?php if ($task['planned_end_date']): ?>
                            <?= date('M d, Y', strtotime($task['planned_end_date'])) ?>
                        <?php else: ?>
                            <span class="not-set">No due date</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $daysLate = 0;
                        if ($task['planned_end_date'] && $task['status'] !== 'completed') {
                            $dueDate = strtotime($task['planned_end_date']);
                            $today = strtotime(date('Y-m-d'));
                            if ($today > $dueDate) {
                                $daysLate = floor(($today - $dueDate) / (60 * 60 * 24));
                            }
                        } elseif ($task['actual_end_date'] && $task['planned_end_date']) {
                            $dueDate = strtotime($task['planned_end_date']);
                            $completedDate = strtotime($task['actual_end_date']);
                            if ($completedDate > $dueDate) {
                                $daysLate = floor(($completedDate - $dueDate) / (60 * 60 * 24));
                            }
                        }
                        ?>
                        <?php if ($daysLate > 0): ?>
                            <span class="days-late"><?= $daysLate ?> days late</span>
                        <?php elseif ($task['status'] === 'completed' && $daysLate <= 0): ?>
                            <span class="on-time">On time</span>
                        <?php else: ?>
                            <span class="not-set">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div class="footer-title">Report Information</div>
        <p>This report was automatically generated by the Construction Management System.</p>
        <p>For more information, please contact your system administrator.</p>
        <div class="confidential">Confidential - Internal Use Only</div>
    </div>
</body>
</html>
