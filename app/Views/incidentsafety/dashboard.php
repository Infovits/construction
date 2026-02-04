<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Incident & Safety Dashboard<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.metric-card { transition: transform 0.2s, box-shadow 0.2s; }
.metric-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.severity-critical { @apply bg-red-50 border-red-200; }
.severity-high { @apply bg-orange-50 border-orange-200; }
.severity-medium { @apply bg-yellow-50 border-yellow-200; }
.severity-low { @apply bg-green-50 border-green-200; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-exclamation-triangle mr-2"></i>Incident & Safety Dashboard</h1>
            <p class="text-gray-600 mt-1">Monitor incidents, audits, and safety performance</p>
        </div>
        <a href="<?= base_url('incident-safety/incidents/create') ?>" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            <i class="fas fa-plus mr-2"></i> Report Incident
        </a>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="metric-card bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600 mb-1">Total Incidents</p>
                    <p class="text-3xl font-bold text-gray-900"><?= count($recentIncidents) ?></p>
                </div>
                <i class="fas fa-exclamation-circle fa-3x text-blue-200"></i>
            </div>
        </div>
        <div class="metric-card bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-red-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-red-600 mb-1">Open Incidents</p>
                    <p class="text-3xl font-bold text-gray-900"><?= count($openIncidents) ?></p>
                </div>
                <i class="fas fa-hourglass-start fa-3x text-red-200"></i>
            </div>
        </div>
        <div class="metric-card bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-orange-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-orange-600 mb-1">Critical Issues</p>
                    <p class="text-3xl font-bold text-gray-900"><?= count(array_filter($recentIncidents, fn($i) => $i['severity_id'] == 4)) ?></p>
                </div>
                <i class="fas fa-fire fa-3x text-orange-200"></i>
            </div>
        </div>
        <div class="metric-card bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-green-600 mb-1">Resolved</p>
                    <p class="text-3xl font-bold text-gray-900"><?= count(array_filter($recentIncidents, fn($i) => $i['status'] == 'resolved')) ?></p>
                </div>
                <i class="fas fa-check-circle fa-3x text-green-200"></i>
            </div>
        </div>
    </div>

    <!-- Recent Incidents & Open Items -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <h6 class="text-muted mb-1">Critical (30d)</h6>
                            <h3 class="mb-0 text-danger"><?= count($criticalIncidents) ?></h3>
                        </div>
                        <i class="fas fa-fire fa-3x text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Recent Audits</h6>
                            <h3 class="mb-0"><?= count($recentAudits) ?></h3>
                        </div>
                        <i class="fas fa-clipboard-check fa-3x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Incidents -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Incidents</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Severity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentIncidents as $incident): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('incident-safety/incidents/' . $incident['id']) ?>">
                                            <?= $incident['incident_code'] ?>
                                        </a>
                                    </td>
                                    <td><?= substr($incident['title'], 0, 30) ?>...</td>
                                    <td>
                                        <?php
                                            $severityLabels = [
                                                4 => 'Critical',
                                                3 => 'High',
                                                2 => 'Medium',
                                                1 => 'Low'
                                            ];
                                        ?>
                                        <span class="badge bg-danger">
                                            <?= $severityLabels[$incident['severity_id']] ?? 'Unknown' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            <?= ucfirst(str_replace('_', ' ', $incident['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('incident-safety/incidents') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
        </div>

        <!-- Recent Audits -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Safety Audits</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Conformance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentAudits as $audit): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('incident-safety/audits/' . $audit['id']) ?>">
                                            <?= $audit['audit_code'] ?>
                                        </a>
                                    </td>
                                    <td><?= ucfirst($audit['audit_type']) ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= $audit['conformance_percentage'] ?>%</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= ucfirst($audit['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('incident-safety/audits') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Open Incidents -->
    <?php if (count($openIncidents) > 0): ?>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-exclamation"></i> Open & In-Progress Incidents</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Project</th>
                                <th>Date</th>
                                <th>Severity</th>
                                <th>Assigned To</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($openIncidents as $incident): ?>
                                <tr>
                                    <td><strong><?= $incident['incident_code'] ?></strong></td>
                                    <td><?= substr($incident['title'], 0, 40) ?></td>
                                    <td><?= $incident['project_id'] ?></td>
                                    <td><?= date('M d, Y', strtotime($incident['incident_date'])) ?></td>
                                    <td>
                                        <?php
                                            $severityBadge = [
                                                4 => 'Critical',
                                                3 => 'High',
                                                2 => 'Medium',
                                                1 => 'Low'
                                            ];
                                        ?>
                                        <span class="badge bg-danger">
                                            <?= $severityBadge[$incident['severity_id']] ?? 'Unknown' ?>
                                        </span>
                                    </td>
                                    <td><?= $incident['assigned_to'] ?: '<em>Unassigned</em>' ?></td>
                                    <td>
                                        <a href="<?= base_url('incident-safety/incidents/' . $incident['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('incident-safety/incidents/create') ?>" class="btn btn-danger me-2">
                        <i class="fas fa-plus-circle"></i> Report New Incident
                    </a>
                    <a href="<?= base_url('incident-safety/audits/create') ?>" class="btn btn-info me-2">
                        <i class="fas fa-clipboard-list"></i> Create Safety Audit
                    </a>
                    <a href="<?= base_url('incident-safety/reports/create') ?>" class="btn btn-primary me-2">
                        <i class="fas fa-file-alt"></i> Generate Safety Report
                    </a>
                    <a href="<?= base_url('incident-safety/analytics') ?>" class="btn btn-success">
                        <i class="fas fa-chart-bar"></i> View Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
