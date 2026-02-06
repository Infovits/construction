<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Git Commits</h1>
        <p class="text-gray-600 mt-1">View all commits in the repository</p>
    </div>

    <?php if (isset($error) && $error): ?>
        <!-- Error Message -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-3"></i>
                <p class="text-red-800"><?= esc($error) ?></p>
            </div>
        </div>
    <?php else: ?>
        <!-- Stats Card -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Commits</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= count($commits) ?></p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <i data-lucide="git-commit" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
        </div>

        <!-- Commits Table -->
        <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Commit
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Message
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Author
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($commits)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
                                    <p class="text-sm">No commits found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($commits as $commit): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-gray-100 rounded-full">
                                                <i data-lucide="git-branch" class="w-5 h-5 text-gray-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 font-mono">
                                                    <?= esc($commit['short_hash']) ?>
                                                </div>
                                                <div class="text-xs text-gray-500 font-mono">
                                                    <?= esc($commit['hash']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <?= esc($commit['message']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center bg-indigo-100 rounded-full">
                                                <span class="text-sm font-medium text-indigo-800">
                                                    <?= strtoupper(substr(esc($commit['author']), 0, 1)) ?>
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= esc($commit['author']) ?>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    <?= esc($commit['email']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?= isset($commit['timestamp']) ? date('M j, Y', $commit['timestamp']) : esc($commit['date']) ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?= isset($commit['timestamp']) ? date('g:i A', $commit['timestamp']) : '' ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Initialize Lucide icons when page loads
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
<?= $this->endSection() ?>
