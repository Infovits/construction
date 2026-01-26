<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Milestone Details - <?= esc($milestone['title']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Milestone Header -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800"><?= esc(isset($milestone['title']) ? $milestone['title'] : 'Milestone') ?></h1>
                    <p class="text-sm text-gray-500">
                        Project: <a href="<?= base_url('admin/projects/view/' . $milestone['project_id']) ?>" class="text-indigo-600 hover:text-indigo-800"><?= esc($milestone['project_name']) ?></a>
                        <span class="ml-1 px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full"><?= esc($milestone['project_code']) ?></span>
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="<?= base_url('admin/milestones') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back
                    </a>
                    <a href="<?= base_url('admin/milestones/' . $milestone['id'] . '/edit') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Actions
                        </button>
                        <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                            <div class="py-1">
                                <?php if ($milestone['status'] != 'completed'): ?>
                                <a href="#" onclick="completeMilestone(<?= $milestone['id'] ?>)" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900">
                                    <svg class="mr-3 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Mark Complete
                                </a>
                                <?php endif; ?>
                                <a href="#" onclick="updateProgress()" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900">
                                    <svg class="mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Update Progress
                                </a>
                            </div>
                            <div class="py-1">
                                <a href="<?= base_url('admin/tasks/create?project_id=' . $milestone['project_id']) ?>" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900">
                                    <svg class="mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Create Related Task
                                </a>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-white rounded-lg shadow overflow-hidden">
                                    <div class="px-4 py-5 sm:p-6">
                                        <dl>
                                            <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                                <div class="sm:col-span-1">
                                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">
                                                        <?php
                                                        $statusColors = [
                                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                                            'completed' => 'bg-green-100 text-green-800',
                                                            'cancelled' => 'bg-red-100 text-red-800',
                                                            'not_started' => 'bg-gray-100 text-gray-800',
                                                            'on_hold' => 'bg-purple-100 text-purple-800'
                                                        ];
                                                        $statusColor = $statusColors[$milestone['status']] ?? 'bg-gray-100 text-gray-800';
                                                        ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColor ?>">
                                                            <?= formatStatus($milestone['status']) ?>
                                                        </span>
                                                    </dd>
                                                </div>
                                                <div class="sm:col-span-1">
                                                    <dt class="text-sm font-medium text-gray-500">Priority</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">
                                                        <?php
                                                        $priorityColors = [
                                                            'low' => 'bg-blue-100 text-blue-800',
                                                            'medium' => 'bg-yellow-100 text-yellow-800',
                                                            'high' => 'bg-orange-100 text-orange-800',
                                                            'urgent' => 'bg-red-100 text-red-800'
                                                        ];
                                                        $priorityColor = $priorityColors[$milestone['priority']] ?? 'bg-gray-100 text-gray-800';
                                                        ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $priorityColor ?>">
                                                            <?= ucfirst($milestone['priority']) ?>
                                                        </span>
                                                    </dd>
                                                </div>
                                                <div class="sm:col-span-1">
                                                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">
                                                        <?= ucfirst(str_replace('_', ' ', $milestone['milestone_type'] ?? 'General')) ?>
                                                    </dd>
                                                </div>
                                                <div class="sm:col-span-1">
                                                    <dt class="text-sm font-medium text-gray-500">Critical</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">
                                                        <?php if (isset($milestone['is_critical']) && $milestone['is_critical']): ?>
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                Critical
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                Normal
                                                            </span>
                                                        <?php endif; ?>
                                                    </dd>
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <dt class="text-sm font-medium text-gray-500">Progress</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">
                                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                            <?php
                                                            $progressColor = 'bg-blue-600';
                                                            if ($milestone['progress_percentage'] < 25) {
                                                                $progressColor = 'bg-red-600';
                                                            } else if ($milestone['progress_percentage'] < 50) {
                                                                $progressColor = 'bg-orange-500';
                                                            } else if ($milestone['progress_percentage'] < 75) {
                                                                $progressColor = 'bg-yellow-500';
                                                            } else {
                                                                $progressColor = 'bg-green-500';
                                                            }
                                                            ?>
                                                            <div class="<?= $progressColor ?> h-2.5 rounded-full" style="width: <?= $milestone['progress_percentage'] ?>%"></div>
                                                        </div>
                                                        <p class="mt-1 text-right"><?= $milestone['progress_percentage'] ?>%</p>
                                                    </dd>
                                                </div>
                                                <div class="sm:col-span-1">
                                                    <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">
                                                        <?= isset($milestone['planned_start_date']) && $milestone['planned_start_date'] ? date('M d, Y', strtotime($milestone['planned_start_date'])) : 'Not set' ?>
                                                    </dd>
                                                </div>
                                                <div class="sm:col-span-1">
                                                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">
                                                        <?php if (isset($milestone['planned_end_date']) && $milestone['planned_end_date']): ?>
                                                            <?= date('M d, Y', strtotime($milestone['planned_end_date'])) ?>
                                                            <?php if (isOverdue($milestone['planned_end_date'], $milestone['status'])): ?>
                                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                                    <svg class="mr-1 h-3 w-3 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                                    </svg>
                                                                    Overdue
                                                                </span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-gray-400">Not set</span>
                                                        <?php endif; ?>
                                                    </dd>
                                                </div>
                                                <div class="sm:col-span-1">
                                                    <dt class="text-sm font-medium text-gray-500">Completion Date</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">
                                                        <?php if ($milestone['status'] === 'completed' && isset($milestone['actual_end_date']) && $milestone['actual_end_date']): ?>
                                                            <?= date('M d, Y', strtotime($milestone['actual_end_date'])) ?>
                                                        <?php else: ?>
                                                            <span class="text-gray-400">Not completed</span>
                                                        <?php endif; ?>
                                                    </dd>
                                                </div>

                                                <div class="sm:col-span-1">
                                                    <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">
                                                        <?php if ($milestone['assigned_to'] && $assigned_user): ?>
                                                            <div class="flex items-center">
                                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-medium">
                                                                    <?= generateAvatarInitials($assigned_user['first_name'], $assigned_user['last_name']) ?>
                                                                </div>
                                                                <div class="ml-2">
                                                                    <?= esc($assigned_user['first_name'] . ' ' . $assigned_user['last_name']) ?>
                                                                </div>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-gray-400">Unassigned</span>
                                                        <?php endif; ?>
                                                    </dd>
                                                </div>
                                                <div class="sm:col-span-1">
                                                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">
                                                        <?= date('M d, Y g:i A', strtotime($milestone['created_at'])) ?>
                                                    </dd>
                                                </div>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 rounded-lg shadow overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                        Milestone Summary
                                    </h3>
                                </div>
                                <div class="p-4">
                                    <?php if (isset($milestone['estimated_cost']) || isset($milestone['actual_cost'])): ?>
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <?php if (isset($milestone['estimated_cost']) && $milestone['estimated_cost']): ?>
                                        <div class="text-center">
                                            <p class="text-sm font-medium text-gray-500">Estimated Cost</p>
                                            <p class="mt-1 text-2xl font-semibold text-indigo-600">MWK <?= number_format($milestone['estimated_cost'], 2) ?></p>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (isset($milestone['actual_cost']) && $milestone['actual_cost']): ?>
                                        <div class="text-center">
                                            <p class="text-sm font-medium text-gray-500">Actual Cost</p>
                                            <p class="mt-1 text-2xl font-semibold <?= isset($milestone['estimated_cost']) && $milestone['actual_cost'] > $milestone['estimated_cost'] ? 'text-red-600' : 'text-green-600' ?>">
                                                MWK <?= number_format($milestone['actual_cost'], 2) ?>
                                            </p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (isset($milestone['risk_level']) && $milestone['risk_level']): ?>
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-gray-500 mb-2">Risk Level</p>
                                        <?php
                                        $riskColors = [
                                            'low' => 'bg-green-100 text-green-800',
                                            'medium' => 'bg-yellow-100 text-yellow-800',
                                            'high' => 'bg-red-100 text-red-800',
                                            'critical' => 'bg-black bg-opacity-20 text-white'
                                        ];
                                        $riskColor = $riskColors[$milestone['risk_level']] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium <?= $riskColor ?>">
                                            <?= ucfirst($milestone['risk_level']) ?> Risk
                                        </span>
                                    </div>
                                    <?php endif; ?>

                                    <div class="text-center mt-4">
                                        <p class="text-sm text-gray-500">
                                            <?php if ($milestone['planned_end_date']): ?>
                                                <?php
                                                $daysLeft = ceil((strtotime($milestone['planned_end_date']) - time()) / (60 * 60 * 24));
                                                if ($daysLeft > 0): ?>
                                                    <span class="font-medium"><?= $daysLeft ?> days</span> remaining
                                                <?php elseif ($daysLeft == 0): ?>
                                                    <span class="font-medium text-orange-600">Due today</span>
                                                <?php else: ?>
                                                    <span class="font-medium text-red-600"><?= abs($daysLeft) ?> days overdue</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (isset($milestone['description']) && $milestone['description']): ?>
                    <div class="mt-6">
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Description
                                </h3>
                            </div>
                            <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                                <div class="sm:px-6 sm:py-5">
                                    <p class="text-sm text-gray-900"><?= nl2br(esc($milestone['description'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information Tabs -->
    <div class="mt-8">
        <div class="bg-white shadow rounded-lg overflow-hidden" x-data="{ activeTab: 'deliverables' }">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button @click="activeTab = 'deliverables'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'deliverables', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'deliverables' }" class="w-1/5 py-4 px-1 text-center border-b-2 font-medium text-sm">
                        <svg class="mx-auto h-5 w-5 mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Deliverables
                    </button>
                    <button @click="activeTab = 'criteria'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'criteria', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'criteria' }" class="w-1/5 py-4 px-1 text-center border-b-2 font-medium text-sm">
                        <svg class="mx-auto h-5 w-5 mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Success Criteria
                    </button>
                    <button @click="activeTab = 'dependencies'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'dependencies', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'dependencies' }" class="w-1/5 py-4 px-1 text-center border-b-2 font-medium text-sm">
                        <svg class="mx-auto h-5 w-5 mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        Dependencies
                    </button>
                    <button @click="activeTab = 'risks'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'risks', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'risks' }" class="w-1/5 py-4 px-1 text-center border-b-2 font-medium text-sm">
                        <svg class="mx-auto h-5 w-5 mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Risks
                    </button>
                    <button @click="activeTab = 'tasks'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'tasks', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tasks' }" class="w-1/5 py-4 px-1 text-center border-b-2 font-medium text-sm">
                        <svg class="mx-auto h-5 w-5 mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Related Tasks
                    </button>
                </nav>
            </div>
            <div class="p-6">
                <!-- Deliverables Tab Content -->
                <div x-show="activeTab === 'deliverables'">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Expected Deliverables</h3>
                    <?php if (isset($milestone['deliverables']) && $milestone['deliverables']): ?>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <div class="prose prose-sm max-w-none text-gray-700">
                                <?= nl2br(esc($milestone['deliverables'])) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-500 italic">No deliverables specified for this milestone.</p>
                    <?php endif; ?>
                </div>

                <!-- Success Criteria Tab Content -->
                <div x-show="activeTab === 'criteria'" x-cloak>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Success Criteria</h3>
                    <?php if (isset($milestone['success_criteria']) && $milestone['success_criteria']): ?>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <div class="prose prose-sm max-w-none text-gray-700">
                                <?= nl2br(esc($milestone['success_criteria'])) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-500 italic">No success criteria defined for this milestone.</p>
                    <?php endif; ?>
                </div>

                <!-- Dependencies Tab Content -->
                <div x-show="activeTab === 'dependencies'" x-cloak>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Milestone Dependencies</h3>
                    </div>
                    <?php if (!empty($dependencies)): ?>
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dependent Milestone</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($dependencies as $dep): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="<?= base_url('admin/milestones/view/' . $dep['id']) ?>" class="text-indigo-600 hover:text-indigo-900">
                                                <?= esc($dep['title']) ?>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= esc($dep['project_name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $depStatusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'in_progress' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'not_started' => 'bg-gray-100 text-gray-800',
                                                'on_hold' => 'bg-purple-100 text-purple-800'
                                            ];
                                            $depStatusColor = $depStatusColors[$dep['status']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $depStatusColor ?>">
                                                <?= formatStatus($dep['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php if ($dep['planned_end_date']): ?>
                                                <?= date('M d, Y', strtotime($dep['planned_end_date'])) ?>
                                                <?php if (isOverdue($dep['planned_end_date'], $dep['status'])): ?>
                                                    <div class="mt-1">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Overdue</span>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">Not set</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <?php
                                                $depProgressColor = 'bg-blue-600';
                                                if ($dep['progress_percentage'] < 25) {
                                                    $depProgressColor = 'bg-red-600';
                                                } else if ($dep['progress_percentage'] < 50) {
                                                    $depProgressColor = 'bg-orange-500';
                                                } else if ($dep['progress_percentage'] < 75) {
                                                    $depProgressColor = 'bg-yellow-500';
                                                } else {
                                                    $depProgressColor = 'bg-green-500';
                                                }
                                                ?>
                                                <div class="<?= $depProgressColor ?> h-1.5 rounded-full" style="width: <?= $dep['progress_percentage'] ?>%"></div>
                                            </div>
                                            <div class="text-xs text-gray-500 text-right mt-1"><?= $dep['progress_percentage'] ?>%</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="<?= base_url('admin/milestones/view/' . $dep['id']) ?>" class="text-indigo-600 hover:text-indigo-900">
                                                <span class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                                    <svg class="mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-10 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No dependencies</h3>
                            <p class="mt-1 text-sm text-gray-500">There are no dependencies set for this milestone.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Risks Tab Content -->
                <div x-show="activeTab === 'risks'" x-cloak>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Risk Assessment</h3>
                    <?php if (isset($milestone['risk_description']) && $milestone['risk_description']): ?>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="text-base font-medium text-gray-900">Risk Description</h4>
                                <?php
                                $riskColors = [
                                    'low' => 'bg-green-100 text-green-800',
                                    'medium' => 'bg-yellow-100 text-yellow-800',
                                    'high' => 'bg-red-100 text-red-800',
                                    'critical' => 'bg-black bg-opacity-20 text-white'
                                ];
                                $riskColor = isset($milestone['risk_level']) ? ($riskColors[$milestone['risk_level']] ?? 'bg-gray-100 text-gray-800') : 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium <?= $riskColor ?>">
                                    <?= ucfirst(isset($milestone['risk_level']) ? $milestone['risk_level'] : 'Low') ?> Risk
                                </span>
                            </div>
                            <div class="prose prose-sm max-w-none text-gray-700">
                                <?= nl2br(esc($milestone['risk_description'])) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-500 italic">No risk assessment provided for this milestone.</p>
                    <?php endif; ?>
                </div>

                <!-- Related Tasks Tab Content -->
                <div x-show="activeTab === 'tasks'" x-cloak>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Related Tasks</h3>
                        <a href="<?= base_url('admin/tasks/create?project_id=' . $milestone['project_id']) ?>" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create Task
                        </a>
                    </div>
                    
                    <?php if (!empty($related_tasks)): ?>
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($related_tasks as $task): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="<?= base_url('admin/tasks/view/' . $task['id']) ?>" class="text-indigo-600 hover:text-indigo-900">
                                                <?= esc($task['title']) ?>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $taskStatusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'in_progress' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'not_started' => 'bg-gray-100 text-gray-800',
                                                'on_hold' => 'bg-purple-100 text-purple-800'
                                            ];
                                            $taskStatusColor = $taskStatusColors[$task['status']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $taskStatusColor ?>">
                                                <?= formatStatus($task['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= $task['assigned_to'] ? esc($task['assigned_name']) : 'Unassigned' ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php if ($task['planned_end_date']): ?>
                                                <?= date('M d, Y', strtotime($task['planned_end_date'])) ?>
                                                <?php if (isOverdue($task['planned_end_date'], $task['status'])): ?>
                                                    <div class="mt-1">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Overdue</span>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">Not set</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <?php
                                                $taskProgressColor = 'bg-blue-600';
                                                if ($task['progress_percentage'] < 25) {
                                                    $taskProgressColor = 'bg-red-600';
                                                } else if ($task['progress_percentage'] < 50) {
                                                    $taskProgressColor = 'bg-orange-500';
                                                } else if ($task['progress_percentage'] < 75) {
                                                    $taskProgressColor = 'bg-yellow-500';
                                                } else {
                                                    $taskProgressColor = 'bg-green-500';
                                                }
                                                ?>
                                                <div class="<?= $taskProgressColor ?> h-1.5 rounded-full" style="width: <?= $task['progress_percentage'] ?>%"></div>
                                            </div>
                                            <div class="text-xs text-gray-500 text-right mt-1"><?= $task['progress_percentage'] ?>%</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="<?= base_url('admin/tasks/view/' . $task['id']) ?>" class="text-indigo-600 hover:text-indigo-900">
                                                <span class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                                    <svg class="mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-10 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks</h3>
                            <p class="mt-1 text-sm text-gray-500">No related tasks found for this milestone.</p>
                            <div class="mt-6">
                                <a href="<?= base_url('admin/tasks/create?project_id=' . $milestone['project_id']) ?>" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create First Task
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    <?php if (isset($milestone['notes']) && $milestone['notes']): ?>
    <div class="mt-6">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Additional Notes</h3>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                <div class="sm:px-6 sm:py-5">
                    <p class="text-sm text-gray-900"><?= nl2br(esc($milestone['notes'])) ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Progress Update Modal -->
<div class="fixed inset-0 overflow-y-auto hidden" id="progressModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="<?= base_url('admin/milestones/' . $milestone['id'] . '/update-progress') ?>" method="post">
                <?= csrf_field() ?>
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                                Update Progress
                            </h3>
                            <div class="mt-4">
                                <div class="mb-4">
                                    <label for="progress_percentage" class="block text-sm font-medium text-gray-700">Progress Percentage</label>
                                    <input type="number" name="progress_percentage" id="progress_percentage" 
                                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                           min="0" max="100" value="<?= $milestone['progress_percentage'] ?>">
                                </div>
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Progress Notes</label>
                                    <textarea name="notes" id="notes" rows="3"
                                              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                              placeholder="Add notes about the progress update..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update Progress
                    </button>
                    <button type="button" onclick="closeProgressModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
[x-cloak] { display: none !important; }
</style>

<script>
function completeMilestone(milestoneId) {
    if (confirm('Are you sure you want to mark this milestone as completed?')) {
        fetch('<?= base_url('admin/milestones/complete') ?>/' + milestoneId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: '<?= csrf_token() ?>=<?= csrf_hash() ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error completing milestone: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error completing milestone');
        });
    }
}

function updateProgress() {
    document.getElementById('progressModal').classList.remove('hidden');
}

function closeProgressModal() {
    document.getElementById('progressModal').classList.add('hidden');
}
</script>
<?= $this->endSection() ?>
