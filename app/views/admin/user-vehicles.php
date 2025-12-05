<?php
$title = "User Vehicles - " . ($user['name'] ?? 'Unknown User');
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/users">User Management</a></li>
            <li class="breadcrumb-item"><a href="/admin/users/<?php echo $user['id']; ?>">User Profile</a></li>
            <li class="breadcrumb-item active" aria-current="page">User Vehicles</li>
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
                                <i class="bi bi-truck me-2"></i>User Vehicles
                            </h1>
                            <p class="text-muted mb-0">
                                All vehicles owned by <?php echo e($user['name']); ?> (<?php echo e($user['email']); ?>)
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge bg-primary fs-6">
                                <i class="bi bi-car-front me-1"></i>
                                <?php echo count($vehicles); ?> Vehicle(s)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="user-avatar-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="bi bi-person fs-1"></i>
                            </div>
                            <h5><?php echo e($user['name']); ?></h5>
                            <p class="text-muted mb-0"><?php echo e($user['email']); ?></p>
                            <span class="badge bg-info mt-1"><?php echo ucfirst($user['role']); ?></span>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-primary"><?php echo $stats['total_vehicles'] ?? 0; ?></div>
                                            <div class="text-muted">Total Vehicles</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-success"><?php echo $stats['currently_owned'] ?? 0; ?></div>
                                            <div class="text-muted">Currently Owned</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-info"><?php echo $stats['sold_transferred'] ?? 0; ?></div>
                                            <div class="text-muted">Sold/Transferred</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex">
                                        <a href="/admin/users/<?php echo $user['id']; ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-left me-1"></i> Back to User
                                        </a>
                                        <button type="button" class="btn btn-outline-success" onclick="exportUserVehicles()">
                                            <i class="bi bi-download me-1"></i> Export List
                                        </button>
                                        <a href="/admin/vehicles/add?user_id=<?php echo $user['id']; ?>" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Add Vehicle
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicles Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filter Vehicles</h5>
                </div>
                <div class="card-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Ownership Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="all">All Vehicles</option>
                                        <option value="current">Currently Owned</option>
                                        <option value="sold">Sold/Transferred</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="vehicle_status" class="form-label">Vehicle Status</label>
                                    <select class="form-select" id="vehicle_status" name="vehicle_status">
                                        <option value="all">All Status</option>
                                        <option value="none">Normal</option>
                                        <option value="stolen">Stolen</option>
                                        <option value="no_customs_duty">No Customs</option>
                                        <option value="changed_engine">Changed Engine</option>
                                        <option value="changed_color">Changed Color</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           placeholder="VIN, Plate, Make, Model...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-filter me-1"></i> Apply Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicles List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Vehicle List</h5>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <select class="form-select form-select-sm" id="perPage" onchange="changePerPage(this.value)">
                                <option value="10">10 per page</option>
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                                <option value="100">100 per page</option>
                            </select>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshList()">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($vehicles)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-truck display-1 text-muted"></i>
                        <h5 class="text-muted mt-3">No Vehicles Found</h5>
                        <p class="text-muted">This user doesn't own any vehicles yet.</p>
                        <a href="/admin/vehicles/add?user_id=<?php echo $user['id']; ?>" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle me-1"></i> Add First Vehicle
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>VIN</th>
                                    <th>Vehicle</th>
                                    <th>Plate</th>
                                    <th>Status</th>
                                    <th>Ownership</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vehicles as $vehicle): ?>

                                    <?php $vehicle = (array)$vehicle  ?>
                                <?php
                                $statusBadges = [
                                    'none' => ['class' => 'bg-success', 'label' => 'Normal'],
                                    'stolen' => ['class' => 'bg-danger', 'label' => 'Stolen'],
                                    'no_customs_duty' => ['class' => 'bg-warning', 'label' => 'No Customs'],
                                    'changed_engine' => ['class' => 'bg-info', 'label' => 'Changed Engine'],
                                    'changed_color' => ['class' => 'bg-secondary', 'label' => 'Changed Color']
                                ];
                                $statusInfo = $statusBadges[$vehicle['current_status']] ?? ['class' => 'bg-secondary', 'label' => ucfirst($vehicle['current_status'])];
                                
                                $ownershipBadges = [
                                    'current' => ['class' => 'bg-success', 'label' => 'Current'],
                                    'previous' => ['class' => 'bg-secondary', 'label' => 'Previous']
                                ];
                                $ownershipInfo = $ownershipBadges[$vehicle['ownership_status']] ?? ['class' => 'bg-secondary', 'label' => 'Unknown'];
                                ?>
                                <tr>
                                    <td>
                                        <code><?php echo e($vehicle['vin']); ?></code>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo e($vehicle['make']); ?> <?php echo e($vehicle['model']); ?></div>
                                        <small class="text-muted"><?php echo e($vehicle['year']); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($vehicle['current_plate_number']): ?>
                                        <span class="badge bg-dark"><?php echo e($vehicle['current_plate_number']); ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $statusInfo['class']; ?>">
                                            <?php echo $statusInfo['label']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $ownershipInfo['class']; ?>">
                                            <?php echo $ownershipInfo['label']; ?>
                                        </span>
                                        <?php if ($vehicle['ownership_status'] === 'previous'): ?>
                                        <br>
                                        <small class="text-muted">
                                            to <?php echo e($vehicle['new_owner_name'] ?? 'Unknown'); ?>
                                        </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo date('M j, Y', strtotime($vehicle['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/search/vehicle/<?php echo $vehicle['id']; ?>" 
                                               class="btn btn-outline-primary" target="_blank" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/admin/vehicles/<?php echo $vehicle['id']; ?>/edit" 
                                               class="btn btn-outline-info" title="Edit Vehicle">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if ($vehicle['ownership_status'] === 'current'): ?>
                                            <button type="button" class="btn btn-outline-warning" 
                                                    onclick="transferVehicle(<?php echo $vehicle['id']; ?>)" 
                                                    title="Transfer Vehicle">
                                                <i class="bi bi-arrow-left-right"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmRemove(<?php echo $vehicle['id']; ?>)" 
                                                    title="Remove from User">
                                                <i class="bi bi-person-dash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                    <nav aria-label="Vehicle pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $pagination['current_page'] == 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo buildPageUrl($pagination['current_page'] - 1); ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <?php if ($i == 1 || $i == $pagination['total_pages'] || abs($i - $pagination['current_page']) <= 2): ?>
                                    <li class="page-item <?php echo $pagination['current_page'] == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo buildPageUrl($i); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php elseif (abs($i - $pagination['current_page']) == 3): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $pagination['current_page'] == $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo buildPageUrl($pagination['current_page'] + 1); ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Statistics -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Vehicle Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="display-5 text-primary"><?php echo $stats_by_make['total'] ?? 0; ?></div>
                                    <div class="text-muted">Different Makes</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="display-5 text-success"><?php echo $stats_by_year['oldest'] ?? 'N/A'; ?></div>
                                    <div class="text-muted">Oldest Vehicle</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="display-5 text-info"><?php echo $stats_by_year['newest'] ?? 'N/A'; ?></div>
                                    <div class="text-muted">Newest Vehicle</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="display-5 text-warning"><?php echo $stats_by_status['stolen'] ?? 0; ?></div>
                                    <div class="text-muted">Stolen Vehicles</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($stats_by_make['details'])): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Vehicles by Make</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Make</th>
                                            <th>Count</th>
                                            <th>Percentage</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stats_by_make['details'] as $make): ?>
                                        <tr>
                                            <td><?php echo e($make['make']); ?></td>
                                            <td><?php echo $make['count']; ?></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: <?php echo $make['percentage']; ?>%;">
                                                        <?php echo $make['percentage']; ?>%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($make['has_stolen']): ?>
                                                <span class="badge bg-danger">Has Stolen</span>
                                                <?php else: ?>
                                                <span class="badge bg-success">Clean</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remove Confirmation Modal -->
<div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeModalLabel">Remove Vehicle from User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning: This action requires careful consideration</strong>
                </div>
                <p>You are about to remove this vehicle from <?php echo e($user['name']); ?>'s ownership.</p>
                <div class="mb-3">
                    <label for="remove_reason" class="form-label">Reason for Removal</label>
                    <select class="form-select" id="remove_reason" required>
                        <option value="">Select a reason</option>
                        <option value="error">Registration Error</option>
                        <option value="fraud">Suspected Fraud</option>
                        <option value="duplicate">Duplicate Entry</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="remove_notes" class="form-label">Additional Notes</label>
                    <textarea class="form-control" id="remove_notes" rows="3" 
                              placeholder="Provide details about why this vehicle is being removed..."></textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirm_remove" required>
                    <label class="form-check-label" for="confirm_remove">
                        I confirm that this vehicle should be removed from this user's ownership
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRemoveBtn" disabled>
                    <i class="bi bi-person-dash me-1"></i> Remove Vehicle
                </button>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<style>
.user-avatar-lg {
    width: 100px;
    height: 100px;
}

.progress {
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    background-color: #0d6efd;
    border-radius: 4px;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
<?php $styles = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeUserVehicles();
});

