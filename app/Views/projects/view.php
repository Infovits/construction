<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Project Details Page -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= esc($project['name']) ?></h1>
            <div class="flex items-center space-x-4 text-sm text-gray-600">
                <span>Project Code: <code class="bg-gray-100 px-2 py-1 rounded font-mono"><?= esc($project['project_code']) ?></code></span>
                <span>•</span>
                <span><?= esc($project['client_name'] ?? 'No Client') ?></span>
                <span>•</span>
                <span><?= getStatusBadge($project['status'], 'project') ?></span>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/projects') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Projects
            </a>
            <a href="<?= base_url('admin/projects/edit/' . $project['id']) ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit Project
            </a>
        </div>
    </div>

    <!-- Project Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Progress</p>
                    <p class="text-2xl font-bold text-indigo-600"><?= round($project['progress_percentage'] ?? 0, 1) ?>%</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full" style="width: <?= $project['progress_percentage'] ?? 0 ?>%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Budget</p>
                    <p class="text-2xl font-bold text-green-600"><?= formatCurrency($project['estimated_budget'] ?? 0) ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <div class="mt-2">
                <p class="text-sm text-gray-500">Spent: <?= formatCurrency($budget_tracking['total_spent'] ?? 0) ?></p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Tasks</p>
                    <p class="text-2xl font-bold text-blue-600"><?= count($tasks ?? []) ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-square" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <div class="mt-2">
                <p class="text-sm text-gray-500">Completed: <?= count(array_filter($tasks ?? [], fn($t) => $t['status'] === 'completed')) ?></p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Team</p>
                    <p class="text-2xl font-bold text-purple-600"><?= count($project['team_members'] ?? []) ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <div class="mt-2">
                <p class="text-sm text-gray-500">Members active</p>
            </div>
        </div>
    </div>

    <!-- Project Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Project Information</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <p class="mt-1 text-sm text-gray-900"><?= formatDate($project['start_date'] ?? null) ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <p class="mt-1 text-sm text-gray-900"><?= formatDate($project['planned_end_date'] ?? null) ?></p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <div class="mt-1 text-sm text-gray-900">
                        <?= !empty($project['description']) ? nl2br(esc($project['description'])) : '<em class="text-gray-500">No description provided</em>' ?>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Location</label>
                    <p class="mt-1 text-sm text-gray-900"><?= esc($project['site_address'] ?? 'Not specified') ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Priority</label>
                    <div class="mt-1"><?= getPriorityBadge($project['priority'] ?? 'medium') ?></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Project Type</label>
                    <p class="mt-1 text-sm text-gray-900"><?= ucfirst(str_replace('_', ' ', $project['project_type'] ?? 'general')) ?></p>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="space-y-6">
            <!-- Project Team -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">Project Team</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php if (!empty($project['project_manager_name'])): ?>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i data-lucide="user-check" class="w-4 h-4 text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900"><?= esc($project['project_manager_name']) ?></p>
                                <p class="text-xs text-gray-500">Project Manager</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['site_supervisor_name'])): ?>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i data-lucide="hard-hat" class="w-4 h-4 text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900"><?= esc($project['site_supervisor_name']) ?></p>
                                <p class="text-xs text-gray-500">Site Supervisor</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (empty($project['project_manager_name']) && empty($project['site_supervisor_name'])): ?>
                        <p class="text-gray-500 italic text-sm">No team members assigned</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t">
                        <a href="<?= base_url('admin/projects/team/' . $project['id']) ?>" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                            Manage Team →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">Financial Summary</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Estimated Budget</span>
                            <span class="text-sm font-medium text-gray-900"><?= formatCurrency($project['estimated_budget'] ?? 0) ?></span>
                        </div>
                        
                        <?php if (!empty($project['contract_value']) && $project['contract_value'] > 0): ?>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Contract Value</span>
                            <span class="text-sm font-medium text-gray-900"><?= formatCurrency($project['contract_value']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['actual_cost']) && $project['actual_cost'] > 0): ?>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Actual Cost</span>
                            <span class="text-sm font-medium text-gray-900"><?= formatCurrency($project['actual_cost']) ?></span>
                        </div>
                        
                        <div class="pt-2 border-t">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Remaining Budget</span>
                                <?php $remaining = ($project['estimated_budget'] ?? 0) - ($project['actual_cost'] ?? 0); ?>
                                <span class="text-sm font-medium <?= $remaining >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= formatCurrency($remaining) ?>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
                </div>
                <div class="p-6">
                    <?php if (!empty($recent_activities)): ?>
                        <div class="space-y-4">
                            <?php foreach (array_slice($recent_activities, 0, 5) as $activity): ?>
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-indigo-500 rounded-full mt-2"></div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900"><?= esc($activity['activity']) ?></p>
                                        <p class="text-xs text-gray-500">by <?= esc($activity['user']) ?> • <?= timeAgo($activity['timestamp']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">No recent activity</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="border-b">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button class="tab-button active py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600" 
                        data-tab="tasks">
                    <i data-lucide="check-square" class="w-4 h-4 mr-2 inline"></i>
                    Tasks (<?= count($tasks ?? []) ?>)
                </button>
                <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                        data-tab="milestones">
                    <i data-lucide="flag" class="w-4 h-4 mr-2 inline"></i>
                    Milestones (<?= count($milestones ?? []) ?>)
                </button>
                <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                        data-tab="team">
                    <i data-lucide="users" class="w-4 h-4 mr-2 inline"></i>
                    Team (<?= count($project['team_members'] ?? []) ?>)
                </button>
                <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                        data-tab="timeline">
                    <i data-lucide="calendar" class="w-4 h-4 mr-2 inline"></i>
                    Timeline
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Tasks Tab -->
            <div id="tasks-content" class="tab-content">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Project Tasks</h3>
                    <a href="<?= base_url('admin/tasks/create?project_id=' . $project['id']) ?>" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Add Task
                    </a>
                </div>
                
                <?php if (empty($tasks)): ?>
                    <div class="text-center py-12">
                        <i data-lucide="check-square" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks yet</h3>
                        <p class="text-gray-500 mb-4">Create your first task to get started.</p>
                        <a href="<?= base_url('admin/tasks/create?project_id=' . $project['id']) ?>" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Create First Task
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($tasks as $task): ?>
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">
                                            <a href="<?= base_url('admin/tasks/view/' . $task['id']) ?>" 
                                               class="hover:text-indigo-600">
                                                <?= esc($task['title']) ?>
                                            </a>
                                        </h4>
                                        <div class="flex items-center space-x-4 mt-1 text-sm text-gray-500">
                                            <span><?= getStatusBadge($task['status'], 'task') ?></span>
                                            <span><?= getPriorityBadge($task['priority'] ?? 'medium') ?></span>
                                            <span>Due: <?= formatDate($task['due_date'] ?? null) ?></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="<?= base_url('admin/tasks/' . $task['id'] . '/edit') ?>"
                                           class="text-gray-400 hover:text-indigo-600">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <a href="<?= base_url('admin/tasks/view/' . $task['id']) ?>" 
                                           class="text-gray-400 hover:text-indigo-600">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Milestones Tab -->
            <div id="milestones-content" class="tab-content hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Project Milestones</h3>
                    <a href="<?= base_url('admin/milestones/create?project_id=' . $project['id']) ?>" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Add Milestone
                    </a>
                </div>
                
                <?php if (empty($milestones)): ?>
                    <div class="text-center py-12">
                        <i data-lucide="flag" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No milestones yet</h3>
                        <p class="text-gray-500 mb-4">Create your first milestone to track progress.</p>
                        <a href="<?= base_url('admin/milestones/create?project_id=' . $project['id']) ?>" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Create First Milestone
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($milestones as $milestone): ?>
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">
                                            <a href="<?= base_url('admin/milestones/' . $milestone['id']) ?>"
                                               class="hover:text-indigo-600">
                                                <?= esc($milestone['title']) ?>
                                            </a>
                                        </h4>
                                        <div class="flex items-center space-x-4 mt-1 text-sm text-gray-500">
                                            <span><?= getStatusBadge($milestone['status'], 'milestone') ?></span>
                                            <span>Due: <?= formatDate($milestone['due_date'] ?? null) ?></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="<?= base_url('admin/milestones/edit/' . $milestone['id']) ?>" 
                                           class="text-gray-400 hover:text-indigo-600">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <a href="<?= base_url('admin/milestones/' . $milestone['id']) ?>"
                                           class="text-gray-400 hover:text-indigo-600">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Team Tab -->
            <div id="team-content" class="tab-content hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Team Members</h3>
                    <a href="<?= base_url('admin/projects/team/' . $project['id']) ?>" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Manage Team
                    </a>
                </div>
                
                <?php if (empty($project['team_members'])): ?>
                    <div class="text-center py-12">
                        <i data-lucide="users" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No team members yet</h3>
                        <p class="text-gray-500 mb-4">Add team members to start collaboration.</p>
                        <a href="<?= base_url('admin/projects/team/' . $project['id']) ?>" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Add Team Members
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($project['team_members'] as $member): ?>
                            <div class="border rounded-lg p-4 text-center">
                                <div class="w-12 h-12 bg-gray-200 rounded-full mx-auto mb-3 flex items-center justify-center">
                                    <i data-lucide="user" class="w-6 h-6 text-gray-600"></i>
                                </div>
                                <h4 class="font-medium text-gray-900"><?= esc($member['first_name'] . ' ' . $member['last_name']) ?></h4>
                                <p class="text-sm text-gray-500"><?= esc($member['role'] ?? 'Team Member') ?></p>
                                <p class="text-xs text-gray-400"><?= esc($member['email']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Timeline Tab -->
            <div id="timeline-content" class="tab-content hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Project Timeline</h3>
                
                <?php if (!empty($timeline)): ?>
                    <div class="space-y-6">
                        <?php foreach ($timeline as $event): ?>
                            <div class="flex items-start space-x-4">
                                <div class="w-3 h-3 bg-indigo-500 rounded-full mt-2"></div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900"><?= esc($event['title']) ?></h4>
                                    <p class="text-sm text-gray-600"><?= esc($event['description']) ?></p>
                                    <p class="text-xs text-gray-500 mt-1"><?= formatDate($event['date']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i data-lucide="calendar" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No timeline events</h3>
                        <p class="text-gray-500">Timeline events will appear here as the project progresses.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.getAttribute('data-tab');
            
            // Remove active state from all buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Add active state to clicked button
            button.classList.add('active', 'border-indigo-500', 'text-indigo-600');
            button.classList.remove('border-transparent', 'text-gray-500');
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show target tab content
            const targetContent = document.getElementById(targetTab + '-content');
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }
        });
    });
    
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
<?= $this->endSection() ?>
