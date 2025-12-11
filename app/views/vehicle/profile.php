<?php
$title = "Vehicle Details - " . ($vehicle['make'] ?? 'Unknown') . " " . ($vehicle['model'] ?? '');
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=  url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?=  url('vehicles') ?>">My Vehicles</a></li>
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
                                <?= e($model['make'] ?? 'Unknown'); ?> <?= e($model['model'] ?? ''); ?> (<?= e($vehicle['year'] ?? ''); ?>)
                            </h1>
                            <p class="text-muted mb-0">
                                VIN: <?= e($vehicle['vin'] ?? 'N/A'); ?> | 
                                Current Plate: <?= e($vehicle['current_plate'] ?? 'N/A'); ?>
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
                                <?php if ($vehicle['user_id'] === $user['id']): ?>
                                <a href="<?= url('vehicle/edit/'.$vehicle['vin']) ?>" class="btn btn-outline-secondary border border-secondary btn-sm">
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
                            <span class="fs-5 badge bg-dark"><?= e($vehicle['current_plate'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Make</strong><br>
                            <?= e($model['make'] ?? 'N/A'); ?>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Model</strong><br>
                            <?= e($model['model'] ?? 'N/A'); ?>
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
                    <?php if (!empty($user)): ?>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Current Owner</strong><br>
                            <?= e($user['name']); ?>
                            <?php if ($user['id'] === get_user_id() ?? false): ?>
                            <span class="badge bg-success ms-1">You</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Role</strong><br>
                            <span class="badge bg-info"><?= ucfirst($user['role']); ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Ownership Since</strong><br>
                            <?= e(isset($transfer[0]['created_at']) ? date('M j, Y', strtotime($transfer[0]['created_at'])) : 'N/A'); ?>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong class="text-muted">Ownership Type</strong><br>
                            <span class="badge bg-<?= $transfer[0]['buyer_id'] === $user['id']  ? 'success' : 'primary'; ?>">
                                <?= $transfer[0]['buyer_id'] === $user['id']  ? 'Original Owner' : 'Transferred'; ?>
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
            <?php if ($vehicle['user_id'] === $user['id'] && $vehicle['current_status'] !== 'stolen'): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-arrow-left-right me-2"></i>Transfer Vehicle
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Transfer ownership of this vehicle to another user.</p>
                    <div class="d-grid gap-2">
                        <a class="btn btn-outline-warning border" href="<?= url('vehicles/transfer/'.$vehicle['vin']) ?>">
                            <i class="bi bi-arrow-left-right me-1"></i> Initiate Transfer
                        </a>
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
                    <?php if ($vehicle['user_id'] === $user['id']): ?>
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
                                <?php if ($vehicle['user_id'] === $user['id']): ?>
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
                        <?php if ($vehicle['user_id'] === $user['id']): ?>
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
                    <?php if ($vehicle['user_id'] === $user['id']): ?>
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
                                    <?php if ($vehicle['user_id'] === $user['id']): ?>
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
                        <?php if ($vehicle['user_id'] === $user['id']): ?>
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
                        <a class="btn btn-outline-warning text-start border" href="<?= url('vehicles/view/ownership-history/'.$vehicle['vin']) ?>">
                            <i class="bi bi-diagram-3 me-2"></i> View Ownership History
                        </a>
                        <a class="btn btn-outline-info text-start border" href="<?= url('vehicles/view/status-history/'.$vehicle['vin']) ?>">
                            <i class="bi bi-diagram-3 me-2"></i> View Status History
                        </a>
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
                    <?php if ($vehicle['user_id'] === $user['id']): ?>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="assignNewPlate()">
                        <i class="bi bi-plus-circle me-1"></i> Assign New Plate
                    </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($plates)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Plate Number</th>
                                    <th>Assigned Date</th>
                                    <th>Assigned By</th>
                                    <th>Status</th>
                                    <?php if ($user['id'] === get_user_id()): ?>
                                    <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($plates as $plate): ?>
                                <tr class="<?= $plate['is_current'] ? 'table-active' : ''; ?>">
                                    <td>
                                        <strong class="<?= $plate['is_current'] ? 'text-primary' : ''; ?>">
                                            <?= e($plate['plate_number']); ?>
                                        </strong>
                                        <?php if ($plate['is_current']): ?>
                                        <span class="badge bg-success ms-1">Current</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($plate['assigned_at'])); ?></td>
                                    <td>
                                        <?= e($plate['assigned_by_name'] ?? 'System'); ?>
                                        <?php if (isset($plate['assigned_by_role'])): ?>
                                        <br><small class="text-muted">(<?= ucfirst($plate['assigned_by_role']); ?>)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $plate['is_current'] ? 'success' : 'secondary'; ?>">
                                            <?= $plate['is_current'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <?php if ($user['id'] === get_user_id()): ?>
                                    <td>
                                        <?php if (!$plate['is_current']): ?>
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
                        <?php if ($vehicle['user_id'] === $user['id']): ?>
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
</div>

<!-- Assign Plate Modal -->
<div class="modal fade" id="assignPlateModal" tabindex="-1" aria-labelledby="assignPlateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignPlateModalLabel">Assign New Plate Number</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignPlateForm" method="POST" action="<?= url('api/vehicle/assign-new-plate/'.$vehicle['vin']) ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_plate" class="form-label">Plate Number</label>
                        <input type="text" class="form-control" id="new_plate" name="new_plate" data-validation="required|plate_number"
                               placeholder="Enter plate number" required>
                        <div class="form-text">Enter the new plate number for this vehicle</div>
                    </div>
                    <div class="mb-3">
                        <label for="assign_date" class="form-label">Date Assigned</label>
                        <input type="date" class="form-control" id="assign_date" name="assign_date" 
                               placeholder="Assigned Date" required>
                        <div class="form-text">Enter the Date of new plate number for this vehicle</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_note" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="new_note" name="new_note" rows="2" 
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
    $(() => {
        $('#new_plate').on('keyup', function(){

        })

    });
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
    
    
    // Plate assignment form
    const plateForm = document.getElementById('assignPlateForm');
    if (plateForm) {
        plateForm.addEventListener('submit', handlePlateAssignment);
    }
}

// Real-time validation for all fields
    const plateForm = document.getElementById('assignPlateForm');
    const plateFields = plateForm.querySelectorAll('[data-validation]');
    plateFields.forEach(field => {
        field.addEventListener('blur', function() {
            window.FormValidation.validateField(this);
        });
        
        field.addEventListener('input', function() {
            window.FormValidation.clearFieldValidation(this);
        });
    });

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


function assignNewPlate() {
    const modal = new bootstrap.Modal(document.getElementById('assignPlateModal'));
    modal.show();
}

function handlePlateAssignment(e) {
    e.preventDefault();
    const form = e.target;
    plate = $('#new_plate').val();
    note = $('#new_note').val();
    assign_date = $('#assign_date').val();

    $.ajax({
        method: 'POST',
        url: form.action,
        data: {
            plate: plate,
            note: note,
            assign_date: assign_date
        },
        success: function (response) {
            const data = JSON.parse(response);
            if (data.success) {
                App.showToast('Plate number assigned as current', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                App.showToast(data.error, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Plate update error:', xhr.responseText);
        }
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

        $.ajax({
            type: "POST",
            url: appUrl + "/api/vehicle/change-current-plate",
            data: {
                plate_id: plateId
            },
            success: function (response) {
                const data = JSON.parse(response);
                if (data.success) {
                    App.showToast('Plate number set as current', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.error || 'Operation failed');
                }
            },
            error: function(error){
                console.error('Plate update error:', error);
                App.showToast(error.message, 'error');
            }
        });
       
    }
}

</script>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>