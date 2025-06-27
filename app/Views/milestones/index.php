<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Milestones Management
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class=" py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-800">Project Milestones</h1>
            <div class="flex space-x-3">
                <a href="<?= base_url('admin/milestones/create') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create Milestone
                </a>
                <a href="<?= base_url('admin/milestones/calendar') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Calendar View
                </a>
                <a href="<?= base_url('admin/milestones/report') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Report
                </a>
            </div>
        </div>
        <div class="p-6">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 text-red-700">
                    <p><?= session()->getFlashdata('error') ?></p>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 text-green-700">
                    <p><?= session()->getFlashdata('success') ?></p>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-indigo-600 rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 text-center">
                        <div class="text-3xl font-bold text-white mb-1"><?= $stats['total'] ?></div>
                        <div class="text-sm text-indigo-100">Total Milestones</div>
                    </div>
                </div>
                
                <div class="bg-amber-500 rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 text-center">
                        <div class="text-3xl font-bold text-white mb-1"><?= $stats['pending'] ?></div>
                        <div class="text-sm text-amber-100">Pending</div>
                    </div>
                </div>
                
                <div class="bg-blue-500 rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 text-center">
                        <div class="text-3xl font-bold text-white mb-1"><?= $stats['in_progress'] ?></div>
                        <div class="text-sm text-blue-100">In Progress</div>
                    </div>
                </div>
                
                <div class="bg-green-600 rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 text-center">
                        <div class="text-3xl font-bold text-white mb-1"><?= $stats['completed'] ?></div>
                        <div class="text-sm text-green-100">Completed</div>
                    </div>
                </div>
            </div>

            <!-- Overdue Alert -->
            <?php if (!empty($overdue_milestones)): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Overdue Milestones</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <?php foreach ($overdue_milestones as $milestone): ?>
                                        <li>
                                            <a href="<?= base_url('admin/milestones/view/' . $milestone['id']) ?>" class="font-medium hover:text-red-600">
                                                <?= esc($milestone['title']) ?>
                                            </a>
                                            (<?= esc($milestone['project_name']) ?>) - 
                                            Due: <?= date('M d, Y', strtotime($milestone['due_date'])) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="mb-6">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 sm:p-6">
                        <form method="GET" action="<?= base_url('admin/milestones') ?>">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div>
                                    <label for="project_filter" class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                                    <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            id="project_filter" name="project_id">
                                        <option value="">All Projects</option>
                                        <?php foreach($projects as $project): ?>
                                            <option value="<?= $project['id'] ?>" 
                                                    <?= ($filters['project_id'] ?? '') == $project['id'] ? 'selected' : '' ?>>
                                                <?= esc($project['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            id="status_filter" name="status">
                                        <option value="">All Status</option>
                                        <option value="pending" <?= ($filters['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="in_progress" <?= ($filters['status'] ?? '') == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                        <option value="completed" <?= ($filters['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="cancelled" <?= ($filters['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="date_filter" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                    <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                            id="date_filter" name="date_range">
                                        <option value="">All Dates</option>
                                        <option value="overdue" <?= ($filters['date_range'] ?? '') == 'overdue' ? 'selected' : '' ?>>Overdue</option>
                                        <option value="this_week" <?= ($filters['date_range'] ?? '') == 'this_week' ? 'selected' : '' ?>>This Week</option>
                                        <option value="this_month" <?= ($filters['date_range'] ?? '') == 'this_month' ? 'selected' : '' ?>>This Month</option>
                                        <option value="next_month" <?= ($filters['date_range'] ?? '') == 'next_month' ? 'selected' : '' ?>>Next Month</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                    <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                           id="search" name="search" placeholder="Search milestones..." value="<?= $filters['search'] ?? '' ?>">
                                </div>
                                
                                <div class="self-end">
                                    <div class="flex items-center space-x-2">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            Filter
                                        </button>
                                        <a href="<?= base_url('admin/milestones') ?>" class="inline-flex items-center p-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Milestones Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="milestonesTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Milestone</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($milestones as $milestone): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-normal">
                                    <div class="max-w-xs">
                                        <div class="text-sm font-medium text-gray-900"><?= esc($milestone['title']) ?></div>
                                        <?php if ($milestone['description']): ?>
                                        <div class="text-sm text-gray-500 mt-1"><?= esc(substr($milestone['description'], 0, 100)) ?><?= strlen($milestone['description']) > 100 ? '...' : '' ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <a href="<?= base_url('admin/projects/view/' . $milestone['project_id']) ?>" class="font-medium text-indigo-600 hover:text-indigo-900">
                                            <?= esc($milestone['project_name']) ?>
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500"><?= esc($milestone['project_code']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ][$milestone['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClasses ?>">
                                        <?= ucwords(str_replace('_', ' ', $milestone['status'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($milestone['due_date']): ?>
                                        <div class="text-sm text-gray-900"><?= date('M d, Y', strtotime($milestone['due_date'])) ?></div>
                                        <?php if (strtotime($milestone['due_date']) < time() && $milestone['status'] != 'completed'): ?>
                                            <div class="text-xs text-red-600 flex items-center mt-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                Overdue
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">Not set</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($milestone['completion_date']): ?>
                                        <div class="text-sm text-gray-900"><?= date('M d, Y', strtotime($milestone['completion_date'])) ?></div>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">Not completed</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: <?= $milestone['progress_percentage'] ?>%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1 inline-block"><?= $milestone['progress_percentage'] ?>%</span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="<?= base_url('admin/milestones/view/' . $milestone['id']) ?>" 
                                           class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="<?= base_url('admin/milestones/edit/' . $milestone['id']) ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <?php if ($milestone['status'] != 'completed'): ?>
                                        <button type="button" onclick="completeMilestone(<?= $milestone['id'] ?>)" 
                                                class="text-green-600 hover:text-green-900 p-1 rounded-full hover:bg-green-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <?php endif; ?>
                                        <button type="button" onclick="deleteMilestone(<?= $milestone['id'] ?>, '<?= esc($milestone['title']) ?>')" 
                                                class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (empty($milestones)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-flag fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No milestones found matching your criteria.</p>
                            <a href="<?= base_url('admin/milestones/create') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Milestone
                            </a>
                        </div>
                    <?php endif; ?>

            <!-- Pagination -->
            <?php if (isset($pager)): ?>
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-3">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <?php if ($pager->hasPrevious()): ?>
                            <a href="<?= $pager->getPrevious() ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                        <?php else: ?>
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-50 cursor-not-allowed">
                                Previous
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($pager->hasNext()): ?>
                            <a href="<?= $pager->getNext() ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        <?php else: ?>
                            <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-50 cursor-not-allowed">
                                Next
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium"><?= $pager->getCurrentFirstItem() ?></span>
                                to
                                <span class="font-medium"><?= $pager->getCurrentLastItem() ?></span>
                                of
                                <span class="font-medium"><?= $pager->getTotal() ?></span>
                                results
                            </p>
                        </div>
                        <div>
                            <?= $pager->links() ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable if needed
    if ($.fn.DataTable !== undefined) {
        $('#milestonesTable').DataTable({
            "pageLength": 25,
            "order": [[ 3, "asc" ]], // Order by due date
            "columnDefs": [
                { "orderable": false, "targets": 6 } // Actions column
            ]
        });
    }
});

function completeMilestone(milestoneId) {
    if (confirm('Are you sure you want to mark this milestone as completed?')) {
        $.post('<?= base_url('admin/milestones/complete') ?>/' + milestoneId, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error completing milestone: ' + response.message);
            }
        }).fail(function() {
            alert('Error completing milestone');
        });
    }
}

function deleteMilestone(milestoneId, milestoneName) {
    if (confirm('Are you sure you want to delete the milestone "' + milestoneName + '"? This action cannot be undone.')) {
        $.post('<?= base_url('admin/milestones/delete') ?>/' + milestoneId, {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error deleting milestone: ' + response.message);
            }
        }).fail(function() {
            alert('Error deleting milestone');
        });
    }
}

// Auto-submit form when filters change
$('#project_filter, #status_filter, #date_filter').on('change', function() {
    $(this).closest('form').submit();
});
</script>
<?= $this->endSection() ?>
