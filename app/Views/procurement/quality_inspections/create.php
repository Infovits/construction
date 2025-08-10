<?php helper('currency'); ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Quality Inspection</h1>
            <p class="text-gray-600">Inspect received materials for quality compliance</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/quality-inspections') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Inspection Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form id="inspectionForm" action="<?= base_url('admin/quality-inspections/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <!-- Header Information -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Inspection Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Inspection Number -->
                    <div>
                        <label for="inspection_number" class="block text-sm font-medium text-gray-700 mb-2">Inspection Number</label>
                        <input type="text" id="inspection_number" name="inspection_number" 
                               value="<?= old('inspection_number', $inspection_number ?? '') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required readonly>
                    </div>

                    <!-- GRN Item Selection -->
                    <div>
                        <label for="grn_item_id" class="block text-sm font-medium text-gray-700 mb-2">GRN Item <span class="text-red-500">*</span></label>
                        <select id="grn_item_id" name="grn_item_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                required onchange="loadGrnItemDetails(this.value)">
                            <option value="">Select GRN Item</option>
                            <?php if (isset($grnItems)): ?>
                                <?php foreach ($grnItems as $item): ?>
                                    <option value="<?= $item['id'] ?>" 
                                            data-material-id="<?= $item['material_id'] ?>"
                                            data-material-name="<?= esc($item['material_name']) ?>"
                                            data-grn-number="<?= esc($item['grn_number']) ?>"
                                            data-supplier-name="<?= esc($item['supplier_name']) ?>"
                                            data-quantity-delivered="<?= $item['quantity_delivered'] ?>"
                                            <?= old('grn_item_id') == $item['id'] ? 'selected' : '' ?>>
                                        GRN #<?= esc($item['grn_number']) ?> - <?= esc($item['material_name']) ?> (<?= number_format($item['quantity_delivered'], 3) ?> <?= esc($item['unit']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Inspector -->
                    <div>
                        <label for="inspector_id" class="block text-sm font-medium text-gray-700 mb-2">Inspector <span class="text-red-500">*</span></label>
                        <select id="inspector_id" name="inspector_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                required>
                            <option value="">Select Inspector</option>
                            <?php if (isset($inspectors)): ?>
                                <?php foreach ($inspectors as $inspector): ?>
                                    <option value="<?= $inspector['id'] ?>" 
                                            <?= old('inspector_id') == $inspector['id'] ? 'selected' : '' ?>>
                                        <?= esc($inspector['first_name'] . ' ' . $inspector['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Inspection Date -->
                    <div>
                        <label for="inspection_date" class="block text-sm font-medium text-gray-700 mb-2">Inspection Date <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="inspection_date" name="inspection_date" 
                               value="<?= old('inspection_date', date('Y-m-d\TH:i')) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                    </div>

                    <!-- Inspection Type -->
                    <div>
                        <label for="inspection_type" class="block text-sm font-medium text-gray-700 mb-2">Inspection Type</label>
                        <select id="inspection_type" name="inspection_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="incoming" <?= old('inspection_type', 'incoming') === 'incoming' ? 'selected' : '' ?>>Incoming</option>
                            <option value="random" <?= old('inspection_type') === 'random' ? 'selected' : '' ?>>Random</option>
                            <option value="complaint" <?= old('inspection_type') === 'complaint' ? 'selected' : '' ?>>Complaint</option>
                            <option value="audit" <?= old('inspection_type') === 'audit' ? 'selected' : '' ?>>Audit</option>
                        </select>
                    </div>

                    <!-- Material Info (Auto-populated) -->
                    <div>
                        <label for="material_info" class="block text-sm font-medium text-gray-700 mb-2">Material</label>
                        <input type="text" id="material_info" 
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg" 
                               readonly placeholder="Select GRN Item first">
                        <input type="hidden" id="material_id" name="material_id">
                    </div>
                </div>
            </div>

            <!-- Item Details -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Item Information</h2>
                <div id="itemDetails" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">GRN Number</label>
                        <div id="grnNumber" class="text-sm text-gray-900">-</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Supplier</label>
                        <div id="supplierName" class="text-sm text-gray-900">-</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Quantity Delivered</label>
                        <div id="quantityDelivered" class="text-sm text-gray-900">-</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Material Name</label>
                        <div id="materialName" class="text-sm text-gray-900">-</div>
                    </div>
                </div>
            </div>

            <!-- Inspection Details -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Inspection Results</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Quantities -->
                    <div>
                        <label for="quantity_inspected" class="block text-sm font-medium text-gray-700 mb-2">Quantity Inspected <span class="text-red-500">*</span></label>
                        <input type="number" id="quantity_inspected" name="quantity_inspected" 
                               step="0.001" min="0" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required onchange="validateQuantities()">
                    </div>

                    <div>
                        <label for="quantity_passed" class="block text-sm font-medium text-gray-700 mb-2">Quantity Passed</label>
                        <input type="number" id="quantity_passed" name="quantity_passed" 
                               step="0.001" min="0" value="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               onchange="validateQuantities()">
                    </div>

                    <div>
                        <label for="quantity_failed" class="block text-sm font-medium text-gray-700 mb-2">Quantity Failed</label>
                        <input type="number" id="quantity_failed" name="quantity_failed" 
                               step="0.001" min="0" value="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               onchange="validateQuantities()">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Overall Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Overall Status <span class="text-red-500">*</span></label>
                        <select id="status" name="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                required onchange="toggleDefectFields(this.value)">
                            <option value="">Select Status</option>
                            <option value="pending" <?= old('status') === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="in_progress" <?= old('status') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="passed" <?= old('status') === 'passed' ? 'selected' : '' ?>>Passed</option>
                            <option value="failed" <?= old('status') === 'failed' ? 'selected' : '' ?>>Failed</option>
                            <option value="conditional" <?= old('status') === 'conditional' ? 'selected' : '' ?>>Conditional</option>
                        </select>
                    </div>

                    <!-- Overall Grade -->
                    <div>
                        <label for="overall_grade" class="block text-sm font-medium text-gray-700 mb-2">Overall Grade</label>
                        <select id="overall_grade" name="overall_grade" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Grade</option>
                            <option value="A" <?= old('overall_grade') === 'A' ? 'selected' : '' ?>>A - Excellent</option>
                            <option value="B" <?= old('overall_grade') === 'B' ? 'selected' : '' ?>>B - Good</option>
                            <option value="C" <?= old('overall_grade') === 'C' ? 'selected' : '' ?>>C - Satisfactory</option>
                            <option value="D" <?= old('overall_grade') === 'D' ? 'selected' : '' ?>>D - Below Standard</option>
                            <option value="F" <?= old('overall_grade') === 'F' ? 'selected' : '' ?>>F - Failed</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Defects & Actions -->
            <div id="defectSection" class="p-6 border-b border-gray-200 hidden">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Defects & Corrective Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="defect_description" class="block text-sm font-medium text-gray-700 mb-2">Defect Description</label>
                        <textarea id="defect_description" name="defect_description" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Describe defects found..."><?= old('defect_description') ?></textarea>
                    </div>

                    <div>
                        <label for="corrective_action" class="block text-sm font-medium text-gray-700 mb-2">Corrective Action Required</label>
                        <textarea id="corrective_action" name="corrective_action" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Required actions to address defects..."><?= old('corrective_action') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Inspector Notes -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Inspector Notes & Attachments</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="inspector_notes" class="block text-sm font-medium text-gray-700 mb-2">Inspector Notes</label>
                        <textarea id="inspector_notes" name="inspector_notes" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Additional observations and notes..."><?= old('inspector_notes') ?></textarea>
                    </div>

                    <div>
                        <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                        <input type="file" id="attachments" name="attachments[]" multiple 
                               accept="image/*,.pdf,.doc,.docx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Upload photos, documents, or certificates. Max 10MB each.</p>
                    </div>
                </div>
            </div>

            <!-- Inspection Criteria -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Inspection Criteria</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Visual Inspection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Visual Inspection</label>
                            <select name="criteria[visual]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">N/A</option>
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                            </select>
                        </div>

                        <!-- Dimensional Check -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dimensional Check</label>
                            <select name="criteria[dimensional]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">N/A</option>
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                            </select>
                        </div>

                        <!-- Material Compliance -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Material Compliance</label>
                            <select name="criteria[material_compliance]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">N/A</option>
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                            </select>
                        </div>

                        <!-- Documentation -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Documentation</label>
                            <select name="criteria[documentation]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">N/A</option>
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Packaging -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Packaging</label>
                            <select name="criteria[packaging]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">N/A</option>
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                            </select>
                        </div>

                        <!-- Labeling -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Labeling</label>
                            <select name="criteria[labeling]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">N/A</option>
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                            </select>
                        </div>

                        <!-- Quantity Check -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Check</label>
                            <select name="criteria[quantity]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">N/A</option>
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                            </select>
                        </div>

                        <!-- Sample Testing -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sample Testing</label>
                            <select name="criteria[sample_testing]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">N/A</option>
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="p-6 bg-gray-50 rounded-b-lg">
                <div class="flex flex-col sm:flex-row gap-2 sm:justify-end">
                    <a href="<?= base_url('admin/quality-inspections') ?>" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" name="action" value="save_draft" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Save Draft
                    </button>
                    <button type="submit" name="action" value="complete" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="clipboard-check" class="w-4 h-4 mr-2"></i>
                        Complete Inspection
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load GRN item details if already selected
    const grnItemSelect = document.getElementById('grn_item_id');
    if (grnItemSelect.value) {
        loadGrnItemDetails(grnItemSelect.value);
    }

    // Initialize status change handling
    const statusSelect = document.getElementById('status');
    if (statusSelect.value) {
        toggleDefectFields(statusSelect.value);
    }
});

