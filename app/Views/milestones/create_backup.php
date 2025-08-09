<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Milestone Create/Edit Page -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= isset($milestone) ? 'Edit Milestone' : 'Create New Milestone' ?></h1>
            <p class="text-gray-600">Configure milestone details and settings</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/milestones') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Milestones
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                </div>
                <div>
                    <p class="font-medium"><?= session('error') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                </div>
                <div>
                    <p class="font-medium"><?= session('success') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Validation Errors -->
    <?php if (session('errors')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                </div>
                <div>
                    <p class="font-medium">Please fix the following errors:</p>
                    <ul class="list-disc list-inside mt-2">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Milestone Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form action="<?= isset($milestone) ? base_url('admin/milestones/update/' . $milestone['id']) : base_url('admin/milestones/store') ?>" method="post" class="p-6 space-y-6" id="milestoneForm">
            <?= csrf_field() ?>
            
            <!-- Basic Information Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Milestone Title *</label>
                        <input type="text" id="title" name="title" required
                               value="<?= old('title', isset($milestone) ? $milestone['title'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="text-red-500 text-sm mt-1" id="title-error"></div>
                    </div>
                    
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project *</label>
                        <select id="project_id" name="project_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Project</option>
                            <?php if(isset($projects) && is_array($projects)): ?>
                                <?php foreach($projects as $project): ?>
                                    <option value="<?= $project['id'] ?>" 
                                            <?= old('project_id', isset($milestone) ? $milestone['project_id'] : ($selected_project ?? '')) == $project['id'] ? 'selected' : '' ?>>
                                        <?= esc($project['name']) ?> (<?= esc($project['project_code']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="text-red-500 text-sm mt-1" id="project_id-error"></div>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('description', isset($milestone) ? $milestone['description'] : '') ?></textarea>
                </div>
            </div>

            <!-- Milestone Details Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Milestone Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label for="milestone_type" class="block text-sm font-medium text-gray-700 mb-2">Milestone Type</label>
                        <select id="milestone_type" name="milestone_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="planning" <?= old('milestone_type', isset($milestone) ? $milestone['milestone_type'] : 'planning') == 'planning' ? 'selected' : '' ?>>Planning</option>
                            <option value="design" <?= old('milestone_type', isset($milestone) ? $milestone['milestone_type'] : 'planning') == 'design' ? 'selected' : '' ?>>Design</option>
                            <option value="construction" <?= old('milestone_type', isset($milestone) ? $milestone['milestone_type'] : 'planning') == 'construction' ? 'selected' : '' ?>>Construction</option>
                            <option value="inspection" <?= old('milestone_type', isset($milestone) ? $milestone['milestone_type'] : 'planning') == 'inspection' ? 'selected' : '' ?>>Inspection</option>
                            <option value="delivery" <?= old('milestone_type', isset($milestone) ? $milestone['milestone_type'] : 'planning') == 'delivery' ? 'selected' : '' ?>>Delivery</option>
                            <option value="other" <?= old('milestone_type', isset($milestone) ? $milestone['milestone_type'] : 'planning') == 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="pending" <?= old('status', isset($milestone) ? $milestone['status'] : 'pending') == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="in_progress" <?= old('status', isset($milestone) ? $milestone['status'] : 'pending') == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="completed" <?= old('status', isset($milestone) ? $milestone['status'] : 'pending') == 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= old('status', isset($milestone) ? $milestone['status'] : 'pending') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <div class="text-red-500 text-sm mt-1" id="status-error"></div>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                        <select id="priority" name="priority" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="low" <?= old('priority', isset($milestone) ? $milestone['priority'] : 'medium') == 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= old('priority', isset($milestone) ? $milestone['priority'] : 'medium') == 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= old('priority', isset($milestone) ? $milestone['priority'] : 'medium') == 'high' ? 'selected' : '' ?>>High</option>
                            <option value="urgent" <?= old('priority', isset($milestone) ? $milestone['priority'] : 'medium') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                        </select>
                        <div class="text-red-500 text-sm mt-1" id="priority-error"></div>
                    </div>

                    <div>
                        <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-2">Progress (%)</label>
                        <input type="number" id="progress_percentage" name="progress_percentage" min="0" max="100"
                               value="<?= old('progress_percentage', isset($milestone) ? $milestone['progress_percentage'] : 0) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                
                <div class="mt-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_critical" name="is_critical" value="1"
                               <?= old('is_critical', isset($milestone) ? $milestone['is_critical'] : 0) ? 'checked' : '' ?>
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_critical" class="ml-2 block text-sm text-gray-900">
                            Mark as Critical Milestone
                        </label>
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
                               value="<?= old('start_date', isset($milestone) ? $milestone['planned_start_date'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date *</label>
                        <input type="date" id="due_date" name="planned_end_date" required
                               value="<?= old('due_date', isset($milestone) ? $milestone['planned_end_date'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="text-red-500 text-sm mt-1" id="due_date-error"></div>
                    </div>

                    <div>
                        <label for="completion_date" class="block text-sm font-medium text-gray-700 mb-2">Completion Date</label>
                        <input type="date" id="completion_date" name="completion_date" 
                               value="<?= old('completion_date', isset($milestone) ? $milestone['actual_end_date'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Leave empty if not completed yet</p>
                    </div>
                </div>
            </div>

            <!-- Assignment Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assignment</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                        <select id="assigned_to" name="assigned_to"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Unassigned</option>
                            <?php if(isset($users) && is_array($users)): ?>
                                <?php foreach($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" 
                                            <?= old('assigned_to', isset($milestone) ? $milestone['assigned_to'] : '') == $user['id'] ? 'selected' : '' ?>>
                                        <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label for="reviewer_id" class="block text-sm font-medium text-gray-700 mb-2">Reviewer</label>
                        <select id="reviewer_id" name="reviewer_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">No Reviewer</option>
                            <?php if(isset($users) && is_array($users)): ?>
                                <?php foreach($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" 
                                            <?= old('reviewer_id', isset($milestone) ? ($milestone['reviewer_id'] ?? '') : '') == $user['id'] ? 'selected' : '' ?>>
                                        <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Deliverables & Success Criteria Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Deliverables & Success Criteria</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="deliverables" class="block text-sm font-medium text-gray-700 mb-2">Expected Deliverables</label>
                        <textarea id="deliverables" name="deliverables" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="List the expected deliverables for this milestone..."><?= old('deliverables', isset($milestone) ? $milestone['deliverables'] : '') ?></textarea>
                    </div>

                    <div>
                        <label for="success_criteria" class="block text-sm font-medium text-gray-700 mb-2">Success Criteria</label>
                        <textarea id="success_criteria" name="success_criteria" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Define the criteria for milestone completion..."><?= old('success_criteria', isset($milestone) ? $milestone['success_criteria'] : '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Dependencies Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dependencies</h3>
                <div>
                    <label for="dependency_milestones" class="block text-sm font-medium text-gray-700 mb-2">Dependent Milestones</label>
                    <select id="dependency_milestones" name="dependency_milestones[]" multiple
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <!-- Will be populated dynamically based on project selection -->
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Select milestones that must be completed before this milestone can start.</p>
                </div>
            </div>

            <!-- Budget Information Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Budget Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-2">Estimated Cost</label>
                        <input type="number" id="estimated_cost" name="estimated_cost" step="0.01" min="0"
                               value="<?= old('estimated_cost', isset($milestone) ? $milestone['estimated_cost'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="actual_cost" class="block text-sm font-medium text-gray-700 mb-2">Actual Cost</label>
                        <input type="number" id="actual_cost" name="actual_cost" step="0.01" min="0"
                               value="<?= old('actual_cost', isset($milestone) ? $milestone['actual_cost'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="budget_variance" class="block text-sm font-medium text-gray-700 mb-2">Budget Variance (%)</label>
                        <input type="number" id="budget_variance" name="budget_variance" step="0.01" readonly
                               value="<?= old('budget_variance', isset($milestone) ? $milestone['budget_variance'] : '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50">
                    </div>
                </div>
            </div>

            <!-- Risk Assessment Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Risk Assessment</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="risk_level" class="block text-sm font-medium text-gray-700 mb-2">Risk Level</label>
                        <select id="risk_level" name="risk_level"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="low" <?= old('risk_level', isset($milestone) ? $milestone['risk_level'] : 'low') == 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= old('risk_level', isset($milestone) ? $milestone['risk_level'] : 'low') == 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= old('risk_level', isset($milestone) ? $milestone['risk_level'] : 'low') == 'high' ? 'selected' : '' ?>>High</option>
                            <option value="critical" <?= old('risk_level', isset($milestone) ? $milestone['risk_level'] : 'low') == 'critical' ? 'selected' : '' ?>>Critical</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="risk_description" class="block text-sm font-medium text-gray-700 mb-2">Risk Description</label>
                        <textarea id="risk_description" name="risk_description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Describe potential risks and mitigation strategies..."><?= old('risk_description', isset($milestone) ? $milestone['risk_description'] : '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Additional Notes Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Notes</h3>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('notes', isset($milestone) ? $milestone['notes'] : '') ?></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="border-t pt-6 flex flex-col sm:flex-row gap-3 justify-end">
                <a href="<?= base_url('admin/milestones') ?>" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors" id="submitBtn">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    <?= isset($milestone) ? 'Update Milestone' : 'Create Milestone' ?>
                </button>
                <?php if (isset($milestone)): ?>
                <a href="<?= base_url('admin/milestones/view/' . $milestone['id']) ?>" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                    View Milestone
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('milestoneForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Clear previous validation errors
    function clearErrors() {
        document.querySelectorAll('.text-red-500').forEach(el => {
            if (el.id.includes('-error')) {
                el.textContent = '';
            }
        });
    }

    // Validate form before submission
    function validateForm() {
        clearErrors();
        let isValid = true;

        // Required field validation
        const requiredFields = ['title', 'project_id', 'due_date', 'priority', 'status'];
        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (!element.value.trim()) {
                document.getElementById(field + '-error').textContent = 'This field is required';
                isValid = false;
            }
        });

        // Date validation
        const startDate = document.getElementById('start_date').value;
        const dueDate = document.getElementById('due_date').value;
        const completionDate = document.getElementById('completion_date').value;

        if (startDate && dueDate && new Date(startDate) > new Date(dueDate)) {
            document.getElementById('due_date-error').textContent = 'Due date must be after start date';
            isValid = false;
        }

        return isValid;
    }

    // Handle form submission
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }

        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 animate-spin"></i>Processing...';
    });

    // Load dependent milestones when project changes
    document.getElementById('project_id').addEventListener('change', function() {
        var projectId = this.value;
        if (projectId) {
            loadProjectMilestones(projectId);
        } else {
            document.getElementById('dependency_milestones').innerHTML = '';
        }
    });

    // Load milestones on page load if project is pre-selected
    if (document.getElementById('project_id').value) {
        loadProjectMilestones(document.getElementById('project_id').value);
    }

    // Validate dates on change
    document.getElementById('start_date').addEventListener('change', validateDates);
    document.getElementById('due_date').addEventListener('change', validateDates);
    document.getElementById('completion_date').addEventListener('change', validateDates);

    // Auto-calculate budget variance
    document.getElementById('estimated_cost').addEventListener('input', calculateBudgetVariance);
    document.getElementById('actual_cost').addEventListener('input', calculateBudgetVariance);

    // Auto-update progress and completion date based on status
    document.getElementById('status').addEventListener('change', function() {
        var status = this.value;
        var progressField = document.getElementById('progress_percentage');
        var completionField = document.getElementById('completion_date');
        
        if (status === 'completed') {
            progressField.value = 100;
            if (!completionField.value) {
                completionField.value = new Date().toISOString().split('T')[0];
            }
        } else if (status === 'pending') {
            progressField.value = 0;
            completionField.value = '';
        } else if (status === 'in_progress' && progressField.value == 0) {
            progressField.value = 50;
            completionField.value = '';
        }
    });
    
    // Initialize Lucide icons
    lucide.createIcons();
});

