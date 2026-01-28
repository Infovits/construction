<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quality Inspection Details</h1>
            <p class="text-gray-600">Inspection #<?= esc($inspection['inspection_number']) ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/quality-inspections') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
            <?php if ($inspection['status'] === 'pending'): ?>
                <a href="<?= base_url('admin/quality-inspections/' . $inspection['id'] . '/edit') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                    Edit
                </a>
                <?php if ($inspection['inspector_id'] == session('user_id')): ?>
                    <a href="<?= base_url('admin/quality-inspections/' . $inspection['id'] . '/inspect') ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i data-lucide="clipboard-check" class="w-4 h-4 mr-2"></i>
                        Conduct Inspection
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <a href="<?= base_url('admin/quality-inspections/export/pdf') ?>" 
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
               target="_blank">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                Export PDF
            </a>
        </div>
    </div>

    <!-- Inspection Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Inspection Number</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['inspection_number']) ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Inspection Type</label>
                    <div class="text-sm text-gray-900"><?= ucfirst($inspection['inspection_type']) ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        <?php
                        switch($inspection['status']) {
                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                            case 'passed': echo 'bg-green-100 text-green-800'; break;
                            case 'failed': echo 'bg-red-100 text-red-800'; break;
                            case 'conditional': echo 'bg-orange-100 text-orange-800'; break;
                            default: echo 'bg-gray-100 text-gray-800'; break;
                        }
                        ?>">
                        <?= ucfirst($inspection['status']) ?>
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Inspection Date</label>
                    <div class="text-sm text-gray-900"><?= date('M j, Y \a\t g:i A', strtotime($inspection['inspection_date'])) ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Overall Grade</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['overall_grade'] ?? 'N/A') ?></div>
                </div>
            </div>
        </div>

        <!-- Material Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Material Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Material Name</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['material_name'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Material Code</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['material_code'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Unit</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['unit'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">GRN Number</label>
                    <?php if (!empty($inspection['grn_number'])): ?>
                        <a href="<?= base_url('admin/goods-receipt/' . $inspection['grn_item_id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800">
                            <?= esc($inspection['grn_number']) ?>
                        </a>
                    <?php else: ?>
                        <div class="text-sm text-gray-500">N/A</div>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Supplier</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['supplier_name'] ?? 'N/A') ?></div>
                </div>
            </div>
        </div>

        <!-- Inspector Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Inspector Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Inspector</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['inspector_name'] ?? 'Unassigned') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Inspector Email</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['inspector_email'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Department</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['inspector_department'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Phone</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['inspector_phone'] ?? 'N/A') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inspection Results -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Inspection Results</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="text-sm font-medium text-blue-600">Quantity Inspected</div>
                    <div class="text-2xl font-bold text-blue-900"><?= number_format($inspection['quantity_inspected'] ?? 0, 3) ?> <?= esc($inspection['unit'] ?? '') ?></div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="text-sm font-medium text-green-600">Quantity Passed</div>
                    <div class="text-2xl font-bold text-green-900"><?= number_format($inspection['quantity_passed'] ?? 0, 3) ?> <?= esc($inspection['unit'] ?? '') ?></div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="text-sm font-medium text-red-600">Quantity Failed</div>
                    <div class="text-2xl font-bold text-red-900"><?= number_format($inspection['quantity_failed'] ?? 0, 3) ?> <?= esc($inspection['unit'] ?? '') ?></div>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="text-sm font-medium text-purple-600">Pass Rate</div>
                    <?php 
                    $totalInspected = $inspection['quantity_inspected'] ?? 0;
                    $passed = $inspection['quantity_passed'] ?? 0;
                    $passRate = $totalInspected > 0 ? round(($passed / $totalInspected) * 100, 1) : 0;
                    ?>
                    <div class="text-2xl font-bold text-purple-900"><?= $passRate ?>%</div>
                </div>
            </div>

            <!-- Defects & Actions -->
            <?php if (!empty($inspection['defect_description']) || !empty($inspection['corrective_action'])): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Defect Description</label>
                        <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 min-h-20">
                            <?= nl2br(esc($inspection['defect_description'] ?? 'No defects reported')) ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Corrective Action Required</label>
                        <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 min-h-20">
                            <?= nl2br(esc($inspection['corrective_action'] ?? 'No corrective action required')) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Inspector Notes -->
            <?php if (!empty($inspection['inspector_notes'])): ?>
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Inspector Notes</label>
                    <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                        <?= nl2br(esc($inspection['inspector_notes'])) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Inspection Criteria -->
    <?php if (!empty($inspection['criteria'])): ?>
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Inspection Criteria</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <?php 
                    $criteria = json_decode($inspection['criteria'] ?? '{}', true);
                    $criteriaLabels = [
                        'visual' => 'Visual Inspection',
                        'dimensional' => 'Dimensional Check',
                        'material_compliance' => 'Material Compliance',
                        'documentation' => 'Documentation',
                        'packaging' => 'Packaging',
                        'labeling' => 'Labeling',
                        'quantity' => 'Quantity Check',
                        'sample_testing' => 'Sample Testing'
                    ];
                    
                    foreach ($criteriaLabels as $key => $label): ?>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-gray-700 mb-2"><?= $label ?></div>
                            <div class="text-sm font-semibold
                                <?php 
                                $value = $criteria[$key] ?? '';
                                echo $value === 'pass' ? 'text-green-600' : ($value === 'fail' ? 'text-red-600' : 'text-gray-500');
                                ?>">
                                <?= ucfirst($value ?: 'N/A') ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Attachments -->
    <?php if (!empty($inspection['attachments'])): ?>
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Attachments</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($inspection['attachments'] as $attachment): ?>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-700"><?= esc($attachment['filename']) ?></span>
                                <span class="text-xs text-gray-500"><?= esc($attachment['file_size']) ?></span>
                            </div>
                            <div class="flex gap-2">
                                <a href="<?= base_url('uploads/' . esc($attachment['file_path'])) ?>" 
                                   target="_blank" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded hover:bg-blue-200">
                                    <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                                    View
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
lucide.createIcons();
</script>

<?= $this->endSection() ?>