<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-eye me-2"></i>Cost Code Details
                        </h5>
                        <div>
                            <a href="<?= base_url('admin/accounting/cost-codes/' . $costCode['id'] . '/edit') ?>" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="<?= base_url('admin/accounting/cost-codes') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Cost Codes
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="200">Cost Code:</th>
                                    <td>
                                        <span class="badge bg-primary fs-6"><?= esc($costCode['code']) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td class="fw-semibold"><?= esc($costCode['name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td><?= esc($costCode['description']) ?: '<em class="text-muted">No description</em>' ?></td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>
                                        <?php 
                                        $categoryColors = [
                                            'labor' => 'success',
                                            'material' => 'info',
                                            'equipment' => 'warning',
                                            'subcontractor' => 'primary',
                                            'overhead' => 'secondary',
                                            'other' => 'dark'
                                        ];
                                        $color = $categoryColors[$costCode['category']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= ucfirst($costCode['category']) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Cost Type:</th>
                                    <td>
                                        <?php $typeColor = $costCode['cost_type'] == 'direct' ? 'success' : 'info'; ?>
                                        <span class="badge bg-<?= $typeColor ?>"><?= ucfirst($costCode['cost_type']) ?> Cost</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Unit of Measure:</th>
                                    <td><?= esc($costCode['unit_of_measure']) ?: '<em class="text-muted">Not specified</em>' ?></td>
                                </tr>
                                <tr>
                                    <th>Standard Rate:</th>
                                    <td>
                                        <?php if ($costCode['standard_rate']): ?>
                                            <span class="fw-bold text-success">$<?= number_format($costCode['standard_rate'], 2) ?></span>
                                        <?php else: ?>
                                            <em class="text-muted">Not set</em>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <?php if ($costCode['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td><?= date('M j, Y \a\t g:i A', strtotime($costCode['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td><?= date('M j, Y \a\t g:i A', strtotime($costCode['updated_at'])) ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-chart-bar me-2"></i>Usage Statistics
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="text-primary mb-1"><?= number_format($costCode['usage_count'] ?? 0) ?></h4>
                                                <small class="text-muted">Times Used</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success mb-1">$<?= number_format($costCode['total_cost_tracked'] ?? 0, 2) ?></h4>
                                            <small class="text-muted">Total Cost</small>
                                        </div>
                                    </div>
                                    
                                    <?php if (($costCode['usage_count'] ?? 0) > 0): ?>
                                        <hr>
                                        <div class="text-center">
                                            <small class="text-muted">
                                                Average per use: 
                                                <strong>$<?= number_format(($costCode['total_cost_tracked'] ?? 0) / ($costCode['usage_count'] ?? 1), 2) ?></strong>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteCostCode(<?= $costCode['id'] ?>)">
                                        <i class="fas fa-trash me-1"></i>Delete Cost Code
                                    </button>
                                    <button type="button" class="btn btn-outline-<?= $costCode['is_active'] ? 'warning' : 'success' ?>" 
                                            onclick="toggleCostCode(<?= $costCode['id'] ?>)">
                                        <i class="fas fa-toggle-<?= $costCode['is_active'] ? 'on' : 'off' ?> me-1"></i>
                                        <?= $costCode['is_active'] ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteCostCode(id) {
    if (confirm('Are you sure you want to delete this cost code? This action cannot be undone.')) {
        fetch(`<?= base_url('admin/accounting/cost-codes') ?>/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?= base_url('admin/accounting/cost-codes') ?>';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the cost code.');
        });
    }
}

function toggleCostCode(id) {
    fetch(`<?= base_url('admin/accounting/cost-codes') ?>/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
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
        alert('An error occurred while updating the cost code status.');
    });
}
</script>
<?= $this->endSection() ?>