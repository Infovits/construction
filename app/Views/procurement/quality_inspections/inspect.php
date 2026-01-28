<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Conduct Quality Inspection<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Conduct Quality Inspection</h1>
            <p class="text-gray-600">Inspection #<?= esc($inspection['inspection_number']) ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/quality-inspections/' . $inspection['id']) ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Details
            </a>
            <a href="<?= base_url('admin/quality-inspections') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Inspection Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Inspection Number</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['inspection_number']) ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Inspection Type</label>
                    <div class="text-sm text-gray-900"><?= ucfirst($inspection['inspection_type']) ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <?= ucfirst($inspection['status']) ?>
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Inspection Date</label>
                    <div class="text-sm text-gray-900"><?= date('M j, Y \a\t g:i A', strtotime($inspection['inspection_date'])) ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Quantity to Inspect</label>
                    <div class="text-sm text-gray-900"><?= number_format($inspection['quantity_inspected'] ?? 0, 3) ?> <?= esc($inspection['unit'] ?? '') ?></div>
                </div>
            </div>
        </div>

        <!-- Material Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Material Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Material Name</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['material_name'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Material Code</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['material_code'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Unit</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['unit'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">GRN Number</label>
                    <?php if (!empty($inspection['grn_number'])): ?>
                        <a href="<?= base_url('admin/goods-receipt/' . $inspection['grn_item_id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800">
                            <?= esc($inspection['grn_number']) ?>
                        </a>
                    <?php else: ?>
                        <div class="text-sm text-gray-500">N/A</div>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Supplier</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['supplier_name'] ?? 'N/A') ?></div>
                </div>
            </div>
        </div>

        <!-- Inspector Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Inspector Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Inspector</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['inspector_name'] ?? 'Unassigned') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Inspector Email</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['inspector_email'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Department</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['inspector_department'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Phone</label>
                    <div class="text-sm text-gray-900"><?= esc($inspection['inspector_phone'] ?? 'N/A') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inspection Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Inspection Results</h3>
            <p class="text-sm text-gray-600 mt-1">Please complete the inspection by filling in the results below</p>
        </div>
        <div class="p-6">
            <?= form_open('admin/quality-inspections/' . $inspection['id'] . '/complete', ['class' => 'space-y-6']) ?>
                
                <!-- Inspection Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Inspection Status <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="status" value="passed" class="mr-2 text-green-600 focus:ring-green-500" required>
                            <span class="text-green-600 font-medium">Passed</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="failed" class="mr-2 text-red-600 focus:ring-red-500">
                            <span class="text-red-600 font-medium">Failed</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="conditional" class="mr-2 text-orange-600 focus:ring-orange-500">
                            <span class="text-orange-600 font-medium">Conditional</span>
                        </label>
                    </div>
                </div>

                <!-- Overall Grade -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Overall Grade</label>
                    <select name="overall_grade" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Grade</option>
                        <option value="A">A - Excellent</option>
                        <option value="B">B - Good</option>
                        <option value="C">C - Fair</option>
                        <option value="D">D - Poor</option>
                        <option value="F">F - Fail</option>
                    </select>
                </div>

                <!-- Quantities -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Passed <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity_passed" step="0.001" min="0" max="<?= $inspection['quantity_inspected'] ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        <p class="text-xs text-gray-500 mt-1">Maximum: <?= number_format($inspection['quantity_inspected'], 3) ?> <?= esc($inspection['unit'] ?? '') ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Failed</label>
                        <input type="number" name="quantity_failed" step="0.001" min="0" max="<?= $inspection['quantity_inspected'] ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Maximum: <?= number_format($inspection['quantity_inspected'], 3) ?> <?= esc($inspection['unit'] ?? '') ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Inspected</label>
                        <input type="text" value="<?= number_format($inspection['quantity_inspected'], 3) ?> <?= esc($inspection['unit'] ?? '') ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700" readonly>
                    </div>
                </div>

                <!-- Defect Description (Conditional) -->
                <div id="defect_section" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Defect Description</label>
                    <textarea name="defect_description" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Please describe the defects found..."></textarea>
                </div>

                <!-- Corrective Action (Conditional) -->
                <div id="corrective_action_section" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Corrective Action Required</label>
                    <textarea name="corrective_action" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Please specify corrective actions required..."></textarea>
                </div>

                <!-- Inspector Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Inspector Notes</label>
                    <textarea name="inspector_notes" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Additional notes..."></textarea>
                </div>

                <!-- Inspection Criteria -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Inspection Criteria</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Visual Inspection</div>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[visual]" value="pass" class="mr-2 text-green-600 focus:ring-green-500">
                                    <span class="text-green-600">Pass</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[visual]" value="fail" class="mr-2 text-red-600 focus:ring-red-500">
                                    <span class="text-red-600">Fail</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Dimensional Check</div>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[dimensional]" value="pass" class="mr-2 text-green-600 focus:ring-green-500">
                                    <span class="text-green-600">Pass</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[dimensional]" value="fail" class="mr-2 text-red-600 focus:ring-red-500">
                                    <span class="text-red-600">Fail</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Material Compliance</div>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[material_compliance]" value="pass" class="mr-2 text-green-600 focus:ring-green-500">
                                    <span class="text-green-600">Pass</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[material_compliance]" value="fail" class="mr-2 text-red-600 focus:ring-red-500">
                                    <span class="text-red-600">Fail</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Documentation</div>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[documentation]" value="pass" class="mr-2 text-green-600 focus:ring-green-500">
                                    <span class="text-green-600">Pass</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[documentation]" value="fail" class="mr-2 text-red-600 focus:ring-red-500">
                                    <span class="text-red-600">Fail</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Packaging</div>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[packaging]" value="pass" class="mr-2 text-green-600 focus:ring-green-500">
                                    <span class="text-green-600">Pass</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[packaging]" value="fail" class="mr-2 text-red-600 focus:ring-red-500">
                                    <span class="text-red-600">Fail</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Labeling</div>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[labeling]" value="pass" class="mr-2 text-green-600 focus:ring-green-500">
                                    <span class="text-green-600">Pass</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[labeling]" value="fail" class="mr-2 text-red-600 focus:ring-red-500">
                                    <span class="text-red-600">Fail</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Quantity Check</div>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[quantity]" value="pass" class="mr-2 text-green-600 focus:ring-green-500">
                                    <span class="text-green-600">Pass</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[quantity]" value="fail" class="mr-2 text-red-600 focus:ring-red-500">
                                    <span class="text-red-600">Fail</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Sample Testing</div>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[sample_testing]" value="pass" class="mr-2 text-green-600 focus:ring-green-500">
                                    <span class="text-green-600">Pass</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="criteria[sample_testing]" value="fail" class="mr-2 text-red-600 focus:ring-red-500">
                                    <span class="text-red-600">Fail</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                        Complete Inspection
                    </button>
                    <a href="<?= base_url('admin/quality-inspections/' . $inspection['id']) ?>" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                        <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                        Cancel
                    </a>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
// Show/hide defect and corrective action sections based on status
document.addEventListener('DOMContentLoaded', function() {
    const statusRadios = document.querySelectorAll('input[name="status"]');
    const defectSection = document.getElementById('defect_section');
    const correctiveActionSection = document.getElementById('corrective_action_section');
    
    function toggleSections() {
        const selectedStatus = document.querySelector('input[name="status"]:checked')?.value;
        
        if (selectedStatus === 'failed' || selectedStatus === 'conditional') {
            defectSection.style.display = 'block';
            correctiveActionSection.style.display = 'block';
        } else {
            defectSection.style.display = 'none';
            correctiveActionSection.style.display = 'none';
        }
    }
    
    statusRadios.forEach(radio => {
        radio.addEventListener('change', toggleSections);
    });
    
    // Initialize on load
    toggleSections();
});

lucide.createIcons();
</script>

<?= $this->endSection() ?>