<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Incidents<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.severity-critical { 
    background-color: #fee2e2;
    color: #991b1b;
}
.severity-high { 
    background-color: #ffedd5;
    color: #9a3412;
}
.severity-medium { 
    background-color: #fef3c7;
    color: #92400e;
}
.severity-low { 
    background-color: #dcfce7;
    color: #166534;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-list mr-2"></i>Incident Reports</h1>
            <p class="text-gray-600 mt-1">Manage all incident reports by project, type, and severity</p>
        </div>
        <a href="<?= base_url('incident-safety/incidents/create') ?>" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            <i class="fas fa-plus mr-2"></i> Report Incident
        </a>
    </div>

    <!-- Project Lookup Helper -->
    <?php
    $projectLookup = [];
    if (!empty($projects)) {
        foreach ($projects as $project) {
            $projectLookup[$project['id']] = $project['name'] ?? ($project['project_name'] ?? 'Unknown');
        }
    }
    ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Projects</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>" <?= request()->getGet('project_id') == $project['id'] ? 'selected' : '' ?>>
                                <?= $project['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="incident_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Types</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= $type['id'] ?>" <?= request()->getGet('incident_type_id') == $type['id'] ? 'selected' : '' ?>>
                                <?= $type['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
                    <select name="severity_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Levels</option>
                        <?php foreach ($severities as $sev): ?>
                            <option value="<?= $sev['id'] ?>" <?= request()->getGet('severity_id') == $sev['id'] ? 'selected' : '' ?>>
                                <?= $sev['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Status</option>
                        <option value="reported" <?= request()->getGet('status') == 'reported' ? 'selected' : '' ?>>Reported</option>
                        <option value="investigating" <?= request()->getGet('status') == 'investigating' ? 'selected' : '' ?>>Investigating</option>
                        <option value="under_review" <?= request()->getGet('status') == 'under_review' ? 'selected' : '' ?>>Under Review</option>
                        <option value="resolved" <?= request()->getGet('status') == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                        <option value="closed" <?= request()->getGet('status') == 'closed' ? 'selected' : '' ?>>Closed</option>
                        <option value="reopened" <?= request()->getGet('status') == 'reopened' ? 'selected' : '' ?>>Reopened</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="<?= base_url('incident-safety/incidents') ?>" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Incidents Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Severity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">People Affected</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (count($incidents) > 0): ?>
                        <?php foreach ($incidents as $incident): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-gray-900"><?= $incident['incident_code'] ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="<?= base_url('incident-safety/incidents/' . $incident['id']) ?>" class="text-indigo-600 hover:text-indigo-800">
                                        <?= substr($incident['title'], 0, 40) ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php foreach ($types as $type): ?>
                                        <?php if ($type['id'] == $incident['incident_type_id']): ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <?= $type['name'] ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $projectLookup[$incident['project_id']] ?? $incident['project_id'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= date('M d, Y', strtotime($incident['incident_date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php 
                                    $severityColors = [
                                        4 => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'name' => 'Critical'],
                                        3 => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'name' => 'High'],
                                        2 => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'name' => 'Medium'],
                                        1 => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'name' => 'Low']
                                    ];
                                    $severity = $severityColors[$incident['severity_id']] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'name' => 'Unknown'];
                                    ?>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $severity['bg'] ?> <?= $severity['text'] ?>">
                                        <?= $severity['name'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php if ($incident['affected_people_count'] > 0): ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <?= $incident['affected_people_count'] ?> people
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">None</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php
                                    $statusColors = [
                                        'reported' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                                        'investigating' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                                        'under_review' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
                                        'resolved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                                        'closed' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
                                        'reopened' => ['bg' => 'bg-red-100', 'text' => 'text-red-800']
                                    ];
                                    $status = $statusColors[$incident['status']] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
                                    ?>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $status['bg'] ?> <?= $status['text'] ?>">
                                        <?= ucfirst(str_replace('_', ' ', $incident['status'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="<?= base_url('incident-safety/incidents/' . $incident['id']) ?>" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                        View <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2 block opacity-50"></i>
                                No incidents found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="border-t p-4 bg-gray-50">
            <?= $pager->links() ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
