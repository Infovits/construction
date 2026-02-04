<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Create Audit<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.form-section { @apply bg-white rounded-lg shadow-sm border p-6 space-y-4 mb-6; }
.form-section-title { @apply text-lg font-semibold text-gray-900 mb-4 pb-4 border-b; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-plus-circle mr-2"></i>Create Safety Audit</h1>
            <p class="text-gray-600 mt-1">Schedule and document a new safety audit</p>
        </div>
        <a href="<?= base_url('incident-safety/audits') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Audits
        </a>
    </div>

    <!-- Alerts -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <strong>Validation Errors:</strong>
            <ul class="mt-2 space-y-1">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li>• <?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('incident-safety/audits/store') ?>" method="post" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Audit Information Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-info-circle mr-2"></i>Audit Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project <span class="text-red-600">*</span></label>
                    <select name="project_id" required
                            class="w-full px-3 py-2 border <?= session()->has('errors.project_id') ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Project</option>
                        <?php if (!empty($projects)): ?>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>" 
                                    <?= old('project_id') == $project['id'] ? 'selected' : '' ?>>
                                    <?= $project['name'] ?? ($project['project_name'] ?? 'N/A') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (session()->has('errors.project_id')): ?>
                        <p class="text-red-600 text-sm mt-1"><?= session('errors.project_id') ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Audit Type <span class="text-red-600">*</span></label>
                    <select name="audit_type" required
                            class="w-full px-3 py-2 border <?= session()->has('errors.audit_type') ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Type</option>
                        <option value="routine" <?= old('audit_type') == 'routine' ? 'selected' : '' ?>>Routine</option>
                        <option value="incident_related" <?= old('audit_type') == 'incident_related' ? 'selected' : '' ?>>Incident Related</option>
                        <option value="compliance" <?= old('audit_type') == 'compliance' ? 'selected' : '' ?>>Compliance</option>
                        <option value="follow_up" <?= old('audit_type') == 'follow_up' ? 'selected' : '' ?>>Follow Up</option>
                    </select>
                    <?php if (session()->has('errors.audit_type')): ?>
                        <p class="text-red-600 text-sm mt-1"><?= session('errors.audit_type') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Audit Date <span class="text-red-600">*</span></label>
                    <input type="date" name="audit_date" required
                           class="w-full px-3 py-2 border <?= session()->has('errors.audit_date') ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('audit_date') ?? date('Y-m-d') ?>">
                    <?php if (session()->has('errors.audit_date')): ?>
                        <p class="text-red-600 text-sm mt-1"><?= session('errors.audit_date') ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Auditor <span class="text-red-600">*</span></label>
                    <select name="auditor_id" required
                            class="w-full px-3 py-2 border <?= session()->has('errors.auditor_id') ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Auditor</option>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" 
                                    <?= old('auditor_id') == $user['id'] ? 'selected' : '' ?>>
                                    <?= trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: ($user['username'] ?? 'User') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (session()->has('errors.auditor_id')): ?>
                        <p class="text-red-600 text-sm mt-1"><?= session('errors.auditor_id') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Audit Scope</label>
                <textarea name="audit_scope" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Describe the scope of this audit..."><?= old('audit_scope') ?></textarea>
            </div>
        </div>

        <!-- Findings Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-list-check mr-2"></i>Audit Findings</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Observations</label>
                    <input type="number" name="total_observations" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('total_observations') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Critical Findings</label>
                    <input type="number" name="critical_findings" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('critical_findings') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Major Findings</label>
                    <input type="number" name="major_findings" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('major_findings') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minor Findings</label>
                    <input type="number" name="minor_findings" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('minor_findings') ?>">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Conformance Percentage (%)</label>
                <input type="number" name="conformance_percentage" min="0" max="100"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       value="<?= old('conformance_percentage') ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Findings Summary</label>
                <textarea name="findings_summary" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Summarize the key findings..."><?= old('findings_summary') ?></textarea>
            </div>
        </div>

        <!-- Corrections Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-clock mr-2"></i>Corrective Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date for Corrections</label>
                    <input type="date" name="due_date_for_corrections"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('due_date_for_corrections') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Follow-up Date</label>
                    <input type="date" name="follow_up_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('follow_up_date') ?>">
                </div>
            </div>
        </div>

        <!-- Document Upload Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-file-pdf mr-2"></i>Audit Document</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Audit Report (PDF, DOC, DOCX)</label>
                <input type="file" name="document_path" accept=".pdf,.doc,.docx"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-sm text-gray-600 mt-2">Optional: Upload the audit report document</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between gap-4">
            <a href="<?= base_url('incident-safety/audits') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-times mr-2"></i> Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-save mr-2"></i> Create Audit
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
