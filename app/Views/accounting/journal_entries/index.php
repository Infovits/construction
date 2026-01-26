<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.entry-draft { background: linear-gradient(135deg, #fef3c7, #ffffff); }
.entry-posted { background: linear-gradient(135deg, #dcfce7, #ffffff); }
.entry-reversed { background: linear-gradient(135deg, #fee2e2, #ffffff); }

.status-draft { @apply bg-yellow-100 text-yellow-800; }
.status-posted { @apply bg-green-100 text-green-800; }
.status-reversed { @apply bg-red-100 text-red-800; }

.amount-debit { @apply text-blue-600 font-semibold; }
.amount-credit { @apply text-green-600 font-semibold; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Journal Entries</h1>
            <p class="text-gray-600">Manage double-entry bookkeeping transactions</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/accounting/journal-entries/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                New Journal Entry
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <?php if (!empty($stats)): ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($stats as $stat): ?>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 
                <?= $stat['status'] === 'draft' ? 'border-l-yellow-500' : 
                   ($stat['status'] === 'posted' ? 'border-l-green-500' : 'border-l-red-500') ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide
                            <?= $stat['status'] === 'draft' ? 'text-yellow-600' : 
                               ($stat['status'] === 'posted' ? 'text-green-600' : 'text-red-600') ?>">
                            <?= ucfirst($stat['status']) ?> Entries
                        </p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stat['entry_count'] ?></p>
                        <p class="text-sm text-gray-500">MWK <?= number_format($stat['total_amount'], 2) ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center
                        <?= $stat['status'] === 'draft' ? 'bg-yellow-100' : 
                           ($stat['status'] === 'posted' ? 'bg-green-100' : 'bg-red-100') ?>">
                        <i data-lucide="<?= $stat['status'] === 'draft' ? 'edit-3' : 
                                          ($stat['status'] === 'posted' ? 'check-circle' : 'x-circle') ?>" 
                           class="w-6 h-6 <?= $stat['status'] === 'draft' ? 'text-yellow-600' : 
                                            ($stat['status'] === 'posted' ? 'text-green-600' : 'text-red-600') ?>"></i>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search entries..." 
                       value="<?= esc($filters['search']) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="draft" <?= $filters['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="posted" <?= $filters['status'] === 'posted' ? 'selected' : '' ?>>Posted</option>
                    <option value="reversed" <?= $filters['status'] === 'reversed' ? 'selected' : '' ?>>Reversed</option>
                </select>
            </div>
            <div>
                <input type="date" name="date_from" placeholder="From Date"
                       value="<?= esc($filters['date_from']) ?>"
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <input type="date" name="date_to" placeholder="To Date"
                       value="<?= esc($filters['date_to']) ?>"
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="search" class="w-4 h-4"></i>
            </button>
        </form>
    </div>

    <!-- Journal Entries Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entry Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lines</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($entries)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="book-open" class="w-12 h-12 mx-auto mb-4 text-gray-400"></i>
                                <p class="text-lg font-medium">No journal entries found</p>
                                <p class="text-sm">Get started by creating your first journal entry.</p>
                                <a href="<?= base_url('admin/accounting/journal-entries/create') ?>" class="inline-flex items-center mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                    Create Journal Entry
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($entries as $entry): ?>
                            <tr class="hover:bg-gray-50 entry-<?= $entry['status'] ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="flex items-center">
                                            <i data-lucide="file-text" class="w-4 h-4 mr-2 text-gray-400"></i>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= esc($entry['entry_number']) ?>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            <?= esc(substr($entry['description'], 0, 50)) ?><?= strlen($entry['description']) > 50 ? '...' : '' ?>
                                        </div>
                                        <?php if (!empty($entry['reference_type'])): ?>
                                            <div class="text-xs text-blue-600 mt-1">
                                                Ref: <?= esc($entry['reference_type']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('M j, Y', strtotime($entry['entry_date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    MWK <?= number_format($entry['total_debit'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <?= $entry['line_count'] ?? 0 ?> lines
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium status-<?= $entry['status'] ?>">
                                        <i data-lucide="<?= $entry['status'] === 'draft' ? 'edit-3' : 
                                                          ($entry['status'] === 'posted' ? 'check-circle' : 'x-circle') ?>" 
                                           class="w-3 h-3 mr-1"></i>
                                        <?= ucfirst($entry['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="<?= base_url('admin/accounting/journal-entries/' . $entry['id']) ?>" 
                                           class="text-indigo-600 hover:text-indigo-900" title="View">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <?php if ($entry['status'] === 'draft'): ?>
                                            <a href="<?= base_url('admin/accounting/journal-entries/' . $entry['id'] . '/edit') ?>" 
                                               class="text-blue-600 hover:text-blue-900" title="Edit">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                            <button onclick="postEntry(<?= $entry['id'] ?>)" 
                                                    class="text-green-600 hover:text-green-900" title="Post">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                            </button>
                                            <button onclick="deleteEntry(<?= $entry['id'] ?>, '<?= esc($entry['journal_number']) ?>')" 
                                                    class="text-red-600 hover:text-red-900" title="Delete">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        <?php elseif ($entry['status'] === 'posted'): ?>
                                            <button onclick="reverseEntry(<?= $entry['id'] ?>)" 
                                                    class="text-orange-600 hover:text-orange-900" title="Reverse">
                                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Delete</h3>
        <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete journal entry "<span id="entryNumber"></span>"? This action cannot be undone.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                Cancel
            </button>
            <form id="deleteForm" method="POST" class="inline">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function postEntry(id) {
    if (confirm('Are you sure you want to post this journal entry? Posted entries cannot be edited.')) {
        fetch(`<?= base_url('admin/accounting/journal-entries') ?>/${id}/post`, {
            method: 'POST',
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
            alert('An error occurred while posting the entry.');
        });
    }
}

function reverseEntry(id) {
    if (confirm('Are you sure you want to reverse this journal entry? This will create a reversal entry.')) {
        fetch(`<?= base_url('admin/accounting/journal-entries') ?>/${id}/reverse`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Journal entry reversed successfully. Reversal entry ID: ' + data.reversal_id);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while reversing the entry.');
        });
    }
}

function deleteEntry(id, entryNumber) {
    document.getElementById('entryNumber').textContent = entryNumber;
    document.getElementById('deleteForm').action = `<?= base_url('admin/accounting/journal-entries') ?>/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}
</script>

<?= $this->endSection() ?>