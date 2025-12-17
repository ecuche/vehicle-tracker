
<?php
$title = "Stolen Vehicles - Admin";
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="<?= url('admin/dashboard'); ?>">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('admin/vehicles'); ?>">Vehicle Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Stolen Vehicles</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-exclamation-triangle me-2 text-danger"></i>Stolen Vehicles
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
                        <li><a href="<?= url("admin/vehicles/stolen?page={$current_page}&per_page={$option}") ?>" class="dropdown-item <?= $per_page == $option ? 'active' : ''; ?>" href="#"><?= $option; ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <!-- Refresh Button -->
            <a href="<?= url('admin/vehicles/stolen'); ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </div>

     <!-- Stats Cards -->
    <div class="row mb-4">
         <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Stolen Vehicles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['total'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-car-front-fill fs-2 text-gray-300"></i>
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
            <h6 class="m-0 font-weight-bold text-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>Stolen Vehicles Report
                <span class="badge bg-danger ms-2"><?= $total_items ?? 0; ?> vehicles</span>
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
                            <th>Stolen Information</th>
                            <th>Owner Information</th>
                            <th>Status</th>
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
                                        <div class="bg-danger text-white d-flex align-items-center justify-content-center" 
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
                                    <strong>Stolen Date:</strong> <?= date('M j, Y', strtotime($vehicle['status_date'])); ?><br>
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
                                <span class="badge bg-danger">Still Stolen</span><br>
                                <small class="text-muted">Stolen for: <?= getDaysStolen($vehicle['status_date']); ?> days</small>
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
                    <i class="bi bi-exclamation-triangle display-4 mb-3 text-danger"></i>
                    <h4>No Stolen Vehicles</h4>
                    <p>No stolen vehicles found matching your criteria.</p>
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
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-graph-up me-2"></i>Stolen Vehicles Trend
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="stolenTrendChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-pie-chart me-2"></i>Recovery Status
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="recoveryChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let selectedVehicles = [];
let currentVehicleId = null;
let currentVehicleIdReport = null;

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

// Checkbox selection functions
document.addEventListener('DOMContentLoaded', function() {
    // Main checkbox event
    document.getElementById('mainCheckbox').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.vehicle-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            updateSelection(checkbox);
        });
        updateMarkFoundButton();
    });
    
    // Individual checkbox events
    document.querySelectorAll('.vehicle-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelection(this);
            updateMarkFoundButton();
            updateSelectAllCheckbox();
        });
    });
    
    // Bulk mark as found
    document.getElementById('markFoundBtn').addEventListener('click', function() {
        if (selectedVehicles.length > 0) {
            if (confirm(`Mark ${selectedVehicles.length} vehicle(s) as found?`)) {
                markMultipleAsFound();
            }
        }
    });
});

function updateSelection(checkbox) {
    const vehicleId = checkbox.dataset.vehicleId;
    const index = selectedVehicles.indexOf(vehicleId);
    
    if (checkbox.checked && index === -1) {
        selectedVehicles.push(vehicleId);
    } else if (!checkbox.checked && index !== -1) {
        selectedVehicles.splice(index, 1);
    }
}

function updateMarkFoundButton() {
    const markFoundBtn = document.getElementById('markFoundBtn');
    if (selectedVehicles.length > 0) {
        markFoundBtn.disabled = false;
        markFoundBtn.textContent = `Mark as Found (${selectedVehicles.length})`;
    } else {
        markFoundBtn.disabled = true;
        markFoundBtn.textContent = 'Mark as Found';
    }
}

function updateSelectAllCheckbox() {
    const mainCheckbox = document.getElementById('mainCheckbox');
    const checkboxes = document.querySelectorAll('.vehicle-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
    
    if (allChecked) {
        mainCheckbox.checked = true;
        mainCheckbox.indeterminate = false;
    } else if (anyChecked) {
        mainCheckbox.checked = false;
        mainCheckbox.indeterminate = true;
    } else {
        mainCheckbox.checked = false;
        mainCheckbox.indeterminate = false;
    }
}

function markAsFound(vehicleId) {
    currentVehicleId = vehicleId;
    const modal = new bootstrap.Modal(document.getElementById('markFoundModal'));
    modal.show();
}

function addPoliceReport(vehicleId) {
    currentVehicleIdReport = vehicleId;
    const modal = new bootstrap.Modal(document.getElementById('policeReportModal'));
    modal.show();
}

function changeVehicleStatus(vehicleId, newStatus) {
    if (confirm('Are you sure you want to change the vehicle status?')) {
        fetch('/api/admin/vehicle/change-status', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                vehicle_id: vehicleId,
                new_status: newStatus,
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
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error. Please try again.');
        });
    }
}

