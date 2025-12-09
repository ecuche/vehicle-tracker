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
            <li class="breadcrumb-item"><a href="/admin/users/<?= $user['id']; ?>">User Profile</a></li>
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
                                All vehicles owned by <?= e($user['name']); ?> (<?= e($user['email']); ?>)
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge bg-primary fs-6">
                                <i class="bi bi-car-front me-1"></i>
                                <?= count($vehicles); ?> Vehicle(s)
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
                            <h5><?= e($user['name']); ?></h5>
                            <p class="text-muted mb-0"><?= e($user['email']); ?></p>
                            <span class="badge bg-info mt-1"><?= ucfirst($user['role']); ?></span>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-primary"><?= $stats['total_vehicles'] ?? 0; ?></div>
                                            <div class="text-muted">Total Vehicles</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-success"><?= $stats['currently_owned'] ?? 0; ?></div>
                                            <div class="text-muted">Currently Owned</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-info"><?= $stats['sold_transferred'] ?? 0; ?></div>
                                            <div class="text-muted">Sold/Transferred</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex">
                                        <a href="<?= url('admin/users/'.$user['email']); ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-left me-1"></i> Back to User
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
                        <a href="/admin/vehicles/add?user_id=<?= $user['id']; ?>" class="btn btn-primary mt-2">
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
                                    <th>Registered</th>
                                    <th>Ownership</th>
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
                                
                                ?>
                                <tr>
                                    <td>
                                        <a href="<?= url('admin/manage-vehicle/'.$vehicle['vin']) ?>">
                                            <div class="text-light"><?= e($vehicle['vin']); ?></code>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-light"><?= e($vehicle['make']); ?> <?= e($vehicle['model']); ?></div>
                                        <small class="text-muted"><?= e($vehicle['year']); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($vehicle['current_plate']): ?>
                                        <span class="badge bg-dark"><?= e($vehicle['current_plate']); ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $statusInfo['class']; ?>">
                                            <?= $statusInfo['label']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-light">
                                            <?= date('M j, Y', strtotime($vehicle['created_at'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-light">
                                            <?php if ($vehicle['buyer_id'] === $vehicle['user_id']): ?>
                                                <span class="badge bg-success">
                                                    Current
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">
                                                    Sold
                                                </span>
                                            <?php endif; ?>
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
                            <li class="page-item <?= $pagination['current_page'] == 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= buildPageUrl($pagination['current_page'] - 1); ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <?php if ($i == 1 || $i == $pagination['total_pages'] || abs($i - $pagination['current_page']) <= 2): ?>
                                    <li class="page-item <?= $pagination['current_page'] == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?= buildPageUrl($i); ?>"><?= $i; ?></a>
                                    </li>
                                <?php elseif (abs($i - $pagination['current_page']) == 3): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= $pagination['current_page'] == $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= buildPageUrl($pagination['current_page'] + 1); ?>">
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
    window.location.href = `/admin/users/<?= $user['id']; ?>/vehicles?${params.toString()}`;
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
    url.pathname = `/admin/users/<?= $user['id']; ?>/vehicles/export`;
    
    window.location.href = url.toString();
}

let currentVehicleId = null;

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
    
    fetch(`/admin/users/<?= $user['id']; ?>/vehicles/${currentVehicleId}/remove`, {
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