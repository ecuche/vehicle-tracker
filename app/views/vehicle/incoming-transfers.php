<?php
$title = "Incoming Vehicle Transfers";
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('dashboard'); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('vehicles'); ?>">My Vehicles</a></li>
            <li class="breadcrumb-item active" aria-current="page">Incoming Transfers</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-inbox me-2 text-primary"></i></i>Incoming Vehicle Transfers
        </h1>
        <div class="d-flex align-items-center">
            <!-- Search Form -->
            <form method="GET" class="d-flex me-3" onsubmit="return false;">
                <input type="text" 
                       class="form-control form-control-sm" 
                       id="searchInput" 
                       placeholder="Search vehicles..." 
                       value="<?= htmlspecialchars($search ?? ''); ?>">
                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="applySearch()">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            
            <!-- Pagination Selector -->
            <div class="me-3">
                <select class="form-select form-select-sm" id="perPageSelect" onchange="updatePerPage()">
                    <?php foreach ([5, 10, 15, 20, 25] as $option): ?>
                    <option value="<?= $option; ?>" <?= $per_page == $option ? 'selected' : ''; ?>>
                        <?= $option; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Navigation Buttons -->
            <div class="btn-group me-2">
                <a href="<?= url('vehicles/outgoing-transfers'); ?>" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-send me-1"></i>Outgoing
                </a>
                <a href="<?= url('vehicles/completed-transfers'); ?>" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-check-circle-fill me-1"></i> Completed
                </a>
            </div>
            
            <!-- Refresh Button -->
            <a href="<?= url('vehicles/incoming-transfers'); ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-arrow-repeat"></i>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['pending'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Accepted
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['accepted'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['rejected'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-circle-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Transfers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['total'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicles Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-inbox me-2"></i>Vehicles Being Transferred to You
                <span class="badge bg-primary ms-2"><?= $total_items ?? 0; ?> vehicles</span>
            </h6>
            <div class="d-flex">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Select Status
                    </button>
                    <ul class="dropdown-menu bg-secondary text-light">
                        <li><a class="dropdown-item" href="<?= url('vehicles/outgoing-transfers') ?>">Outgoing</a></li>
                        <li><a class="dropdown-item" href="<?= url('vehicles/completed-transfers') ?>">Completed</a></li>
                        <li><a class="dropdown-item" href="<?= url('vehicles/rejected-transfers') ?>">Rejected</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($vehicles)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Vehicle Details</th>
                            <th>Current Owner</th>
                            <th>Transfer Status</th>
                            <th>Date Requested</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <?php if (!empty($vehicle['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($vehicle['image_url']); ?>" 
                                             alt="Vehicle" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 60px;">
                                            <i class="bi bi-car-front fs-5"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong>
                                            <?= htmlspecialchars($vehicle['make']); ?> 
                                            <?= htmlspecialchars($vehicle['model']); ?> 
                                            (<?= htmlspecialchars($vehicle['year']); ?>)
                                        </strong><br>
                                        <small class="text-muted">VIN: <?= htmlspecialchars($vehicle['vin']); ?></small><br>
                                        <small class="text-muted">Plate: <?= htmlspecialchars($vehicle['plate_number']); ?></small><br>
                                        <small class="text-muted">Color: <?= htmlspecialchars($vehicle['color']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong><?= htmlspecialchars($vehicle['name']); ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($vehicle['email']); ?></small><br>
                                    <small class="text-muted"><?= htmlspecialchars($vehicle['phone']); ?></small>
                                </div>
                            </td>
                            <td>
                                <?= getTransferStatusBadge($vehicle['transfer_status']); ?><br>
                                <small class="text-muted">
                                    <?= getStatusDescription($vehicle['transfer_status']); ?>
                                </small>
                                <?php if (!empty($vehicle['transfer_note'])): ?>
                                <br><small class="text-muted">Note: <?= htmlspecialchars($vehicle['transfer_note']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= date('M j, Y', strtotime($vehicle['created_at'])); ?><br>
                                <small class="text-muted">
                                    <?= formatRelativeTime($vehicle['created_at']); ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('search/vehicle-profile/'. $vehicle['vin']); ?>" 
                                       class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="<?= url('vehicles/pending-transfer/'. $vehicle['vin']); ?>" 
                                       class="btn btn-outline-warning">
                                        <i class="bi bi-chevron-double-right" style="font-size: 2rem"></i> 
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Previous Page -->
                    <li class="page-item <?= $current_page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" 
                           href="<?= buildPaginationUrl($current_page - 1); ?>" 
                           aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == 1 || $i == $total_pages || ($i >= $current_page - 2 && $i <= $current_page + 2)): ?>
                        <li class="page-item <?= $i == $current_page ? 'active' : ''; ?>">
                            <a class="page-link" href="<?= buildPaginationUrl($i); ?>">
                                <?= $i; ?>
                            </a>
                        </li>
                        <?php elseif ($i == $current_page - 3 || $i == $current_page + 3): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <!-- Next Page -->
                    <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" 
                           href="<?= buildPaginationUrl($current_page + 1); ?>" 
                           aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
                <div class="text-center mt-2">
                    <small class="text-muted">
                        Showing <?= (($current_page - 1) * $per_page) + 1; ?> to 
                        <?= min($current_page * $per_page, $total_items); ?> of 
                        <?= $total_items; ?> vehicles
                    </small>
                </div>
            </nav>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="text-center py-5">
                <div class="text-muted">
                    <i class="bi bi-truck fs-1 mb-3"></i>
                    <h4>No Incoming Transfers</h4>
                    <p>No vehicles are being transferred to you at the moment.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Activity -->
    <?php if (!empty($recent_activity)): ?>
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-clock-history me-2"></i>Recent Transfer Activity
            </h6>
        </div>
        <div class="card-body">
            <div class="timeline">
                <?php foreach ($recent_activity as $activity): ?>
                <div class="timeline-item">
                    <div class="timeline-marker bg-<?= getActivityColor($activity['action']); ?>"></div>
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1"><?= htmlspecialchars($activity['title']); ?></h6>
                            <small class="text-muted">
                                <?= formatRelativeTime($activity['timestamp']); ?>
                            </small>
                        </div>
                        <p class="mb-1 small"><?= htmlspecialchars($activity['description']); ?></p>
                        <small class="text-muted">
                            Vehicle: <?= htmlspecialchars($activity['vehicle_make']); ?> 
                            <?= htmlspecialchars($activity['vehicle_model']); ?>
                        </small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Transfer Action Modal -->
<div class="modal fade" id="transferActionModal" tabindex="-1" aria-labelledby="transferActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferActionModalLabel">Transfer Vehicle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="vehicleDetails">
                    <!-- Vehicle details will be loaded here -->
                </div>
                <div class="mb-3" id="actionSection">
                    <!-- Action-specific content -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmActionBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>
<script>
let currentTransferId = null;
let currentAction = null;

function applySearch() {
    const search = document.getElementById('searchInput').value;
    const url = new URL(window.location.href);
    
    if (search) {
        url.searchParams.set('search', search);
    } else {
        url.searchParams.delete('search');
    }
    
    // Reset to page 1
    url.searchParams.set('page', '1');
    
    window.location.href = url.toString();
}

function updatePerPage() {
    const perPage = document.getElementById('perPageSelect').value;
    const url = new URL(window.location.href);
    
    url.searchParams.set('per_page', perPage);
    url.searchParams.set('page', '1'); // Reset to first page
    
    window.location.href = url.toString();
}

</script>
<?php $scripts = ob_get_clean();
include 'app/Views/layouts/main.php'; 
?>