<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Safety Reports<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.report-card { @apply bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-file-alt mr-2"></i>Safety Reports</h1>
            <p class="text-gray-600 mt-1">Generate and manage safety reports for projects</p>
        </div>
        <a href="<?= base_url('incident-safety/reports/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            <i class="fas fa-plus mr-2"></i> New Report
        </a>
    </div>

    <!-- Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex justify-between items-center">
            <span><?= session()->getFlashdata('success') ?></span>
            <button type="button" class="text-green-800 hover:text-green-900 text-xl">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex justify-between items-center">
            <span><?= session()->getFlashdata('error') ?></span>
            <button type="button" class="text-red-800 hover:text-red-900 text-xl">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form method="GET" action="<?= base_url('incident-safety/reports') ?>" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Projects</option>
                        <?php if (!empty($projects)): ?>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>" 
                                    <?= request()->getGet('project_id') == $project['id'] ? 'selected' : '' ?>>
                                    <?= $project['name'] ?? ($project['project_name'] ?? 'N/A') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                    <select name="report_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Types</option>
                        <option value="daily" <?= request()->getGet('report_type') == 'daily' ? 'selected' : '' ?>>Daily</option>
                        <option value="weekly" <?= request()->getGet('report_type') == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                        <option value="monthly" <?= request()->getGet('report_type') == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                        <option value="quarterly" <?= request()->getGet('report_type') == 'quarterly' ? 'selected' : '' ?>>Quarterly</option>
                        <option value="annual" <?= request()->getGet('report_type') == 'annual' ? 'selected' : '' ?>>Annual</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Status</option>
                        <option value="draft" <?= request()->getGet('status') == 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="pending_review" <?= request()->getGet('status') == 'pending_review' ? 'selected' : '' ?>>Pending Review</option>
                        <option value="approved" <?= request()->getGet('status') == 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="published" <?= request()->getGet('status') == 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Report Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Generated By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (!empty($reports)): ?>
                        <?php foreach ($reports as $report): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <strong class="text-gray-900"><?= $report['report_code'] ?></strong>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700"><?= $report['project_name'] ?? 'N/A' ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $reportTypeColors = [
                                        'daily' => 'blue',
                                        'weekly' => 'green',
                                        'monthly' => 'purple',
                                        'quarterly' => 'indigo',
                                        'annual' => 'red'
                                    ];
                                    $color = $reportTypeColors[$report['report_type'] ?? ''] ?? 'gray';
                                    $bgClass = "bg-{$color}-100";
                                    $textClass = "text-{$color}-800";
                                    ?>
                                    <span class="<?= $bgClass ?> <?= $textClass ?> px-3 py-1 rounded-full text-xs font-medium">
                                        <?= ucfirst($report['report_type'] ?? '') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?= date('M d, Y', strtotime($report['report_period_start'])) ?> - 
                                    <?= date('M d, Y', strtotime($report['report_period_end'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700"><?= $report['generated_by_name'] ?? 'N/A' ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $statusColors = [
                                        'draft' => 'yellow',
                                        'pending_review' => 'blue',
                                        'approved' => 'green',
                                        'published' => 'purple'
                                    ];
                                    $color = $statusColors[$report['status'] ?? ''] ?? 'gray';
                                    $bgClass = "bg-{$color}-100";
                                    $textClass = "text-{$color}-800";
                                    ?>
                                    <span class="<?= $bgClass ?> <?= $textClass ?> px-3 py-1 rounded-full text-xs font-medium">
                                        <?= ucfirst(str_replace('_', ' ', $report['status'] ?? '')) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="<?= base_url('incident-safety/reports/' . $report['id']) ?>" 
                                       class="text-indigo-600 hover:text-indigo-900 transition" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                <p>No reports found. <a href="<?= base_url('incident-safety/reports/create') ?>" class="text-indigo-600 hover:text-indigo-900">Create one</a></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if (!empty($pager)): ?>
        <nav class="mt-6 flex justify-center">
            <?= $pager->links() ?>
        </nav>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
