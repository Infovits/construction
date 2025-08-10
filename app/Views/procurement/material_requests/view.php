<?php helper('currency'); ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Material Request #<?= esc($materialRequest['request_number']) ?></h1>
            <div class="flex items-center space-x-4 mt-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    <?php
                    switch($materialRequest['status']) {
                        case 'draft': echo 'bg-gray-100 text-gray-800'; break;
                        case 'pending_approval': echo 'bg-yellow-100 text-yellow-800'; break;
                        case 'approved': echo 'bg-green-100 text-green-800'; break;
                        case 'rejected': echo 'bg-red-100 text-red-800'; break;
                        case 'completed': echo 'bg-blue-100 text-blue-800'; break;
                        default: echo 'bg-gray-100 text-gray-800'; break;
                    }
                    ?>">
                    <?= ucfirst(str_replace('_', ' ', $materialRequest['status'])) ?>
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    <?php
                    switch($materialRequest['priority']) {
                        case 'low': echo 'bg-gray-100 text-gray-800'; break;
                        case 'medium': echo 'bg-blue-100 text-blue-800'; break;
                        case 'high': echo 'bg-yellow-100 text-yellow-800'; break;
                        case 'urgent': echo 'bg-red-100 text-red-800'; break;
                        default: echo 'bg-gray-100 text-gray-800'; break;
                    }
                    ?>">
                    <?= ucfirst($materialRequest['priority']) ?> Priority
                </span>
                <span class="text-gray-600">Created <?= date('M j, Y', strtotime($materialRequest['created_at'])) ?></span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('admin/material-requests') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to List
            </a>
            
            <?php if ($materialRequest['status'] === 'draft'): ?>
                <a href="<?= base_url('admin/material-requests/' . $materialRequest['id'] . '/edit') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                    Edit
                </a>
                <button onclick="submitForApproval(<?= $materialRequest['id'] ?>)" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                    Submit for Approval
                </button>
            <?php endif; ?>
            
            <?php if ($materialRequest['status'] === 'pending_approval'): ?>
                <button onclick="approveRequest(<?= $materialRequest['id'] ?>)" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                    Approve
                </button>
                <button onclick="rejectRequest(<?= $materialRequest['id'] ?>)" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                    Reject
                </button>
            <?php endif; ?>
            
            <?php if ($materialRequest['status'] === 'approved'): ?>
                <a href="<?= base_url('admin/purchase-orders/create?material_request_id=' . $materialRequest['id']) ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-lucide="shopping-cart" class="w-4 h-4 mr-2"></i>
                    Create Purchase Order
                </a>
            <?php endif; ?>
            
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                Print
            </button>
        </div>
    </div>

    <!-- Request Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Request Details -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Request Information</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Request Number:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($materialRequest['request_number']) ?></span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Request Date:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= date('M j, Y', strtotime($materialRequest['request_date'])) ?></span>
                </div>
                <?php if (!empty($materialRequest['required_date'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Required Date:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= date('M j, Y', strtotime($materialRequest['required_date'])) ?></span>
                </div>
                <?php endif; ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Requested By:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($materialRequest['requester_first_name'] . ' ' . $materialRequest['requester_last_name']) ?></span>
                </div>
                <?php if (!empty($materialRequest['project_name'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Project:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($materialRequest['project_name']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($materialRequest['department_name'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Department:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($materialRequest['department_name']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Approval Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Approval Status</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Current Status:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= ucfirst(str_replace('_', ' ', $materialRequest['status'])) ?></span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Priority Level:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= ucfirst($materialRequest['priority']) ?></span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Total Estimated Cost:</span>
                    <span class="text-sm text-gray-900 ml-2 font-medium">MWK <?= number_format($materialRequest['total_estimated_cost'], 2) ?></span>
                </div>
                
                <?php if ($materialRequest['status'] === 'approved' && !empty($materialRequest['approved_date'])): ?>
                <hr class="my-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Approved By:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($materialRequest['approver_first_name'] . ' ' . $materialRequest['approver_last_name']) ?></span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Approved Date:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= date('M j, Y g:i A', strtotime($materialRequest['approved_date'])) ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($materialRequest['status'] === 'rejected'): ?>
                <hr class="my-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Rejected By:</span>
                    <span class="text-sm text-gray-900 ml-2"><?= esc($materialRequest['approver_first_name'] . ' ' . $materialRequest['approver_last_name']) ?></span>
                </div>
                <?php if (!empty($materialRequest['rejection_reason'])): ?>
                <div>
                    <span class="text-sm font-medium text-gray-500">Rejection Reason:</span>
                    <p class="text-sm text-gray-900 mt-1"><?= nl2br(esc($materialRequest['rejection_reason'])) ?></p>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Requested Materials -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Requested Materials</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Requested</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Approved</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specifications</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (isset($materialRequest['items']) && !empty($materialRequest['items'])): ?>
                        <?php foreach ($materialRequest['items'] as $item): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= esc($item['material_name']) ?></div>
                                        <div class="text-sm text-gray-500"><?= esc($item['item_code']) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= number_format($item['quantity_requested'], 3) ?> <?= esc($item['unit']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($item['quantity_approved'] !== null): ?>
                                        <span class="text-green-600 font-medium"><?= number_format($item['quantity_approved'], 3) ?> <?= esc($item['unit']) ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    MWK <?= number_format($item['estimated_unit_cost'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    MWK <?= number_format($item['estimated_total_cost'], 2) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if (!empty($item['specification_notes'])): ?>
                                        <p class="text-sm text-gray-900"><?= nl2br(esc($item['specification_notes'])) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($item['urgency_notes'])): ?>
                                        <p class="text-xs text-red-600 mt-1"><strong>Urgent:</strong> <?= nl2br(esc($item['urgency_notes'])) ?></p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Summary -->
        <?php if (isset($materialRequest['items']) && !empty($materialRequest['items'])): ?>
        <div class="p-6 bg-gray-50 border-t">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-500">Total Items: <?= count($materialRequest['items']) ?></span>
                <span class="text-lg font-bold text-gray-900">Total Estimated Cost: MWK <?= number_format($materialRequest['total_estimated_cost'], 2) ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Notes -->
    <?php if (!empty($materialRequest['notes'])): ?>
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h2>
        <p class="text-gray-700"><?= nl2br(esc($materialRequest['notes'])) ?></p>
    </div>
    <?php endif; ?>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Approve Material Request</h3>
            <form id="approvalForm" method="POST">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Approval Notes</label>
                    <textarea name="approval_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional approval notes..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Material Request</h3>
            <form id="rejectionForm" method="POST">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason *</label>
                    <textarea name="rejection_reason" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Please provide reason for rejection..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Submit for Approval Modal -->
<div id="submitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Submit for Approval</h3>
            <p class="text-gray-600 mb-4">Are you sure you want to submit this material request for approval? You will not be able to edit it after submission.</p>
            <form id="submitForm" method="POST">
                <?= csrf_field() ?>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveRequest(id) {
    document.getElementById('approvalForm').action = `<?= base_url('admin/material-requests/') ?>${id}/approve`;
    document.getElementById('approvalModal').classList.remove('hidden');
}

function rejectRequest(id) {
    document.getElementById('rejectionForm').action = `<?= base_url('admin/material-requests/') ?>${id}/reject`;
    document.getElementById('rejectionModal').classList.remove('hidden');
}

function submitForApproval(id) {
    document.getElementById('submitForm').action = `<?= base_url('admin/material-requests/') ?>${id}/submit`;
    document.getElementById('submitModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('approvalModal').classList.add('hidden');
    document.getElementById('rejectionModal').classList.add('hidden');
    document.getElementById('submitModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = ['approvalModal', 'rejectionModal', 'submitModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target == modal) {
            modal.classList.add('hidden');
        }
    });
}

// Initialize Lucide icons
lucide.createIcons();
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .bg-white {
        background: white !important;
    }
    
    .shadow-sm, .border {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
    }
}
</style>

<?= $this->endSection() ?>