<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Milestone Report
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Milestone Report</h1>
            <div class="flex space-x-3">
                <a href="<?= base_url('admin/milestones') ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Milestones
                </a>
                <a href="<?= base_url('admin/milestones/exportExcel') . '?' . http_build_query($filters) ?>" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a>
                <a href="<?= base_url('admin/milestones/exportPdf') . '?' . http_build_query($filters) ?>" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-file-pdf mr-2"></i>Generate PDF Report
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Filters -->
            <div class="mb-6 bg-gray-50 rounded-lg p-4">
                <form method="GET" action="<?= base_url('admin/milestones/report') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                        <select id="project_id" name="project_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" id="date_from" name="date_from"
                               value="<?= $filters['date_from'] ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" id="date_to" name="date_to"
                               value="<?= $filters['date_to'] ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-search mr-2"></i>Generate Report
                        </button>
                    </div>
                </form>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-indigo-600 rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 text-center">
                        <div class="text-3xl font-bold text-white mb-1"><?= $stats['total'] ?></div>
                        <div class="text-sm text-indigo-100">Total Milestones</div>
                    </div>
                </div>

                <div class="bg-green-600 rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 text-center">
                        <div class="text-3xl font-bold text-white mb-1"><?= $stats['completed'] ?></div>
                        <div class="text-sm text-green-100">Completed</div>
                    </div>
                </div>

                <div class="bg-red-600 rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 text-center">
                        <div class="text-3xl font-bold text-white mb-1"><?= $stats['overdue'] ?></div>
                        <div class="text-sm text-red-100">Overdue</div>
                    </div>
                </div>

                <div class="bg-blue-600 rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 text-center">
                        <div class="text-3xl font-bold text-white mb-1"><?= $stats['upcoming'] ?></div>
                        <div class="text-sm text-blue-100">Upcoming</div>
                    </div>
                </div>
            </div>

            <!-- Report Summary -->
            <div class="mb-6 bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Report Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">
                            <strong>Report Period:</strong>
                            <?= date('M d, Y', strtotime($filters['date_from'])) ?> -
                            <?= date('M d, Y', strtotime($filters['date_to'])) ?>
                        </p>
                        <?php if (!empty($filters['project_id'])): ?>
                            <p class="text-sm text-gray-600 mt-1">
                                <strong>Project:</strong>
                                <?php
                                $projectName = '';
                                foreach($projects as $project) {
                                    if ($project['id'] == $filters['project_id']) {
                                        $projectName = $project['name'];
                                        break;
                                    }
                                }
                                echo esc($projectName);
                                ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">
                            <strong>Completion Rate:</strong>
                            <?= $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0 ?>%
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            <strong>On-Time Performance:</strong>
                            <?php
                            $onTime = $stats['total'] - $stats['overdue'];
                            echo $stats['total'] > 0 ? round(($onTime / $stats['total']) * 100, 1) : 0;
                            ?>%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Milestones Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="milestonesReportTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Milestone</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Late</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($milestones as $milestone): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-normal">
                                    <div class="max-w-xs">
                                        <div class="text-sm font-medium text-gray-900"><?= esc($milestone['title']) ?></div>
                                        <?php if ($milestone['description']): ?>
                                        <div class="text-sm text-gray-500 mt-1 truncate"><?= esc($milestone['description']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= esc($milestone['project_name']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusClasses = [
                                        'not_started' => 'bg-gray-100 text-gray-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'review' => 'bg-purple-100 text-purple-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'on_hold' => 'bg-orange-100 text-orange-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ][$milestone['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClasses ?>">
                                        <?= ucwords(str_replace('_', ' ', $milestone['status'] ?: 'not_started')) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($milestone['planned_end_date']): ?>
                                        <div class="text-sm text-gray-900"><?= date('M d, Y', strtotime($milestone['planned_end_date'])) ?></div>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($milestone['status'] === 'completed' && isset($milestone['actual_end_date']) && $milestone['actual_end_date'] && strtotime($milestone['actual_end_date']) > 0): ?>
                                        <div class="text-sm text-gray-900"><?= date('M d, Y', strtotime($milestone['actual_end_date'])) ?></div>
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

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $daysLate = 0;
                                    if ($milestone['planned_end_date'] && $milestone['status'] !== 'completed') {
                                        $dueDate = strtotime($milestone['planned_end_date']);
                                        $today = strtotime(date('Y-m-d'));
                                        if ($today > $dueDate) {
                                            $daysLate = floor(($today - $dueDate) / (60 * 60 * 24));
                                        }
                                    } elseif ($milestone['actual_end_date'] && $milestone['planned_end_date']) {
                                        $dueDate = strtotime($milestone['planned_end_date']);
                                        $completedDate = strtotime($milestone['actual_end_date']);
                                        if ($completedDate > $dueDate) {
                                            $daysLate = floor(($completedDate - $dueDate) / (60 * 60 * 24));
                                        }
                                    }
                                    ?>
                                    <?php if ($daysLate > 0): ?>
                                        <span class="text-sm text-red-600 font-medium"><?= $daysLate ?> days</span>
                                    <?php elseif ($milestone['status'] === 'completed' && $daysLate <= 0): ?>
                                        <span class="text-sm text-green-600 font-medium">On time</span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (empty($milestones)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-chart-line fa-3x text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No milestones found</h3>
                        <p class="text-gray-500">Try adjusting your filters to see milestone data.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable if available
    if (typeof $ !== 'undefined' && $.fn.DataTable !== undefined) {
        $('#milestonesReportTable').DataTable({
            "pageLength": 50,
            "order": [[ 3, "asc" ]], // Order by due date
            "columnDefs": [
                { "orderable": false, "targets": [6] } // Days Late column
            ],
            "dom": '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
            "language": {
                "search": "Filter milestones:",
                "lengthMenu": "Show _MENU_ milestones per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ milestones",
                "infoEmpty": "No milestones found",
                "infoFiltered": "(filtered from _MAX_ total milestones)"
            }
        });
    }
});
</script>
<?= $this->endSection() ?>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        font-size: 12px;
    }
    .bg-gray-50 {
        background-color: #f9fafb !important;
        -webkit-print-color-adjust: exact;
    }
}
</style>
