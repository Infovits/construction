<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Upcoming Milestones
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Upcoming Milestones</h1>
            <div class="flex space-x-3">
                <a href="<?= base_url('admin/milestones') ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Milestones
                </a>
                <a href="<?= base_url('admin/milestones/calendar') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-calendar mr-2"></i>Calendar View
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Filters -->
            <div class="mb-6 bg-gray-50 rounded-lg p-4">
                <form method="GET" action="<?= base_url('admin/milestones/upcoming') ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                        <select id="project_id" name="project_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Projects</option>
                            <?php foreach($projects as $project): ?>
                                <option value="<?= $project['id'] ?>"
                                        <?= ($project_id ?? '') == $project['id'] ? 'selected' : '' ?>>
                                    <?= esc($project['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="days" class="block text-sm font-medium text-gray-700 mb-1">Days Ahead</label>
                        <select id="days" name="days"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="7" <?= ($days ?? 30) == 7 ? 'selected' : '' ?>>Next 7 days</option>
                            <option value="14" <?= ($days ?? 30) == 14 ? 'selected' : '' ?>>Next 14 days</option>
                            <option value="30" <?= ($days ?? 30) == 30 ? 'selected' : '' ?>>Next 30 days</option>
                            <option value="60" <?= ($days ?? 30) == 60 ? 'selected' : '' ?>>Next 60 days</option>
                            <option value="90" <?= ($days ?? 30) == 90 ? 'selected' : '' ?>>Next 90 days</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-search mr-2"></i>Update View
                        </button>
                    </div>
                </form>
            </div>

            <!-- Summary -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Showing milestones due in the next <?= $days ?> days
                        </h3>
                        <p class="text-sm text-blue-700 mt-1">
                            Total upcoming milestones: <strong><?= count($milestones) ?></strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Milestones List -->
            <?php if (!empty($milestones)): ?>
                <div class="space-y-4">
                    <?php
                    $today = strtotime(date('Y-m-d'));
                    $tomorrow = strtotime('+1 day');
                    $weekFromNow = strtotime('+7 days');
                    ?>

                    <?php foreach ($milestones as $milestone): ?>
                        <?php
                        $dueDate = strtotime($milestone['planned_end_date']);
                        $daysUntilDue = floor(($dueDate - $today) / (60 * 60 * 24));

                        // Determine urgency class
                        if ($daysUntilDue < 0) {
                            $urgencyClass = 'border-l-red-500 bg-red-50';
                            $badgeClass = 'bg-red-100 text-red-800';
                            $urgencyText = 'Overdue';
                        } elseif ($daysUntilDue === 0) {
                            $urgencyClass = 'border-l-orange-500 bg-orange-50';
                            $badgeClass = 'bg-orange-100 text-orange-800';
                            $urgencyText = 'Due Today';
                        } elseif ($daysUntilDue === 1) {
                            $urgencyClass = 'border-l-yellow-500 bg-yellow-50';
                            $badgeClass = 'bg-yellow-100 text-yellow-800';
                            $urgencyText = 'Due Tomorrow';
                        } elseif ($daysUntilDue <= 7) {
                            $urgencyClass = 'border-l-blue-500 bg-blue-50';
                            $badgeClass = 'bg-blue-100 text-blue-800';
                            $urgencyText = 'Due This Week';
                        } else {
                            $urgencyClass = 'border-l-gray-500 bg-gray-50';
                            $badgeClass = 'bg-gray-100 text-gray-800';
                            $urgencyText = 'Upcoming';
                        }
                        ?>
                        <div class="border-l-4 rounded-lg shadow-sm overflow-hidden <?= $urgencyClass ?>">
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <a href="<?= base_url('admin/milestones/' . $milestone['id']) ?>" class="hover:text-blue-600">
                                                    <?= esc($milestone['title']) ?>
                                                </a>
                                            </h3>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full <?= $badgeClass ?>">
                                                <?= $urgencyText ?>
                                            </span>
                                        </div>

                                        <div class="mt-2 text-sm text-gray-600">
                                            <span class="font-medium">Project:</span>
                                            <a href="<?= base_url('admin/projects/view/' . $milestone['project_id']) ?>" class="text-blue-600 hover:text-blue-800">
                                                <?= esc($milestone['project_name']) ?>
                                            </a>
                                        </div>

                                        <?php if ($milestone['description']): ?>
                                            <div class="mt-2 text-sm text-gray-700">
                                                <?= esc(substr($milestone['description'], 0, 150)) ?>
                                                <?= strlen($milestone['description']) > 150 ? '...' : '' ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="ml-4 text-right">
                                        <div class="text-lg font-bold text-gray-900">
                                            <?php if ($daysUntilDue < 0): ?>
                                                <span class="text-red-600"><?= abs($daysUntilDue) ?> days overdue</span>
                                            <?php elseif ($daysUntilDue === 0): ?>
                                                <span class="text-orange-600">Due today</span>
                                            <?php elseif ($daysUntilDue === 1): ?>
                                                <span class="text-yellow-600">Tomorrow</span>
                                            <?php else: ?>
                                                <span class="text-gray-600"><?= $daysUntilDue ?> days</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Due: <?= date('M d, Y', $dueDate) ?>
                                        </div>

                                        <div class="mt-2">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                <?= ucwords(str_replace('_', ' ', $milestone['status'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-tasks mr-2"></i>
                                            Progress: <?= $milestone['progress_percentage'] ?>%
                                        </div>

                                        <?php if ($milestone['assigned_to']): ?>
                                            <?php
                                            $assignedUser = $this->userModel->find($milestone['assigned_to']);
                                            if ($assignedUser): ?>
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <i class="fas fa-user mr-2"></i>
                                                    Assigned to: <?= esc($assignedUser['first_name'] . ' ' . $assignedUser['last_name']) ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex space-x-2">
                                        <a href="<?= base_url('admin/milestones/' . $milestone['id']) ?>"
                                           class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        <a href="<?= base_url('admin/milestones/' . $milestone['id'] . '/edit') ?>"
                                           class="inline-flex items-center px-3 py-1 border border-transparent rounded-md text-sm font-medium text-indigo-600 bg-indigo-100 hover:bg-indigo-200">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-4">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $milestone['progress_percentage'] ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-calendar-check fa-4x text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No upcoming milestones</h3>
                    <p class="text-gray-500 mb-6">There are no milestones due in the next <?= $days ?> days.</p>
                    <div class="flex justify-center space-x-3">
                        <a href="<?= base_url('admin/milestones/create') ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-plus mr-2"></i>Create Milestone
                        </a>
                        <a href="<?= base_url('admin/milestones') ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-list mr-2"></i>View All Milestones
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Auto-submit form when filters change
    $('#project_id, #days').on('change', function() {
        $(this).closest('form').submit();
    });
});
</script>
<?= $this->endSection() ?>
