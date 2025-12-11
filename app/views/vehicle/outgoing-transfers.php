<?php
$title = "Outgoing Vehicle Transfers";
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('dashboard'); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('vehicles'); ?>">My Vehicles</a></li>
            <li class="breadcrumb-item active" aria-current="page">Outgoing Transfers</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-box-arrow-up me-2 text-primary"></i>Outgoing Vehicle Transfers
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
                <a href="<?= url('vehicles/completed-transfers'); ?>" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-check-circle-fill me-1"></i> Completed
                </a>
            </div>
            
           
            <!-- Refresh Button -->
            <a href="<?= url('vehicles/outgoing-transfers'); ?>" class="btn btn-sm btn-outline-primary">
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
                <i class="bi bi-box-arrow-up me-2"></i>Vehicles You're Transferring
                <span class="badge bg-primary ms-2"><?= $total_items ?? 0; ?> vehicles</span>
            </h6>
            <div class="d-flex">
                <!-- Status Filter -->
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Select Status
                    </button>
                    <ul class="dropdown-menu bg-secondary text-light">
                        <li><a class="dropdown-item" href="<?= url('vehicles/incoming-transfers') ?>">Incoming</a></li>
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
                            <th>Recipient</th>
                            <th>Transfer Status</th>
                            <th>Date Sent</th>
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
                                <?php if (!empty($vehicle['rejection_reason'])): ?>
                                <br><small class="text-danger">Reason: <?= htmlspecialchars($vehicle['transfer_note']); ?></small>
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
                                    <a href="<?= url('search/vehicle-profile/'.$vehicle['vin']); ?>" 
                                       class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i> View
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
                    <i class="bi bi-box-arrow-up fs-1 mb-3"></i>
                    <h4>No Outgoing Transfers</h4>
                    <p>You haven't initiated any vehicle transfers yet.</p>
                    <a href="<?= url('vehicles'); ?>" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-lg me-2"></i>Initiate New Transfer
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Transfer Chart -->
    <?php if (!empty($chart_data)): ?>
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-graph-up me-2"></i>Transfer Statistics
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <canvas id="transferChart" height="100"></canvas>
                </div>
                <div class="col-md-4">
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Successful Transfers
                            <span class="badge bg-success rounded-pill">
                                <?= $stats['successful'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Average Response Time
                            <span class="badge bg-info rounded-pill">
                                <?= $stats['avg_response_time'] ?? '0'; ?> days
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Active Transfers
                            <span class="badge bg-warning rounded-pill">
                                <?= $stats['pending'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Transfer Success Rate
                            <span class="badge bg-primary rounded-pill">
                                <?= $stats['success_rate'] ?? '0%'; ?>
                            </span>
                        </div>
                    </div>
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

function filterByStatus() {
    const status = document.getElementById('statusFilter').value;
    const url = new URL(window.location.href);
    
    if (status !== 'all') {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
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
                                ${data.vehicle.images && data.vehicle.images.length > 0 ? 
                                    `<img src="${data.vehicle.images[0]}" alt="Vehicle" class="img-fluid rounded" style="max-height: 150px;">` :
                                    `<div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="height: 150px;">
                                        <i class="bi bi-car-front fs-1"></i>
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

function cancelTransfer(transferId) {
    if (!confirm('Are you sure you want to cancel this transfer request?')) {
        return;
    }
    
    fetch(`/api/vehicle/cancel-transfer/${transferId}`, {
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
            alert('Transfer request cancelled successfully.');
            window.location.reload();
        } else {
            alert(data.message || 'Failed to cancel transfer.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    });
}

// Initialize Chart
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($chart_data)): ?>
    const ctx = document.getElementById('transferChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_data['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']); ?>,
            datasets: [
                {
                    label: 'Transfers Initiated',
                    data: <?= json_encode($chart_data['initiated'] ?? [0, 0, 0, 0, 0, 0]); ?>,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Transfers Completed',
                    data: <?= json_encode($chart_data['completed'] ?? [0, 0, 0, 0, 0, 0]); ?>,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.05)',
                    tension: 0.4,
                    fill: true
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
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
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
</style>
<?php $scripts = ob_get_clean(); ?>

<?php 
include 'app/Views/layouts/main.php'; 
?>