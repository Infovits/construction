<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quality Inspections</h1>
            <p class="text-gray-600">Manage quality control and inspection processes</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/quality-inspections/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">    
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                New Inspection
            </a>
            <a href="<?= base_url('admin/quality-inspections/my-inspections') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">    
                <i data-lucide="user-check" class="w-4 h-4 mr-2"></i>
                My Inspections
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="pending" <?= (isset($filters['status']) && $filters['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="passed" <?= (isset($filters['status']) && $filters['status'] === 'passed') ? 'selected' : '' ?>>Passed</option>
                    <option value="failed" <?= (isset($filters['status']) && $filters['status'] === 'failed') ? 'selected' : '' ?>>Failed</option>
                    <option value="conditional" <?= (isset($filters['status']) && $filters['status'] === 'conditional') ? 'selected' : '' ?>>Conditional</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Inspection Type</label>
                <select name="inspection_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="incoming" <?= (isset($filters['inspection_type']) && $filters['inspection_type'] === 'incoming') ? 'selected' : '' ?>>Incoming</option>
                    <option value="random" <?= (isset($filters['inspection_type']) && $filters['inspection_type'] === 'random') ? 'selected' : '' ?>>Random</option>
                    <option value="complaint" <?= (isset($filters['inspection_type']) && $filters['inspection_type'] === 'complaint') ? 'selected' : '' ?>>Complaint</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Inspector</label>
                <select name="inspector_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Inspectors</option>
                    <?php if (isset($inspectors)): ?>
                        <?php foreach ($inspectors as $inspector): ?>
                            <option value="<?= $inspector['id'] ?>" <?= (isset($filters['inspector_id']) && $filters['inspector_id'] == $inspector['id']) ? 'selected' : '' ?>>
                                <?= esc($inspector['first_name'] . ' ' . $inspector['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="flex items-end">
                <div class="flex gap-2 w-full">
                    <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                        Filter
                    </button>
                    <a href="<?= base_url('admin/quality-inspections') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Inspections List -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Quality Inspections</h2>
        </div>
        
        <?php if (isset($inspections) && !empty($inspections)): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inspection #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GRN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inspector</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($inspections as $inspection): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= esc($inspection['inspection_number']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= esc($inspection['material_name'] ?? 'N/A') ?></div>
                                        <div class="text-sm text-gray-500"><?= esc($inspection['material_code'] ?? '') ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($inspection['grn_number'])): ?>
                                        <a href="<?= base_url('admin/goods-receipt/' . $inspection['grn_id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800">
                                            <?= esc($inspection['grn_number']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= ucfirst($inspection['inspection_type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= esc($inspection['inspector_name'] ?? 'Unassigned') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php
                                        switch($inspection['status']) {
                                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'passed': echo 'bg-green-100 text-green-800'; break;
                                            case 'failed': echo 'bg-red-100 text-red-800'; break;
                                            case 'conditional': echo 'bg-orange-100 text-orange-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800'; break;
                                        }
                                        ?>">
                                        <?= ucfirst($inspection['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M j, Y', strtotime($inspection['inspection_date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="<?= base_url('admin/quality-inspections/' . $inspection['id']) ?>" 
                                           class="text-indigo-600 hover:text-indigo-900" title="View">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <?php if ($inspection['status'] === 'pending'): ?>
                                            <a href="<?= base_url('admin/quality-inspections/' . $inspection['id'] . '/edit') ?>" 
                                               class="text-blue-600 hover:text-blue-900" title="Edit">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Quality Inspections Found</h3>
                <p class="text-gray-500 mb-4">Start by creating a new quality inspection or adjust your search filters.</p>
                <a href="<?= base_url('admin/quality-inspections/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    New Inspection
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
lucide.createIcons();
</script>

<?= $this->endSection() ?>
