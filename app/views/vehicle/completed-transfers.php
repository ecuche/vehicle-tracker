<?php
$title = "Completed Vehicle Transfers";
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('dashboard'); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('vehicles'); ?>">My Vehicles</a></li>
            <li class="breadcrumb-item active" aria-current="page">Completed Transfers</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-check-circle-fill me-2 text-primary"></i>Completed Vehicle Transfers
        </h1>
        <div class="d-flex align-items-center">
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
                <a href="<?= url('vehicles/incoming-transfers'); ?>" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-inbox me-1"></i> Incoming
                </a>
                <a href="<?= url('vehicles/outgoing-transfers'); ?>" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-send me-1"></i> Outgoing
                </a>
            </div>
            
            <!-- Refresh Button -->
            <a href="<?= url('vehicles/completed-transfers'); ?>" class="btn btn-sm btn-outline-primary">
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

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Completed Transfers</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date Range</label>
                    <div class="input-group input-group-sm">
                        <input type="date" class="form-control" name="start_date" 
                               value="<?= htmlspecialchars($start_date ?? date('Y-m-d', strtotime('-30 days'))); ?>">
                        <span class="input-group-text">to</span>
                        <input type="date" class="form-control" name="end_date" 
                               value="<?= htmlspecialchars($end_date ?? date('Y-m-d')); ?>">
                    </div>
                </div>
                <div class="col-md-3"></div>

                <div class="col-md-3 d-flex align-items-end">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-funnel me-1"></i> Apply
                        </button>
                        <a href="<?= url('vehicles/completed-transfers'); ?>" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </div>
                 <div class="col-md-3 d-flex align-items-end">
                    <div class="dropdown d-flex align-items-end">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Select Status
                        </button>
                        <ul class="dropdown-menu bg-secondary text-light">
                            <li><a class="dropdown-item" href="<?= url('vehicles/outgoing-transfers') ?>">Outgoing</a></li>
                            <li><a class="dropdown-item" href="<?= url('vehicles/incoming-transfers') ?>">Incoming</a></li>
                            <li><a class="dropdown-item" href="<?= url('vehicles/rejected-transfers') ?>">Rejected</a></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Vehicles Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-clock-history me-2"></i>Completed Transfer History
                <span class="badge bg-primary ms-2"><?= $total_items ?? 0; ?> vehicles</span>
            </h6>
            <div class="d-flex">
                <!-- Quick Stats -->
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
                            <th>Vehicle Information</th>
                            <th>Transfer Details</th>
                            <th>Parties Involved</th>
                            <th>Completion Date</th>
                            <th>Status</th>
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
                                        <small class="text-muted">Color: <?= htmlspecialchars($vehicle['color']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($vehicle['buyer_id'] == $_SESSION['user_id']): ?>
                                <span class="badge bg-primary">Incoming</span>
                                <?php else: ?>
                                <span class="badge bg-info">Outgoing</span>
                                <?php endif; ?>
                                <br>
                                <small class="text-muted">
                                    Initiated: <?= date('M j, Y', strtotime($vehicle['created_at'])); ?>
                                </small>
                                <?php if (!empty($vehicle['transfer_note'])): ?>
                                <br><small class="text-muted">Note: <?= htmlspecialchars($vehicle['transfer_note']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small">
                                    <strong>From:</strong> <?= htmlspecialchars($vehicle['seller_name']); ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($vehicle['seller_email']); ?></small><br>
                                    <strong>To:</strong> <?= htmlspecialchars($vehicle['buyer_name']); ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($vehicle['buyer_email']); ?></small>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($vehicle['reponse_date'])): ?>
                                    <?= date('M j, Y', strtotime($vehicle['response_date'])); ?><br>
                                    <small class="text-muted">
                                        <?= formatRelativeTime($vehicle['response_date']); ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= getTransferStatusBadge($vehicle['status']); ?><br>
                                <?php if (!empty($vehicle['response'])): ?>
                                <small class="text-danger"><?= htmlspecialchars($vehicle['response']); ?></small>
                                <?php endif; ?>
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
                    <i class="bi bi-clock-history fs-1 mb-3"></i>
                    <h4>No Completed Transfers</h4>
                    <p>No completed vehicle transfers found matching your criteria.</p>
                    <a href="<?= url('vehicles/transfers'); ?>" class="btn btn-primary mt-2">
                        <i class="bi bi-arrow-left-right me-2"></i>View All Transfers
                    </a>
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
                        <i class="bi bi-bar-chart me-2"></i>Monthly Transfer Summary
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
                        <i class="bi bi-pie-chart me-2"></i>Transfer Status Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Transfer Details Modal -->
<div class="modal fade" id="transferDetailsModal" tabindex="-1" aria-labelledby="transferDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferDetailsModalLabel">Transfer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="transferDetailsContent">
                    <!-- Details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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

function viewTransferDetails(transferId) {
    fetch(`/api/vehicle/transfer-details/${transferId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = new bootstrap.Modal(document.getElementById('transferDetailsModal'));
            const contentDiv = document.getElementById('transferDetailsContent');
            
            const isIncoming = data.transfer.buyer_id === currentUserId;
            const direction = isIncoming ? 'Incoming' : 'Outgoing';
            const directionClass = isIncoming ? 'primary' : 'info';
            
            contentDiv.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-${directionClass} text-white">
                            <h6 class="mb-0">Transfer Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th>Transfer ID:</th>
                                    <td>#${data.transfer.id}</td>
                                </tr>
                                <tr>
                                    <th>Direction:</th>
                                    <td><span class="badge bg-${directionClass}">${direction}</span></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>${getTransferStatusBadge(data.transfer.status)}</td>
                                </tr>
                                <tr>
                                    <th>Initiated:</th>
                                    <td>${new Date(data.transfer.created_at).toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <th>Completed:</th>
                                    <td>${new Date(data.transfer.completed_at).toLocaleString()}</td>
                                </tr>
                                ${data.transfer.note ? `
                                <tr>
                                    <th>Notes:</th>
                                    <td>${data.transfer.note}</td>
                                </tr>
                                ` : ''}
                                ${data.transfer.rejection_reason ? `
                                <tr>
                                    <th>Rejection Reason:</th>
                                    <td class="text-danger">${data.transfer.rejection_reason}</td>
                                </tr>
                                ` : ''}
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0">Vehicle Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                ${data.vehicle.image_url ? 
                                    `<img src="${data.vehicle.image_url}" alt="Vehicle" class="img-fluid rounded" style="max-height: 150px;">` :
                                    `<div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="height: 150px;">
                                        <i class="bi bi-car-front fs-5"></i>
                                    </div>`
                                }
                            </div>
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th>Make/Model:</th>
                                    <td>${data.vehicle.make} ${data.vehicle.model}</td>
                                </tr>
                                <tr>
                                    <th>Year:</th>
                                    <td>${data.vehicle.year}</td>
                                </tr>
                                <tr>
                                    <th>Color:</th>
                                    <td>${data.vehicle.color}</td>
                                </tr>
                                <tr>
                                    <th>VIN:</th>
                                    <td><code>${data.vehicle.vin}</code></td>
                                </tr>
                                <tr>
                                    <th>Plate:</th>
                                    <td><code>${data.vehicle.plate_number}</code></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Seller Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th>Name:</th>
                                    <td>${data.seller.name}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>${data.seller.email}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>${data.seller.phone}</td>
                                </tr>
                                <tr>
                                    <th>NIN:</th>
                                    <td>${data.seller.nin}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Buyer Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th>Name:</th>
                                    <td>${data.buyer.name}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>${data.buyer.email}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>${data.buyer.phone}</td>
                                </tr>
                                <tr>
                                    <th>NIN:</th>
                                    <td>${data.buyer.nin}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            `;
            
            modal.show();
        } else {
            alert('Failed to load transfer details.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    });
}

function exportToCSV() {
    const url = new URL(window.location.href);
    url.searchParams.set('export', 'csv');
    window.location.href = url.toString();
}

// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($chart_data)): ?>
    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_data['monthly_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']); ?>,
            datasets: [
                {
                    label: 'Incoming',
                    data: <?= json_encode($chart_data['monthly_incoming'] ?? [0, 0, 0, 0, 0, 0]); ?>,
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1
                },
                {
                    label: 'Outgoing',
                    data: <?= json_encode($chart_data['monthly_outgoing'] ?? [0, 0, 0, 0, 0, 0]); ?>,
                    backgroundColor: '#1cc88a',
                    borderColor: '#1cc88a',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                x: {
                    stacked: false,
                },
                y: {
                    stacked: false,
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chart_data['status_labels'] ?? ['Completed', 'Rejected', 'Cancelled']); ?>,
            datasets: [{
                data: <?= json_encode($chart_data['status_data'] ?? [0, 0, 0]); ?>,
                backgroundColor: ['#1cc88a', '#e74a3b', '#6c757d'],
                hoverBackgroundColor: ['#17a673', '#d52a1e', '#545b62'],
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
});
</script>

<style>
.breadcrumb {
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

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

.btn-group .btn {
    border-radius: 0.25rem;
}

.btn-group .btn:not(:last-child) {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.btn-group .btn:not(:first-child) {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e3e6f0;
}
</style>
<?php $scripts = ob_get_clean(); 
include 'app/Views/layouts/main.php'; 
?>