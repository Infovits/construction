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
        <!-- Recent Incidents -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Recent Incidents</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Severity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (count($recentIncidents) > 0): ?>
                            <?php foreach ($recentIncidents as $incident): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="<?= base_url('incident-safety/incidents/' . $incident['id']) ?>" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                            <?= $incident['incident_code'] ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?= substr($incident['title'], 0, 40) ?>...</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                            $severityConfig = [
                                                4 => ['label' => 'Critical', 'class' => 'bg-red-100 text-red-800'],
                                                3 => ['label' => 'High', 'class' => 'bg-orange-100 text-orange-800'],
                                                2 => ['label' => 'Medium', 'class' => 'bg-yellow-100 text-yellow-800'],
                                                1 => ['label' => 'Low', 'class' => 'bg-green-100 text-green-800']
                                            ];
                                            $sev = $severityConfig[$incident['severity_id']] ?? ['label' => 'Unknown', 'class' => 'bg-gray-100 text-gray-800'];
                                        ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $sev['class'] ?>">
                                            <?= $sev['label'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?= ucfirst(str_replace('_', ' ', $incident['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                    No recent incidents
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50">
                <a href="<?= base_url('incident-safety/incidents') ?>" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                    View All Incidents <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Open Action Items -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Open Action Items</h3>
            </div>
            <div class="p-6 space-y-4">
                <?php if (!empty($openActionSteps) && count($openActionSteps) > 0): ?>
                    <?php foreach (array_slice($openActionSteps, 0, 5) as $step): ?>
                        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex-shrink-0">
                                <i class="fas fa-tasks text-orange-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900"><?= $step['description'] ?? 'No description' ?></p>
                                <p class="text-xs text-gray-600 mt-1">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Due: <?= !empty($step['due_date']) ? date('M d, Y', strtotime($step['due_date'])) : 'Not set' ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                    Pending
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-check-circle text-green-400 text-5xl mb-3"></i>
                        <p class="text-gray-500 font-medium">No open action items</p>
                        <p class="text-sm text-gray-400 mt-1">All actions have been completed</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($openActionSteps) && count($openActionSteps) > 5): ?>
                <div class="px-6 py-4 border-t bg-gray-50">
                    <a href="<?= base_url('incident-safety/incidents') ?>" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                        View All Action Items <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Audits & Critical Items -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Audits -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Recent Safety Audits</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($recentAudits) && count($recentAudits) > 0): ?>
                            <?php foreach (array_slice($recentAudits, 0, 5) as $audit): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= !empty($audit['audit_date']) ? date('M d, Y', strtotime($audit['audit_date'])) : 'N/A' ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?= $audit['project_name'] ?? 'N/A' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                        $score = $audit['overall_score'] ?? 0;
                                        $scoreClass = $score >= 80 ? 'text-green-600' : ($score >= 60 ? 'text-yellow-600' : 'text-red-600');
                                        ?>
                                        <span class="text-sm font-bold <?= $scoreClass ?>">
                                            <?= $score ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-clipboard-check text-3xl mb-2 block"></i>
                                    No recent audits
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50">
                <a href="<?= base_url('incident-safety/audits') ?>" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                    View All Audits <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Critical Incidents -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-red-50 to-red-100">
                <h3 class="text-lg font-semibold text-red-900">Critical Incidents (30 Days)</h3>
            </div>
            <div class="p-6 space-y-4">
                <?php if (!empty($criticalIncidents) && count($criticalIncidents) > 0): ?>
                    <?php foreach (array_slice($criticalIncidents, 0, 5) as $critical): ?>
                        <a href="<?= base_url('incident-safety/incidents/' . $critical['id']) ?>" class="block p-4 bg-red-50 rounded-lg hover:bg-red-100 transition border border-red-200">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900"><?= $critical['incident_code'] ?></p>
                                    <p class="text-sm text-gray-700 mt-1"><?= substr($critical['title'], 0, 50) ?>...</p>
                                    <p class="text-xs text-gray-600 mt-2">
                                        <i class="fas fa-calendar mr-1"></i>
                                        <?= date('M d, Y', strtotime($critical['incident_date'])) ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-shield-alt text-green-400 text-5xl mb-3"></i>
                        <p class="text-gray-500 font-medium">No critical incidents</p>
                        <p class="text-sm text-gray-400 mt-1">Great safety record!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