function loadGrnItemDetails(grnItemId) {
    if (!grnItemId) {
        document.getElementById('itemDetails').classList.add('hidden');
        document.getElementById('material_id').value = '';
        document.getElementById('material_info').value = '';
        document.getElementById('quantity_inspected').value = '';
        return;
    }

    const selectElement = document.getElementById('grn_item_id');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    // Populate hidden fields
    document.getElementById('material_id').value = selectedOption.getAttribute('data-material-id') || '';
    
    // Show item details
    document.getElementById('grnNumber').textContent = selectedOption.getAttribute('data-grn-number') || '-';
    document.getElementById('supplierName').textContent = selectedOption.getAttribute('data-supplier-name') || '-';
    document.getElementById('quantityDelivered').textContent = selectedOption.getAttribute('data-quantity-delivered') || '-';
    document.getElementById('materialName').textContent = selectedOption.getAttribute('data-material-name') || '-';
    document.getElementById('material_info').value = selectedOption.getAttribute('data-material-name') || '';
    
    // Set default quantity to inspect (full delivery quantity)
    const quantityDelivered = parseFloat(selectedOption.getAttribute('data-quantity-delivered')) || 0;
    document.getElementById('quantity_inspected').value = quantityDelivered.toFixed(3);
    document.getElementById('quantity_passed').value = quantityDelivered.toFixed(3);
    document.getElementById('quantity_failed').value = '0.000';
    
    // Show the details section
    document.getElementById('itemDetails').classList.remove('hidden');
    
    validateQuantities();
}

