<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Safety Audits<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.audit-card { 
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
    padding: 1.5rem;
    transition: box-shadow 0.2s;
}
.audit-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-clipboard-check mr-2"></i>Safety Audits</h1>
            <p class="text-gray-600 mt-1">Track and manage safety audits and compliance</p>
        </div>
        <a href="<?= base_url('incident-safety/audits/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            <i class="fas fa-plus mr-2"></i> New Audit
        </a>
    </div>

    <!-- Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex justify-between items-center">
            <span><?= session()->getFlashdata('success') ?></span>
            <button type="button" class="text-green-800 hover:text-green-900 text-xl">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex justify-between items-center">
            <span><?= session()->getFlashdata('error') ?></span>
            <button type="button" class="text-red-800 hover:text-red-900 text-xl">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form method="GET" action="<?= base_url('incident-safety/audits') ?>" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Projects</option>
                        <?php if (!empty($projects)): ?>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>" 
                                    <?= request()->getGet('project_id') == $project['id'] ? 'selected' : '' ?>>
                                    <?= $project['name'] ?? ($project['project_name'] ?? 'N/A') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Audit Type</label>
                    <select name="audit_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Types</option>
                        <option value="routine" <?= request()->getGet('audit_type') == 'routine' ? 'selected' : '' ?>>Routine</option>
                        <option value="incident_related" <?= request()->getGet('audit_type') == 'incident_related' ? 'selected' : '' ?>>Incident Related</option>
                        <option value="compliance" <?= request()->getGet('audit_type') == 'compliance' ? 'selected' : '' ?>>Compliance</option>
                        <option value="follow_up" <?= request()->getGet('audit_type') == 'follow_up' ? 'selected' : '' ?>>Follow Up</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Status</option>
                        <option value="draft" <?= request()->getGet('status') == 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="completed" <?= request()->getGet('status') == 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="reported" <?= request()->getGet('status') == 'reported' ? 'selected' : '' ?>>Reported</option>
                        <option value="addressed" <?= request()->getGet('status') == 'addressed' ? 'selected' : '' ?>>Addressed</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Audits Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Audit Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Auditor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Conformance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (!empty($audits)): ?>
                        <?php foreach ($audits as $audit): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <strong class="text-gray-900"><?= $audit['audit_code'] ?></strong>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700"><?= $audit['project_name'] ?? 'N/A' ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $auditTypeColors = [
                                        'routine' => 'blue',
                                        'incident_related' => 'red',
                                        'compliance' => 'green',
                                        'follow_up' => 'purple'
                                    ];
                                    $color = $auditTypeColors[$audit['audit_type'] ?? ''] ?? 'gray';
                                    $bgClass = "bg-{$color}-100";
                                    $textClass = "text-{$color}-800";
                                    ?>
                                    <span class="<?= $bgClass ?> <?= $textClass ?> px-3 py-1 rounded-full text-xs font-medium">
                                        <?= ucfirst(str_replace('_', ' ', $audit['audit_type'] ?? '')) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700"><?= $audit['auditor_name'] ?? 'N/A' ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700"><?= date('M d, Y', strtotime($audit['audit_date'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-green-500" style="width: <?= $audit['conformance_percentage'] ?? 0 ?>%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700"><?= $audit['conformance_percentage'] ?? 0 ?>%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $statusColors = [
                                        'draft' => 'gray',
                                        'completed' => 'green',
                                        'reported' => 'blue',
                                        'addressed' => 'purple'
                                    ];
                                    $color = $statusColors[$audit['status'] ?? ''] ?? 'gray';
                                    $bgClass = "bg-{$color}-100";
                                    $textClass = "text-{$color}-800";
                                    ?>
                                    <span class="<?= $bgClass ?> <?= $textClass ?> px-3 py-1 rounded-full text-xs font-medium">
                                        <?= ucfirst($audit['status'] ?? '') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <a href="<?= base_url('incident-safety/audits/' . $audit['id']) ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 transition" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('incident-safety/audits/edit/' . $audit['id']) ?>" 
                                           class="text-blue-600 hover:text-blue-900 transition" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteAudit(<?= $audit['id'] ?>)" 
                                                class="text-red-600 hover:text-red-900 transition" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                <p>No audits found. <a href="<?= base_url('incident-safety/audits/create') ?>" class="text-indigo-600 hover:text-indigo-900">Create one</a></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if (!empty($pager)): ?>
        <nav class="mt-4">
            <?= $pager->links() ?>
        </nav>
    <?php endif; ?>
</div>

<script>
function deleteAudit(auditId) {
    if (confirm('Are you sure you want to delete this audit? This action cannot be undone.')) {
        fetch('<?= base_url("incident-safety/audits/delete/") ?>' + auditId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error deleting audit');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the audit');
        });
    }
}
</script>

<?= $this->endSection() ?>
