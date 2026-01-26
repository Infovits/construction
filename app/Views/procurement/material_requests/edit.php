<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Material Request</h1>
            <p class="text-gray-600">Request Number: <?= esc($materialRequest['request_number']) ?></p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <a href="<?= base_url('admin/material-requests/' . $materialRequest['id']) ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="eye" class="w-4 h-4 mr-2"></i> View
            </a>
            <a href="<?= base_url('admin/material-requests') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
            <?php
            switch($materialRequest['status']) {
                case 'draft':
                    echo 'bg-yellow-100 text-yellow-800';
                    break;
                case 'pending_approval':
                    echo 'bg-blue-100 text-blue-800';
                    break;
                case 'approved':
                    echo 'bg-green-100 text-green-800';
                    break;
                case 'rejected':
                    echo 'bg-red-100 text-red-800';
                    break;
                default:
                    echo 'bg-gray-100 text-gray-800';
            }
            ?>">
            Status: <?= ucfirst(str_replace('_', ' ', $materialRequest['status'])) ?>
        </span>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="<?= base_url('admin/material-requests/' . $materialRequest['id']) ?>" method="post" id="material-request-form">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">

            <div class="p-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                        <select name="project_id" id="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Project (Optional)</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>" <?= $materialRequest['project_id'] == $project['id'] ? 'selected' : '' ?>>
                                    <?= esc($project['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select name="department_id" id="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Department (Optional)</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= $department['id'] ?>" <?= $materialRequest['department_id'] == $department['id'] ? 'selected' : '' ?>>
                                    <?= esc($department['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="required_date" class="block text-sm font-medium text-gray-700 mb-2">Required Date</label>
                        <input type="date" name="required_date" id="required_date"
                               value="<?= $materialRequest['required_date'] ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select name="priority" id="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="low" <?= $materialRequest['priority'] == 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= $materialRequest['priority'] == 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= $materialRequest['priority'] == 'high' ? 'selected' : '' ?>>High</option>
                            <option value="urgent" <?= $materialRequest['priority'] == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Additional notes or requirements..."><?= esc($materialRequest['notes']) ?></textarea>
                </div>

                <!-- Items Section -->
                <div class="border-t pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Requested Items</h3>
                        <button type="button" id="add-item" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Item
                        </button>
                    </div>

                    <div id="items-container">
                        <?php if (!empty($materialRequest['items'])): ?>
                            <?php foreach ($materialRequest['items'] as $index => $item): ?>
                                <div class="item-row bg-gray-50 p-4 rounded-lg mb-4" data-index="<?= $index ?>">
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Material</label>
                                            <select name="items[<?= $index ?>][material_id]" class="material-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                                <option value="">Select Material</option>
                                                <?php foreach ($materials as $material): ?>
                                                    <option value="<?= $material['id'] ?>" <?= $item['material_id'] == $material['id'] ? 'selected' : '' ?>>
                                                        <?= esc($material['name']) ?> (<?= esc($material['item_code']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                            <input type="number" step="0.01" name="items[<?= $index ?>][quantity_requested]"
                                                   value="<?= $item['quantity_requested'] ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Est. Unit Cost</label>
                                            <input type="number" step="0.01" name="items[<?= $index ?>][estimated_unit_cost]"
                                                   value="<?= $item['estimated_unit_cost'] ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                        </div>

                                        <div class="flex items-end">
                                            <button type="button" class="remove-item w-full px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Specification Notes</label>
                                            <textarea name="items[<?= $index ?>][specification_notes]" rows="2"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                      placeholder="Technical specifications..."><?= esc($item['specification_notes']) ?></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Urgency Notes</label>
                                            <textarea name="items[<?= $index ?>][urgency_notes]" rows="2"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                      placeholder="Why is this urgent?"><?= esc($item['urgency_notes']) ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="<?= base_url('admin/material-requests') ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Update Request
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template for new items -->
<div id="item-template" class="hidden">
    <div class="item-row bg-gray-50 p-4 rounded-lg mb-4" data-index="__INDEX__">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Material</label>
                <select name="items[__INDEX__][material_id]" class="material-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    <option value="">Select Material</option>
                    <?php foreach ($materials as $material): ?>
                        <option value="<?= $material['id'] ?>">
                            <?= esc($material['name']) ?> (<?= esc($material['item_code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                <input type="number" step="0.01" name="items[__INDEX__][quantity_requested]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Est. Unit Cost</label>
                <input type="number" step="0.01" name="items[__INDEX__][estimated_unit_cost]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>

            <div class="flex items-end">
                <button type="button" class="remove-item w-full px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Specification Notes</label>
                <textarea name="items[__INDEX__][specification_notes]" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Technical specifications..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urgency Notes</label>
                <textarea name="items[__INDEX__][urgency_notes]" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Why is this urgent?"></textarea>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = <?= count($materialRequest['items'] ?? []) ?>;

    // Add item button
    document.getElementById('add-item').addEventListener('click', function() {
        addItem();
    });

    // Remove item buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            const itemRow = e.target.closest('.item-row');
            if (itemRow) {
                itemRow.remove();
                updateItemIndices();
            }
        }
    });

    function addItem() {
        const template = document.getElementById('item-template').innerHTML;
        const newItem = template.replace(/__INDEX__/g, itemIndex);
        document.getElementById('items-container').insertAdjacentHTML('beforeend', newItem);
        itemIndex++;
    }

    function updateItemIndices() {
        const itemRows = document.querySelectorAll('.item-row');
        itemRows.forEach((row, index) => {
            row.setAttribute('data-index', index);
            // Update all input names within this row
            const inputs = row.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                }
            });
        });
        itemIndex = itemRows.length;
    }
});
</script>

<?= $this->endSection() ?>
