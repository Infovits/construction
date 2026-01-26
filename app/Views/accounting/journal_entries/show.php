<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= $title ?></h1>
            <p class="mt-2 text-gray-600">View journal entry details and perform actions</p>
        </div>
        <div class="flex space-x-3">
            <?php if ($entry['status'] === 'draft'): ?>
                <a href="<?= base_url('admin/accounting/journal-entries/' . $entry['id'] . '/edit') ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                    Edit Entry
                </a>
                <button onclick="postEntry(<?= $entry['id'] ?>)"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                    Post Entry
                </button>
                <button onclick="deleteEntry(<?= $entry['id'] ?>)"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                    Delete Entry
                </button>
            <?php elseif ($entry['status'] === 'posted'): ?>
                <button onclick="reverseEntry(<?= $entry['id'] ?>)"
                        class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i>
                    Reverse Entry
                </button>
            <?php endif; ?>
            <a href="<?= base_url('admin/accounting/journal-entries') ?>" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Messages -->
    <?php if (session('success')): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-green-800"><?= session('success') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-red-800"><?= session('error') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Entry Details -->
    <div class="bg-white rounded-xl shadow-lg border overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">Journal Entry Details</h2>
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    <?php if ($entry['status'] === 'draft'): ?>bg-white bg-opacity-20 text-white<?php endif; ?>
                    <?php if ($entry['status'] === 'posted'): ?>bg-green-100 text-green-800<?php endif; ?>
                    <?php if ($entry['status'] === 'reversed'): ?>bg-red-100 text-red-800<?php endif; ?>">
                    <?= ucfirst($entry['status']) ?>
                </span>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Header Information -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Entry Number</label>
                    <div class="text-lg font-semibold text-gray-900"><?= $entry['entry_number'] ?></div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Entry Date</label>
                    <div class="text-lg font-semibold text-gray-900"><?= date('M d, Y', strtotime($entry['entry_date'])) ?></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Reference</label>
                    <div class="text-lg font-semibold text-gray-900"><?= $entry['reference_type'] ?: 'N/A' ?></div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Description</label>
                    <div class="text-lg font-semibold text-gray-900"><?= $entry['description'] ?></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Total Amount</label>
                    <div class="text-xl font-bold text-green-600">MWK <?= number_format($entry['total_debit'], 2) ?></div>
                </div>
            </div>

            <!-- Status Information -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Status Information</h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Created At:</span>
                        <div class="font-semibold"><?= date('M d, Y H:i', strtotime($entry['created_at'])) ?></div>
                    </div>
                    
                    <?php if ($entry['posted_at']): ?>
                        <div>
                            <span class="text-gray-600">Posted At:</span>
                            <div class="font-semibold"><?= date('M d, Y H:i', strtotime($entry['posted_at'])) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($entry['reversed_at']): ?>
                        <div>
                            <span class="text-gray-600">Reversed At:</span>
                            <div class="font-semibold text-red-600"><?= date('M d, Y H:i', strtotime($entry['reversed_at'])) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($entry['reversal_reason']): ?>
                        <div>
                            <span class="text-gray-600">Reversal Reason:</span>
                            <div class="font-semibold"><?= esc($entry['reversal_reason']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Journal Lines -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Journal Lines</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Line #</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Account</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Description</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700 border-b">Debit</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700 border-b">Credit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php $totalDebits = 0; $totalCredits = 0; ?>
                            <?php if (isset($entry['lines'])): ?>
                                <?php foreach ($entry['lines'] as $line): ?>
                                    <?php 
                                    $totalDebits += $line['debit_amount'];
                                    $totalCredits += $line['credit_amount'];
                                    ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 border-b">
                                            <?= $line['line_order'] ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm border-b">
                                            <div class="font-medium text-gray-900"><?= $line['account_code'] ?></div>
                                            <div class="text-gray-600"><?= $line['account_name'] ?></div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 border-b">
                                            <?= $line['description'] ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-medium border-b">
                                            <?php if ($line['debit_amount'] > 0): ?>
                                                <span class="text-green-600">MWK <?= number_format($line['debit_amount'], 2) ?></span>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-medium border-b">
                                            <?php if ($line['credit_amount'] > 0): ?>
                                                <span class="text-red-600">MWK <?= number_format($line['credit_amount'], 2) ?></span>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700 border-t" colspan="3">
                                    Totals:
                                </th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-green-600 border-t">
                                    MWK <?= number_format($totalDebits, 2) ?>
                                </th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-red-600 border-t">
                                    MWK <?= number_format($totalCredits, 2) ?>
                                </th>
                            </tr>
                            <tr>
                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700" colspan="3">
                                    Balance Check:
                                </th>
                                <th class="px-4 py-2 text-right text-sm font-bold <?= abs($totalDebits - $totalCredits) < 0.01 ? 'text-green-600' : 'text-red-600' ?>" colspan="2">
                                    <?= abs($totalDebits - $totalCredits) < 0.01 ? 'Balanced ✓' : 'Out of Balance (' . number_format(abs($totalDebits - $totalCredits), 2) . ')' ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Action Modals -->
<div id="confirmModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600"></i>
            </div>
            <div class="mt-4 text-center">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Confirm Action</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500" id="modalMessage">Are you sure you want to perform this action?</p>
                </div>
                <div class="mt-4 flex justify-center space-x-3">
                    <button id="confirmBtn" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Confirm
                    </button>
                    <button onclick="closeModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function postEntry(id) {
    showConfirmModal(
        'Post Journal Entry',
        'Are you sure you want to post this journal entry? Once posted, it cannot be edited.',
        function() {
            fetch(`/construction/public/admin/accounting/journal-entries/${id}/post`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                closeModal();
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                closeModal();
                alert('Error: ' + error.message);
            });
        }
    );
}

function reverseEntry(id) {
    showConfirmModal(
        'Reverse Journal Entry',
        'Are you sure you want to reverse this journal entry? This will create a new reversal entry.',
        function() {
            fetch(`/construction/public/admin/accounting/journal-entries/${id}/reverse`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                closeModal();
                if (data.success) {
                    alert('Journal entry reversed successfully. Reversal entry ID: ' + data.reversal_id);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                closeModal();
                alert('Error: ' + error.message);
            });
        }
    );
}

function deleteEntry(id) {
    showConfirmModal(
        'Delete Journal Entry',
        'Are you sure you want to delete this journal entry? This action cannot be undone.',
        function() {
            fetch(`/construction/public/admin/accounting/journal-entries/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                closeModal();
                if (response.ok) {
                    window.location.href = '/construction/public/admin/accounting/journal-entries';
                } else {
                    alert('Error deleting journal entry');
                }
            })
            .catch(error => {
                closeModal();
                alert('Error: ' + error.message);
            });
        }
    );
}

function showConfirmModal(title, message, confirmCallback) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('confirmBtn').onclick = confirmCallback;
    document.getElementById('confirmModal').classList.remove('hidden');
    
    // Initialize lucide icons for modal
    lucide.createIcons();
}

function closeModal() {
    document.getElementById('confirmModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?= $this->endSection() ?>