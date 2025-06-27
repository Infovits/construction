<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Project Category Details -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Category Details</h1>
            <p class="text-gray-600">View category information and associated projects</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/project-categories') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Categories
            </a>
            <a href="<?= base_url('admin/project-categories/edit/' . $category['id']) ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit Category
            </a>
        </div>
    </div>

    <!-- Category Information Card -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6">
            <div class="flex items-start space-x-4">
                <div class="w-16 h-16 rounded-lg flex items-center justify-center" style="background-color: <?= esc($category['color_code']) ?>20;">
                    <div class="w-8 h-8 rounded-full" style="background-color: <?= esc($category['color_code']) ?>"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h2 class="text-xl font-bold text-gray-900"><?= esc($category['name']) ?></h2>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $category['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= $category['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                    <?php if ($category['description']): ?>
                        <p class="text-gray-600 mb-4"><?= esc($category['description']) ?></p>
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-500">Company:</span>
                            <p class="text-gray-900"><?= esc($category['company_name'] ?? 'N/A') ?></p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Created:</span>
                            <p class="text-gray-900"><?= date('M j, Y', strtotime($category['created_at'])) ?></p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Projects Count:</span>
                            <p class="text-gray-900"><?= $project_count ?> project(s)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Associated Projects -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Associated Projects (<?= $project_count ?>)</h3>
        </div>

        <?php if (!empty($projects)): ?>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($projects as $project): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?= esc($project['name']) ?></div>
                                    <div class="text-sm text-gray-500"><?= esc($project['project_code']) ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= ucfirst(str_replace('_', ' ', $project['project_type'])) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-<?= getStatusBadgeClass($project['status'], 'project') ?>-100 text-<?= getStatusBadgeClass($project['status'], 'project') ?>-800">
                                    <?= ucfirst(str_replace('_', ' ', $project['status'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: <?= $project['progress_percentage'] ?>%"></div>
                                    </div>
                                    <span class="text-sm text-gray-900"><?= $project['progress_percentage'] ?>%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $project['start_date'] ? date('M j, Y', strtotime($project['start_date'])) : 'Not set' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= base_url('admin/projects/view/' . $project['id']) ?>" 
                                   class="text-indigo-600 hover:text-indigo-900">View Project</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="folder-x" class="w-6 h-6 text-gray-400"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">No projects found</h3>
                <p class="text-sm text-gray-500">This category hasn't been assigned to any projects yet.</p>
                <div class="mt-4">
                    <a href="<?= base_url('admin/projects/create') ?>" class="inline-flex items-center px-3 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                        Create Project
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Category Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Project Status Distribution</h3>
            <?php if (!empty($projects)): ?>
                <?php
                $statusCounts = [];
                foreach ($projects as $project) {
                    $status = $project['status'];
                    $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
                }
                ?>
                <div class="space-y-3">
                    <?php foreach ($statusCounts as $status => $count): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600"><?= ucfirst(str_replace('_', ' ', $status)) ?></span>
                            <span class="text-sm font-medium text-gray-900"><?= $count ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-500">No project data available</p>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Category Information</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Color Code</span>
                    <span class="text-sm font-medium text-gray-900"><?= esc($category['color_code']) ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Status</span>
                    <span class="text-sm font-medium <?= $category['is_active'] ? 'text-green-600' : 'text-red-600' ?>">
                        <?= $category['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Last Updated</span>
                    <span class="text-sm font-medium text-gray-900"><?= date('M j, Y', strtotime($category['updated_at'])) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
});
</script>
<?= $this->endSection() ?>
