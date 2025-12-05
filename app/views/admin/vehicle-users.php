<?php
$title = "Vehicle Users - " . ($vehicle['make'] ?? '') . " " . ($vehicle['model'] ?? '');
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/vehicles">Vehicle Management</a></li>
            <li class="breadcrumb-item"><a href="/admin/vehicles/<?= $vehicle['id']; ?>">Vehicle Profile</a></li>
            <li class="breadcrumb-item active" aria-current="page">Vehicle Users</li>
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
                                <i class="bi bi-people me-2"></i>Vehicle Users
                            </h1>
                            <p class="text-muted mb-0">
                                All users who have owned <?= e($vehicle['make']); ?> <?= e($vehicle['model']); ?> (<?= e($vehicle['year']); ?>)
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge bg-primary fs-6">
                                <i class="bi bi-person me-1"></i>
                                <?= count($users); ?> User(s)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="vehicle-icon-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="bi bi-truck fs-1"></i>
                            </div>
                            <h5><?= e($vehicle['make']); ?> <?= e($vehicle['model']); ?></h5>
                            <p class="text-muted mb-0"><?= e($vehicle['year']); ?></p>
                            <code class="text-muted mt-1"><?= e($vehicle['vin']); ?></code>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-primary"><?= $stats['total_owners'] ?? 0; ?></div>
                                            <div class="text-muted">Total Owners</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-success"><?= $stats['previous_owners'] ?></div>
                                            <div class="text-muted">Previous Owner</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-info"><?= $stats['transfers'] ?? 0; ?></div>
                                            <div class="text-muted">Transfers Made</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex">
                                        <a href="<?= url('admin/vehicles') ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-left me-1"></i> Back to Vehicle
                                        </a>
                                        <a href="<?= url('admin/manage-vehicle/'.$vehicle['vin']) ?>" class="btn btn-outline-danger">
                                            <i class="bi bi-pen me-1"></i> Update Vehicle
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

    <!-- Current Owner -->
    <?php if ($current_owner): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-check me-2"></i>Current Owner
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="user-avatar-md bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center">
                                <i class="bi bi-person fs-4"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-1"><?= e($current_owner['name']); ?></h5>
                            <p class="text-muted mb-1">
                                <i class="bi bi-envelope me-1"></i><?= e($current_owner['email']); ?><br>
                                <i class="bi bi-phone me-1"></i><?= e($current_owner['phone'] ?? 'N/A'); ?>
                            </p>
                            <div class="mt-2">
                                <span class="badge bg-info"><?= ucfirst($current_owner['role']); ?></span>
                                <span class="badge bg-dark"><strong>Since :</strong>  <?= !empty($current_owner['ot.created_at']) ? date('M j, Y', strtotime($current_owner['ot.created_at'])) : "Not sold yet"; ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid gap-2">
                                <a href="<?= url('admin/manage-user/'.$current_owner['email']) ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-person me-1"></i> View Profile
                                </a>
                                <button type="button" class="btn btn-outline-warning" onclick="contactOwner('<?= e($current_owner['email']); ?>')">
                                    <i class="bi bi-envelope me-1"></i> Contact
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Previous Owners -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>Previous Owners
                    </h5>
                    <span class="badge bg-secondary">
                        <?= count($previous_owners); ?> Previous Owner(s)
                    </span>
                </div>
                <div class="card-body">
                    <?php if (empty($previous_owners)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-person-x display-1 text-muted"></i>
                        <h5 class="text-muted mt-3">No Previous Owners</h5>
                        <p class="text-muted">This vehicle has only had one owner.</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Owner</th>
                                    <th>Contact</th>
                                    <th>Ownership start</th>
                                    <th>Ownership end</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($previous_owners as $owner): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <div>   
                                                <a href="<?= url('admin/manage-user/'.$owner['email']) ?>">
                                                    <strong><?= ucwords(e($owner['name'])); ?></strong><br>
                                                    <small class="text-muted"><?= ucfirst($owner['role']); ?></small>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>
                                            <div><i class="bi bi-envelope me-1"></i><?= e($owner['email']); ?></div>
                                            <?php if ($owner['phone']): ?>
                                            <div><i class="bi bi-phone me-1"></i><?= e($owner['phone']); ?></div>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?= date('M j, Y', strtotime($owner['created_at'])); ?><br>
                                    </td>
                                    <td>
                                        <?= date('M j, Y', strtotime($owner['created_at'])); ?><br>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination for Previous Owners -->
                    <?php if ($previous_owners_pagination['total_pages'] > 1): ?>
                    <nav aria-label="Previous owners pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= $previous_owners_pagination['current_page'] == 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= buildPreviousOwnersPageUrl($previous_owners_pagination['current_page'] - 1); ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $previous_owners_pagination['total_pages']; $i++): ?>
                                <?php if ($i == 1 || $i == $previous_owners_pagination['total_pages'] || abs($i - $previous_owners_pagination['current_page']) <= 2): ?>
                                    <li class="page-item <?= $previous_owners_pagination['current_page'] == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?= buildPreviousOwnersPageUrl($i); ?>"><?= $i; ?></a>
                                    </li>
                                <?php elseif (abs($i - $previous_owners_pagination['current_page']) == 3): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= $previous_owners_pagination['current_page'] == $previous_owners_pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= buildPreviousOwnersPageUrl($previous_owners_pagination['current_page'] + 1); ?>">
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
.vehicle-icon-lg {
    width: 100px;
    height: 100px;
}

.user-avatar-md {
    width: 80px;
    height: 80px;
}

.user-avatar-sm {
    width: 36px;
    height: 36px;
    font-size: 1rem;
}

.card.border-success {
    border-width: 2px;
}

.progress {
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    background-color: #0d6efd;
    border-radius: 4px;
    color: white;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
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
    initializeVehicleUsers();
});

function initializeVehicleUsers() {
    // Search functionality for add owner modal
    const searchInput = document.getElementById('new_owner_search');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(searchNewOwner, 500));
    }

    // Validate add owner form
    const confirmBtn = document.getElementById('confirmAddOwnerBtn');
    if (confirmBtn) {
        // Validation will be handled when owner is selected
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


function contactOwner(email) {
    App.showToast(`Opening email to ${email}`, 'info');
    window.location.href = `mailto:${email}`;
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
</script>

<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>