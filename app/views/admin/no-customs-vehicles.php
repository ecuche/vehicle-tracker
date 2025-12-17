<?php
$title = "No Customs Duty Vehicles - Admin";
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="<?= url('admin/dashboard'); ?>">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('admin/vehicles'); ?>">Vehicle Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">No Customs Duty Vehicles</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-shield-exclamation me-2 text-warning"></i>No Customs Duty Vehicles
        </h1>
        <div class="d-flex align-items-center">
            
            <!-- Pagination Selector -->
            <div class="me-3">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= $per_page?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark">
                    <?php foreach ([5, 10, 15, 20, 25] as $option): ?>
                        <li><a href="<?= url("admin/vehicles/no-customs?page={$current_page}&per_page={$option}") ?>" class="dropdown-item <?= $per_page == $option ? 'active' : ''; ?>" href="#"><?= $option; ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Refresh Button -->
            <a href="<?= url('admin/vehicles/no-customs'); ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-arrow-clockwise"></i>
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
                                No Customs Vehicles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['total'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-ban fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Completed Transfers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['sold'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-tags fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Transfers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['pending'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-arrow-left-right fs-2 text-gray-300"></i>
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
                                Rejected Transfers
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

        
    </div>

    <!-- Vehicles Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="bi bi-shield-exclamation me-2"></i>No Customs Duty Vehicles
                <span class="badge bg-warning ms-2"><?= $total_items ?? 0; ?> vehicles</span>
            </h6>
            <div class="d-flex">
                <div class="me-3">
                    <small class="text-muted">
                        Showing <?= (($current_page - 1) * $per_page) + 1; ?>-<?= min($current_page * $per_page, $total_items); ?> of <?= $total_items; ?>
                    </small>
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
                            <th>Customs Information</th>
                            <th>Owner Information</th>
                            <th>Investigation Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                        <tr class="">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <?php if (!empty($vehicle['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($vehicle['image_url']); ?>" 
                                             alt="Vehicle" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                        <div class="bg-warning text-white d-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 60px;">
                                            <i class="bi bi-car-front fs-2"></i>
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
                                <small class="text-muted">
                                    <strong>Flagged Date:</strong> <?= date('M j, Y', strtotime($vehicle['status_date'])); ?><br>
                                    <strong>Flagged By:</strong> <?= htmlspecialchars($vehicle['flagged_by'] ?? 'System'); ?><br>
                                    <strong>Reason:</strong> <?= htmlspecialchars($vehicle['status_reason'] ?? 'No customs duty paid'); ?>
                                </small>
                            </td>
                            <td>
                                <div>
                                    <strong><?= htmlspecialchars($vehicle['name']); ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($vehicle['email']); ?></small><br>
                                    <small class="text-muted"><?= htmlspecialchars($vehicle['phone']); ?></small><br>
                                    <small class="text-muted">NIN: <?= htmlspecialchars($vehicle['nin']); ?></small>
                                </div>
                            </td>
                            <td>
                                <?= getInvestigationBadge($vehicle['status_reason']); ?><br>
                                <small class="text-muted">
                                    <?= htmlspecialchars($vehicle['status_reason'] ?? 'No notes'); ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('search/vehicle-profile/' . $vehicle['vin']); ?>" 
                                       class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('admin/manage-vehicle/' . $vehicle['vin']); ?>" 
                                       class="btn btn-outline-warning">
                                         <i class="bi bi-pencil-square"></i>Edit
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
            </nav>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="text-center py-5">
                <div class="text-muted">
                    <i class="bi bi-shield-exclamation display-4 mb-3 text-warning"></i>
                    <h4>No Customs Duty Vehicles</h4>
                    <p>No vehicles flagged for customs duty issues found matching your criteria.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Charts -->
    <?php if (!empty($chart_data)): ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-bar-chart me-2"></i>Customs Issues Trend
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="customsTrendChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-pie-chart me-2"></i>Investigation Status
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="investigationChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean(); 
ob_start();
?>
<style>
.breadcrumb {
    background-color: #f8f9fa;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
}

.breadcrumb-item a {
    color: #0d6efd;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: #6c757d;
}

.table-warning {
    background-color: rgba(246, 194, 62, 0.05) !important;
}

.table-info {
    background-color: rgba(78, 115, 223, 0.05) !important;
}

.table-success {
    background-color: rgba(28, 200, 138, 0.05) !important;
}

.table-danger {
    background-color: rgba(231, 74, 59, 0.05) !important;
}

.badge.bg-warning {
    background-color: #f6c23e !important;
    color: #000;
}

.badge.bg-info {
    background-color: #4e73df !important;
}

.badge.bg-success {
    background-color: #1cc88a !important;
}

.badge.bg-danger {
    background-color: #e74a3b !important;
}

.btn-group .dropdown-toggle::after {
    margin-left: 0.5em;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}
</style>
<?php
$styles = ob_get_clean();
ob_start(); 
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentVehicleId = null;
let currentVehicleIdClear = null;
let currentVehicleIdPenalty = null;

function applySearch() {
    const search = document.getElementById('searchInput').value;
    const url = new URL(window.location.href);
    
    if (search) {
        url.searchParams.set('search', search);
    } else {
        url.searchParams.delete('search');
    }
    
    url.searchParams.set('page', '1');
    
    window.location.href = url.toString();
}

function updatePerPage() {
    const perPage = document.getElementById('perPageSelect').value;
    const url = new URL(window.location.href);
    
    url.searchParams.set('per_page', perPage);
    url.searchParams.set('page', '1');
    
    window.location.href = url.toString();
}


// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($chart_data)): ?>
    // Customs Trend Chart
    const customsTrendCtx = document.getElementById('customsTrendChart').getContext('2d');
    const customsTrendChart = new Chart(customsTrendCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_data['monthly_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']); ?>,
            datasets: [{
                label: 'Customs Issues Reported',
                data: <?= json_encode($chart_data['issues_data'] ?? [0, 0, 0, 0, 0, 0]); ?>,
                backgroundColor: '#f6c23e',
                borderColor: '#f6c23e',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Investigation Chart
    const investigationCtx = document.getElementById('investigationChart').getContext('2d');
    const investigationChart = new Chart(investigationCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Under Investigation', 'Cleared', 'Penalty Applied'],
            datasets: [{
                data: <?= json_encode($chart_data['investigation_status'] ?? [0, 0, 0, 0]); ?>,
                backgroundColor: ['#6c757d', '#4e73df', '#1cc88a', '#e74a3b'],
                hoverBackgroundColor: ['#545b62', '#2e59d9', '#17a673', '#d52a1e'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    <?php endif; ?>
    
    // Event listeners
    document.getElementById('confirmInvestigation').addEventListener('click', confirmInvestigation);
    document.getElementById('confirmClearance').addEventListener('click', confirmClearance);
    document.getElementById('confirmPenalty').addEventListener('click', confirmPenalty);
});
</script>

<?php 
$scripts = ob_get_clean(); 
include 'app/Views/layouts/main.php'; 
?>