function initializeUserVehicles() {
    // Filter form submission
    const filterForm = document.getElementById('filterForm');
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        applyFilters();
    });

    // Remove confirmation handlers
    const removeReason = document.getElementById('remove_reason');
    const confirmRemove = document.getElementById('confirm_remove');
    const confirmRemoveBtn = document.getElementById('confirmRemoveBtn');

    if (removeReason && confirmRemove && confirmRemoveBtn) {
        function validateRemoveForm() {
            const isValid = removeReason.value !== '' && confirmRemove.checked;
            confirmRemoveBtn.disabled = !isValid;
        }

        removeReason.addEventListener('change', validateRemoveForm);
        confirmRemove.addEventListener('change', validateRemoveForm);
    }

    // Set up remove confirmation button
    if (confirmRemoveBtn) {
        confirmRemoveBtn.addEventListener('click', removeVehicleFromUser);
    }
}

function applyFilters() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    for (const [key, value] of formData.entries()) {
        if (value && value !== 'all') {
            params.append(key, value);
        }
    }
    
    // Reload page with filters
    window.location.href = `/admin/users/<?php echo $user['id']; ?>/vehicles?${params.toString()}`;
}

function changePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.set('page', 1); // Reset to first page
    window.location.href = url.toString();
}

function refreshList() {
    window.location.reload();
}

