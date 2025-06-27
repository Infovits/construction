<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Project Create/Edit Page -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= isset($project) ? 'Edit Project' : 'Create New Project' ?></h1>
            <p class="text-gray-600">Configure project details and settings</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/projects') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Projects
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                </div>
                <div>
                    <p class="font-medium"><?= session()->getFlashdata('error') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                </div>
                <div>
                    <p class="font-medium"><?= session()->getFlashdata('success') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Project Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form action="<?= isset($project) ? base_url('admin/projects/update/' . $project['id']) : base_url('admin/projects/store') ?>" method="post" class="p-6 space-y-6">
            <?= csrf_field() ?>
            
            <!-- Basic Information Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Project Name *</label>
                        <input type="text" id="name" name="name" required
                               value="<?= old('name', isset($project) ? $project['name'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="project_code" class="block text-sm font-medium text-gray-700 mb-2">Project Code *</label>
                        <input type="text" id="project_code" name="project_code" required
                               value="<?= old('project_code', isset($project) ? $project['project_code'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">Company *</label>
                        <select id="company_id" name="company_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Company</option>
                            <?php foreach($companies as $company): ?>
                                <option value="<?= $company['id'] ?>" 
                                        <?= old('company_id', isset($project) ? $project['company_id'] : '') == $company['id'] ? 'selected' : '' ?>>
                                    <?= esc($company['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                        <select id="client_id" name="client_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Client</option>
                            <?php foreach($clients as $client): ?>
                                <option value="<?= $client['id'] ?>" 
                                        <?= old('client_id', isset($project) ? $project['client_id'] : '') == $client['id'] ? 'selected' : '' ?>>
                                    <?= esc($client['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select id="category_id" name="category_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Category</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                        <?= old('category_id', isset($project) ? $project['category_id'] : '') == $category['id'] ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('description', isset($project) ? $project['description'] : '') ?></textarea>
                </div>
            </div>

            <!-- Project Details Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Project Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label for="project_type" class="block text-sm font-medium text-gray-700 mb-2">Project Type *</label>
                        <select id="project_type" name="project_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Type</option>
                            <option value="residential" <?= old('project_type', isset($project) ? $project['project_type'] : '') == 'residential' ? 'selected' : '' ?>>Residential</option>
                            <option value="commercial" <?= old('project_type', isset($project) ? $project['project_type'] : '') == 'commercial' ? 'selected' : '' ?>>Commercial</option>
                            <option value="industrial" <?= old('project_type', isset($project) ? $project['project_type'] : '') == 'industrial' ? 'selected' : '' ?>>Industrial</option>
                            <option value="infrastructure" <?= old('project_type', isset($project) ? $project['project_type'] : '') == 'infrastructure' ? 'selected' : '' ?>>Infrastructure</option>
                            <option value="renovation" <?= old('project_type', isset($project) ? $project['project_type'] : '') == 'renovation' ? 'selected' : '' ?>>Renovation</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select id="priority" name="priority"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="low" <?= old('priority', isset($project) ? $project['priority'] : '') == 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= old('priority', isset($project) ? $project['priority'] : '') == 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= old('priority', isset($project) ? $project['priority'] : '') == 'high' ? 'selected' : '' ?>>High</option>
                            <option value="urgent" <?= old('priority', isset($project) ? $project['priority'] : '') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="planning" <?= old('status', isset($project) ? $project['status'] : '') == 'planning' ? 'selected' : '' ?>>Planning</option>
                            <option value="active" <?= old('status', isset($project) ? $project['status'] : '') == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="on_hold" <?= old('status', isset($project) ? $project['status'] : '') == 'on_hold' ? 'selected' : '' ?>>On Hold</option>
                            <option value="completed" <?= old('status', isset($project) ? $project['status'] : '') == 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= old('status', isset($project) ? $project['status'] : '') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-2">Progress (%)</label>
                        <input type="number" id="progress_percentage" name="progress_percentage" min="0" max="100"
                               value="<?= old('progress_percentage', isset($project) ? $project['progress_percentage'] : 0) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Financial Information Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="estimated_budget" class="block text-sm font-medium text-gray-700 mb-2">Estimated Budget *</label>
                        <input type="number" id="estimated_budget" name="estimated_budget" step="0.01" required
                               value="<?= old('estimated_budget', isset($project) ? $project['estimated_budget'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="contract_value" class="block text-sm font-medium text-gray-700 mb-2">Contract Value</label>
                        <input type="number" id="contract_value" name="contract_value" step="0.01"
                               value="<?= old('contract_value', isset($project) ? $project['contract_value'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select id="currency" name="currency"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="USD" <?= old('currency', isset($project) ? $project['currency'] : 'USD') == 'USD' ? 'selected' : '' ?>>USD</option>
                            <option value="EUR" <?= old('currency', isset($project) ? $project['currency'] : 'USD') == 'EUR' ? 'selected' : '' ?>>EUR</option>
                            <option value="GBP" <?= old('currency', isset($project) ? $project['currency'] : 'USD') == 'GBP' ? 'selected' : '' ?>>GBP</option>
                            <option value="CAD" <?= old('currency', isset($project) ? $project['currency'] : 'USD') == 'CAD' ? 'selected' : '' ?>>CAD</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Timeline Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" id="start_date" name="start_date"
                               value="<?= old('start_date', isset($project) ? $project['start_date'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="planned_end_date" class="block text-sm font-medium text-gray-700 mb-2">Planned End Date</label>
                        <input type="date" id="planned_end_date" name="planned_end_date"
                               value="<?= old('planned_end_date', isset($project) ? $project['planned_end_date'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="actual_end_date" class="block text-sm font-medium text-gray-700 mb-2">Actual End Date</label>
                        <input type="date" id="actual_end_date" name="actual_end_date"
                               value="<?= old('actual_end_date', isset($project) ? $project['actual_end_date'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Site Information Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Site Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label for="site_address" class="block text-sm font-medium text-gray-700 mb-2">Site Address</label>
                        <textarea id="site_address" name="site_address" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('site_address', isset($project) ? $project['site_address'] : '') ?></textarea>
                    </div>

                    <div>
                        <label for="site_city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" id="site_city" name="site_city"
                               value="<?= old('site_city', isset($project) ? $project['site_city'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="site_state" class="block text-sm font-medium text-gray-700 mb-2">State/Province</label>
                        <input type="text" id="site_state" name="site_state"
                               value="<?= old('site_state', isset($project) ? $project['site_state'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Project Management Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Project Management</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="project_manager_id" class="block text-sm font-medium text-gray-700 mb-2">Project Manager</label>
                        <select id="project_manager_id" name="project_manager_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Project Manager</option>
                            <?php foreach($users as $user): ?>
                                <option value="<?= $user['id'] ?>" 
                                        <?= old('project_manager_id', isset($project) ? $project['project_manager_id'] : '') == $user['id'] ? 'selected' : '' ?>>
                                    <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="site_supervisor_id" class="block text-sm font-medium text-gray-700 mb-2">Site Supervisor</label>
                        <select id="site_supervisor_id" name="site_supervisor_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Site Supervisor</option>
                            <?php foreach($users as $user): ?>
                                <option value="<?= $user['id'] ?>" 
                                        <?= old('site_supervisor_id', isset($project) ? $project['site_supervisor_id'] : '') == $user['id'] ? 'selected' : '' ?>>
                                    <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Additional Options Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Options</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="requires_permit" name="requires_permit" value="1"
                               <?= old('requires_permit', isset($project) ? $project['requires_permit'] : 0) ? 'checked' : '' ?>
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="requires_permit" class="ml-2 block text-sm text-gray-900">
                            Requires Construction Permit
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="border-t pt-6 flex flex-col sm:flex-row gap-3 justify-end">
                <a href="<?= base_url('admin/projects') ?>" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    <?= isset($project) ? 'Update Project' : 'Create Project' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate project code based on name
    const nameField = document.getElementById('name');
    const codeField = document.getElementById('project_code');
    
    nameField.addEventListener('input', function() {
        const name = this.value;
        const code = name.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().substring(0, 10);
        if (code && !codeField.value) {
            codeField.value = code;
        }
    });

    // Validate dates
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('planned_end_date');
    
    function validateDates() {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        
        if (start && end && start > end) {
            alert('End date must be after start date');
            endDate.value = '';
        }
    }
    
    startDate.addEventListener('change', validateDates);
    endDate.addEventListener('change', validateDates);
    
    // Initialize Lucide icons
    lucide.createIcons();
});
</script>
<?= $this->endSection() ?>
