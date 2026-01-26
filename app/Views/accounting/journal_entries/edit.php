<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= $title ?></h1>
            <p class="mt-2 text-gray-600">Edit journal entry (only draft entries can be modified)</p>
        </div>
        <div class="flex space-x-3">
            <a href="<?= base_url('admin/accounting/journal-entries/' . $entry['id']) ?>" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                View Entry
            </a>
            <a href="<?= base_url('admin/accounting/journal-entries') ?>" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Messages -->
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

    <!-- Entry Status Warning -->
    <?php if ($entry['status'] !== 'draft'): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-yellow-800">This journal entry is <?= ucfirst($entry['status']) ?> and cannot be edited.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-lg border overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Edit Journal Entry</h2>
        </div>
        
        <form method="POST" action="<?= base_url('admin/accounting/journal-entries/' . $entry['id']) ?>" id="journalEntryForm">
            <?= csrf_field() ?>
            <div class="p-6 space-y-6">
                <!-- Header Fields -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Journal Number</label>
                        <input type="text" 
                               name="journal_number" 
                               value="<?= $entry['journal_number'] ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50"
                               readonly>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Entry Date <span class="text-red-500">*</span></label>
                        <input type="date" 
                               name="entry_date" 
                               value="<?= old('entry_date', $entry['entry_date']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= isset($validation) && $validation->hasError('entry_date') ? 'border-red-500' : '' ?>"
                               <?= $entry['status'] !== 'draft' ? 'readonly' : 'required' ?>>
                        <?php if (isset($validation) && $validation->hasError('entry_date')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $validation->getError('entry_date') ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reference</label>
                        <input type="text" 
                               name="reference" 
                               value="<?= old('reference', $entry['reference']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter reference number"
                               <?= $entry['status'] !== 'draft' ? 'readonly' : '' ?>>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="description" 
                               value="<?= old('description', $entry['description']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= isset($validation) && $validation->hasError('description') ? 'border-red-500' : '' ?>"
                               placeholder="Enter description"
                               <?= $entry['status'] !== 'draft' ? 'readonly' : 'required' ?>>
                        <?php if (isset($validation) && $validation->hasError('description')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $validation->getError('description') ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Entry Status and Info -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Status:</span>
                            <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold
                                <?php if ($entry['status'] === 'draft'): ?>bg-gray-100 text-gray-800<?php endif; ?>
                                <?php if ($entry['status'] === 'posted'): ?>bg-green-100 text-green-800<?php endif; ?>
                                <?php if ($entry['status'] === 'reversed'): ?>bg-red-100 text-red-800<?php endif; ?>">
                                <?= ucfirst($entry['status']) ?>
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600">Created:</span>
                            <span class="ml-2"><?= date('M d, Y', strtotime($entry['created_at'])) ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Total Amount:</span>
                            <span class="ml-2 font-semibold">MWK <?= number_format($entry['total_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Journal Lines -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Journal Lines</h3>
                        <?php if ($entry['status'] === 'draft'): ?>
                            <button type="button" id="addLineBtn" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                Add Line
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Account</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Description</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Type</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Amount</th>
                                    <?php if ($entry['status'] === 'draft'): ?>
                                        <th class="px-3 py-2 text-center text-sm font-medium text-gray-700 border-b">Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody id="journalLines">
                                <?php if (isset($entry['lines'])): ?>
                                    <?php foreach ($entry['lines'] as $index => $line): ?>
                                        <tr class="border-b journal-line">
                                            <td class="px-3 py-2">
                                                <?php if ($entry['status'] === 'draft'): ?>
                                                    <select name="lines[<?= $index ?>][account_id]" 
                                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm account-select" 
                                                            onchange="updateAccountInfo(this)" required>
                                                        <option value="">Select Account</option>
                                                        <?php foreach ($accounts as $account): ?>
                                                            <option value="<?= $account['id'] ?>" <?= $account['id'] == $line['account_id'] ? 'selected' : '' ?>>
                                                                <?= $account['account_code'] ?> - <?= $account['account_name'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <div class="text-sm font-medium"><?= $line['account_code'] ?> - <?= $line['account_name'] ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-3 py-2">
                                                <?php if ($entry['status'] === 'draft'): ?>
                                                    <input type="text" 
                                                           name="lines[<?= $index ?>][description]" 
                                                           value="<?= $line['description'] ?>"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                           placeholder="Line description">
                                                <?php else: ?>
                                                    <div class="text-sm"><?= $line['description'] ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-3 py-2">
                                                <?php if ($entry['status'] === 'draft'): ?>
                                                    <div class="flex items-center space-x-2">
                                                        <label class="flex items-center">
                                                            <input type="radio" 
                                                                   name="lines[<?= $index ?>][type]" 
                                                                   value="debit" 
                                                                   class="mr-1 type-radio" 
                                                                   onchange="updateTotals()" 
                                                                   <?= $line['debit_amount'] > 0 ? 'checked' : '' ?> required>
                                                            <span class="text-sm text-green-600 font-medium">Debit</span>
                                                        </label>
                                                        <label class="flex items-center">
                                                            <input type="radio" 
                                                                   name="lines[<?= $index ?>][type]" 
                                                                   value="credit" 
                                                                   class="mr-1 type-radio" 
                                                                   onchange="updateTotals()" 
                                                                   <?= $line['credit_amount'] > 0 ? 'checked' : '' ?> required>
                                                            <span class="text-sm text-red-600 font-medium">Credit</span>
                                                        </label>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-sm font-medium">
                                                        <?php if ($line['debit_amount'] > 0): ?>
                                                            <span class="text-green-600">Debit</span>
                                                        <?php else: ?>
                                                            <span class="text-red-600">Credit</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-3 py-2">
                                                <?php if ($entry['status'] === 'draft'): ?>
                                                    <input type="number" 
                                                           name="lines[<?= $index ?>][amount]" 
                                                           value="<?= $line['debit_amount'] > 0 ? $line['debit_amount'] : $line['credit_amount'] ?>"
                                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm amount-input"
                                                           step="0.01" 
                                                           min="0"
                                                           placeholder="0.00"
                                                           onchange="updateTotals()" required>
                                                <?php else: ?>
                                                    <div class="text-sm font-medium text-right">
                                                        MWK <?= number_format($line['debit_amount'] > 0 ? $line['debit_amount'] : $line['credit_amount'], 2) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <?php if ($entry['status'] === 'draft'): ?>
                                                <td class="px-3 py-2 text-center">
                                                    <button type="button" 
                                                            onclick="removeLine(this)"
                                                            class="text-red-600 hover:text-red-800">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="mt-4 bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-3 gap-4 text-sm font-medium">
                            <div></div>
                            <div class="text-center">
                                <div class="text-gray-700">Total Debits:</div>
                                <div id="totalDebits" class="text-lg font-bold text-green-600">
                                    MWK <?= number_format(array_sum(array_column($entry['lines'] ?? [], 'debit_amount')), 2) ?>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-700">Total Credits:</div>
                                <div id="totalCredits" class="text-lg font-bold text-red-600">
                                    MWK <?= number_format(array_sum(array_column($entry['lines'] ?? [], 'credit_amount')), 2) ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <div class="text-gray-700">Difference:</div>
                            <div id="difference" class="text-lg font-bold text-green-600">MWK 0.00</div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Enter additional notes (optional)"
                              <?= $entry['status'] !== 'draft' ? 'readonly' : '' ?>><?= old('notes', $entry['notes']) ?></textarea>
                </div>

                <!-- Submit Buttons -->
                <?php if ($entry['status'] === 'draft'): ?>
                    <div class="flex justify-end space-x-3 pt-6 border-t">
                        <button type="button" 
                                onclick="submitForm()"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="save" class="w-4 h-4 mr-2 inline"></i>
                            Update Entry
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
let lineCounter = <?= count($entry['lines'] ?? []) ?>;
const accounts = <?= json_encode($accounts) ?>;
const isDraft = <?= $entry['status'] === 'draft' ? 'true' : 'false' ?>;

document.addEventListener('DOMContentLoaded', function() {
    if (isDraft) {
        document.getElementById('addLineBtn')?.addEventListener('click', addLine);
        updateTotals();
    }
});

function addLine() {
    if (!isDraft) return;
    
    const tbody = document.getElementById('journalLines');
    const row = document.createElement('tr');
    row.className = 'border-b journal-line';
    row.innerHTML = `
        <td class="px-3 py-2">
            <select name="lines[${lineCounter}][account_id]" 
                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm account-select" 
                    onchange="updateAccountInfo(this)" required>
                <option value="">Select Account</option>
                ${accounts.map(account => 
                    `<option value="${account.id}">${account.account_code} - ${account.account_name}</option>`
                ).join('')}
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="text" 
                   name="lines[${lineCounter}][description]" 
                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                   placeholder="Line description">
        </td>
        <td class="px-3 py-2">
            <div class="flex items-center space-x-2">
                <label class="flex items-center">
                    <input type="radio" 
                           name="lines[${lineCounter}][type]" 
                           value="debit" 
                           class="mr-1 type-radio" 
                           onchange="updateTotals()" required>
                    <span class="text-sm text-green-600 font-medium">Debit</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" 
                           name="lines[${lineCounter}][type]" 
                           value="credit" 
                           class="mr-1 type-radio" 
                           onchange="updateTotals()" required>
                    <span class="text-sm text-red-600 font-medium">Credit</span>
                </label>
            </div>
        </td>
        <td class="px-3 py-2">
            <input type="number" 
                   name="lines[${lineCounter}][amount]" 
                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm amount-input"
                   step="0.01" 
                   min="0"
                   placeholder="0.00"
                   onchange="updateTotals()" required>
        </td>
        <td class="px-3 py-2 text-center">
            <button type="button" 
                    onclick="removeLine(this)"
                    class="text-red-600 hover:text-red-800">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    lineCounter++;
    
    // Initialize lucide icons for the new row
    lucide.createIcons();
}

function removeLine(button) {
    if (!isDraft) return;
    
    const row = button.closest('tr');
    if (document.querySelectorAll('.journal-line').length > 2) {
        row.remove();
        updateTotals();
    } else {
        alert('You must have at least 2 journal lines');
    }
}

function updateTotals() {
    if (!isDraft) return;
    
    let totalDebits = 0;
    let totalCredits = 0;
    
    document.querySelectorAll('.journal-line').forEach(row => {
        const amountInput = row.querySelector('.amount-input');
        const typeRadio = row.querySelector('input[name*="[type]"]:checked');
        
        if (amountInput && typeRadio && amountInput.value) {
            const amount = parseFloat(amountInput.value) || 0;
            if (typeRadio.value === 'debit') {
                totalDebits += amount;
            } else if (typeRadio.value === 'credit') {
                totalCredits += amount;
            }
        }
    });
    
    document.getElementById('totalDebits').textContent = `MWK ${totalDebits.toFixed(2)}`;
    document.getElementById('totalCredits').textContent = `MWK ${totalCredits.toFixed(2)}`;
    
    const difference = totalDebits - totalCredits;
    const diffElement = document.getElementById('difference');
    diffElement.textContent = `MWK ${Math.abs(difference).toFixed(2)}`;
    
    if (difference === 0) {
        diffElement.className = 'text-lg font-bold text-green-600';
    } else {
        diffElement.className = 'text-lg font-bold text-red-600';
    }
}

function submitForm() {
    if (!isDraft) return;
    
    const form = document.getElementById('journalEntryForm');
    
    // Convert new format to backend expected format for new lines
    document.querySelectorAll('.journal-line').forEach((row, index) => {
        const amountInput = row.querySelector('.amount-input');
        const typeRadio = row.querySelector('input[name*="[type]"]:checked');
        
        if (amountInput && typeRadio && amountInput.value) {
            const amount = parseFloat(amountInput.value) || 0;
            
            // Create hidden fields for backend
            const debitInput = document.createElement('input');
            debitInput.type = 'hidden';
            debitInput.name = `lines[${index}][debit_amount]`;
            debitInput.value = typeRadio.value === 'debit' ? amount : 0;
            
            const creditInput = document.createElement('input');
            creditInput.type = 'hidden';
            creditInput.name = `lines[${index}][credit_amount]`;
            creditInput.value = typeRadio.value === 'credit' ? amount : 0;
            
            form.appendChild(debitInput);
            form.appendChild(creditInput);
        }
    });
    
    form.submit();
}

function updateAccountInfo(select) {
    if (!isDraft) return;
    
    const accountId = select.value;
    if (accountId) {
        const account = accounts.find(acc => acc.id == accountId);
        if (account) {
            const row = select.closest('tr');
            const descInput = row.querySelector('input[name*="[description]"]');
            if (!descInput.value) {
                descInput.value = account.account_name;
            }
        }
    }
}
</script>

<?= $this->endSection() ?>