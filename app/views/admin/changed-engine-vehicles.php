<?php
$title = "Changed Engine Vehicles - Admin";
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="<?= url('admin/dashboard'); ?>">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('admin/vehicles'); ?>">Vehicle Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Changed Engine Vehicles</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-gear me-2 text-info"></i>Changed Engine Vehicles
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
            
            <!-- Refresh Button -->
            <a href="<?= url('admin/vehicles/changed-engine'); ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </div>

     <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Changed Engine Vehicles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['total'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-gear-fill fs-2 text-gray-300"></i>
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
            <h6 class="m-0 font-weight-bold text-info">
                <i class="bi bi-gear me-2"></i>Changed Engine Vehicles
                <span class="badge bg-info ms-2"><?= $total_items ?? 0; ?> vehicles</span>
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
                            <th>Engine Information</th>
                            <th>Change Details</th>
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
                                        <div class="bg-info text-white d-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 60px;">
                                            <i class="bi bi-car-front-fill fs-3"></i>
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
                                    <strong>Engine:</strong> <?= htmlspecialchars($vehicle['status_reason'] ?? 'N/A'); ?><br>
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <strong>Change Date:</strong> <?= date('M j, Y', strtotime($vehicle['status_date'])); ?><br>
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
                    <i class="bi bi-gear fs-1 mb-3 text-info"></i>
                    <h4>No Changed Engine Vehicles</h4>
                    <p>No vehicles with engine changes found matching your criteria.</p>
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
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-bar-chart me-2"></i>Engine Changes Trend
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="engineChangesChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-pie-chart me-2"></i>Approval Status
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="approvalChart" height="200"></canvas>
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

.table-success {
    background-color: rgba(28, 200, 138, 0.05) !important;
}

.table-danger {
    background-color: rgba(231, 74, 59, 0.05) !important;
}

.table-info {
    background-color: rgba(54, 185, 204, 0.05) !important;
}

.badge.bg-warning {
    background-color: #f6c23e !important;
    color: #000;
}

.badge.bg-success {
    background-color: #1cc88a !important;
}

.badge.bg-danger {
    background-color: #e74a3b !important;
}

.badge.bg-info {
    background-color: #36b9cc !important;
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
let currentVehicleIdApprove = null;
let currentVehicleIdReject = null;
let currentVehicleIdInspect = null;
let currentVehicleIdDocuments = null;

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

function approveEngineChange(vehicleId) {
    currentVehicleIdApprove = vehicleId;
    const modal = new bootstrap.Modal(document.getElementById('approveModal'));
    modal.show();
}

function rejectEngineChange(vehicleId) {
    currentVehicleIdReject = vehicleId;
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

function requestInspection(vehicleId) {
    currentVehicleIdInspect = vehicleId;
    const modal = new bootstrap.Modal(document.getElementById('inspectionModal'));
    modal.show();
}

function viewEngineDocuments(vehicleId) {
    currentVehicleIdDocuments = vehicleId;
    
    fetch(`/api/admin/vehicle/engine-documents/${vehicleId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = new bootstrap.Modal(document.getElementById('documentsModal'));
            const contentDiv = document.getElementById('documentsContent');
            
            let documentsHtml = '<div class="row">';
            
            if (data.documents && data.documents.length > 0) {
                data.documents.forEach(doc => {
                    documentsHtml += `
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-file-${getDocumentIcon(doc.type)} me-2 text-primary"></i>
                                    ${doc.title}
                                </h6>
                                <p class="card-text small">${doc.description || 'No description'}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Uploaded: ${new Date(doc.upload_date).toLocaleDateString()}</small>
                                    <div class="btn-group btn-group-sm">
                                        <a href="${doc.file_url}" class="btn btn-outline-primary" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="${doc.file_url}" class="btn btn-outline-success" download>
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                });
            } else {
                documentsHtml = `
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No documents uploaded for this engine change.
                    </div>
                </div>
                `;
            }
            
            documentsHtml += '</div>';
            contentDiv.innerHTML = documentsHtml;
            modal.show();
        } else {
            alert('Failed to load documents.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    });
}

function getDocumentIcon(type) {
    const icons = {
        'pdf': 'file-pdf',
        'image': 'file-image',
        'word': 'file-word',
        'excel': 'file-excel'
    };
    return icons[type] || 'file';
}

function updateEngineInfo(vehicleId) {
    // This would open a form to update engine information
    // For now, redirect to edit page
    window.location.href = `/admin/vehicles/edit-engine/${vehicleId}`;
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

function confirmApprove() {
    const approvalNotes = document.getElementById('approvalNotes').value;
    
    if (!approvalNotes.trim()) {
        alert('Please provide approval notes.');
        return;
    }
    
    const confirmBtn = document.getElementById('confirmApprove');
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Approving...';
    confirmBtn.disabled = true;
    
    fetch('/api/admin/vehicle/approve-engine-change', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            vehicle_id: currentVehicleIdApprove,
            approval_notes: approvalNotes,
            certificate_number: document.getElementById('certificateNumber').value,
            valid_until: document.getElementById('validUntil').value,
            _token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Engine change approved successfully!');
            window.location.reload();
        } else {
            alert(data.message || 'Failed to approve engine change.');
            confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i>Approve Change';
            confirmBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
        confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i>Approve Change';
        confirmBtn.disabled = false;
    });
}

function confirmReject() {
    const rejectionReason = document.getElementById('rejectionReason').value;
    
    if (!rejectionReason.trim()) {
        alert('Please provide a rejection reason.');
        return;
    }
    
    const confirmBtn = document.getElementById('confirmReject');
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Rejecting...';
    confirmBtn.disabled = true;
    
    fetch('/api/admin/vehicle/reject-engine-change', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            vehicle_id: currentVehicleIdReject,
            rejection_reason: rejectionReason,
            required_actions: document.getElementById('requiredActions').value,
            deadline_date: document.getElementById('deadlineDate').value,
            _token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Engine change rejected successfully!');
            window.location.reload();
        } else {
            alert(data.message || 'Failed to reject engine change.');
            confirmBtn.innerHTML = '<i class="fas fa-times me-2"></i>Reject Change';
            confirmBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
        confirmBtn.innerHTML = '<i class="fas fa-times me-2"></i>Reject Change';
        confirmBtn.disabled = false;
    });
}

function confirmInspection() {
    const inspectionReason = document.getElementById('inspectionReason').value;
    
    if (!inspectionReason.trim()) {
        alert('Please provide an inspection reason.');
        return;
    }
    
    const confirmBtn = document.getElementById('confirmInspection');
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Requesting...';
    confirmBtn.disabled = true;
    
    fetch('/api/admin/vehicle/request-engine-inspection', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            vehicle_id: currentVehicleIdInspect,
            inspection_reason: inspectionReason,
            inspection_requirements: document.getElementById('inspectionRequirements').value,
            preferred_date: document.getElementById('preferredInspectionDate').value,
            inspection_location: document.getElementById('inspectionLocation').value,
            _token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Inspection requested successfully!');
            window.location.reload();
        } else {
            alert(data.message || 'Failed to request inspection.');
            confirmBtn.innerHTML = '<i class="fas fa-search me-2"></i>Request Inspection';
            confirmBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
        confirmBtn.innerHTML = '<i class="fas fa-search me-2"></i>Request Inspection';
        confirmBtn.disabled = false;
    });
}

