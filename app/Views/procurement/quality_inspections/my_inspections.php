<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Quality Inspections</h1>
            <p class="text-gray-600">Inspections assigned to you</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <a href="<?= base_url('admin/quality-inspections') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> All Inspections
            </a>
        </div>
    </div>

    <!-- Inspections Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Pending Inspections</h2>
        </div>

        <div class="overflow-x-auto">
            <?php if (!empty($inspections)): ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Inspection ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Material
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assigned Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($inspections as $inspection): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        #<?= esc($inspection['id']) ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        GRN: <?= esc($inspection['grn_number']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= esc($inspection['material_name']) ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= esc($inspection['item_code']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php
                                        switch($inspection['inspection_type']) {
                                            case 'incoming':
                                                echo 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'random':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'complaint':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                            case 'audit':
                                                echo 'bg-purple-100 text-purple-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= ucfirst(str_replace('_', ' ', $inspection['inspection_type'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= number_format($inspection['quantity_inspected']) ?> <?= esc($inspection['unit']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M j, Y', strtotime($inspection['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="<?= base_url('admin/quality-inspections/' . $inspection['id'] . '/inspect') ?>"
                                           class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700 transition-colors">
                                            <i data-lucide="clipboard-check" class="w-3 h-3 mr-1"></i>
                                            Conduct
                                        </a>
                                        <a href="<?= base_url('admin/quality-inspections/' . $inspection['id']) ?>"
                                           class="inline-flex items-center px-3 py-1 bg-gray-600 text-white text-xs rounded-md hover:bg-gray-700 transition-colors">
                                            <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="clipboard-check" class="w-12 h-12 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Pending Inspections</h3>
                    <p class="text-gray-500 mb-6">You don't have any quality inspections assigned to you at the moment.</p>
                    <a href="<?= base_url('admin/quality-inspections') ?>"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                        View All Inspections
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <?php if (!empty($inspections)): ?>
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <i data-lucide="clipboard-check" class="w-4 h-4 text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Pending</dt>
                        <dd class="text-2xl font-semibold text-gray-900"><?= count($inspections) ?></dd>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">Incoming Inspections</dt>
                        <dd class="text-2xl font-semibold text-gray-900">
                            <?= count(array_filter($inspections, function($insp) { return $insp['inspection_type'] === 'incoming'; })) ?>
                        </dd>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-4 h-4 text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">This Week</dt>
                        <dd class="text-2xl font-semibold text-gray-900">
                            <?php
                            $thisWeek = count(array_filter($inspections, function($insp) {
                                return strtotime($insp['created_at']) >= strtotime('monday this week');
                            }));
                            echo $thisWeek;
                            ?>
                        </dd>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Initialize Lucide icons
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>

<?= $this->endSection() ?>
