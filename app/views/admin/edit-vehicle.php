<?php
$title = "Edit Vehicle - " . ($vehicle['make'] ?? '') . " " . ($vehicle['model'] ?? '');
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/vehicles">Vehicle Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Vehicle</li>
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
                                <i class="bi bi-truck me-2"></i>Edit Vehicle
                            </h1>
                            <p class="text-muted mb-0">
                                Update vehicle information and status
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php
                            $statusBadges = [
                                'none' => ['class' => 'bg-success', 'label' => 'Normal'],
                                'stolen' => ['class' => 'bg-danger', 'label' => 'Stolen'],
                                'no_customs_duty' => ['class' => 'bg-warning', 'label' => 'No Customs'],
                                'changed_engine' => ['class' => 'bg-info', 'label' => 'Changed Engine'],
                                'changed_color' => ['class' => 'bg-secondary', 'label' => 'Changed Color']
                            ];
                            $currentStatus = $vehicle['current_status'] ?? 'none';
                            $statusInfo = $statusBadges[$currentStatus] ?? ['class' => 'bg-secondary', 'label' => ucfirst($currentStatus)];
                            ?>
                            <span class="badge <?= $statusInfo['class']; ?> fs-6">
                                <?= $statusInfo['label']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Edit Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Vehicle Information
                    </h5>
                </div>
                <div class="card-body">
                    <form id="editVehicleForm" method="POST" action="<?= url('api/admin/update/vehicle') ?>">
                        <input id="id" value="<?= e($vehicle['id']); ?>" hidden>
                        <!-- Vehicle Identification -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Vehicle Identification</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vin" class="form-label">
                                        <strong>VIN Number</strong>
                                    </label>
                                    <input type="text" class="form-control" id="vin" name="vin" 
                                           value="<?= e($vehicle['vin'] ?? ''); ?>" required>
                                    <div class="form-text text-warning">
                                        <i class="bi bi-lock me-1"></i>VIN cannot be changed
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="current_plate_number" class="form-label">
                                        <strong>Current Plate Number</strong>
                                    </label>
                                    <input type="text" class="form-control" id="current_plate_number" 
                                           name="current_plate_number" 
                                           value="<?= e($vehicle['current_plate_number'] ?? ''); ?>">
                                    <div class="form-text">Current active plate number</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Registration Date</strong>
                                    </label>
                                    <div class="form-control bg-dark">
                                        <?= date('M j, Y g:i A', strtotime($vehicle['created_at'])); ?>
                                    </div>
                                    <div class="form-text">Date when vehicle was first registered</div>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Vehicle Details</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="make" class="form-label">
                                        <strong>Make</strong>
                                    </label>
                                    <select class="form-select" id="vehicle_make" name="vehicle_make" required>
                                        <option value="">Select Make</option>
<?php foreach ($vehicle_makes as $make): ?>
                                        <option value="<?= $make['make']; ?>" <?= (isset($vehicle['make']) && $vehicle['make'] === $make['make']) ? 'selected' : ''; ?>><?= e($make['make']); ?></option>
<?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="model" class="form-label">
                                        <strong>Model</strong>
                                    </label>
                                     <select class="form-select" id="vehicle_model" name="vehicle_model" required>
                                        <option value="">Select Model</option>
<?php foreach ($vehicle_models as $model): ?>
                                        <option value="<?= $model['id']; ?>"<?= (isset($vehicle['model']) && $vehicle['model'] === $model['model']) ? 'selected' : ''; ?>><?= e($model['model']); ?></option>
