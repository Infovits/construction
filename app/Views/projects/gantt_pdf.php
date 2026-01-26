<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gantt Chart - <?= esc($project['name']) ?></title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .header h2 {
            font-size: 18px;
            margin: 0 0 10px 0;
            color: #666;
        }
        
        .project-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }
        
        .export-info {
            text-align: right;
            font-size: 10px;
            color: #999;
        }
        
        .chart-container {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 30px;
        }
        
        .gantt-chart {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        .gantt-chart th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .gantt-chart td {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: middle;
        }
        
        .gantt-chart tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .gantt-chart tr:hover {
            background-color: #e9ecef;
        }
        
        .task-row {
            height: 40px;
        }
        
        .milestone-row {
            height: 40px;
            background-color: #fff3cd !important;
        }
        
        .task-name {
            font-weight: bold;
            color: #333;
        }
        
        .task-details {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }
        
        .progress-bar {
            width: 100%;
            height: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #28a745;
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .critical-path {
            border-left: 4px solid #dc3545;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-not-started {
            background-color: #6c757d;
            color: white;
        }
        
        .status-in-progress {
            background-color: #007bff;
            color: white;
        }
        
        .status-completed {
            background-color: #28a745;
            color: white;
        }
        
        .status-on-hold {
            background-color: #ffc107;
            color: #212529;
        }
        
        .priority-badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .priority-high {
            background-color: #dc3545;
            color: white;
        }
        
        .priority-medium {
            background-color: #ffc107;
            color: #212529;
        }
        
        .priority-low {
            background-color: #28a745;
            color: white;
        }
        
        .legend {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        
        .legend h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
        }
        
        .legend-item {
            display: inline-block;
            margin-right: 20px;
            font-size: 11px;
            color: #666;
        }
        
        .legend-color {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 2px;
            margin-right: 5px;
            vertical-align: middle;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= esc($project['name']) ?></h1>
        <h2>Gantt Chart Report</h2>
        <div class="project-info">
            <div>
                <strong>Project Code:</strong> <?= esc($project['project_code']) ?><br>
                <strong>Client:</strong> <?= esc($project['client_name'] ?? 'N/A') ?><br>
                <strong>Start Date:</strong> <?= date('M d, Y', strtotime($project['start_date'])) ?><br>
                <strong>End Date:</strong> <?= date('M d, Y', strtotime($project['planned_end_date'])) ?>
            </div>
            <div class="export-info">
                <strong>Export Date:</strong> <?= $export_date ?><br>
                <strong>Generated by:</strong> <?= esc(session('user_name') ?? 'System') ?><br>
                <strong>Company:</strong> <?= esc($project['company_name'] ?? 'N/A') ?>
            </div>
        </div>
    </div>

    <div class="chart-container">
        <table class="gantt-chart">
            <thead>
                <tr>
                    <th style="width: 25%;">Task/Milestone</th>
                    <th style="width: 15%;">Assigned To</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 10%;">Priority</th>
                    <th style="width: 10%;">Start Date</th>
                    <th style="width: 10%;">End Date</th>
                    <th style="width: 10%;">Progress</th>
                    <th style="width: 10%;">Duration</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Combine and sort tasks and milestones by start date
                $all_items = [];
                
                // Add tasks
                foreach ($tasks as $task) {
                    $all_items[] = [
                        'type' => 'task',
                        'data' => $task,
                        'start_date' => $task['planned_start_date'],
                        'end_date' => $task['planned_end_date']
                    ];
                }
                
                // Add milestones
                foreach ($milestones as $milestone) {
                    $all_items[] = [
                        'type' => 'milestone',
                        'data' => $milestone,
                        'start_date' => $milestone['planned_end_date'],
                        'end_date' => $milestone['planned_end_date']
                    ];
                }
                
                // Sort by start date
                usort($all_items, function($a, $b) {
                    return strtotime($a['start_date']) - strtotime($b['start_date']);
                });
                
                foreach ($all_items as $item):
                    $data = $item['data'];
                    $is_milestone = $item['type'] === 'milestone';
                    $start_date = new DateTime($item['start_date']);
                    $end_date = new DateTime($item['end_date']);
                    $duration = $start_date->diff($end_date)->days + 1;
                ?>
                    <tr class="<?= $is_milestone ? 'milestone-row' : 'task-row' ?> <?= isset($data['is_critical_path']) && $data['is_critical_path'] ? 'critical-path' : '' ?>">
                        <td>
                            <div class="task-name">
                                <?= $is_milestone ? '🎯 ' : '📋 ' ?><?= esc($data['title']) ?>
                                <?php if (isset($data['is_critical_path']) && $data['is_critical_path']): ?>
                                    <span style="font-size: 10px; color: #dc3545; margin-left: 5px;">(Critical Path)</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($is_milestone): ?>
                                <div class="task-details">Milestone</div>
                            <?php else: ?>
                                <div class="task-details">Task ID: <?= $data['id'] ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= esc($data['assigned_name'] ?? 'Unassigned') ?>
                        </td>
                        <td>
                            <?php 
                            $status = $data['status'] ?? 'not_started';
                            $status_class = '';
                            $status_text = '';
                            
                            switch ($status) {
                                case 'completed':
                                    $status_class = 'status-completed';
                                    $status_text = 'Completed';
                                    break;
                                case 'in_progress':
                                    $status_class = 'status-in-progress';
                                    $status_text = 'In Progress';
                                    break;
                                case 'on_hold':
                                    $status_class = 'status-on-hold';
                                    $status_text = 'On Hold';
                                    break;
                                default:
                                    $status_class = 'status-not-started';
                                    $status_text = 'Not Started';
                            }
                            ?>
                            <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                        </td>
                        <td>
                            <?php 
                            $priority = $data['priority'] ?? 'medium';
                            $priority_class = '';
                            $priority_text = '';
                            
                            switch ($priority) {
                                case 'high':
                                    $priority_class = 'priority-high';
                                    $priority_text = 'High';
                                    break;
                                case 'low':
                                    $priority_class = 'priority-low';
                                    $priority_text = 'Low';
                                    break;
                                default:
                                    $priority_class = 'priority-medium';
                                    $priority_text = 'Medium';
                            }
                            ?>
                            <span class="priority-badge <?= $priority_class ?>"><?= $priority_text ?></span>
                        </td>
                        <td><?= date('M d, Y', strtotime($item['start_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($item['end_date'])) ?></td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $data['progress_percentage'] ?? 0 ?>%"></div>
                            </div>
                            <div style="text-align: center; font-size: 10px; margin-top: 2px;">
                                <?= $data['progress_percentage'] ?? 0 ?>%
                            </div>
                        </td>
                        <td><?= $duration ?> day<?= $duration != 1 ? 's' : '' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="legend">
        <h3>Legend</h3>
        <div class="legend-item">
            <span class="legend-color" style="background-color: #28a745;"></span>
            Completed Tasks
        </div>
        <div class="legend-item">
            <span class="legend-color" style="background-color: #007bff;"></span>
            In Progress Tasks
        </div>
        <div class="legend-item">
            <span class="legend-color" style="background-color: #6c757d;"></span>
            Not Started Tasks
        </div>
        <div class="legend-item">
            <span class="legend-color" style="background-color: #ffc107;"></span>
            On Hold Tasks
        </div>
        <div class="legend-item">
            <span class="legend-color" style="background-color: #fff3cd; border: 1px solid #dee2e6;"></span>
            Milestones
        </div>
        <div class="legend-item">
            <span style="display: inline-block; width: 12px; height: 12px; border-left: 4px solid #dc3545; margin-right: 5px;"></span>
            Critical Path
        </div>
    </div>

    <div class="footer">
        <p>This Gantt Chart was generated automatically by the Project Management System.</p>
        <p>For more information, please contact your system administrator.</p>
    </div>
</body>
</html>