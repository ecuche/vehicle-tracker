<?php
$title = "Vehicle History - " . ($vehicle['make'] ?? '') . " " . ($vehicle['model'] ?? '');
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $_ENV['APP_URL'] ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= $_ENV['APP_URL'] ?>/search/vehicle-profile/<?=$vehicle['vin']; ?>">Vehicle Details</a></li>
            <li class="breadcrumb-item active" aria-current="page">Vehicle History</li>
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
                                <i class="bi bi-clock-history me-2 text-primary"></i>Vehicle History
                            </h1>
                            <p class="text-muted mb-0">
                                Complete history and timeline for <?=e($vehicle['make']); ?> <?=e($vehicle['model']); ?> (<?=e($vehicle['year']); ?>)
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                                    <i class="bi bi-printer me-1"></i> Print
                                </button>
                                <button type="button" class="btn btn-outline-success" onclick="exportHistory()">
                                    <i class="bi bi-download me-1"></i> Export
                                </button>
                                <a href="<?= $_ENV['APP_URL'] ?>/vehicles/<?=$vehicle['id']; ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Back
                                </a>
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
                        <div class="col-md-3 text-center border-end">
                            <div class="mb-3">
                                <i class="bi bi-truck display-4 text-primary"></i>
                            </div>
                            <h5><?=e($vehicle['make']); ?> <?=e($vehicle['model']); ?></h5>
                            <p class="text-muted mb-0"><?=e($vehicle['year']); ?></p>
                        </div>
                        <div class="col-md-3 text-center border-end">
                            <div class="mb-2">
                                <strong class="text-muted">VIN</strong>
                            </div>
                            <code class="fs-5"><?=e($vehicle['vin']); ?></code>
                        </div>
                        <div class="col-md-3 text-center border-end">
                            <div class="mb-2">
                                <strong class="text-muted">Current Plate</strong>
                            </div>
                            <span class="badge bg-dark fs-6"><?=e($vehicle['current_plate_number'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <strong class="text-muted">Current Status</strong>
                            </div>
                            <?php
                            $statusBadges = [
                                'none' => ['class' => 'bg-success', 'label' => 'Normal', 'icon' => 'bi-check-circle'],
                                'stolen' => ['class' => 'bg-danger', 'label' => 'Stolen', 'icon' => 'bi-exclamation-triangle'],
                                'no_customs_duty' => ['class' => 'bg-warning', 'label' => 'No Customs', 'icon' => 'bi-shield-exclamation'],
                                'changed_engine' => ['class' => 'bg-info', 'label' => 'Changed Engine', 'icon' => 'bi-gear'],
                                'changed_color' => ['class' => 'bg-secondary', 'label' => 'Changed Color', 'icon' => 'bi-palette']
                            ];
                            $currentStatus = $vehicle['current_status'] ?? 'none';
                            $statusInfo = $statusBadges[$currentStatus] ?? ['class' => 'bg-secondary', 'label' => ucfirst($currentStatus), 'icon' => 'bi-question-circle'];
                            ?>
                            <span class="badge <?=$statusInfo['class']; ?> fs-6">
                                <i class="<?=$statusInfo['icon']; ?> me-1"></i>
                                <?=$statusInfo['label']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <nav>
                        <div class="nav nav-pills nav-fill" id="historyTabs" role="tablist">
                            <button class="nav-link active" id="timeline-tab" data-bs-toggle="tab" 
                                    data-bs-target="#timeline" type="button" role="tab">
                                <i class="bi bi-activity me-2"></i>Complete Timeline
                            </button>
                            <button class="nav-link" id="ownership-tab" data-bs-toggle="tab" 
                                    data-bs-target="#ownership" type="button" role="tab">
                                <i class="bi bi-people me-2"></i>Ownership History
                            </button>
                            <button class="nav-link" id="plates-tab" data-bs-toggle="tab" 
                                    data-bs-target="#plates" type="button" role="tab">
                                <i class="bi bi-123 me-2"></i>Plate History
                            </button>
                            <button class="nav-link" id="status-tab" data-bs-toggle="tab" 
                                    data-bs-target="#status" type="button" role="tab">
                                <i class="bi bi-info-circle me-2"></i>Status History
                            </button>
                            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" 
                                    data-bs-target="#documents" type="button" role="tab">
                                <i class="bi bi-file-earmark me-2"></i>Document History
                            </button>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- History Content -->
    <div class="row">
        <div class="col-12">
            <div class="tab-content" id="historyTabContent">
                <!-- Complete Timeline Tab -->
                <div class="tab-pane fade show active" id="timeline" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-activity me-2"></i>Complete Vehicle Timeline
                            </h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="showDetails" checked>
                                <label class="form-check-label" for="showDetails">Show Details</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($timeline_events)): ?>
                            <div class="timeline-vertical">
                                <?php foreach ($timeline_events as $event): ?>
                                <div class="timeline-item-vertical" data-event-type="<?=$event['type']; ?>">
                                    <div class="timeline-marker-vertical bg-<?=getEventColor($event['type']); ?>">
                                        <i class="<?=getEventIcon($event['type']); ?>"></i>
                                    </div>
                                    <div class="timeline-content-vertical">
                                        <div class="timeline-header">
                                            <h6 class="mb-1 text-capitalize">
                                                <?=formatEventType($event['type']); ?>
                                            </h6>
                                            <small class="text-muted">
                                                <?=date('F j, Y g:i A', strtotime($event['timestamp'])); ?>
                                            </small>
                                        </div>
                                        <div class="timeline-body">
                                            <p class="mb-1"><?=e($event['description']); ?></p>
                                            <?php if (!empty($event['details'])): ?>
                                            <div class="timeline-details mt-2">
                                                <?php foreach ($event['details'] as $key => $value): ?>
                                                <small class="text-muted">
                                                    <strong><?=e(ucfirst(str_replace('_', ' ', $key))); ?>:</strong> 
                                                    <?=e($value); ?>
                                                </small><br>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($event['user'])): ?>
                                            <div class="timeline-user mt-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-person me-1"></i>
                                                    By: <?=e($event['user']['name']); ?>
                                                    <?php if ($event['user']['role']): ?>
                                                    (<?=ucfirst($event['user']['role']); ?>)
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-clock-history display-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No History Available</h5>
                                <p class="text-muted">This vehicle doesn't have any recorded history yet.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Ownership History Tab -->
                <div class="tab-pane fade" id="ownership" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-people me-2"></i>Ownership History
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($ownership_history)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Owner</th>
                                            <th>Contact</th>
                                            <th>Ownership Period</th>
                                            <th>Transfer Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ownership_history as $history): ?>
                                        <tr class="<?=$history['is_current'] ? 'table-active' : ''; ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                    <div>
                                                        <strong><?=e($history['owner_name']); ?></strong>
                                                        <?php if ($history['is_current']): ?>
                                                        <span class="badge bg-success ms-1">Current</span>
                                                        <?php endif; ?>
                                                        <br>
                                                        <small class="text-muted"><?=ucfirst($history['owner_role']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small>
                                                    <?php if ($history['owner_email']): ?>
                                                    <div><i class="bi bi-envelope me-1"></i><?=e($history['owner_email']); ?></div>
                                                    <?php endif; ?>
                                                    <?php if ($history['owner_phone']): ?>
                                                    <div><i class="bi bi-phone me-1"></i><?=e($history['owner_phone']); ?></div>
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?=date('M j, Y', strtotime($history['start_date'])); ?><br>
                                                <small class="text-muted">
                                                    <?=$history['end_date'] ? 
                                                        'to ' . date('M j, Y', strtotime($history['end_date'])) : 
                                                        'to Present'; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?=$history['transfer_type'] === 'registration' ? 'success' : 'primary'; ?>">
                                                    <?=ucfirst($history['transfer_type']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?=$history['status'] === 'completed' ? 'success' : 'secondary'; ?>">
                                                    <?=ucfirst($history['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!$history['is_current']): ?>
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        onclick="viewTransferDetails(<?=$history['transfer_id']; ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-people display-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No Ownership History</h5>
                                <p class="text-muted">This vehicle has only had one owner.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Plate History Tab -->
                <div class="tab-pane fade" id="plates" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-123 me-2"></i>Plate Number History
                            </h5>
                            <span class="badge bg-primary">
                                <?=count($plate_history); ?> Plate(s)
                            </span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($plate_history)): ?>
                            <div class="row">
                                <?php foreach ($plate_history as $plate): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 <?=$plate['is_current'] ? 'border-primary' : ''; ?>">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="card-title mb-0 <?=$plate['is_current'] ? 'text-primary' : ''; ?>">
                                                    <?=e($plate['plate_number']); ?>
                                                </h5>
                                                <?php if ($plate['is_current']): ?>
                                                <span class="badge bg-primary">Current</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <strong>Assigned:</strong> <?=date('M j, Y', strtotime($plate['assigned_date'])); ?>
                                                </small>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <strong>By:</strong> <?=e($plate['assigned_by_name'] ?? 'System'); ?>
                                                </small>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <strong>Status:</strong> 
                                                    <span class="badge bg-<?=$plate['is_active'] ? 'success' : 'secondary'; ?>">
                                                        <?=$plate['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </small>
                                            </div>
                                            <?php if ($plate['notes']): ?>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <strong>Notes:</strong> <?=e($plate['notes']); ?>
                                                </small>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-123 display-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No Plate History</h5>
                                <p class="text-muted">This vehicle doesn't have any plate number history.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Status History Tab -->
                <div class="tab-pane fade" id="status" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>Status Change History
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($status_history)): ?>
                            <div class="timeline-vertical">
                                <?php foreach ($status_history as $status): ?>
                                <div class="timeline-item-vertical">
                                    <div class="timeline-marker-vertical bg-<?=getStatusColor($status['status']); ?>">
                                        <i class="bi bi-<?=getStatusIcon($status['status']); ?>"></i>
                                    </div>
                                    <div class="timeline-content-vertical">
                                        <div class="timeline-header">
                                            <h6 class="mb-1">
                                                Status Changed to 
                                                <span class="badge bg-<?=getStatusColor($status['status']); ?>">
                                                    <?=formatStatus($status['status']); ?>
                                                </span>
                                            </h6>
                                            <small class="text-muted">
                                                <?=date('F j, Y g:i A', strtotime($status['changed_at'])); ?>
                                            </small>
                                        </div>
                                        <div class="timeline-body">
                                            <?php if ($status['reason']): ?>
                                            <p class="mb-2"><strong>Reason:</strong> <?=e($status['reason']); ?></p>
                                            <?php endif; ?>
                                            <div class="timeline-user">
                                                <small class="text-muted">
                                                    <i class="bi bi-person me-1"></i>
                                                    Changed by: <?=e($status['changed_by_name'] ?? 'System'); ?>
                                                    <?php if ($status['changed_by_role']): ?>
                                                    (<?=ucfirst($status['changed_by_role']); ?>)
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-info-circle display-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No Status History</h5>
                                <p class="text-muted">This vehicle's status has never been changed.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Document History Tab -->
                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-file-earmark me-2"></i>Document History
                            </h5>
                            <span class="badge bg-primary">
                                <?=count($document_history); ?> Document(s)
                            </span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($document_history)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Document</th>
                                            <th>Type</th>
                                            <th>Uploaded</th>
                                            <th>Uploaded By</th>
                                            <th>Size</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($document_history as $document): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-file-earmark-text me-3 text-primary fs-4"></i>
                                                    <div>
                                                        <strong><?=e($document['name']); ?></strong><br>
                                                        <small class="text-muted"><?=e($document['description'] ?? 'No description'); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?=e(strtoupper($document['file_type'])); ?></span>
                                            </td>
                                            <td>
                                                <?=date('M j, Y', strtotime($document['created_at'])); ?><br>
                                                <small class="text-muted"><?=date('g:i A', strtotime($document['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <?=e($document['uploaded_by_name'] ?? 'System'); ?><br>
                                                <small class="text-muted"><?=ucfirst($document['uploaded_by_role'] ?? 'System'); ?></small>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= htmlspecialchars("formatFileSize(document['file_size']); ") ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?=e($document['url']); ?>" 
                                                       class="btn btn-outline-primary" target="_blank">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?=e($document['url']); ?>" 
                                                       class="btn btn-outline-success" download>
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-file-earmark-x display-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No Document History</h5>
                                <p class="text-muted">No documents have been uploaded for this vehicle.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Details Modal -->
<div class="modal fade" id="transferDetailsModal" tabindex="-1" aria-labelledby="transferDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferDetailsModalLabel">Transfer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="transferDetailsContent">
                <!-- Transfer details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<style>
.timeline-vertical {
    position: relative;
    padding-left: 60px;
}

.timeline-item-vertical {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker-vertical {
    position: absolute;
    left: -60px;
    top: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    border: 3px solid white;
    box-shadow: 0 0 0 3px currentColor;
}

.timeline-content-vertical {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid currentColor;
}

.timeline-item-vertical:not(:last-child) .timeline-content-vertical::after {
    content: '';
    position: absolute;
    left: -40px;
    top: 40px;
    bottom: -30px;
    width: 2px;
    background: #e9ecef;
}

.timeline-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 10px;
}

.timeline-header h6 {
    flex: 1;
    margin-bottom: 0;
}

.user-avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
}

.nav-pills .nav-link.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.timeline-details {
    background: white;
    padding: 10px;
    border-radius: 5px;
    border-left: 3px solid #0d6efd;
}

@media print {
    .btn, .nav, .breadcrumb, .card-header .form-check {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        break-inside: avoid;
    }
    
    .timeline-content-vertical {
        background: white !important;
        border: 1px solid #000 !important;
    }
}
</style>
<?php $styles = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeVehicleHistory();
});

function initializeVehicleHistory() {
    // Timeline details toggle
    const showDetailsToggle = document.getElementById('showDetails');
    showDetailsToggle.addEventListener('change', function() {
        const details = document.querySelectorAll('.timeline-details');
        details.forEach(detail => {
            detail.style.display = this.checked ? 'block' : 'none';
        });
    });

    // Filter timeline by event type
    setupTimelineFilters();
}

function setupTimelineFilters() {
    // This would set up filtering for different event types
    // Implementation depends on specific filtering requirements
}

function exportHistory() {
    App.showToast('Preparing history export...', 'info');
    
    // Generate export URL with current tab and filters
    const activeTab = document.querySelector('#historyTabs .nav-link.active').id.replace('-tab', '');
    const exportUrl = `/vehicles/<?=$vehicle['id']; ?>/history/export?tab=${activeTab}`;
    
    window.location.href = exportUrl;
}

function viewTransferDetails(transferId) {
    const modal = new bootstrap.Modal(document.getElementById('transferDetailsModal'));
    const modalContent = document.getElementById('transferDetailsContent');
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <p class="text-muted mt-2">Loading transfer details...</p>
        </div>
    `;
    
    // Fetch transfer details
    fetch(`/vehicles/transfers/${transferId}/details`)
        .then(response => response.json())
        .then(data => {
            modalContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Transfer Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Transfer ID:</strong></td>
                                <td>${data.id}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td><span class="badge bg-primary">${data.transfer_type}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td><span class="badge bg-success">${data.status}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td>${new Date(data.created_at).toLocaleString()}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Parties Involved</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>From:</strong></td>
                                <td>${data.from_user.name}<br><small>${data.from_user.email}</small></td>
                            </tr>
                            <tr>
                                <td><strong>To:</strong></td>
                                <td>${data.to_user.name}<br><small>${data.to_user.email}</small></td>
                            </tr>
                        </table>
                    </div>
                </div>
                ${data.amount ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Financial Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Amount:</strong></td>
                                <td>₦${parseFloat(data.amount).toLocaleString()}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                ` : ''}
                ${data.notes ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Transfer Notes</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0">${data.notes}</p>
                            </div>
                        </div>
                    </div>
                </div>
                ` : ''}
            `;
        })
        .catch(error => {
            console.error('Error loading transfer details:', error);
            modalContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load transfer details. Please try again.
                </div>
            `;
        });
    
    modal.show();
}

// Search and filter functionality
function filterTimeline(eventType) {
    const timelineItems = document.querySelectorAll('.timeline-item-vertical');
    
    timelineItems.forEach(item => {
        if (eventType === 'all' || item.getAttribute('data-event-type') === eventType) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Print optimized history
function printHistory() {
    window.print();
}
</script>

<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>