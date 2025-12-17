<?php
$title = "Normal Vehicles - Admin";
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="<?= url('admin/dashboard'); ?>">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('admin/vehicles'); ?>">Vehicle Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Normal Vehicles</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-check-circle me-2 text-success"></i>Normal Vehicles
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
                        <li><a href="<?= url("admin/vehicles/normal?page={$current_page}&per_page={$option}") ?>" class="dropdown-item <?= $per_page == $option ? 'active' : ''; ?>" href="#"><?= $option; ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Refresh Button -->
            <a href="<?= url('admin/vehicles/normal'); ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Normal Vehicles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['total'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-car-front fs-2 text-gray-300"></i>
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
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-check-circle me-2 text-success"></i>Normal Status Vehicles
                <span class="badge bg-success ms-2"><?= $total_items ?? 0; ?> vehicles</span>
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
                            <th>Owner Information</th>
                            <th>Registration Details</th>
                            <th>Current Status</th>
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
                                <div>
                                    <strong><?= htmlspecialchars($vehicle['name']); ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($vehicle['email']); ?></small><br>
                                    <small class="text-muted"><?= htmlspecialchars($vehicle['phone']); ?></small><br>
                                    <small class="text-muted">NIN: <?= htmlspecialchars($vehicle['nin']); ?></small>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <strong>Registered:</strong> <?= date('M j, Y', strtotime($vehicle['created_at'])); ?><br>
                                    <strong>Last Updated:</strong> <?= date('M j, Y', strtotime($vehicle['updated_at'] ?? 'now')); ?><br>
                                    <strong>Transfers:</strong> <?= $vehicle['transfer_count'] ?? 0; ?><br>
                                    <strong>Plate Changes:</strong> <?= $vehicle['plate_count'] ?? 0; ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-success">Normal</span><br>
                                <small class="text-muted">Status since: <?= date('M j, Y', strtotime($vehicle['created_at'])); ?></small>
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
                    <i class="bi bi-check-circle display-4 mb-3 text-success"></i>
                    <h4>No Normal Vehicles</h4>
                    <p>No vehicles with normal status found matching your criteria.</p>
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-bar-chart me-2"></i>Normal Vehicles by Month
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-pie-chart me-2"></i>Vehicle Type Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="200"></canvas>
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

.table th {
    font-weight: 600;
    color: #4e73df;
}

.badge.bg-success {
    background-color: #1cc88a !important;
}

.btn-group .dropdown-toggle::after {
    margin-left: 0.5em;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.img-thumbnail {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}
</style>
<?php 
$styles = ob_get_clean(); 
ob_start();
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentVehicleId = null;
let currentNewStatus = null;

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

function changeVehicleStatus(vehicleId, newStatus) {
    currentVehicleId = vehicleId;
    currentNewStatus = newStatus;
    
    const statusLabels = {
        'stolen': 'Mark as Stolen',
        'no_customs_duty': 'Mark as No Customs Duty',
        'changed_engine': 'Mark as Changed Engine',
        'changed_color': 'Mark as Changed Color'
    };
    
    const modalLabel = document.getElementById('changeStatusModalLabel');
    modalLabel.textContent = statusLabels[newStatus] || 'Change Vehicle Status';
    
    const modal = new bootstrap.Modal(document.getElementById('changeStatusModal'));
    modal.show();
}

function confirmStatusChange() {
    const reason = document.getElementById('statusReason').value;
    
    if (!reason.trim()) {
        alert('Please provide a reason for the status change.');
        return;
    }
    
    const confirmBtn = document.getElementById('confirmStatusChange');
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    confirmBtn.disabled = true;
    
    fetch('/api/admin/vehicle/change-status', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            vehicle_id: currentVehicleId,
            new_status: currentNewStatus,
            reason: reason,
            _token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Vehicle status updated successfully!');
            window.location.reload();
        } else {
            alert(data.message || 'Failed to update vehicle status.');
            confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i>Change Status';
            confirmBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
        confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i>Change Status';
        confirmBtn.disabled = false;
    });
}

function exportToCSV() {
    const url = new URL(window.location.href);
    url.searchParams.set('export', 'csv');
    window.location.href = url.toString();
}

function confirmDeleteVehicle(vehicleId) {
    if (confirm('Are you sure you want to delete this vehicle? This action cannot be undone.')) {
        fetch(`/api/admin/vehicle/delete/${vehicleId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ _token: csrfToken })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Vehicle deleted successfully!');
                window.location.reload();
            } else {
                alert(data.message || 'Failed to delete vehicle.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error. Please try again.');
        });
    }
}

// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($chart_data)): ?>
    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_data['monthly_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']); ?>,
            datasets: [{
                label: 'Normal Vehicles Registered',
                data: <?= json_encode($chart_data['monthly_data'] ?? [0, 0, 0, 0, 0, 0]); ?>,
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true
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
    
    // Type Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    const typeChart = new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chart_data['type_labels'] ?? ['Cars', 'Trucks', 'Motorcycles']); ?>,
            datasets: [{
                data: <?= json_encode($chart_data['type_data'] ?? [0, 0, 0]); ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
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
    document.getElementById('confirmStatusChange').addEventListener('click', confirmStatusChange);
});
</script>

<?php $scripts = ob_get_clean(); 
include 'app/Views/layouts/main.php'; 
?>