function loadProjectMilestones(projectId) {
    const depSelect = document.getElementById('dependency_milestones');
    depSelect.innerHTML = '<option value="">Loading...</option>';
    
    fetch('<?= base_url('admin/milestones/getProjectMilestones') ?>/' + projectId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            depSelect.innerHTML = '';
            
            if (data.milestones && data.milestones.length > 0) {
                data.milestones.forEach(function(milestone) {
                    // Exclude current milestone from dropdown if editing
                    <?php if (isset($milestone)): ?>
                    if (milestone.id != <?= $milestone['id'] ?>) {
                    <?php endif; ?>
                        var option = document.createElement('option');
                        option.value = milestone.id;
                        option.textContent = milestone.title;
                        depSelect.appendChild(option);
                    <?php if (isset($milestone)): ?>
                    }
                    <?php endif; ?>
                });
            } else {
                depSelect.innerHTML = '<option value="">No milestones available</option>';
            }

            // Restore dependency selections if editing
            <?php if (isset($milestone) && isset($milestone_dependencies) && is_array($milestone_dependencies)): ?>
            var selectedDeps = [<?= implode(',', array_column($milestone_dependencies, 'id')) ?>];
            
            // Convert selectedDeps to strings for comparison
            selectedDeps = selectedDeps.map(String);
            
            // Set selected options
            Array.from(depSelect.options).forEach(function(option) {
                if (selectedDeps.includes(option.value)) {
                    option.selected = true;
                }
            });
            <?php endif; ?>
        })
        .catch(error => {
            console.error('Failed to load project milestones:', error);
            depSelect.innerHTML = '<option value="">Error loading milestones</option>';
        });
}

