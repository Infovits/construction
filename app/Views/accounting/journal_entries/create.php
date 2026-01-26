<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= $title ?></h1>
            <p class="mt-2 text-gray-600">Create a new journal entry with debits and credits</p>
        </div>
        <a href="<?= base_url('admin/accounting/journal-entries') ?>" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Journal Entries
        </a>
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

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-lg border overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Journal Entry Details</h2>
        </div>
        
        <form method="POST" action="<?= base_url('admin/accounting/journal-entries') ?>" id="journalEntryForm">
            <?= csrf_field() ?>
            <div class="p-6 space-y-6">
                <!-- Header Fields -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Entry Number</label>
                        <input type="text" 
                               name="entry_number" 
                               value="<?= old('entry_number', $nextEntryNumber) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               readonly>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Entry Date <span class="text-red-500">*</span></label>
                        <input type="date" 
                               name="entry_date" 
                               value="<?= old('entry_date', date('Y-m-d')) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= isset($validation) && $validation->hasError('entry_date') ? 'border-red-500' : '' ?>"
                               required>
                        <?php if (isset($validation) && $validation->hasError('entry_date')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $validation->getError('entry_date') ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reference</label>
                        <input type="text" 
                               name="reference" 
                               value="<?= old('reference') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter reference number">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="description" 
                               value="<?= old('description') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= isset($validation) && $validation->hasError('description') ? 'border-red-500' : '' ?>"
                               placeholder="Enter description"
                               required>
                        <?php if (isset($validation) && $validation->hasError('description')): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $validation->getError('description') ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Journal Lines -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Journal Lines</h3>
                        <button type="button" id="addLineBtn" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Add Line
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Account</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Description</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Type</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Amount</th>
                                    <th class="px-3 py-2 text-center text-sm font-medium text-gray-700 border-b">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="journalLines">
                                <!-- Lines will be added here by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="mt-4 bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-3 gap-4 text-sm font-medium">
                            <div></div>
                            <div class="text-center">
                                <div class="text-gray-700">Total Debits:</div>
                                <div id="totalDebits" class="text-lg font-bold text-green-600">MWK 0.00</div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-700">Total Credits:</div>
                                <div id="totalCredits" class="text-lg font-bold text-red-600">MWK 0.00</div>
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <div class="text-gray-700">Difference:</div>
                            <div id="difference" class="text-lg font-bold">MWK 0.00</div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Enter additional notes (optional)"><?= old('notes') ?></textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <button type="button" 
                            name="save_as_draft" 
                            value="1"
                            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                            onclick="submitForm(true)">
                        <i data-lucide="save" class="w-4 h-4 mr-2 inline"></i>
                        Save as Draft
                    </button>
                    <button type="button" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            onclick="submitForm(false)">
                        <i data-lucide="check" class="w-4 h-4 mr-2 inline"></i>
                        Post Entry
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let lineCounter = 0;
const accounts = <?= json_encode($accounts) ?>;

document.addEventListener('DOMContentLoaded', function() {
    // Add initial lines
    addLine();
    addLine();
    
    document.getElementById('addLineBtn').addEventListener('click', addLine);
});

function addLine() {
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
    const row = button.closest('tr');
    if (document.querySelectorAll('.journal-line').length > 2) {
        row.remove();
        updateTotals();
    } else {
        alert('You must have at least 2 journal lines');
    }
}

function updateTotals() {
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

function submitForm(saveAsDraft) {
    const form = document.getElementById('journalEntryForm');
    
    // Convert new format to backend expected format
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
    
    // Add hidden field for save_as_draft if needed
    let draftInput = document.querySelector('input[name="save_as_draft"]');
    if (draftInput) {
        draftInput.remove();
    }
    
    if (saveAsDraft) {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'save_as_draft';
        hiddenInput.value = '1';
        form.appendChild(hiddenInput);
    }
    
    form.submit();
}

function updateAccountInfo(select) {
    // Optional: Add logic to auto-populate description based on account
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