function confirmRecovery() {
    const recoveryDate = document.getElementById('recoveryDate').value;
    const recoveryLocation = document.getElementById('recoveryLocation').value;
    const recoveryNotes = document.getElementById('recoveryNotes').value;
    
    if (!recoveryDate || !recoveryLocation) {
        alert('Please fill in all required fields.');
        return;
    }
    
    const confirmBtn = document.getElementById('confirmRecovery');
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    confirmBtn.disabled = true;
    
    fetch('/api/admin/vehicle/mark-found', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            vehicle_id: currentVehicleId,
            recovery_date: recoveryDate,
            recovery_location: recoveryLocation,
            recovery_notes: recoveryNotes,
            _token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Vehicle marked as found successfully!');
            window.location.reload();
        } else {
            alert(data.message || 'Failed to mark vehicle as found.');
            confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i>Mark as Found';
            confirmBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
        confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i>Mark as Found';
        confirmBtn.disabled = false;
    });
}

function markMultipleAsFound() {
    const recoveryDate = prompt('Enter recovery date (YYYY-MM-DD):', '<?= date('Y-m-d'); ?>');
    const recoveryLocation = prompt('Enter recovery location:');
    
    if (!recoveryDate || !recoveryLocation) {
        alert('Both recovery date and location are required.');
        return;
    }
    
    fetch('/api/admin/vehicle/mark-multiple-found', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            vehicle_ids: selectedVehicles,
            recovery_date: recoveryDate,
            recovery_location: recoveryLocation,
            _token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`${selectedVehicles.length} vehicle(s) marked as found successfully!`);
            window.location.reload();
        } else {
            alert(data.message || 'Failed to mark vehicles as found.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    });
}

function confirmPoliceReport() {
    const policeStation = document.getElementById('policeStation').value;
    const reportNumber = document.getElementById('reportNumber').value;
    const reportDate = document.getElementById('reportDate').value;
    
    if (!policeStation || !reportNumber || !reportDate) {
        alert('Please fill in all required fields.');
        return;
    }
    
    const confirmBtn = document.getElementById('confirmPoliceReport');
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    confirmBtn.disabled = true;
    
    fetch('/api/admin/vehicle/add-police-report', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            vehicle_id: currentVehicleIdReport,
            police_station: policeStation,
            report_number: reportNumber,
            report_date: reportDate,
            investigating_officer: document.getElementById('investigatingOfficer').value,
            report_notes: document.getElementById('reportNotes').value,
            _token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Police report added successfully!');
            window.location.reload();
        } else {
            alert(data.message || 'Failed to add police report.');
            confirmBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Report';
            confirmBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
        confirmBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Report';
        confirmBtn.disabled = false;
    });
}

function exportToCSV() {
    const url = new URL(window.location.href);
    url.searchParams.set('export', 'csv');
    window.location.href = url.toString();
}

function confirmDeleteVehicle(vehicleId) {
    if (confirm('Are you sure you want to delete this stolen vehicle record? This action cannot be undone.')) {
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
                alert('Vehicle record deleted successfully!');
                window.location.reload();
            } else {
                alert(data.message || 'Failed to delete vehicle record.');
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
    // Stolen Trend Chart
    const stolenTrendCtx = document.getElementById('stolenTrendChart').getContext('2d');
    const stolenTrendChart = new Chart(stolenTrendCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_data['monthly_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']); ?>,
            datasets: [{
                label: 'Vehicles Stolen',
                data: <?= json_encode($chart_data['stolen_data'] ?? [0, 0, 0, 0, 0, 0]); ?>,
                borderColor: '#e74a3b',
                backgroundColor: 'rgba(231, 74, 59, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Vehicles Recovered',
                data: <?= json_encode($chart_data['recovered_data'] ?? [0, 0, 0, 0, 0, 0]); ?>,
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
    
    // Recovery Chart
    const recoveryCtx = document.getElementById('recoveryChart').getContext('2d');
    const recoveryChart = new Chart(recoveryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Still Stolen', 'Recovered'],
            datasets: [{
                data: <?= json_encode($chart_data['recovery_status'] ?? [0, 0]); ?>,
                backgroundColor: ['#e74a3b', '#1cc88a'],
                hoverBackgroundColor: ['#d52a1e', '#17a673'],
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
    document.getElementById('confirmRecovery').addEventListener('click', confirmRecovery);
    document.getElementById('confirmPoliceReport').addEventListener('click', confirmPoliceReport);
});
</script>

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

.table-danger {
    background-color: rgba(231, 74, 59, 0.05) !important;
}

.table-success {
    background-color: rgba(28, 200, 138, 0.05) !important;
}

.badge.bg-danger {
    background-color: #e74a3b !important;
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

.form-check-input:indeterminate {
    background-color: #6c757d;
    border-color: #6c757d;
}
</style>
<?php $scripts = ob_get_clean(); ?>

<?php 


include 'app/Views/layouts/main.php'; 
?>