function validateDates() {
    var startDate = new Date(document.getElementById('start_date').value);
    var dueDate = new Date(document.getElementById('due_date').value);
    var completionDate = new Date(document.getElementById('completion_date').value);
    
    // Clear previous errors
    document.getElementById('due_date-error').textContent = '';
    
    if (startDate && dueDate && startDate > dueDate) {
        document.getElementById('due_date-error').textContent = 'Due date must be after start date';
        return false;
    }
    
    if (completionDate && dueDate && completionDate > dueDate) {
        console.warn('Completion date is after due date - milestone may be overdue');
    }
    
    return true;
}

function calculateBudgetVariance() {
    var estimated = parseFloat(document.getElementById('estimated_cost').value) || 0;
    var actual = parseFloat(document.getElementById('actual_cost').value) || 0;
    
    if (estimated > 0 && actual > 0) {
        var variance = ((actual - estimated) / estimated) * 100;
        document.getElementById('budget_variance').value = variance.toFixed(2);
    } else {
        document.getElementById('budget_variance').value = '';
    }
}

// Add real-time validation
document.getElementById('title').addEventListener('blur', function() {
    const errorEl = document.getElementById('title-error');
    if (this.value.trim().length < 3) {
        errorEl.textContent = 'Title must be at least 3 characters long';
    } else {
        errorEl.textContent = '';
    }
});

document.getElementById('project_id').addEventListener('change', function() {
    const errorEl = document.getElementById('project_id-error');
    if (!this.value) {
        errorEl.textContent = 'Please select a project';
    } else {
        errorEl.textContent = '';
    }
});

document.getElementById('due_date').addEventListener('change', function() {
    const errorEl = document.getElementById('due_date-error');
    if (!this.value) {
        errorEl.textContent = 'Due date is required';
    } else {
        errorEl.textContent = '';
    }
    validateDates();
});
</script>
<?= $this->endSection() ?>