<?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="year" class="form-label">
                                        <strong>Year</strong>
                                    </label>
                                    <input type="number" class="form-control" id="year" name="year" value="<?= e($vehicle['year'] ?? ''); ?>" 
                                           min="1900" max="<?= date('Y'); ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Update Vehicle Status</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="current_status" class="form-label">
                                        <strong>Vehicle Status</strong>
                                    </label>
                                    <select class="form-select" id="current_status" name="current_status">
                                        <option selected>Change Status</option>
                                        <option value="none">Normal</option>
                                        <option value="stolen">Stolen</option>
                                        <option value="no_customs_duty">No Customs Duty</option>
                                        <option value="changed_engine">Changed Engine</option>
                                        <option value="changed_color">Changed Color</option>
                                    </select>
                                    <div class="form-text">Update the vehicle's current status</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status_reason" class="form-label">
                                        <strong>Status Reason</strong>
                                    </label>
                                    <textarea class="form-control" id="status_reason" name="status_reason" 
                                              rows="3" placeholder="Enter reason for status change..."><?= e($vehicle['status_reason'] ?? ''); ?></textarea>
                                    <div class="form-text">Provide details about the status change</div>
                                </div>
                            </div>
                        </div>

                        <!-- Ownership Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Ownership Information</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Owner Full Name</strong>
                                    </label>
                                    <div class="form-control bg-dark">
                                        <?= ucwords($user['name']); ?>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Owner NIN</strong>
                                    </label>
                                    <div class="form-control bg-dark">
                                        <?= $user['nin']; ?>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Owner Phone Number</strong>
                                    </label>
                                    <div class="form-control bg-dark">
                                        <?= $user['phone']; ?>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Owner Email</strong>
                                    </label>
                                    <div class="form-control bg-dark" id="current_owner_email">
                                        <?= $user['email']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <!-- Transfer Ownership -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Transfer Ownership</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Select Field</strong>
                                    </label>
                                    <select class="form-control bg-dark" id="new_owner_field" name="new_owner_field">
                                        <option selected>Select Field</option>
                                        <option value="nin">NIN</option>
                                        <option value="email">Email</option>
                                        <option value="phone">Phone Number</option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Value</strong>
                                    </label>
                                    <input class="form-control bg-dark" name="new_owner_value" id="new_owner_value">
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>New Owner Name</strong>
                                    </label>
                                    <div class="form-control bg-dark" id="new_owner_name">
                                        Full Name
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>New Owner NIN</strong>
                                    </label>
                                    <div class="form-control bg-dark" id="new_owner_nin">
                                        NIN
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>New Owner Phone Number</strong>
                                    </label>
                                    <div class="form-control bg-dark" id="new_owner_phone">
                                        Phone Number
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>New Owner Email</strong>
                                    </label>
                                    <div class="form-control bg-dark" id="new_owner_email">
                                        Email
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="<?= url('admin/vehicles') ?>" class="btn btn-secondary me-md-2">
                                        <i class="bi bi-arrow-left me-1"></i> Back to Vehicles
                                    </a>
                                    <button type="button" class="btn btn-outline-danger me-md-2" 
                                            onclick="showDeleteConfirmation()">
                                        <i class="bi bi-trash me-1"></i> Delete Vehicle
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="bi bi-check-circle me-1"></i> Update Vehicle
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Vehicle Summary & Actions -->
        <div class="col-lg-4">
            <!-- Vehicle Summary -->
            <div class="card mb-4">
                <div class="card-header bg-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Vehicle Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="vehicle-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h5 class="mt-3 mb-1"><?= e($vehicle['make']); ?> <?= e($vehicle['model']); ?></h5>
                        <p class="text-muted mb-2"><?= e($vehicle['year']); ?></p>
                        <code class="bg-dark text-white px-2 py-1 rounded"><?= e($vehicle['vin']); ?></code>
                    </div>

                    <div class="list-group list-group-flush bg-dark">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Current Owner</span>
                            <span class="text-end">
                                <strong><?= e($owner['name'] ?? 'Unknown'); ?></strong><br>
                                <small class="text-muted"><?= e($owner['email'] ?? 'N/A'); ?></small>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Plate Numbers</span>
                            <span class="badge bg-primary rounded-pill">
                                <?= $vehicle_stats['plate_count'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Ownership Transfers</span>
                            <span class="badge bg-info rounded-pill">
                                <?= $vehicle_stats['transfer_count'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Status Changes</span>
                            <span class="badge bg-warning rounded-pill">
                                <?= $vehicle_stats['status_change_count'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Last Updated</span>
                            <small class="text-muted">
                                <?= date('M j, Y', strtotime($vehicle['updated_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header bg-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= url('vehicles/view/'.$vehicle['vin']) ?>" 
                           class="btn btn-outline-primary text-start">
                            <i class="bi bi-eye me-2"></i> View Public Profile
                        </a>
                        <a href="<?= $_ENV['APP_URL'] ?>/admin/vehicle/users/<?= $vehicle['vin']; ?>" 
                           class="btn btn-outline-success text-start">
                           <i class="bi bi-clock-history me-2"></i>Vehicle Users History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Vehicle Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>This action cannot be undone!</strong>
                </div>
                <p>You are about to permanently delete the vehicle:</p>
                <div class="card bg-light">
                    <div class="card-body">
                        <strong><?= e($vehicle['make']); ?> <?= e($vehicle['model']); ?> (<?= e($vehicle['year']); ?>)</strong><br>
                        <small class="text-muted">VIN: <?= e($vehicle['vin']); ?></small>
                    </div>
                </div>
                <p class="mt-3 text-danger">
                    <strong>Warning:</strong> This will delete all vehicle data including:
                </p>
                <ul class="text-danger small">
                    <li>Vehicle information and details</li>
                    <li>Ownership history</li>
                    <li>Plate number history</li>
                    <li>All associated images and documents</li>
                    <li>Transfer records</li>
                </ul>
                <div class="mb-3">
                    <label for="confirmDelete" class="form-label">
                        Type <strong>DELETE VEHICLE</strong> to confirm:
                    </label>
                    <input type="text" class="form-control" id="confirmDelete" 
                           placeholder="Type DELETE VEHICLE here">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                    <i class="bi bi-trash me-1"></i> Delete Vehicle
                </button>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<style>
.vehicle-icon {
    background: linear-gradient(135deg, #0d6efd, #0dcaf0);
}

.timeline-small {
    position: relative;
    padding-left: 20px;
}

.timeline-item-small {
    position: relative;
    margin-bottom: 10px;
}

.timeline-marker-small {
    position: absolute;
    left: -20px;
    top: 5px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content-small {
    padding-left: 10px;
}

.timeline-item-small:not(:last-child) .timeline-content-small {
    border-left: 2px solid #e9ecef;
    padding-bottom: 10px;
    margin-left: -12px;
    padding-left: 22px;
}

.card .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}
</style>
<?php $styles = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
    $(() => {
        $('#vehicle_make').on('change', function() {
            const selectedMake = $(this).val();
            if (selectedMake) {
                $.ajax({
                    url: appUrl + `/api/vehicle/get-models`,
                    method: 'POST',
                    data : {make: selectedMake},
                    success: function(models) {
                        const modelSelect = $('#vehicle_model');
                        modelSelect.empty();
                        modelSelect.append('<option value="">Select Model</option>');
                        const parsedModels = JSON.parse(models);
                        parsedModels.forEach(model => {
                            modelSelect.append(`<option value="${model.id}">${model.model}</option>`);
                        });
                    },
                    error: function() {
                        console.error('Failed to fetch vehicle models');
                    }
                });
            } else {
                $('#vehicle_model').empty().append('<option value="">Select Model</option>');
            }
        });


        $('#new_owner_value, #new_owner_field').on('change keyup', function(){
            field = $('#new_owner_field').val();
            value = $('#new_owner_value').val();
            new_name = $('#new_owner_name');
            new_nin = $('#new_owner_nin');
            new_phone = $('#new_owner_phone');
            new_email = $('#new_owner_email');
            if(
                (field === 'email' && App.validateEmail(value)) ||
                (field === 'nin' && App.validateNIN(value)) ||
                (field === 'phone' && App.validatePhone(value))
            ){
                 $.ajax({
                    url: appUrl + `/api/admin/get-user`,
                    method: 'POST',
                    data : {field: field, value: value},
                    success: function(data) {
                        var data = JSON.parse(data);
                        new_name.text(data.name);
                        new_nin.text(data.nin);
                        new_phone.text(data.phone);
                        new_email.text(data.email);
                    },
                    error: function() {
                        console.error('Failed to fetch Driver');
                    }
                });
            }
        });
    });
document.addEventListener('DOMContentLoaded', function() {
    initializeEditVehicleForm();
});

function initializeEditVehicleForm() {
    const form = document.getElementById('editVehicleForm');
    const confirmDeleteInput = document.getElementById('confirmDelete');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    // Status change handler
    document.getElementById('current_status').addEventListener('change', function() {
        const reasonField = document.getElementById('status_reason');
        if (this.value !== 'none') {
            reasonField.placeholder = 'Please provide details for the status change...';
        } else {
            reasonField.placeholder = 'Enter reason for status change...';
        }
    });

    // Delete confirmation handler
    confirmDeleteInput.addEventListener('input', function() {
        confirmDeleteBtn.disabled = this.value !== 'DELETE VEHICLE';
    });

    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitVehicleUpdate();
    });

    // Set up delete confirmation
    confirmDeleteBtn.addEventListener('click', deleteVehicle);
}

function submitVehicleUpdate() {
    const form = document.getElementById('editVehicleForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitBtn');
    var id = $('#id').val();
    var vin = $('#vin').val();
    var plate_number = $('#current_plate_number').val();
    var model_id = $('#vehicle_model').val();
    var year = $('#year').val();
    var status = $('#current_status').val();
    var reason = $('#status_reason').val();
    var new_owner_email = $('#new_owner_email').text();
    var current_owner_email = $('#current_owner_email').text();

    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Updating...';

    $.ajax({
        type: "POST",
        url: form.action,
        data: {
            id: id,
            vin: vin,
            current_plate: plate_number,
            vehicle_model_id: model_id,
            year: year,
            current_status: status,
            status_reason: reason,
            new_owner_email: new_owner_email,
            current_owner_email: current_owner_email

        },
        success: function (response) {
            const data = JSON.parse(response);
            if (data.success) {
                App.showToast('Vehicle updated successfully!', 'success');
                // Reload page to show changes
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                App.showToast(data.error, 'danger');
            }
        },
        error: function(error){
            console.error('Update error:', error);
            App.showToast(error.message, 'error');
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Update Vehicle';
        }
    });

}

function showDeleteConfirmation() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function deleteVehicle() {
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    // Disable button and show loading
    confirmDeleteBtn.disabled = true;
    confirmDeleteBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Deleting...';

    fetch(`/admin/vehicles/<?= $vehicle['id']; ?>/delete`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            App.showToast('Vehicle deleted successfully!', 'success');
            
            // Close modal and redirect
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            setTimeout(() => {
                window.location.href = '/admin/vehicles';
            }, 1500);
        } else {
            throw new Error(data.error || 'Delete failed');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        App.showToast(error.message, 'error');
        
        // Re-enable button
        confirmDeleteBtn.disabled = false;
        confirmDeleteBtn.innerHTML = '<i class="bi bi-trash me-1"></i> Delete Vehicle';
    });
}

function viewVehicleHistory() {
    window.open(`/search/vehicle/<?= $vehicle['id']; ?>`, '_blank');
}

</script>

<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>