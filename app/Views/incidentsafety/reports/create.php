<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Create Report<?= $this->endSection() ?>

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
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-plus-circle mr-2"></i>Create Safety Report</h1>
            <p class="text-gray-600 mt-1">Generate a new safety report for a project</p>
        </div>
        <a href="<?= base_url('incident-safety/reports') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Reports
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

    <form action="<?= base_url('incident-safety/reports/store') ?>" method="post" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Report Information Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-info-circle mr-2"></i>Report Information</h3>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Type <span class="text-red-600">*</span></label>
                    <select name="report_type" required
                            class="w-full px-3 py-2 border <?= session()->has('errors.report_type') ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Type</option>
                        <option value="daily" <?= old('report_type') == 'daily' ? 'selected' : '' ?>>Daily</option>
                        <option value="weekly" <?= old('report_type') == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                        <option value="monthly" <?= old('report_type') == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                        <option value="quarterly" <?= old('report_type') == 'quarterly' ? 'selected' : '' ?>>Quarterly</option>
                        <option value="annual" <?= old('report_type') == 'annual' ? 'selected' : '' ?>>Annual</option>
                    </select>
                    <?php if (session()->has('errors.report_type')): ?>
                        <p class="text-red-600 text-sm mt-1"><?= session('errors.report_type') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Period Start <span class="text-red-600">*</span></label>
                    <input type="date" name="report_period_start" required
                           class="w-full px-3 py-2 border <?= session()->has('errors.report_period_start') ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('report_period_start') ?>">
                    <?php if (session()->has('errors.report_period_start')): ?>
                        <p class="text-red-600 text-sm mt-1"><?= session('errors.report_period_start') ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Period End <span class="text-red-600">*</span></label>
                    <input type="date" name="report_period_end" required
                           class="w-full px-3 py-2 border <?= session()->has('errors.report_period_end') ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('report_period_end') ?>">
                    <?php if (session()->has('errors.report_period_end')): ?>
                        <p class="text-red-600 text-sm mt-1"><?= session('errors.report_period_end') ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Safety Statistics Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-chart-line mr-2"></i>Safety Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Incidents Reported</label>
                    <input type="number" name="total_incidents_reported" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('total_incidents_reported') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Near Misses</label>
                    <input type="number" name="total_near_misses" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('total_near_misses') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Injured Workers</label>
                    <input type="number" name="total_injured_workers" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('total_injured_workers') ?>">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lost Time Incidents</label>
                    <input type="number" name="lost_time_incidents" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('lost_time_incidents') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Safety Audits Conducted</label>
                    <input type="number" name="safety_audits_conducted" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('safety_audits_conducted') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Training Sessions Held</label>
                    <input type="number" name="training_sessions_held" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           value="<?= old('training_sessions_held') ?>">
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
                              placeholder="Summarize key safety achievements..."><?= old('key_highlights') ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Challenges Identified</label>
                    <textarea name="challenges_identified" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Describe any challenges or areas of concern..."><?= old('challenges_identified') ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recommendations</label>
                    <textarea name="recommendations" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Provide recommendations for improvement..."><?= old('recommendations') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Report Document Section -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-file-pdf mr-2"></i>Report Document</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Report File (PDF, DOC, DOCX)</label>
                <input type="file" name="report_file_path" accept=".pdf,.doc,.docx"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-sm text-gray-600 mt-2">Optional: Upload the generated report file</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between gap-4">
            <a href="<?= base_url('incident-safety/reports') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-times mr-2"></i> Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-save mr-2"></i> Create Report
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