function exportUserVehicles() {
    App.showToast('Preparing export...', 'info');
    
    const url = new URL(window.location.href);
    url.pathname = `/admin/users/<?php echo $user['id']; ?>/vehicles/export`;
    
    window.location.href = url.toString();
}

function transferVehicle(vehicleId) {
    App.showToast('Redirecting to transfer...', 'info');
    window.open(`/admin/vehicles/${vehicleId}/transfer`, '_blank');
}

let currentVehicleId = null;

function confirmRemove(vehicleId) {
    currentVehicleId = vehicleId;
    const modal = new bootstrap.Modal(document.getElementById('removeModal'));
    
    // Reset form
    document.getElementById('remove_reason').value = '';
    document.getElementById('remove_notes').value = '';
    document.getElementById('confirm_remove').checked = false;
    document.getElementById('confirmRemoveBtn').disabled = true;
    
    modal.show();
}

function removeVehicleFromUser() {
    const reason = document.getElementById('remove_reason').value;
    const notes = document.getElementById('remove_notes').value;
    const confirmBtn = document.getElementById('confirmRemoveBtn');
    
    if (!reason || !currentVehicleId) {
        App.showToast('Please provide a reason for removal', 'error');
        return;
    }
    
    // Disable button and show loading
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Removing...';
    
    fetch(`/admin/users/<?php echo $user['id']; ?>/vehicles/${currentVehicleId}/remove`, {
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
            App.showToast('Vehicle removed successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('removeModal')).hide();
            
            // Reload page after delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.error || 'Failed to remove vehicle');
        }
    })
    .catch(error => {
        console.error('Remove error:', error);
        App.showToast(error.message, 'error');
        
        // Re-enable button
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = '<i class="bi bi-person-dash me-1"></i> Remove Vehicle';
    });
}

// Quick actions
function viewVehicle(vehicleId) {
    window.open(`/search/vehicle/${vehicleId}`, '_blank');
}

function editVehicle(vehicleId) {
    window.location.href = `/admin/vehicles/${vehicleId}/edit`;
}
</script>

<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>