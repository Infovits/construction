<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Create New Milestone
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Create New Milestone</h1>
            <a href="<?= base_url('admin/milestones') ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Milestones
            </a>
        </div>
        <div class="p-6">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/milestones/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Basic Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Milestone Title *</label>
                            <input type="text" id="title" name="title"
                                   value="<?= old('title') ?>" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter milestone title">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Describe this milestone..."><?= old('description') ?></textarea>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project *</label>
                            <select id="project_id" name="project_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Project</option>
                                <?php foreach($projects as $project): ?>
                                    <option value="<?= $project['id'] ?>"
                                            <?= (old('project_id', $selected_project ?? '')) == $project['id'] ? 'selected' : '' ?>>
                                        <?= esc($project['name']) ?> (<?= esc($project['project_code']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select id="priority" name="priority"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="low" <?= old('priority', 'medium') == 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= old('priority', 'medium') == 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= old('priority', 'medium') == 'high' ? 'selected' : '' ?>>High</option>
                            <option value="urgent" <?= old('priority', 'medium') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-2">Initial Progress (%)</label>
                        <input type="number" id="progress_percentage" name="progress_percentage"
                               min="0" max="100" value="<?= old('progress_percentage', 0) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                        <select id="assigned_to" name="assigned_to"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Unassigned</option>
                            <?php foreach($users as $user): ?>
                                <option value="<?= $user['id'] ?>"
                                        <?= old('assigned_to') == $user['id'] ? 'selected' : '' ?>>
                                    <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" id="start_date" name="start_date"
                               value="<?= old('start_date') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Timeline -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Timeline</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="planned_end_date" class="block text-sm font-medium text-gray-700 mb-2">Planned End Date *</label>
                            <input type="date" id="planned_end_date" name="planned_end_date"
                                   value="<?= old('planned_end_date') ?>" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">When should this milestone be completed?</p>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Initial Status</label>
                            <select id="status" name="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="pending" <?= old('status', 'pending') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="in_progress" <?= old('status', 'pending') == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Cost Information -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Cost Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-2">Estimated Cost (MWK)</label>
                            <input type="number" id="estimated_cost" name="estimated_cost"
                                   value="<?= old('estimated_cost', 0) ?>" min="0" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00">
                            <p class="text-xs text-gray-500 mt-1">Estimated cost for this milestone in MWK</p>
                        </div>

                        <div>
                            <label for="actual_cost" class="block text-sm font-medium text-gray-700 mb-2">Actual Cost (MWK)</label>
                            <input type="number" id="actual_cost" name="actual_cost"
                                   value="<?= old('actual_cost', 0) ?>" min="0" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00">
                            <p class="text-xs text-gray-500 mt-1">Actual cost incurred (can be updated later)</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="<?= base_url('admin/milestones') ?>" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-save mr-2"></i>Create Milestone
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-set start date when project is selected and no start date is set
    const projectSelect = document.getElementById('project_id');
    const startDateInput = document.getElementById('start_date');

    projectSelect.addEventListener('change', function() {
        if (!startDateInput.value) {
            // Set start date to today if no date is set
            const today = new Date().toISOString().split('T')[0];
            startDateInput.value = today;
        }
    });

    // Validate dates
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('planned_end_date');

    function validateDates() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && startDate > endDate) {
            alert('End date must be after start date');
            endDateInput.value = '';
        }
    }

    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);

    // Auto-set progress based on status
    const statusSelect = document.getElementById('status');
    const progressInput = document.getElementById('progress_percentage');

    statusSelect.addEventListener('change', function() {
        const status = this.value;

        if (status === 'in_progress' && progressInput.value == 0) {
            progressInput.value = 25;
        }
    });
});
</script>
<?= $this->endSection() ?>
