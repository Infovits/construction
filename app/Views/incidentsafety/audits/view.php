<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Audit Details<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.conformance-badge { 
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 0.875rem;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-clipboard-check mr-2"></i>Audit Details</h1>
            <p class="text-gray-600 text-sm mt-1"><?= $audit['audit_code'] ?? 'Audit' ?></p>
        </div>
        <a href="<?= base_url('incident-safety/audits') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </div>

    <!-- Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex justify-between items-center">
            <span><?= session()->getFlashdata('success') ?></span>
            <button type="button" class="text-green-800 hover:text-green-900 text-xl">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Audit Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Audit Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Audit Code</label>
                        <p class="text-lg font-semibold text-gray-900"><?= $audit['audit_code'] ?? 'N/A' ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Audit Type</label>
                        <p>
                            <?php 
                            $auditTypeColors = [
                                'routine' => 'bg-blue-100 text-blue-800',
                                'incident_related' => 'bg-red-100 text-red-800',
                                'compliance' => 'bg-green-100 text-green-800',
                                'follow_up' => 'bg-purple-100 text-purple-800'
                            ];
                            $typeClass = $auditTypeColors[$audit['audit_type'] ?? ''] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="<?= $typeClass ?> px-3 py-1 rounded-full text-sm font-medium">
                                <?= ucfirst(str_replace('_', ' ', $audit['audit_type'] ?? '')) ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                        <p class="text-lg font-semibold text-gray-900"><?= $audit['project_name'] ?? 'N/A' ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Auditor</label>
                        <p class="text-lg font-semibold text-gray-900"><?= $audit['auditor_name'] ?? 'N/A' ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Audit Date</label>
                        <p class="text-lg font-semibold text-gray-900"><?= date('M d, Y', strtotime($audit['audit_date'])) ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <p>
                            <?php 
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'reported' => 'bg-blue-100 text-blue-800',
                                'addressed' => 'bg-purple-100 text-purple-800'
                            ];
                            $statusClass = $statusColors[$audit['status'] ?? ''] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="<?= $statusClass ?> px-3 py-1 rounded-full text-sm font-medium">
                                <?= ucfirst(str_replace('_', ' ', $audit['status'] ?? '')) ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Audit Scope</label>
                    <p class="text-gray-700 whitespace-pre-wrap"><?= nl2br($audit['audit_scope'] ?? 'No scope defined') ?></p>
                </div>
            </div>

            <!-- Findings Summary -->
            <?php if (!empty($audit['findings_summary'])): ?>
                <div class="bg-white rounded-lg shadow-sm border p-6 mt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b">
                        <i class="fas fa-list-check mr-2"></i>Findings Summary
                    </h3>
                    <p class="text-gray-700 whitespace-pre-wrap"><?= nl2br($audit['findings_summary']) ?></p>
                </div>
            <?php endif; ?>

            <!-- Corrective Actions Timeline -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b">
                    <i class="fas fa-clock mr-2"></i>Corrective Actions Timeline
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date for Corrections</label>
                        <p class="text-lg font-semibold text-gray-900">
                            <?= !empty($audit['due_date_for_corrections']) ? date('M d, Y', strtotime($audit['due_date_for_corrections'])) : 'N/A' ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Follow-up Date</label>
                        <p class="text-lg font-semibold text-gray-900">
                            <?= !empty($audit['follow_up_date']) ? date('M d, Y', strtotime($audit['follow_up_date'])) : 'N/A' ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Attached Documents -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b">
                    <i class="fas fa-file-pdf mr-2"></i>Audit Document
                </h3>
                <?php if (!empty($audit['document_path'])): ?>
                    <a href="<?= base_url('incident-safety/audits/' . $audit['id'] . '/document') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors" download>
                        <i class="fas fa-download mr-2"></i> Download Document
                    </a>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-file mr-1"></i><?= basename($audit['document_path']) ?>
                    </p>
                <?php else: ?>
                    <p class="text-gray-600 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>No document uploaded for this audit.
                    </p>
                <?php endif; ?>
            </div>


            <!-- Audit Findings List -->
            <?php if (!empty($findings)): ?>
                <div class="bg-white rounded-lg shadow-sm border p-6 mt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b">
                        <i class="fas fa-exclamation-circle mr-2"></i>Individual Findings (<?= count($findings) ?>)
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Finding #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Severity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Responsible</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($findings as $finding): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <strong class="text-gray-900"><?= $finding['finding_number'] ?></strong>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700"><?= $finding['category'] ?? 'N/A' ?></td>
                                        <td class="px-6 py-4 text-gray-700"><?= $finding['finding_description'] ?? 'N/A' ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php 
                                            $severityColors = [
                                                'critical' => 'bg-red-100 text-red-800',
                                                'major' => 'bg-orange-100 text-orange-800',
                                                'minor' => 'bg-blue-100 text-blue-800'
                                            ];
                                            $sClass = $severityColors[$finding['severity']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="<?= $sClass ?> px-3 py-1 rounded-full text-xs font-medium">
                                                <?= ucfirst($finding['severity'] ?? '') ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php 
                                            $findStatusColors = [
                                                'open' => 'bg-red-100 text-red-800',
                                                'in_progress' => 'bg-yellow-100 text-yellow-800',
                                                'closed' => 'bg-green-100 text-green-800'
                                            ];
                                            $fClass = $findStatusColors[$finding['status']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="<?= $fClass ?> px-3 py-1 rounded-full text-xs font-medium">
                                                <?= ucfirst($finding['status'] ?? '') ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700"><?= $finding['responsible_name'] ?? 'N/A' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Statistics Card Sidebar -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Conformance Rate</h3>
                <div class="text-center mb-6">
                    <div class="relative inline-flex items-center justify-center w-32 h-32 rounded-full bg-gray-100 mb-4">
                        <div class="text-center">
                            <p class="text-4xl font-bold text-green-600"><?= $audit['conformance_percentage'] ?? 0 ?>%</p>
                        </div>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500" style="width: <?= $audit['conformance_percentage'] ?? 0 ?>%"></div>
                    </div>
                </div>

                <div class="border-t pt-4 space-y-4">
                    <div class="border-l-4 border-gray-500 pl-4">
                        <p class="text-sm text-gray-600">Observations</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $audit['total_observations'] ?? 0 ?></p>
                    </div>

                    <div class="border-l-4 border-red-500 pl-4">
                        <p class="text-sm text-gray-600">Critical</p>
                        <p class="text-3xl font-bold text-red-600"><?= $audit['critical_findings'] ?? 0 ?></p>
                    </div>

                    <div class="border-l-4 border-orange-500 pl-4">
                        <p class="text-sm text-gray-600">Major</p>
                        <p class="text-3xl font-bold text-orange-600"><?= $audit['major_findings'] ?? 0 ?></p>
                    </div>

                    <div class="border-l-4 border-blue-500 pl-4">
                        <p class="text-sm text-gray-600">Minor</p>
                        <p class="text-3xl font-bold text-blue-600"><?= $audit['minor_findings'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
