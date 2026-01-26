<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Client View Page -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= esc($client['name']) ?></h1>
            <p class="text-gray-600">Client Details and Information</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/clients') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Clients
            </a>
            <a href="<?= base_url('admin/clients/' . $client['id'] . '/edit') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit Client
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Client Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Client Name</label>
                            <p class="mt-1 text-sm text-gray-900"><?= esc($client['name']) ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Client Code</label>
                            <p class="mt-1 text-sm text-gray-900"><?= esc($client['client_code']) ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Client Type</label>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                <?= $client['client_type'] === 'individual' ? 'bg-blue-100 text-blue-800' : '' ?>
                                <?= $client['client_type'] === 'company' ? 'bg-green-100 text-green-800' : '' ?>
                                <?= $client['client_type'] === 'government' ? 'bg-purple-100 text-purple-800' : '' ?>">
                                <?= ucfirst($client['client_type']) ?>
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                <?= $client['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= ucfirst($client['status']) ?>
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Company</label>
                            <p class="mt-1 text-sm text-gray-900"><?= esc($client['company_name']) ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Contact Person</label>
                            <p class="mt-1 text-sm text-gray-900"><?= esc($client['contact_person']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <?php if ($client['email']): ?>
                                    <a href="mailto:<?= esc($client['email']) ?>" class="text-indigo-600 hover:text-indigo-900">
                                        <?= esc($client['email']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400">Not provided</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <?php if ($client['phone']): ?>
                                    <a href="tel:<?= esc($client['phone']) ?>" class="text-indigo-600 hover:text-indigo-900">
                                        <?= esc($client['phone']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400">Not provided</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Mobile</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <?php if ($client['mobile']): ?>
                                    <a href="tel:<?= esc($client['mobile']) ?>" class="text-indigo-600 hover:text-indigo-900">
                                        <?= esc($client['mobile']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400">Not provided</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information Card -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Address</label>
                            <p class="mt-1 text-sm text-gray-900"><?= $client['address'] ? esc($client['address']) : 'Not provided' ?></p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">City</label>
                                <p class="mt-1 text-sm text-gray-900"><?= $client['city'] ? esc($client['city']) : 'Not provided' ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">State/Province</label>
                                <p class="mt-1 text-sm text-gray-900"><?= $client['state'] ? esc($client['state']) : 'Not provided' ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Country</label>
                                <p class="mt-1 text-sm text-gray-900"><?= $client['country'] ? esc($client['country']) : 'Not provided' ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Postal Code</label>
                                <p class="mt-1 text-sm text-gray-900"><?= $client['postal_code'] ? esc($client['postal_code']) : 'Not provided' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Information Card -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Business Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Tax Number</label>
                            <p class="mt-1 text-sm text-gray-900"><?= $client['tax_number'] ? esc($client['tax_number']) : 'Not provided' ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Payment Terms</label>
                            <p class="mt-1 text-sm text-gray-900"><?= $client['payment_terms'] ? esc($client['payment_terms']) . ' days' : 'Not set' ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Credit Limit</label>
                            <p class="mt-1 text-sm text-gray-900"><?= $client['credit_limit'] ? 'MWK ' . number_format($client['credit_limit'], 2) : 'Not set' ?></p>
                        </div>
                    </div>
                    <?php if ($client['notes']): ?>
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500">Notes</label>
                            <p class="mt-1 text-sm text-gray-900"><?= esc($client['notes']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions Card -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="<?= base_url('admin/clients/' . $client['id'] . '/edit') ?>" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                            Edit Client
                        </a>
                        <a href="<?= base_url('admin/projects/create?client_id=' . $client['id']) ?>" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            New Project
                        </a>
                        <?php if ($client['email']): ?>
                            <a href="mailto:<?= esc($client['email']) ?>" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                                Send Email
                            </a>
                        <?php endif; ?>
                        <?php if ($client['phone']): ?>
                            <a href="tel:<?= esc($client['phone']) ?>" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                                <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                Call Client
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Client Projects Card -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Projects</h3>
                    <?php if (empty($projects)): ?>
                        <p class="text-sm text-gray-500">No projects found for this client.</p>
                        <a href="<?= base_url('admin/projects/create?client_id=' . $client['id']) ?>" 
                           class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                            <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                            Create first project
                        </a>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($projects as $project): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900"><?= esc($project['name']) ?></h4>
                                        <p class="text-xs text-gray-500"><?= esc($project['project_code']) ?></p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            <?= $project['status'] === 'active' ? 'bg-green-100 text-green-800' : '' ?>
                                            <?= $project['status'] === 'planning' ? 'bg-blue-100 text-blue-800' : '' ?>
                                            <?= $project['status'] === 'on_hold' ? 'bg-yellow-100 text-yellow-800' : '' ?>
                                            <?= $project['status'] === 'completed' ? 'bg-gray-100 text-gray-800' : '' ?>
                                            <?= $project['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : '' ?>">
                                            <?= ucfirst(str_replace('_', ' ', $project['status'])) ?>
                                        </span>
                                        <a href="<?= base_url('admin/projects/view/' . $project['id']) ?>" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            <i data-lucide="external-link" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4">
                            <a href="<?= base_url('admin/projects?client_id=' . $client['id']) ?>" 
                               class="text-sm text-indigo-600 hover:text-indigo-900">
                                View all projects →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Client Timeline Card -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline</h3>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-indigo-600 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Client Created</p>
                                <p class="text-xs text-gray-500"><?= date('M j, Y g:i A', strtotime($client['created_at'])) ?></p>
                            </div>
                        </div>
                        <?php if ($client['updated_at'] !== $client['created_at']): ?>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-green-600 rounded-full mt-2"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Last Updated</p>
                                    <p class="text-xs text-gray-500"><?= date('M j, Y g:i A', strtotime($client['updated_at'])) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
