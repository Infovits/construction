<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Report Incident<?= $this->endSection() ?>

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
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-exclamation-circle mr-2"></i>Report New Incident</h1>
            <p class="text-gray-600 mt-1">Document incident details, affected people, and immediate actions taken</p>
        </div>
        <a href="<?= base_url('incident-safety/incidents') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </div>

    <form method="POST" action="<?= base_url('incident-safety/incidents/store') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- Alerts -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex justify-between items-center">
                <span><?= session()->getFlashdata('error') ?></span>
                <button type="button" class="text-red-800 hover:text-red-900 text-xl">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Basic Information Section -->
        <div class="form-section">
            <h3 class="form-section-title">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project *</label>
                    <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>"><?= $project['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Incident Type *</label>
                    <select name="incident_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Select Type</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Severity Level *</label>
                    <select name="severity_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Select Severity</option>
                        <?php foreach ($severities as $sev): ?>
                            <option value="<?= $sev['id'] ?>"><?= $sev['name'] ?> - <?= $sev['description'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Incident Location</label>
                    <input type="text" name="location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Main Building Site">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Incident Title *</label>
                <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Brief description of incident">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Detailed Description *</label>
                <textarea name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required rows="4" placeholder="Provide detailed information about the incident..."></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Incident Date *</label>
                    <input type="date" name="incident_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Incident Time</label>
                    <input type="time" name="incident_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <!-- People Affected Section -->
        <div class="form-section">
            <h3 class="form-section-title">People Affected</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of People Affected</label>
                    <input type="number" name="affected_people_count" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Affected People Names</label>
                    <input type="text" name="affected_people_names" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., John Smith, Mary Johnson">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Witnesses</label>
                    <input type="number" name="witness_count" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Witness Names</label>
                    <input type="text" name="witness_names" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Peter Brown, Sarah Davis">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Injuries Sustained</label>
                <textarea name="injuries_sustained" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="Describe any injuries sustained..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Property Damage Description</label>
                <textarea name="property_damage_description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="Describe any property damage..."></textarea>
            </div>
        </div>

        <!-- Immediate Actions Section -->
        <div class="form-section">
            <h3 class="form-section-title">Immediate Actions</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Actions Taken</label>
                <textarea name="immediate_actions_taken" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" rows="4" placeholder="Describe immediate actions taken to address the incident..."></textarea>
            </div>
        </div>

        <!-- Photo Upload Section -->
        <div class="form-section">
            <h3 class="form-section-title">Photos & Evidence</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Photos</label>
                <input type="file" name="photos[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" multiple accept="image/*">
                <p class="text-sm text-gray-600 mt-1">Upload photos as evidence of the incident</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo Type</label>
                    <select name="photo_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="evidence">Evidence</option>
                        <option value="overview">Overview</option>
                        <option value="before">Before</option>
                        <option value="after">After</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Photo Description</label>
                <textarea name="photo_description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" rows="2" placeholder="Describe what's in the photos..."></textarea>
            </div>
        </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4 pt-6">
            <button type="submit" class="flex-1 px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-plus-circle mr-2"></i> Report Incident
            </button>
            <a href="<?= base_url('incident-safety/incidents') ?>" class="flex-1 px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors text-center">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
