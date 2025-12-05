<?php
$title = "Vehicle Details - " . ($vehicle['make'] ?? 'Unknown') . " " . ($vehicle['model'] ?? '');
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/vehicles">My Vehicles</a></li>
            <li class="breadcrumb-item active" aria-current="page">Vehicle Details</li>
        </ol>
    </nav>

    <!-- Vehicle Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-1">
                                <?= e($vehicle['make'] ?? 'Unknown'); ?> <?= e($vehicle['model'] ?? ''); ?> (<?= e($vehicle['year'] ?? ''); ?>)
                            </h1>
                            <p class="text-muted mb-0">
                                VIN: <?= e($vehicle['vin'] ?? 'N/A'); ?> | 
                                Current Plate: <?= e($vehicle['current_plate_number'] ?? 'N/A'); ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php
                            $statusBadges = [
                                'none' => ['class' => 'bg-success', 'label' => 'Normal', 'icon' => 'bi-check-circle'],
                                'stolen' => ['class' => 'bg-danger', 'label' => 'Stolen', 'icon' => 'bi-exclamation-triangle'],
                                'no_customs_duty' => ['class' => 'bg-warning', 'label' => 'No Customs Duty', 'icon' => 'bi-shield-exclamation'],
                                'changed_engine' => ['class' => 'bg-info', 'label' => 'Changed Engine', 'icon' => 'bi-gear'],
                                'changed_color' => ['class' => 'bg-secondary', 'label' => 'Changed Color', 'icon' => 'bi-palette']
                            ];
                            $currentStatus = $vehicle['current_status'] ?? 'none';
                            $statusInfo = $statusBadges[$currentStatus] ?? ['class' => 'bg-secondary', 'label' => ucfirst($currentStatus), 'icon' => 'bi-question-circle'];
                            ?>
                            <span class="badge <?= $statusInfo['class']; ?> fs-6 p-2">
                                <i class="<?= $statusInfo['icon']; ?> me-1"></i>
                                <?= $statusInfo['label']; ?>
                            </span>
                            <div class="mt-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                    <i class="bi bi-printer me-1"></i> Print
                                </button>
                                <?php if ($is_owner ?? false): ?>
                                <a href="/vehicles/<?= $vehicle['id']; ?>/edit" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Vehicle Information -->
        <div class="col-lg-6">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Basic Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">VIN Number</strong><br>
                            <code class="fs-5"><?= e($vehicle['vin'] ?? 'N/A'); ?></code>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Current Plate</strong><br>
                            <span class="fs-5 badge bg-dark"><?= e($vehicle['current_plate_number'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Make</strong><br>
                            <?= e($vehicle['make'] ?? 'N/A'); ?>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Model</strong><br>
                            <?= e($vehicle['model'] ?? 'N/A'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Year</strong><br>
                            <?= e($vehicle['year'] ?? 'N/A'); ?>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Registration Date</strong><br>
                            <?= e(isset($vehicle['created_at']) ? date('M j, Y', strtotime($vehicle['created_at'])) : 'N/A'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <strong class="text-muted">Current Status</strong><br>
                            <span class="badge <?= $statusInfo['class']; ?> fs-6">
                                <i class="<?= $statusInfo['icon']; ?> me-1"></i>
                                <?= $statusInfo['label']; ?>
                            </span>
                            <?php if (isset($vehicle['status_updated_at'])): ?>
                                <small class="text-muted ms-2">
                                    (Updated: <?= date('M j, Y', strtotime($vehicle['status_updated_at'])); ?>)
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (isset($vehicle['status_reason']) && $vehicle['status_reason']): ?>
                    <div class="row">
                        <div class="col-12">
                            <strong class="text-muted">Status Reason</strong><br>
                            <div class="alert alert-<?= $currentStatus === 'stolen' ? 'danger' : 'warning'; ?> mt-2">
                                <i class="bi bi-info-circle me-2"></i>
                                <?= e($vehicle['status_reason']); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ownership Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-badge me-2"></i>Ownership Information
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($vehicle['current_owner']) && $vehicle['current_owner']): ?>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Current Owner</strong><br>
                            <?= e($vehicle['current_owner']['name']); ?>
                            <?php if ($is_owner ?? false): ?>
                            <span class="badge bg-success ms-1">You</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Role</strong><br>
                            <span class="badge bg-info"><?= ucfirst($vehicle['current_owner']['role']); ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Ownership Since</strong><br>
                            <?= e(isset($vehicle['current_owner_since']) ? date('M j, Y', strtotime($vehicle['current_owner_since'])) : 'N/A'); ?>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Ownership Type</strong><br>
                            <span class="badge bg-<?= $vehicle['is_original_owner'] ? 'success' : 'primary'; ?>">
                                <?= $vehicle['is_original_owner'] ? 'Original Owner' : 'Transferred'; ?>
                            </span>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-3">
                        <i class="bi bi-person-x display-1 text-muted"></i>
                        <p class="text-muted mt-2">No ownership information available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Transfer Vehicle -->
            <?php if ($is_owner ?? false && $vehicle['current_status'] !== 'stolen'): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-arrow-left-right me-2"></i>Transfer Vehicle
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Transfer ownership of this vehicle to another user.</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="initiateTransfer()">
                            <i class="bi bi-arrow-left-right me-1"></i> Initiate Transfer
                        </button>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            You can transfer this vehicle to another driver by their email, phone number, or NIN.
                        </small>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Vehicle Media & Actions -->
        <div class="col-lg-6">
            <!-- Vehicle Images -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-images me-2"></i>Vehicle Images
                    </h5>
                    <?php if ($is_owner ?? false): ?>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="manageImages()">
                        <i class="bi bi-plus-circle me-1"></i> Add Images
                    </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (isset($vehicle['images']) && !empty($vehicle['images'])): ?>
                    <div class="row" id="vehicleImages">
                        <?php foreach ($vehicle['images'] as $index => $image): ?>
                        <div class="col-6 col-md-4 mb-3 image-item" data-image-id="<?= $image['id']; ?>">
                            <div class="position-relative">
                                <a href="<?= e($image['url']); ?>" data-lightbox="vehicle-images" 
                                   data-title="<?= e($vehicle['make']); ?> <?= e($vehicle['model']); ?> - Image <?= $index + 1; ?>">
                                    <img src="<?= e($image['url']); ?>" class="img-thumbnail w-100" 
                                         alt="Vehicle image" style="height: 120px; object-fit: cover;">
                                </a>
                                <?php if ($is_owner ?? false): ?>
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                        onclick="deleteImage(<?= $image['id']; ?>)" 
                                        style="width: 25px; height: 25px; padding: 0;">
                                    <i class="bi bi-x"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-image display-1 text-muted"></i>
                        <p class="text-muted mt-2">No vehicle images available</p>
                        <?php if ($is_owner ?? false): ?>
                        <button type="button" class="btn btn-primary mt-2" onclick="manageImages()">
                            <i class="bi bi-plus-circle me-1"></i> Add Images
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Vehicle Documents -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark me-2"></i>Vehicle Documents
                    </h5>
                    <?php if ($is_owner ?? false): ?>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="manageDocuments()">
                        <i class="bi bi-plus-circle me-1"></i> Add Documents
                    </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (isset($vehicle['documents']) && !empty($vehicle['documents'])): ?>
                    <div class="list-group list-group-flush" id="vehicleDocuments">
                        <?php foreach ($vehicle['documents'] as $document): ?>
                        <div class="list-group-item px-0 document-item" data-document-id="<?= $document['id']; ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-text me-3 text-primary fs-4"></i>
                                    <div>
                                        <div class="fw-bold"><?= e($document['name']); ?></div>
                                        <small class="text-muted">
                                            Uploaded: <?= date('M j, Y', strtotime($document['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <a href="<?= e($document['url']); ?>" class="btn btn-sm btn-outline-primary" 
                                       target="_blank" title="View Document">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= e($document['url']); ?>" class="btn btn-sm btn-outline-success" 
                                       download title="Download Document">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <?php if ($is_owner ?? false): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteDocument(<?= $document['id']; ?>)" 
                                            title="Delete Document">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-x display-1 text-muted"></i>
                        <p class="text-muted mt-2">No vehicle documents available</p>
                        <?php if ($is_owner ?? false): ?>
                        <button type="button" class="btn btn-primary mt-2" onclick="manageDocuments()">
                            <i class="bi bi-plus-circle me-1"></i> Add Documents
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($is_owner ?? false): ?>
                        <button type="button" class="btn btn-outline-primary text-start" onclick="assignNewPlate()">
                            <i class="bi bi-123 me-2"></i> Assign New Plate Number
                        </button>
                        <button type="button" class="btn btn-outline-info text-start" onclick="viewPlateHistory()">
                            <i class="bi bi-clock-history me-2"></i> View Plate History
                        </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-outline-secondary text-start" onclick="viewOwnershipHistory()">
                            <i class="bi bi-diagram-3 me-2"></i> View Ownership History
                        </button>
                        <button type="button" class="btn btn-outline-warning text-start" onclick="generateReport()">
                            <i class="bi bi-file-pdf me-2"></i> Generate Vehicle Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plate Number History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-123 me-2"></i>Plate Number History
                    </h5>
                    <?php if ($is_owner ?? false): ?>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="assignNewPlate()">
                        <i class="bi bi-plus-circle me-1"></i> Assign New Plate
                    </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (isset($vehicle['plate_history']) && !empty($vehicle['plate_history'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Plate Number</th>
                                    <th>Assigned Date</th>
                                    <th>Assigned By</th>
                                    <th>Status</th>
                                    <?php if ($is_owner ?? false): ?>
                                    <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vehicle['plate_history'] as $plate): ?>
                                <tr class="<?= $plate['is_current'] ? 'table-active' : ''; ?>">
                                    <td>
                                        <strong class="<?= $plate['is_current'] ? 'text-primary' : ''; ?>">
                                            <?= e($plate['plate_number']); ?>
                                        </strong>
                                        <?php if ($plate['is_current']): ?>
                                        <span class="badge bg-success ms-1">Current</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($plate['assigned_date'])); ?></td>
                                    <td>
                                        <?= e($plate['assigned_by_name'] ?? 'System'); ?>
                                        <?php if (isset($plate['assigned_by_role'])): ?>
                                        <br><small class="text-muted">(<?= ucfirst($plate['assigned_by_role']); ?>)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $plate['is_active'] ? 'success' : 'secondary'; ?>">
                                            <?= $plate['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <?php if ($is_owner ?? false): ?>
                                    <td>
                                        <?php if (!$plate['is_current'] && $plate['is_active']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="setAsCurrentPlate(<?= $plate['id']; ?>)">
                                            Set as Current
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-123 display-1 text-muted"></i>
                        <p class="text-muted mt-2">No plate number history available</p>
                        <?php if ($is_owner ?? false): ?>
                        <button type="button" class="btn btn-primary mt-2" onclick="assignNewPlate()">
                            <i class="bi bi-plus-circle me-1"></i> Assign First Plate
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-activity me-2"></i>Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($vehicle['recent_activity']) && !empty($vehicle['recent_activity'])): ?>
                    <div class="timeline">
                        <?php foreach ($vehicle['recent_activity'] as $activity): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-<?= getActivityColor($activity['action']); ?>"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 text-capitalize">
                                            <?= str_replace('_', ' ', $activity['action']); ?>
                                        </h6>
                                        <p class="mb-1 small text-muted">
                                            By: <?= e($activity['user_name'] ?? 'System'); ?>
                                            <?php if (isset($activity['user_role'])): ?>
                                            (<?= ucfirst($activity['user_role']); ?>)
                                            <?php endif; ?>
                                        </p>
                                        <?php if ($activity['description']): ?>
                                        <p class="mb-0 small"><?= e($activity['description']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted"><?= date('M j, g:i A', strtotime($activity['created_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-activity display-1 text-muted"></i>
                        <p class="text-muted mt-2">No recent activity found</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Transfer Vehicle Ownership</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="transferForm" method="POST" action="/vehicles/<?= $vehicle['id']; ?>/transfer">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        You are about to transfer ownership of your vehicle to another user.
                    </div>
                    
                    <div class="mb-3">
                        <label for="recipient_identifier" class="form-label">Recipient (Email, Phone, or NIN)</label>
                        <input type="text" class="form-control" id="recipient_identifier" name="recipient_identifier" 
                               placeholder="Enter recipient's email, phone number, or NIN" required>
                        <div class="form-text">Enter the email address, phone number, or NIN of the recipient</div>
                    </div>
                    
                    <div id="recipientDetails" class="d-none">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Recipient Details</h6>
                                <div id="recipientInfo"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="transfer_notes" class="form-label">Transfer Notes (Optional)</label>
                        <textarea class="form-control" id="transfer_notes" name="transfer_notes" rows="3" 
                                  placeholder="Add any notes about this transfer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="transferBtn" disabled>
                        <i class="bi bi-arrow-left-right me-1"></i> Initiate Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Plate Modal -->
<div class="modal fade" id="assignPlateModal" tabindex="-1" aria-labelledby="assignPlateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignPlateModalLabel">Assign New Plate Number</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignPlateForm" method="POST" action="/vehicles/<?= $vehicle['id']; ?>/plates">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="plate_number" class="form-label">Plate Number</label>
                        <input type="text" class="form-control" id="plate_number" name="plate_number" 
                               placeholder="Enter plate number" required>
                        <div class="form-text">Enter the new plate number for this vehicle</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assignment_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="assignment_notes" name="assignment_notes" rows="2" 
                                  placeholder="Add any notes about this plate assignment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Assign Plate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 3px currentColor;
}

.timeline-content {
    padding-bottom: 20px;
    border-left: 2px solid #e9ecef;
    padding-left: 20px;
}

.timeline-item:last-child .timeline-content {
    border-left: 2px solid transparent;
}

.image-item .btn {
    opacity: 0;
    transition: opacity 0.2s;
}

.image-item:hover .btn {
    opacity: 1;
}

@media print {
    .btn, .breadcrumb, .card-header .btn {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        break-inside: avoid;
    }
    
    .badge {
        border: 1px solid #000 !important;
        color: #000 !important;
        background: white !important;
    }
}
</style>
<?php $styles = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeVehicleDetails();
});

function initializeVehicleDetails() {
    // Initialize lightbox for images
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'imageFadeDuration': 300
        });
    }
    
    // Transfer form handling
    const transferForm = document.getElementById('transferForm');
    if (transferForm) {
        transferForm.addEventListener('submit', handleTransfer);
    }
    
    // Recipient search
    const recipientInput = document.getElementById('recipient_identifier');
    if (recipientInput) {
        recipientInput.addEventListener('input', debounce(searchRecipient, 500));
    }
    
    // Plate assignment form
    const plateForm = document.getElementById('assignPlateForm');
    if (plateForm) {
        plateForm.addEventListener('submit', handlePlateAssignment);
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function initiateTransfer() {
    const modal = new bootstrap.Modal(document.getElementById('transferModal'));
    modal.show();
}

function searchRecipient() {
    const identifier = document.getElementById('recipient_identifier').value.trim();
    const recipientDetails = document.getElementById('recipientDetails');
    const recipientInfo = document.getElementById('recipientInfo');
    const transferBtn = document.getElementById('transferBtn');
    
    if (identifier.length < 3) {
        recipientDetails.classList.add('d-none');
        transferBtn.disabled = true;
        return;
    }
    
    // Show loading
    recipientInfo.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Searching...</div>';
    recipientDetails.classList.remove('d-none');
    
    fetch(`/users/search?q=${encodeURIComponent(identifier)}`)
        .then(response => response.json())
        .then(users => {
            if (users.length > 0) {
                const user = users[0];
                recipientInfo.innerHTML = `
                    <div class="row">
                        <div class="col-12">
                            <strong>Name:</strong> ${user.name}<br>
                            <strong>Email:</strong> ${user.email}<br>
                            <strong>Phone:</strong> ${user.phone || 'N/A'}<br>
                            <strong>Role:</strong> <span class="badge bg-info">${user.role}</span>
                        </div>
                    </div>
                `;
                transferBtn.disabled = false;
            } else {
                recipientInfo.innerHTML = `
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        No user found with that identifier
                    </div>
                `;
                transferBtn.disabled = true;
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            recipientInfo.innerHTML = `
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error searching for user
                </div>
            `;
            transferBtn.disabled = true;
        });
}

function handleTransfer(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const transferBtn = document.getElementById('transferBtn');
    
    // Disable button and show loading
    transferBtn.disabled = true;
    transferBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Transferring...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            App.showToast('Transfer initiated successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('transferModal')).hide();
            
            // Reload page after delay
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            throw new Error(data.error || 'Transfer failed');
        }
    })
    .catch(error => {
        console.error('Transfer error:', error);
        App.showToast(error.message, 'error');
        
        // Re-enable button
        transferBtn.disabled = false;
        transferBtn.innerHTML = '<i class="bi bi-arrow-left-right me-1"></i> Initiate Transfer';
    });
}

function assignNewPlate() {
    const modal = new bootstrap.Modal(document.getElementById('assignPlateModal'));
    modal.show();
}

function handlePlateAssignment(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            App.showToast('Plate number assigned successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('assignPlateModal')).hide();
            window.location.reload();
        } else {
            throw new Error(data.error || 'Plate assignment failed');
        }
    })
    .catch(error => {
        console.error('Plate assignment error:', error);
        App.showToast(error.message, 'error');
    });
}

function manageImages() {
    App.showToast('Image management feature coming soon', 'info');
}

function manageDocuments() {
    App.showToast('Document management feature coming soon', 'info');
}

function deleteImage(imageId) {
    if (confirm('Are you sure you want to delete this image?')) {
        fetch(`/vehicles/<?= $vehicle['id']; ?>/images/${imageId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                App.showToast('Image deleted successfully', 'success');
                document.querySelector(`.image-item[data-image-id="${imageId}"]`).remove();
            } else {
                throw new Error(data.error || 'Delete failed');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            App.showToast(error.message, 'error');
        });
    }
}

function deleteDocument(documentId) {
    if (confirm('Are you sure you want to delete this document?')) {
        fetch(`/vehicles/<?= $vehicle['id']; ?>/documents/${documentId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                App.showToast('Document deleted successfully', 'success');
                document.querySelector(`.document-item[data-document-id="${documentId}"]`).remove();
            } else {
                throw new Error(data.error || 'Delete failed');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            App.showToast(error.message, 'error');
        });
    }
}

function setAsCurrentPlate(plateId) {
    if (confirm('Set this plate number as current?')) {
        fetch(`/vehicles/<?= $vehicle['id']; ?>/plates/${plateId}/set-current`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                App.showToast('Plate number set as current', 'success');
                window.location.reload();
            } else {
                throw new Error(data.error || 'Operation failed');
            }
        })
        .catch(error => {
            console.error('Plate update error:', error);
            App.showToast(error.message, 'error');
        });
    }
}

function viewPlateHistory() {
    // Scroll to plate history section
    document.querySelector('#plateHistory').scrollIntoView({ behavior: 'smooth' });
}

function viewOwnershipHistory() {
    App.showToast('Ownership history feature coming soon', 'info');
}

function generateReport() {
    App.showToast('Report generation feature coming soon', 'info');
}
</script>

<?php
// Helper function for activity colors
function getActivityColor($action) {
    $colors = [
        'register' => 'primary',
        'update' => 'info',
        'transfer' => 'warning',
        'status_change' => 'danger',
        'plate_assignment' => 'success',
        'document_upload' => 'secondary'
    ];
    return $colors[$action] ?? 'secondary';
}
?>

<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>