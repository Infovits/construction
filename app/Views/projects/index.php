<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Projects Management -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Projects</h1>
            <p class="text-gray-600">Manage and track all construction projects</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/projects/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                New Project
            </a>
            <div class="relative">
                <select onchange="window.location.href=this.value" class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="<?= base_url('admin/projects') ?>">All Projects</option>
                    <option value="<?= base_url('admin/projects?status=active') ?>" <?= (isset($_GET['status']) && $_GET['status'] == 'active') ? 'selected' : '' ?>>Active Projects</option>
                    <option value="<?= base_url('admin/projects?status=planning') ?>" <?= (isset($_GET['status']) && $_GET['status'] == 'planning') ? 'selected' : '' ?>>Planning</option>
                    <option value="<?= base_url('admin/projects?status=completed') ?>" <?= (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="<?= base_url('admin/projects?status=on_hold') ?>" <?= (isset($_GET['status']) && $_GET['status'] == 'on_hold') ? 'selected' : '' ?>>On Hold</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Projects</p>
                    <p class="text-2xl font-bold text-indigo-600"><?= $stats['total'] ?></p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="folder" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Projects</p>
                    <p class="text-2xl font-bold text-green-600"><?= $stats['active'] ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="play-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-blue-600"><?= $stats['completed'] ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">On Hold</p>
                    <p class="text-2xl font-bold text-yellow-600"><?= $stats['on_hold'] ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="pause-circle" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900">All Projects</h2>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <input type="text" id="searchProjects" placeholder="Search projects..." 
                               class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manager</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($projects as $project): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <code class="bg-gray-100 px-2 py-1 rounded text-sm font-mono"><?= esc($project['project_code']) ?></code>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <a href="<?= base_url('admin/projects/view/' . $project['id']) ?>" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    <?= esc($project['name']) ?>
                                </a>
                                <?php if (!empty($project['description'])): ?>
                                <p class="text-sm text-gray-500 mt-1"><?= esc(substr($project['description'], 0, 50)) ?>...</p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= esc($project['client_name'] ?? 'N/A') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= esc($project['project_manager_name'] ?? 'Unassigned') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?= getStatusBadge($project['status'], 'project') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?= getPriorityBadge($project['priority']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: <?= $project['progress_percentage'] ?>%"></div>
                            </div>
                            <span class="text-sm text-gray-600 mt-1"><?= round($project['progress_percentage'], 1) ?>%</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium"><?= formatCurrency($project['estimated_budget'], $project['currency']) ?></div>
                                <?php if ($project['actual_cost'] > 0): ?>
                                <div class="text-gray-500">Spent: <?= formatCurrency($project['actual_cost'], $project['currency']) ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div><?= formatDate($project['planned_end_date']) ?></div>
                            <?php if ($project['planned_end_date'] < date('Y-m-d') && $project['status'] !== 'completed'): ?>
                            <div class="text-red-600 flex items-center mt-1">
                                <i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>
                                Overdue
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="relative inline-block text-left">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="toggleDropdown('actions-<?= $project['id'] ?>')">
                                    Actions
                                    <i data-lucide="chevron-down" class="ml-2 w-4 h-4"></i>
                                </button>
                                <div id="actions-<?= $project['id'] ?>" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1">
                                        <a href="<?= base_url('admin/projects/view/' . $project['id']) ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i data-lucide="eye" class="w-4 h-4 mr-3"></i>
                                            View Details
                                        </a>
                                        <a href="<?= base_url('admin/projects/dashboard/' . $project['id']) ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i data-lucide="bar-chart-3" class="w-4 h-4 mr-3"></i>
                                            Dashboard
                                        </a>
                                        <a href="<?= base_url('admin/projects/gantt/' . $project['id']) ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i data-lucide="gantt-chart" class="w-4 h-4 mr-3"></i>
                                            Gantt Chart
                                        </a>
                                        <a href="<?= base_url('admin/projects/team/' . $project['id']) ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i data-lucide="users" class="w-4 h-4 mr-3"></i>
                                            Team
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        <a href="<?= base_url('admin/projects/edit/' . $project['id']) ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i data-lucide="edit" class="w-4 h-4 mr-3"></i>
                                            Edit
                                        </a>
                                        <a href="#" onclick="archiveProject(<?= $project['id'] ?>)" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i data-lucide="archive" class="w-4 h-4 mr-3"></i>
                                            Archive
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Overdue Projects Alert -->
    <?php if (!empty($overdue_projects)): ?>
    <div class="bg-red-50 border-l-4 border-red-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    Overdue Projects (<?= count($overdue_projects) ?>)
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-3">
                        <?php foreach ($overdue_projects as $project): ?>
                        <div class="bg-white p-4 rounded-lg border border-red-200">
                            <h4 class="font-medium text-gray-900"><?= esc($project['name']) ?></h4>
                            <p class="text-sm text-gray-600 mt-1">Client: <?= esc($project['client_name']) ?></p>
                            <p class="text-sm text-red-600 mt-1">Due: <?= formatDate($project['planned_end_date']) ?></p>
                            <a href="<?= base_url('admin/projects/view/' . $project['id']) ?>" class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                View Project
                                <i data-lucide="arrow-right" class="w-3 h-3 ml-1"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchProjects');
    const table = document.querySelector('table tbody');
    const rows = table.querySelectorAll('tr');

    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Initialize Lucide icons
    lucide.createIcons();
});

// Dropdown functionality
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    const allDropdowns = document.querySelectorAll('[id^="actions-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(dd => {
        if (dd.id !== id) {
            dd.classList.add('hidden');
        }
    });
    
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleDropdown"]')) {
        document.querySelectorAll('[id^="actions-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

function archiveProject(projectId) {
    if (confirm('Are you sure you want to archive this project?')) {
        fetch(`<?= base_url('admin/projects/delete/') ?>${projectId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while archiving the project.');
        });
    }
}
</script>
<?= $this->endSection() ?>