function validateQuantities() {
    const inspected = parseFloat(document.getElementById('quantity_inspected').value) || 0;
    const passed = parseFloat(document.getElementById('quantity_passed').value) || 0;
    const failed = parseFloat(document.getElementById('quantity_failed').value) || 0;
    
    // Auto-calculate failed quantity
    const calculatedFailed = Math.max(0, inspected - passed);
    if (Math.abs(failed - calculatedFailed) > 0.001) {
        document.getElementById('quantity_failed').value = calculatedFailed.toFixed(3);
    }
    
    // Update status based on results
    const statusSelect = document.getElementById('status');
    if (inspected > 0) {
        if (failed === 0) {
            statusSelect.value = 'passed';
        } else if (passed === 0) {
            statusSelect.value = 'failed';
        } else {
            statusSelect.value = 'conditional';
        }
        toggleDefectFields(statusSelect.value);
    }
}

function toggleDefectFields(status) {
    const defectSection = document.getElementById('defectSection');
    if (status === 'failed' || status === 'conditional') {
        defectSection.classList.remove('hidden');
        document.getElementById('defect_description').required = true;
    } else {
        defectSection.classList.add('hidden');
        document.getElementById('defect_description').required = false;
    }
}

// Initialize Lucide icons
lucide.createIcons();
</script>

<?= $this->endSection() ?>