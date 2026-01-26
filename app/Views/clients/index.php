<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Clients Management -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Clients</h1>
            <p class="text-gray-600">Manage client information and relationships</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/clients/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Add Client
            </a>
            <div class="flex gap-2">
                <a href="<?= base_url('admin/clients/export/pdf') ?>" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                    Export PDF
                </a>
                <a href="<?= base_url('admin/clients/export/excel') ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                    Export Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Clients</p>
                    <p class="text-2xl font-bold text-indigo-600"><?= $stats['total_clients'] ?></p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Clients</p>
                    <p class="text-2xl font-bold text-green-600"><?= $stats['active_clients'] ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-check" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">New This Month</p>
                    <p class="text-2xl font-bold text-blue-600"><?= $stats['clients_this_month'] ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" name="search" value="<?= esc($search) ?>" 
                       placeholder="Search clients..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            
            <div>
                <label for="client_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="client_type" name="client_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="individual" <?= $type_filter === 'individual' ? 'selected' : '' ?>>Individual</option>
                    <option value="company" <?= $type_filter === 'company' ? 'selected' : '' ?>>Company</option>
                    <option value="government" <?= $type_filter === 'government' ? 'selected' : '' ?>>Government</option>
                </select>
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-lucide="search" class="w-4 h-4 mr-2 inline"></i>
                    Search
                </button>
                <a href="<?= base_url('admin/clients') ?>" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Clients Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($clients)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="users" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">No clients found</p>
                                <p class="text-sm">Get started by adding your first client</p>
                                <a href="<?= base_url('admin/clients/create') ?>" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                    Add Client
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clients as $client): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <i data-lucide="user" class="w-5 h-5 text-indigo-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?= esc($client['name']) ?></div>
                                            <div class="text-sm text-gray-500"><?= esc($client['client_code']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?= $client['client_type'] === 'individual' ? 'bg-blue-100 text-blue-800' : '' ?>
                                        <?= $client['client_type'] === 'company' ? 'bg-green-100 text-green-800' : '' ?>
                                        <?= $client['client_type'] === 'government' ? 'bg-purple-100 text-purple-800' : '' ?>">
                                        <?= ucfirst($client['client_type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= esc($client['email']) ?></div>
                                    <div class="text-sm text-gray-500"><?= esc($client['phone']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= esc($client['company_name']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?= $client['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= ucfirst($client['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M j, Y', strtotime($client['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="<?= base_url('admin/clients/' . $client['id']) ?>"
                                           class="text-indigo-600 hover:text-indigo-900" title="View">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="<?= base_url('admin/clients/' . $client['id'] . '/edit') ?>"
                                           class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <form method="POST" action="<?= base_url('admin/clients/toggle/' . $client['id']) ?>" class="inline" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-900" 
                                                    title="Toggle Status"
                                                    onclick="return confirm('Are you sure you want to <?= $client['status'] === 'active' ? 'deactivate' : 'activate' ?> this client?')">
                                                <i data-lucide="toggle-left" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <a href="<?= base_url('admin/clients/delete/' . $client['id']) ?>" 
                                           class="text-red-600 hover:text-red-900" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pager): ?>
            <div class="px-6 py-3 border-t border-gray-200">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Initialize Lucide icons
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>

<?= $this->endSection() ?>
