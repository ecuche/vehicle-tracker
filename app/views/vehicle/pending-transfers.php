<?php
$title = "Pending Transfer - " . ($vehicle['make'] ?? '') . " " . ($vehicle['model'] ?? '');
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('vehicles') ?>">My Vehicles</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pending Transfer</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-1">
                                <i class="bi bi-hourglass-split me-2 text-warning"></i>Pending Transfer
                            </h1>
                            <p class="text-muted mb-0">
                                Vehicle transfer awaiting your response
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-warning fs-6">
                                <i class="bi bi-clock me-1"></i> Awaiting Response
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Transfer Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> You have a pending vehicle transfer request. 
                        Please review the details and respond within 
                        <strong><?= $transfer['expires_in']; ?> days</strong>.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Vehicle Information</h6>
                            <div class="card bg-dark">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <i class="bi bi-truck display-4 text-primary"></i>
                                        </div>
                                        <div class="col-md-8">
                                            <h5><?= e($model['make']); ?> <?= e($model['model']); ?></h5>
                                            <p class="text-muted mb-1"><?= e($vehicle['year']); ?></p>
                                            <p class="mb-1"><strong>VIN:</strong> <code><?= e($vehicle['vin']); ?></code></p>
                                            <p class="mb-0"><strong>Plate:</strong> <?= e($vehicle['current_plate'] ?? 'N/A'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Transfer Timeline</h6>
                            <div class="transfer-timeline">
                                <div class="timeline-step completed">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <strong>Transfer Initiated</strong>
                                        <small class="text-muted"><?= date('M j, Y g:i A', strtotime($transfer['created_at'])); ?></small>
                                    </div>
                                </div>
                                <div class="timeline-step active">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <strong>Awaiting Your Response</strong>
                                        <small class="text-muted">Pending your acceptance/rejection</small>
                                    </div>
                                </div>
                                <div class="timeline-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <strong>Transfer Processing</strong>
                                        <small class="text-muted">After acceptance</small>
                                    </div>
                                </div>
                                <div class="timeline-step">
                                    <div class="step-number">4</div>
                                    <div class="step-content">
                                        <strong>Transfer Complete</strong>
                                        <small class="text-muted">Ownership transferred</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transfer Details -->
        <div class="col-lg-8">
            <!-- Sender Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-up me-2"></i>Seller Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="user-avatar-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="bi bi-person fs-1"></i>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <h5><?= e($seller['name']); ?></h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <i class="bi bi-envelope me-2 text-muted"></i>
                                        <?= e($seller['email']); ?>
                                    </p>
                                    <?php if ($seller['phone']): ?>
                                    <p class="mb-1">
                                        <i class="bi bi-phone me-2 text-muted"></i>
                                        <?= e($seller['phone']); ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <i class="bi bi-person-badge me-2 text-muted"></i>
                                        <span class="badge bg-info"><?= ucfirst($seller['role']); ?></span>
                                    </p>
                                    <p class="mb-0">
                                        <i class="bi bi-calendar me-2 text-muted"></i>
                                        Initiated: <?= date('M j, Y', strtotime($transfer['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="contactSender()">
                                    <i class="bi bi-envelope me-1"></i> Contact Seller
                                </button>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transfer Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-card-checklist me-2"></i>Transfer Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-muted">Transfer Type</strong>
                                <div class="mt-1">
                                    <span class="badge bg-<?= getTransferTypeBadge($transfer['transfer_type']); ?>">
                                        <?= ucfirst($transfer['transfer_type']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <strong class="text-muted">Transfer Amount</strong>
                                <div class="mt-1">
                                    <?php if ($transfer['transfer_amount']): ?>
                                    <h4 class="text-success">₦<?= number_format($transfer['transfer_amount'], 2); ?></h4>
                                    <?php else: ?>
                                    <span class="text-muted">No amount specified</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <strong class="text-muted">Transfer Status</strong>
                                <div class="mt-1">
                                    <span class="badge bg-warning">
                                        <i class="bi bi-clock me-1"></i> Pending Acceptance
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-muted">Expires In</strong>
                                <div class="mt-1">
                                    
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                
                            </div>
                            
                            <div class="mb-3">
                                <strong class="text-muted">Initiated On</strong>
                                <div class="mt-1">
                                    <?= date('F j, Y g:i A', strtotime($transfer['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($transfer['transfer_note']): ?>
                    <div class="mt-3">
                        <strong class="text-light">Transfer Notes</strong>
                        <div class="card bg-dark mt-1">
                            <div class="card-body">
                                <p class="mb-0"><?= e($transfer['transfer_note']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Terms & Conditions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-check me-2"></i>Important Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Before accepting or rejecting this transfer, please consider the following:</strong>
                    </div>
                    
                    <h6>If You Accept:</h6>
                    <ul>
                        <li>You will become the new legal owner of this vehicle</li>
                        <li>The vehicle will appear in your "My Vehicles" list</li>
                        <li>All vehicle documents and history will be transferred to you</li>
                        <li>Plate numbers remain with the vehicle</li>
                        <li>This action cannot be reversed</li>
                    </ul>
                    
                    <h6>If You Reject:</h6>
                    <ul>
                        <li>The transfer will be cancelled</li>
                        <li>The seller will be notified of your rejection</li>
                        <li>The vehicle remains with the current owner</li>
                        <li>You can provide a reason for rejection (optional)</li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> If you don't respond within <?= $transfer['expires_in']; ?> days, 
                        this transfer will automatically expire and be cancelled.
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Panel -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success btn-lg" onclick="showAcceptModal()">
                            <i class="bi bi-check-circle me-2"></i> Accept Transfer
                        </button>
                        <button type="button" class="btn btn-danger btn-lg" onclick="showRejectModal()">
                            <i class="bi bi-x-circle me-2"></i> Reject Transfer
                        </button>
                        <a href="<?= url('search/vehicle-profile/'.$vehicle['vin']) ?>" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i> View Vehicle Details
                        </a>
                        <button type="button" class="btn btn-outline-info" onclick="downloadTransferAgreement()">
                            <i class="bi bi-download me-1"></i> Download Agreement
                        </button>
                    </div>
                </div>
            </div>

            <!-- Vehicle Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Vehicle Status
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    $statusBadges = [
                        'none' => ['class' => 'bg-success', 'label' => 'Normal', 'icon' => 'bi-check-circle'],
                        'stolen' => ['class' => 'bg-danger', 'label' => 'Stolen', 'icon' => 'bi-exclamation-triangle'],
                        'no_customs_duty' => ['class' => 'bg-warning', 'label' => 'No Customs', 'icon' => 'bi-shield-exclamation'],
                        'changed_engine' => ['class' => 'bg-info', 'label' => 'Changed Engine', 'icon' => 'bi-gear'],
                        'changed_color' => ['class' => 'bg-secondary', 'label' => 'Changed Color', 'icon' => 'bi-palette']
                    ];
                    $currentStatus = $status['current_status'] ?? 'none';
                    $statusInfo = $statusBadges[$currentStatus] ?? ['class' => 'bg-secondary', 'label' => ucfirst($currentStatus), 'icon' => 'bi-question-circle'];
                    ?>
                    <div class="text-center mb-3">
                        <span class="badge <?= $statusInfo['class']; ?> fs-6 p-3">
                            <i class="<?= $statusInfo['icon']; ?> me-2"></i>
                            <?= $statusInfo['label']; ?>
                        </span>
                    </div>
                    
                    <?php if ($status['status_reason']): ?>
                    <div class="alert alert-<?= $currentStatus === 'stolen' ? 'danger' : 'warning'; ?>">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Status Reason:</strong> <?= e($status['status_reason']); ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            This vehicle can be transferred
                        </small>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-headset me-2"></i>Need Help?
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        If you have questions about this transfer or need assistance, 
                        please contact our support team.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="/support" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-envelope me-1"></i> Contact Support
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="viewFAQ()">
                            <i class="bi bi-question-circle me-1"></i> View FAQ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Accept Transfer Modal -->
<div class="modal fade" id="acceptModal" tabindex="-1" aria-labelledby="acceptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="acceptModalLabel">Accept Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Confirm Transfer Acceptance</strong>
                </div>
                
                <p>You are about to accept ownership of:</p>
                <div class="card bg-dark">
                    <div class="card-body">
                        <strong><?= e($model['make']); ?> <?= e($model['model']); ?> (<?= e($vehicle['year']); ?>)</strong><br>
                        <small class="text-muted">VIN: <?= e($vehicle['vin']); ?></small>
                    </div>
                </div>
                
                <p class="mt-3">From: <strong><?= e($seller['name']); ?></strong></p>
                
                <?php if ($transfer['transfer_amount']): ?>
                <div class="alert alert-info">
                    <i class="bi bi-currency-exchange me-2"></i>
                    <strong>Transfer Amount:</strong> ₦<?= number_format($transfer['transfer_amount'], 2); ?>
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="accept_notes" class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="accept_notes" rows="3" 
                              placeholder="Add any notes about accepting this transfer..."></textarea>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirm_accept" required>
                    <label class="form-check-label" for="confirm_accept">
                        I confirm that I want to accept ownership of this vehicle
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmAcceptBtn" disabled onclick="acceptTransfer()">
                    <i class="bi bi-check-circle me-1"></i> Accept Transfer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Transfer Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-x-circle me-2"></i>
                    <strong>Confirm Transfer Rejection</strong>
                </div>
                
                <p>You are about to reject the transfer of:</p>
                <div class="card bg-dark">
                    <div class="card-body">
                        <strong><?= e($model['make']); ?> <?= e($model['model']); ?> (<?= e($vehicle['year']); ?>)</strong><br>
                        <small class="text-muted">VIN: <?= e($vehicle['vin']); ?></small>
                    </div>
                </div>
                
                <p class="mt-3">From: <strong><?= e($seller['name']); ?></strong></p>
                
                <div class="mb-3">
                    <label for="reject_reason" class="form-label">Reason for Rejection</label>
                    <select class="form-select" id="reject_reason" required>
                        <option value="">Select a reason</option>
                        <option value="not_interested">Not Interested</option>
                        <option value="cannot_afford">Cannot Afford</option>
                        <option value="wrong_vehicle">Wrong Vehicle</option>
                        <option value="need_more_info">Need More Information</option>
                        <option value="other">Other Reason</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="reject_notes" class="form-label">Additional Notes (Optional)</label>
                    <textarea class="form-control" id="reject_notes" rows="3" 
                              placeholder="Provide more details about why you're rejecting..."></textarea>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirm_reject" required>
                    <label class="form-check-label" for="confirm_reject">
                        I confirm that I want to reject this vehicle transfer
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRejectBtn" disabled onclick="rejectTransfer()">
                    <i class="bi bi-x-circle me-1"></i> Reject Transfer
                </button>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
ob_start();
?>

<style>
.transfer-timeline {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.timeline-step {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.875rem;
}

.timeline-step.completed .step-number {
    background-color: #198754;
    color: white;
}

.timeline-step.active .step-number {
    background-color: #ffc107;
    color: #000;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
}

.step-content {
    flex: 1;
}

.step-content strong {
    display: block;
    font-size: 0.875rem;
}

.step-content small {
    font-size: 0.75rem;
}

.user-avatar-lg {
    width: 80px;
    height: 80px;
}

.progress {
    background-color: #e9ecef;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    color: white;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-lg {
    padding: 1rem;
    font-size: 1.1rem;
}
</style>
<?php
$styles = ob_get_clean();
ob_start();
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializePendingTransfer();
});

function initializePendingTransfer() {
    // Accept modal validation
    const confirmAccept = document.getElementById('confirm_accept');
    const confirmAcceptBtn = document.getElementById('confirmAcceptBtn');
    
    if (confirmAccept && confirmAcceptBtn) {
        confirmAccept.addEventListener('change', function() {
            confirmAcceptBtn.disabled = !this.checked;
        });
    }

    // Reject modal validation
    const rejectReason = document.getElementById('reject_reason');
    const confirmReject = document.getElementById('confirm_reject');
    const confirmRejectBtn = document.getElementById('confirmRejectBtn');
    
    if (rejectReason && confirmReject && confirmRejectBtn) {
        function validateRejectForm() {
            const isValid = rejectReason.value !== '' && confirmReject.checked;
            confirmRejectBtn.disabled = !isValid;
        }

        rejectReason.addEventListener('change', validateRejectForm);
        confirmReject.addEventListener('change', validateRejectForm);
    }
}

function showAcceptModal() {
    const modal = new bootstrap.Modal(document.getElementById('acceptModal'));
    
    // Reset form
    document.getElementById('accept_notes').value = '';
    document.getElementById('confirm_accept').checked = false;
    document.getElementById('confirmAcceptBtn').disabled = true;
    
    modal.show();
}

function showRejectModal() {
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    
    // Reset form
    document.getElementById('reject_reason').value = '';
    document.getElementById('reject_notes').value = '';
    document.getElementById('confirm_reject').checked = false;
    document.getElementById('confirmRejectBtn').disabled = true;
    
    modal.show();
}

function acceptTransfer() {
    const notes = document.getElementById('accept_notes').value;
    const confirmBtn = document.getElementById('confirmAcceptBtn');
    
    // Disable button and show loading
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Accepting...';
    
    fetch(`/vehicles/transfers/<?= $transfer['id']; ?>/accept`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            VehicleTrackerApp.showToast('Transfer accepted successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('acceptModal')).hide();
            
            // Redirect to vehicles page after delay
            setTimeout(() => {
                window.location.href = '/vehicles';
            }, 2000);
        } else {
            throw new Error(data.error || 'Failed to accept transfer');
        }
    })
    .catch(error => {
        console.error('Accept error:', error);
        VehicleTrackerApp.showToast(error.message, 'error');
        
        // Re-enable button
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Accept Transfer';
    });
}

function rejectTransfer() {
    const reason = document.getElementById('reject_reason').value;
    const notes = document.getElementById('reject_notes').value;
    const confirmBtn = document.getElementById('confirmRejectBtn');
    
    if (!reason) {
        VehicleTrackerApp.showToast('Please select a reason for rejection', 'error');
        return;
    }
    
    // Disable button and show loading
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Rejecting...';
    
    fetch(`/vehicles/transfers/<?= $transfer['id']; ?>/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            reason: reason,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            VehicleTrackerApp.showToast('Transfer rejected successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
            
            // Redirect to dashboard after delay
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 2000);
        } else {
            throw new Error(data.error || 'Failed to reject transfer');
        }
    })
    .catch(error => {
        console.error('Reject error:', error);
        VehicleTrackerApp.showToast(error.message, 'error');
        
        // Re-enable button
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = '<i class="bi bi-x-circle me-1"></i> Reject Transfer';
    });
}

function contactSender() {
    const email = '<?= e($seller['email']); ?>';
    const subject = 'Regarding Vehicle Transfer: <?= e($model['make'] . ' ' . $model['model']); ?>';
    const body = `Hello <?= e($seller['name']); ?>,\n\nI have some questions about the vehicle transfer.\n\nVehicle: <?= e($model['make'] . ' ' . $model['model'] . ' (' . $vehicle['year'] . ')'); ?>\nVIN: <?= e($vehicle['vin']); ?>\n\n`;
    
    window.location.href = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
}

function downloadTransferAgreement() {
    VehicleTrackerApp.showToast('Downloading transfer agreement...', 'info');
    
    // This would typically generate and download a PDF agreement
    setTimeout(() => {
        VehicleTrackerApp.showToast('Agreement downloaded successfully', 'success');
    }, 1000);
}

function viewFAQ() {
    window.open('/faq#vehicle-transfers', '_blank');
}

// Start countdown if element exists
if (document.querySelector('.expiry-timer')) {
    startExpiryCountdown();
    setInterval(startExpiryCountdown, 3600000); // Update every hour
}
</script>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>