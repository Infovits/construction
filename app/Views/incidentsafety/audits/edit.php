<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Edit Audit<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.form-section { 
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.form-section > * + * {
    margin-top: 1rem;
}
.form-section-title { 
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-edit mr-2"></i>Edit Safety Audit</h1>
            <p class="text-gray-600 mt-1">Update audit information and findings</p>
        </div>
        <a href="<?= base_url('incident-safety/audits/'.$audit['id']) ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Audit
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

    <form action="<?= base_url('incident-safety/audits/update/'.$audit['id']) ?>" method="post" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Audit Information Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-info-circle mr-2"></i>Audit Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project <span class="text-red-600">*</span></label>
                    <select name="project_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Project</option>
                        <?php if (!empty($projects)): ?>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>" 
                                    <?= ($audit['project_id'] ?? '') == $project['id'] ? 'selected' : '' ?>>
                                    <?= $project['name'] ?? ($project['project_name'] ?? 'N/A') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Audit Type <span class="text-red-600">*</span></label>
                    <select name="audit_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Type</option>
                        <option value="routine" <?= ($audit['audit_type'] ?? '') == 'routine' ? 'selected' : '' ?>>Routine</option>
                        <option value="incident_related" <?= ($audit['audit_type'] ?? '') == 'incident_related' ? 'selected' : '' ?>>Incident Related</option>
                        <option value="compliance" <?= ($audit['audit_type'] ?? '') == 'compliance' ? 'selected' : '' ?>>Compliance</option>
                        <option value="follow_up" <?= ($audit['audit_type'] ?? '') == 'follow_up' ? 'selected' : '' ?>>Follow Up</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Audit Date <span class="text-red-600">*</span></label>
                    <input type="date" name="audit_date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $audit['audit_date'] ?? '' ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Auditor <span class="text-red-600">*</span></label>
                    <select name="auditor_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Auditor</option>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" 
                                    <?= ($audit['auditor_id'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                                    <?= trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: ($user['username'] ?? 'User') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Audit Scope</label>
                <textarea name="audit_scope" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Describe the scope of this audit..."><?= $audit['audit_scope'] ?? '' ?></textarea>
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
                           value="<?= $audit['total_observations'] ?? '' ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Critical Findings</label>
                    <input type="number" name="critical_findings" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $audit['critical_findings'] ?? '' ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Major Findings</label>
                    <input type="number" name="major_findings" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $audit['major_findings'] ?? '' ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minor Findings</label>
                    <input type="number" name="minor_findings" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $audit['minor_findings'] ?? '' ?>">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Conformance Percentage (%)</label>
                <input type="number" name="conformance_percentage" min="0" max="100"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       value="<?= $audit['conformance_percentage'] ?? '' ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Non-Conformities</label>
                <input type="number" name="non_conformities" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       value="<?= $audit['non_conformities'] ?? '' ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Findings Summary</label>
                <textarea name="findings_summary" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Summarize the key findings..."><?= $audit['findings_summary'] ?? '' ?></textarea>
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
                           value="<?= $audit['due_date_for_corrections'] ?? '' ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Follow-up Date</label>
                    <input type="date" name="follow_up_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $audit['follow_up_date'] ?? '' ?>">
                </div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-toggle-on mr-2"></i>Status</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Audit Status</label>
                <select name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="draft" <?= ($audit['status'] ?? '') == 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="completed" <?= ($audit['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="reported" <?= ($audit['status'] ?? '') == 'reported' ? 'selected' : '' ?>>Reported</option>
                    <option value="addressed" <?= ($audit['status'] ?? '') == 'addressed' ? 'selected' : '' ?>>Addressed</option>
                </select>
            </div>
        </div>

        <!-- Document Upload Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-file-pdf mr-2"></i>Audit Document</h3>
            
            <?php if (!empty($audit['document_path'])): ?>
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        Current document: <strong><?= basename($audit['document_path']) ?></strong>
                    </p>
                    <p class="text-xs text-blue-600 mt-1">Upload a new file to replace it</p>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Audit Report (PDF, DOC, DOCX)</label>
                <input type="file" name="document_path" accept=".pdf,.doc,.docx"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-sm text-gray-600 mt-2">Optional: Upload a new audit report document to replace the existing one</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between gap-4">
            <a href="<?= base_url('incident-safety/audits/'.$audit['id']) ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-times mr-2"></i> Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-save mr-2"></i> Update Audit
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
