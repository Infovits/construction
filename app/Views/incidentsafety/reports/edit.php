<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Edit Report<?= $this->endSection() ?>

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
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-edit mr-2"></i>Edit Safety Report</h1>
            <p class="text-gray-600 mt-1">Update report information and statistics</p>
        </div>
        <a href="<?= base_url('incident-safety/reports/'.$report['id']) ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Report
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

    <form action="<?= base_url('incident-safety/reports/update/'.$report['id']) ?>" method="post" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Report Information Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-info-circle mr-2"></i>Report Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project <span class="text-red-600">*</span></label>
                    <select name="project_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Project</option>
                        <?php if (!empty($projects)): ?>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>" 
                                    <?= ($report['project_id'] ?? '') == $project['id'] ? 'selected' : '' ?>>
                                    <?= $project['name'] ?? ($project['project_name'] ?? 'N/A') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Type <span class="text-red-600">*</span></label>
                    <select name="report_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Type</option>
                        <option value="daily" <?= ($report['report_type'] ?? '') == 'daily' ? 'selected' : '' ?>>Daily</option>
                        <option value="weekly" <?= ($report['report_type'] ?? '') == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                        <option value="monthly" <?= ($report['report_type'] ?? '') == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                        <option value="quarterly" <?= ($report['report_type'] ?? '') == 'quarterly' ? 'selected' : '' ?>>Quarterly</option>
                        <option value="annual" <?= ($report['report_type'] ?? '') == 'annual' ? 'selected' : '' ?>>Annual</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Period Start <span class="text-red-600">*</span></label>
                    <input type="date" name="report_period_start" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $report['report_period_start'] ?? '' ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Period End <span class="text-red-600">*</span></label>
                    <input type="date" name="report_period_end" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $report['report_period_end'] ?? '' ?>">
                </div>
            </div>
        </div>

        <!-- Safety Statistics Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-chart-line mr-2"></i>Safety Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Incidents</label>
                    <input type="number" name="total_incidents" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $report['total_incidents'] ?? 0 ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Near Misses</label>
                    <input type="number" name="total_near_misses" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $report['total_near_misses'] ?? 0 ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Injured Workers</label>
                    <input type="number" name="total_injured_workers" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $report['total_injured_workers'] ?? 0 ?>">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lost Time Incidents</label>
                    <input type="number" name="lost_time_incidents" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $report['lost_time_incidents'] ?? 0 ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Safety Audits Conducted</label>
                    <input type="number" name="safety_audits_conducted" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $report['safety_audits_conducted'] ?? 0 ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Training Sessions Held</label>
                    <input type="number" name="training_sessions_held" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= $report['training_sessions_held'] ?? 0 ?>">
                </div>
            </div>
        </div>

        <!-- Report Content Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-file-text mr-2"></i>Report Content</h3>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Key Highlights</label>
                    <textarea name="key_highlights" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Summarize key safety achievements..."><?= $report['key_highlights'] ?? '' ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Challenges Identified</label>
                    <textarea name="challenges_identified" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Describe any challenges or areas of concern..."><?= $report['challenges_identified'] ?? '' ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recommendations</label>
                    <textarea name="recommendations" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Provide recommendations for improvement..."><?= $report['recommendations'] ?? '' ?></textarea>
                </div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-toggle-on mr-2"></i>Status</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Report Status</label>
                <select name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="draft" <?= ($report['status'] ?? '') == 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="pending_review" <?= ($report['status'] ?? '') == 'pending_review' ? 'selected' : '' ?>>Pending Review</option>
                    <option value="approved" <?= ($report['status'] ?? '') == 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="published" <?= ($report['status'] ?? '') == 'published' ? 'selected' : '' ?>>Published</option>
                </select>
            </div>
        </div>

        <!-- Report Document Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-file-pdf mr-2"></i>Report Document</h3>
            
            <?php if (!empty($report['report_file_path'])): ?>
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        Current document: <strong><?= basename($report['report_file_path']) ?></strong>
                    </p>
                    <p class="text-xs text-blue-600 mt-1">Upload a new file to replace it</p>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Report File (PDF, DOC, DOCX)</label>
                <input type="file" name="report_file_path" accept=".pdf,.doc,.docx"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-sm text-gray-600 mt-2">Optional: Upload a new report file to replace the existing one</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between gap-4">
            <a href="<?= base_url('incident-safety/reports/'.$report['id']) ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-times mr-2"></i> Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-save mr-2"></i> Update Report
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