function exportToCSV() {
    const url = new URL(window.location.href);
    url.searchParams.set('export', 'csv');
    window.location.href = url.toString();
}

function confirmDeleteVehicle(vehicleId) {
    if (confirm('Are you sure you want to delete this vehicle record? This action cannot be undone.')) {
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
    // Engine Changes Chart
    const engineChangesCtx = document.getElementById('engineChangesChart').getContext('2d');
    const engineChangesChart = new Chart(engineChangesCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_data['monthly_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']); ?>,
            datasets: [{
                label: 'Engine Changes Reported',
                data: <?= json_encode($chart_data['changes_data'] ?? [0, 0, 0, 0, 0, 0]); ?>,
                borderColor: '#36b9cc',
                backgroundColor: 'rgba(54, 185, 204, 0.1)',
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
    
    // Approval Chart
    const approvalCtx = document.getElementById('approvalChart').getContext('2d');
    const approvalChart = new Chart(approvalCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Rejected', 'Requires Inspection'],
            datasets: [{
                data: <?= json_encode($chart_data['approval_status'] ?? [0, 0, 0, 0]); ?>,
                backgroundColor: ['#f6c23e', '#1cc88a', '#e74a3b', '#4e73df'],
                hoverBackgroundColor: ['#dda20a', '#17a673', '#d52a1e', '#2e59d9'],
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
    document.getElementById('confirmApprove').addEventListener('click', confirmApprove);
    document.getElementById('confirmReject').addEventListener('click', confirmReject);
    document.getElementById('confirmInspection').addEventListener('click', confirmInspection);
});
</script>


<?php 
$scripts = ob_get_clean(); 
include 'app/Views/layouts/main.php'; 
?>