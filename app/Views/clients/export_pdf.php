<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients Report</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            margin: 0 0 10px 0;
            color: #1f2937;
        }
        
        .header p {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
        }
        
        .company-header {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .report-meta {
            font-size: 12px;
            color: #6b7280;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .stat-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
        }
        
        .filters-section {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-bottom: 30px;
        }
        
        .filters-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .filter-item {
            background: #ffffff;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }
        
        .filter-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        
        .filter-value {
            font-size: 14px;
            color: #1f2937;
            font-weight: 500;
        }
        
        .table-container {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table-header {
            background: #f3f4f6;
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 5px 0;
        }
        
        .table-subtitle {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead th {
            background: #f9fafb;
            padding: 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e5e7eb;
        }
        
        tbody td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13px;
            color: #374151;
        }
        
        tbody tr:hover td {
            background-color: #f9fafb;
        }
        
        .client-info {
            display: flex;
            align-items: center;
        }
        
        .client-avatar {
            width: 32px;
            height: 32px;
            background: #e0e7ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: #4f46e5;
            font-size: 14px;
        }
        
        .client-name {
            font-weight: 600;
            color: #1f2937;
        }
        
        .client-code {
            font-size: 11px;
            color: #6b7280;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-individual { background: #dbeafe; color: #1d4ed8; }
        .badge-company { background: #dcfce7; color: #166534; }
        .badge-government { background: #f3e8ff; color: #7c3aed; }
        
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
        
        .empty-icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .empty-title {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        
        .empty-subtitle {
            font-size: 14px;
            margin: 0;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body { margin: 0; padding: 0; }
            .container { padding: 0; }
            .header { margin-bottom: 20px; }
            .stats-grid { page-break-inside: avoid; }
            .filters-section { page-break-inside: avoid; }
            .table-container { page-break-inside: avoid; }
            .footer { page-break-before: always; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Company Header -->
        <div class="company-header">
            <div class="company-name">Helmet Construction Management System</div>
            <div class="report-title">Clients Report</div>
            <div class="report-meta">Generated on <?= date('F j, Y \a\t g:i A') ?> | Report ID: CL-<?= date('Ymd-His') ?></div>
        </div>

        <!-- Statistics -->
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="display: inline-block; background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb; width: 200px; text-align: left;">
                <div style="font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em; text-align: center;">Total Clients</div>
                <div style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 10px; text-align: center;"><?= $stats['total_clients'] ?></div>
                <div style="font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em; text-align: center;">Active Clients</div>
                <div style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 10px; text-align: center;"><?= $stats['active_clients'] ?></div>
                <div style="font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em; text-align: center;">New This Month</div>
                <div style="font-size: 16px; font-weight: 700; color: #1f2937; text-align: center;"><?= $stats['clients_this_month'] ?></div>
            </div>
        </div>

        <!-- Filters -->
        <?php if ($filters['search'] || $filters['status'] || $filters['client_type']): ?>
            <div class="filters-section">
                <div class="filters-title">Applied Filters</div>
                <div class="filters-grid">
                    <?php if ($filters['search']): ?>
                        <div class="filter-item">
                            <div class="filter-label">Search Term</div>
                            <div class="filter-value"><?= esc($filters['search']) ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if ($filters['status']): ?>
                        <div class="filter-item">
                            <div class="filter-label">Status Filter</div>
                            <div class="filter-value"><?= ucfirst($filters['status']) ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if ($filters['client_type']): ?>
                        <div class="filter-item">
                            <div class="filter-label">Client Type</div>
                            <div class="filter-value"><?= ucfirst($filters['client_type']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Data Table -->
        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">Client Information</h2>
                <p class="table-subtitle">Comprehensive client data with applied filters and search criteria</p>
            </div>
            
            <?php if (empty($clients)): ?>
                <div class="empty-state">
                    <div class="empty-icon">👥</div>
                    <div class="empty-title">No Clients Found</div>
                    <p class="empty-subtitle">No clients match the current filters or search criteria</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client Name</th>
                            <th>Client Code</th>
                            <th>Client Type</th>
                            <th>Status</th>
                            <th>Company</th>
                            <th>Email Address</th>
                            <th>Phone Number</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1; ?>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td style="font-weight: bold; color: #6b7280;"><?= $counter++ ?></td>
                                <td>
                                    <div class="client-name"><?= esc($client['name']) ?></div>
                                </td>
                                <td><?= esc($client['client_code']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $client['client_type'] ?>">
                                        <?= ucfirst($client['client_type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $client['status'] ?>">
                                        <?= ucfirst($client['status']) ?>
                                    </span>
                                </td>
                                <td><?= esc($client['company_name'] ?? 'N/A') ?></td>
                                <td><?= esc($client['email']) ?></td>
                                <td><?= esc($client['phone']) ?></td>
                                <td><?= date('M j, Y', strtotime($client['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Helmet Construction Management System</strong></p>
            <p>Professional client management and reporting solution</p>
            <p>Report generated for administrative use only</p>
            <p class="report-meta">Report ID: CL-<?= date('Ymd-His') ?> | Generated by: <?= session('full_name') ?? 'System' ?></p>
        </div>
    </div>
</body>
</html>
