<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Incident Details<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.status-badge { 
    display: inline-block;
    padding: 0.75rem 1rem;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 0.875rem;
}
.tab-button { 
    padding: 0.5rem 1rem;
    font-weight: 500;
    border-bottom: 2px solid transparent;
    color: #374151;
    transition: all 0.2s;
}
.tab-button:hover {
    color: #111827;
}
.tab-button.active { 
    border-bottom-color: #4f46e5;
    color: #4f46e5;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-eye mr-2"></i>Incident Details</h1>
            <p class="text-gray-600 text-sm mt-1"><?= $incident['incident_code'] ?? 'Incident' ?></p>
        </div>
        <a href="<?= base_url('incident-safety/incidents') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
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
        <!-- Main Incident Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Incident Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Incident Code</label>
                        <p class="text-lg font-semibold text-gray-900"><?= $incident['incident_code'] ?? 'N/A' ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Incident Type</label>
                        <p>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                <?= $type['name'] ?? ($incident['incident_type'] ?? 'N/A') ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                        <p class="text-lg font-semibold text-gray-900"><?= $incident['project_name'] ?? 'N/A' ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Severity Level</label>
                        <p>
                            <?php 
                            $severityClass = [
                                'Critical' => 'bg-red-100 text-red-800',
                                'High' => 'bg-orange-100 text-orange-800',
                                'Medium' => 'bg-yellow-100 text-yellow-800',
                                'Low' => 'bg-green-100 text-green-800'
                            ];
                            $severityName = $severity['name'] ?? ($incident['severity_name'] ?? 'N/A');
                            $sevClass = $severityClass[$severityName] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="<?= $sevClass ?> px-3 py-1 rounded-full text-sm font-medium">
                                <?= $severityName ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reported Date</label>
                        <p class="text-lg font-semibold text-gray-900">
                            <?= !empty($incident['incident_date']) ? date('M d, Y H:i', strtotime($incident['incident_date'])) : 'N/A' ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reported By</label>
                        <p class="text-lg font-semibold text-gray-900"><?= $incident['reported_by_name'] ?? ($incident['reported_by'] ?? 'N/A') ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <p class="text-lg font-semibold text-gray-900"><?= $incident['location'] ?? 'N/A' ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <p>
                            <?php 
                            $statusClass = [
                                'reported' => 'bg-blue-100 text-blue-800',
                                'investigating' => 'bg-yellow-100 text-yellow-800',
                                'under_review' => 'bg-purple-100 text-purple-800',
                                'resolved' => 'bg-green-100 text-green-800',
                                'closed' => 'bg-gray-100 text-gray-800',
                                'reopened' => 'bg-red-100 text-red-800'
                            ];
                            $stClass = $statusClass[$incident['status']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="<?= $stClass ?> px-3 py-1 rounded-full text-sm font-medium">
                                <?= ucfirst(str_replace('_', ' ', $incident['status'] ?? '')) ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <p class="text-lg font-semibold text-gray-900"><?= $incident['title'] ?? 'N/A' ?></p>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <p class="text-gray-700 whitespace-pre-wrap"><?= nl2br($incident['description'] ?? 'No description provided') ?></p>
                </div>
            </div>
        </div>

        <!-- Impact Card Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Incident Impact</h3>
                
                <div class="border-l-4 border-blue-500 pl-4 mb-4">
                    <p class="text-sm text-gray-600">People Affected</p>
                    <p class="text-3xl font-bold text-blue-600"><?= $incident['affected_people_count'] ?? 0 ?></p>
                </div>

                <div class="border-l-4 border-red-500 pl-4 mb-4">
                    <p class="text-sm text-gray-600">Injuries Sustained</p>
                    <p class="text-lg font-semibold text-gray-900"><?= $incident['injuries_sustained'] ?? 'None' ?></p>
                </div>

                <div class="border-l-4 border-orange-500 pl-4 mb-4">
                    <p class="text-sm text-gray-600">Witnesses</p>
                    <p class="text-3xl font-bold text-orange-600"><?= $incident['witness_count'] ?? 0 ?></p>
                </div>

                <div class="border-l-4 border-gray-500 pl-4">
                    <p class="text-sm text-gray-600">Property Damage</p>
                    <p class="text-gray-700"><?= !empty($incident['property_damage_description']) ? $incident['property_damage_description'] : 'None reported' ?></p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Immediate Actions</h3>
                <p class="text-gray-700 whitespace-pre-wrap"><?= nl2br($incident['immediate_actions_taken'] ?? 'No immediate actions recorded') ?></p>
            </div>

            <!-- Status Update Card -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Update Status</h3>
                <form id="statusUpdateForm" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                        <select name="status" id="statusSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="reported" <?= $incident['status'] === 'reported' ? 'selected' : '' ?>>Reported</option>
                            <option value="investigating" <?= $incident['status'] === 'investigating' ? 'selected' : '' ?>>Investigating</option>
                            <option value="under_review" <?= $incident['status'] === 'under_review' ? 'selected' : '' ?>>Under Review</option>
                            <option value="resolved" <?= $incident['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                            <option value="closed" <?= $incident['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                            <option value="reopened" <?= $incident['status'] === 'reopened' ? 'selected' : '' ?>>Reopened</option>
                        </select>
                    </div>
                    <div id="notesSection" class="<?= $incident['status'] === 'resolved' ? '' : 'hidden' ?>">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investigation Findings / Notes</label>
                        <textarea name="notes" id="statusNotes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Enter findings or notes..."></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabbed Content -->
    <div class="bg-white rounded-lg shadow-sm border">
        <!-- Tab Navigation -->
        <div class="flex border-b">
            <button type="button" class="tab-button active px-6 py-3 text-gray-900 border-b-2 border-indigo-600" data-tab="photos">
                <i class="fas fa-images mr-2"></i> Photos
            </button>
            <button type="button" class="tab-button px-6 py-3 text-gray-700 border-b-2 border-transparent hover:border-gray-300" data-tab="actions">
                <i class="fas fa-tasks mr-2"></i> Action Steps
            </button>
            <button type="button" class="tab-button px-6 py-3 text-gray-700 border-b-2 border-transparent hover:border-gray-300" data-tab="investigation">
                <i class="fas fa-search mr-2"></i> Investigation
            </button>
        </div>

        <div class="p-6">
            <!-- Photos Tab -->
            <div class="tab-content active" id="photos">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Incident Evidence</h3>
                <?php if (!empty($photos)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <?php foreach ($photos as $photo): ?>
                            <div class="rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition">
                                <img src="<?= base_url('incident-safety/photos/' . $photo['id']) ?>" alt="Incident Photo" class="w-full h-48 object-cover">
                                <div class="p-3 bg-gray-50">
                                    <p class="font-semibold text-gray-900 text-sm"><?= ucfirst($photo['photo_type'] ?? '') ?></p>
                                    <p class="text-xs text-gray-600 mt-1"><?= $photo['description'] ?? 'No description' ?></p>
                                    <p class="text-xs text-gray-500 mt-2"><?= date('M d, Y', strtotime($photo['uploaded_at'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600">No photos attached to this incident.</p>
                <?php endif; ?>
            </div>

            <!-- Action Steps Tab -->
            <div class="tab-content hidden" id="actions">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Corrective Actions</h3>
                    <button type="button" onclick="toggleActionForm()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                        <i class="fas fa-plus mr-2"></i> Add Action
                    </button>
                </div>

                <!-- Add Action Form -->
                <div id="actionForm" class="hidden mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">New Corrective Action</h4>
                    <form id="addActionForm" class="space-y-3">
                        <?= csrf_field() ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Action Description *</label>
                            <textarea name="action_description" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" rows="2" placeholder="Describe the corrective action to be taken..."></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assign To *</label>
                                <select name="assigned_to" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select Person</option>
                                    <?php if (!empty($users)): ?>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user['id'] ?>"><?= $user['first_name'] . ' ' . $user['last_name'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                                <input type="date" name="due_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="flex gap-2 justify-end">
                            <button type="button" onclick="toggleActionForm()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors text-sm">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                <i class="fas fa-save mr-2"></i> Save Action
                            </button>
                        </div>
                    </form>
                </div>

                <?php if (!empty($actionSteps)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Assigned To</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-700">Progress</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($actionSteps as $action): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $action['action_number'] ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-700"><?= $action['action_description'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= $action['responsible_name'] ?? 'Unassigned' ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= date('M d, Y', strtotime($action['due_date'])) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php 
                                            $actionStatus = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'in_progress' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800'
                                            ];
                                            $actClass = $actionStatus[$action['completion_status']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="<?= $actClass ?> px-3 py-1 rounded-full text-xs font-medium">
                                                <?= ucfirst(str_replace('_', ' ', $action['completion_status'] ?? '')) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($action['completion_status'] === 'completed'): ?>
                                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">100%</span>
                                            <?php else: ?>
                                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-medium">0%</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600">No action steps recorded yet.</p>
                <?php endif; ?>
            </div>

            <!-- Investigation Tab -->
            <div class="tab-content hidden" id="investigation">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Investigation Details</h3>
                
                <?php if (!empty($incident['investigation_findings'])): ?>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investigation Findings</label>
                        <p class="text-gray-700 whitespace-pre-wrap"><?= nl2br($incident['investigation_findings']) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($incident['investigation_completed_date'])): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Investigation Completed</label>
                            <p class="text-lg font-semibold text-gray-900"><?= date('M d, Y H:i', strtotime($incident['investigation_completed_date'])) ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Completed By</label>
                            <p class="text-lg font-semibold text-gray-900"><?= $incident['investigator_name'] ?? 'N/A' ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600">Investigation is pending or not yet completed.</p>
                <?php endif; ?>

                <?php if ($incident['is_safety_audit_required']): ?>
                    <div class="mt-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i> A safety audit is required for this incident.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
                tab.classList.remove('active');
            });
            
            // Remove active from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-indigo-600', 'text-gray-900');
                btn.classList.add('border-transparent', 'text-gray-700');
            });
            
            // Show selected tab
            const selectedTab = document.getElementById(tabId);
            if (selectedTab) {
                selectedTab.classList.remove('hidden');
                selectedTab.classList.add('active');
                
                // Highlight active button
                this.classList.add('active', 'border-indigo-600', 'text-gray-900');
                this.classList.remove('border-transparent', 'text-gray-700');
            }
        });
    });

    // Status update functionality
    const statusSelect = document.getElementById('statusSelect');
    const notesSection = document.getElementById('notesSection');
    const statusForm = document.getElementById('statusUpdateForm');

    // Show/hide notes section based on status
    statusSelect.addEventListener('change', function() {
        if (this.value === 'resolved') {
            notesSection.classList.remove('hidden');
        } else {
            notesSection.classList.add('hidden');
        }
    });

    // Handle status update form submission
    statusForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
        
        fetch('<?= base_url("incident-safety/incidents/" . $incident['id'] . "/status") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4';
                alertDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + data.message;
                document.querySelector('.space-y-6').insertBefore(alertDiv, document.querySelector('.space-y-6').firstChild);
                
                // Reload page after 2 seconds
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                alert('Error: ' + data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating status');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Action steps functionality
    function toggleActionForm() {
        const form = document.getElementById('actionForm');
        form.classList.toggle('hidden');
    }

    const addActionForm = document.getElementById('addActionForm');
    if (addActionForm) {
        addActionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
            
            fetch('<?= base_url("incident-safety/incidents/" . $incident['id'] . "/action-steps") ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Action step added successfully');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding action step');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
    </script>

</div>

<?= $this->endSection() ?>
