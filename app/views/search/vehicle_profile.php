<?php
$title = "Vehicle Profile - " . ($vehicle['vin'] ?? 'Unknown Vehicle');
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $_ENV['APP_URL'] ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= $_ENV['APP_URL'] ?>/search">Vehicle Search</a></li>
            <li class="breadcrumb-item active" aria-current="page">Vehicle Profile</li>
        </ol>
    </nav>

    <!-- Vehicle Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-1">
                                <?= e($vehicle['make'] ?? 'Unknown'); ?> <?= e($vehicle['model'] ?? ''); ?> (<?= e($vehicle['year']) ?? ''; ?>)
                            </h1>
                            <p class="text-muted mb-0">
                                VIN: <?= e($vehicle['vin'] ?? 'N/A'); ?> | 
                                Current Plate: <?= e($vehicle['current_plate_number'] ?? 'N/A'); ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php
                            $statusBadges = [
                                'none' => ['class' => 'bg-success', 'label' => 'Normal'],
                                'stolen' => ['class' => 'bg-danger', 'label' => 'Stolen'],
                                'no_customs_duty' => ['class' => 'bg-warning', 'label' => 'No Customs Duty'],
                                'changed_engine' => ['class' => 'bg-info', 'label' => 'Changed Engine'],
                                'changed_color' => ['class' => 'bg-secondary', 'label' => 'Changed Color']
                            ];
                            $currentStatus = $vehicle['current_status'] ?? 'none';
                            $statusInfo = $statusBadges[$currentStatus] ?? ['class' => 'bg-secondary', 'label' => ucfirst($currentStatus)];
                            ?>
                            <span class="badge <?= $statusInfo['class']; ?> fs-6">
                                <?= $statusInfo['label']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Vehicle Information -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-car-front me-2"></i>Vehicle Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong>VIN Number:</strong><br>
                            <code class="fs-5"><?= e($vehicle['vin'] ?? 'N/A'); ?></code>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong>Current Plate:</strong><br>
                            <span class="fs-5"><?= e($vehicle['current_plate_number'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong>Make:</strong><br>
                            <?= e($vehicle['make'] ?? 'N/A'); ?>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong>Model:</strong><br>
                            <?= e($vehicle['model'] ?? 'N/A'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong>Year:</strong><br>
                            <?= e($vehicle['year'] ?? 'N/A'); ?>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong>Registration Date:</strong><br>
                            <?= e(isset($vehicle['created_at']) ? date('M j, Y', strtotime($vehicle['created_at'])) : 'N/A'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <strong>Current Status:</strong><br>
                            <span class="badge <?= $statusInfo['class']; ?>">
                                <?= $statusInfo['label']; ?>
                            </span>
                            <?php if (isset($vehicle['status_updated_at'])): ?>
                                <small class="text-muted ms-2">
                                    (Updated: <?= date('M j, Y', strtotime($vehicle['status_updated_at'])); ?>)
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Owner -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>Current Owner
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($current_owner) && $current_owner): ?>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong>Name:</strong><br>
                            <?= e($current_owner['name'] ?? 'N/A'); ?>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong>Role:</strong><br>
                            <span class="badge bg-info"><?= ucfirst($current_owner['role'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong>Email:</strong><br>
                            <?php if (isset($current_owner['email'])): ?>
                                <a href="mailto:<?= e($current_owner['email']); ?>">
                                    <?= e($current_owner['email']); ?>
                                </a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong>Phone:</strong><br>
                            <?php if (isset($current_owner['phone'])): ?>
                                <a href="tel:<?= e($current_owner['phone']); ?>">
                                    <?= e($current_owner['phone']); ?>
                                </a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong>NIN:</strong><br>
                            <?= e($current_owner['nin'] ?? 'N/A'); ?>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong>Ownership Since:</strong><br>
                            <?= e(isset($vehicle['current_owner_since']) ? date('M j, Y', strtotime($vehicle['current_owner_since'])) : 'N/A'); ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-3">
                        <i class="bi bi-person-x display-1 text-muted"></i>
                        <p class="text-muted mt-2">No current owner information available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Vehicle Images & Documents -->
        <div class="col-lg-6">
            <!-- Vehicle Images -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-images me-2"></i>Vehicle Images
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($images) && !empty($images)): ?>
                    <div class="row">
                        <?php foreach ($images as $image): ?>
                        <div class="col-6 col-md-4 mb-3">
                            <a href="<?= $_ENV['APP_URL'].'/'.$_ENV['UPLOAD_PATH'].'/'.e($image['image_path']); ?>" data-lightbox="vehicle-images">
                                <img src="<?= $_ENV['APP_URL'].'/'.$_ENV['UPLOAD_PATH'].'/'.e($image['image_path']); ?>" class="img-thumbnail w-100" 
                                     alt="Vehicle image" style="height: 100px; object-fit: cover;">
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-3">
                        <i class="bi bi-image display-1 text-muted"></i>
                        <p class="text-muted mt-2">No vehicle images available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Vehicle Documents -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark me-2"></i>Vehicle Documents
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($documents) && !empty($documents)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($documents as $document): ?>
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-file-earmark-text me-2"></i>
                                    <span><?= e($document['document_type']); ?></span>
                                </div>
                                <a href="<?= $_ENV['APP_URL'].'/'.$_ENV['UPLOAD_PATH'].'/'.e($document['file_path']); ?>" class="btn btn-sm btn-outline-primary" download>
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-3">
                        <i class="bi bi-file-earmark-x display-1 text-muted"></i>
                        <p class="text-muted mt-2">No vehicle documents available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Ownership History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>Ownership History
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($ownership_history) && !empty($ownership_history)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Owner</th>
                                    <th>Contact</th>
                                    <th>Ownership Period</th>
                                    <th>Transfer Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ownership_history as $history): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($history['owner_name']); ?></strong><br>
                                        <small class="text-muted"><?= ucfirst($history['owner_role']); ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <?php if ($history['owner_email']): ?>
                                            <div><?= e($history['owner_email']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($history['owner_phone']): ?>
                                            <div><?= e($history['owner_phone']); ?></div>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?= date('M j, Y', strtotime($history['start_date'])); ?><br>
                                        <small class="text-muted">
                                            <?= $history['end_date'] ? 
                                                'to ' . date('M j, Y', strtotime($history['end_date'])) : 
                                                'to Present'; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $history['transfer_type'] === 'registration' ? 'success' : 'info'; ?>">
                                            <?= ucfirst($history['transfer_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $history['status'] === 'completed' ? 'success' : 'secondary'; ?>">
                                            <?= ucfirst($history['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-clock-history display-1 text-muted"></i>
                        <p class="text-muted mt-2">No ownership history available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Plate Number History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-123 me-2"></i>Plate Number History
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($plate_history) && !empty($plate_history)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Plate Number</th>
                                    <th>Assigned Date</th>
                                    <th>Assigned By</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($plate_history as $plate): ?>
                                <tr class="<?= $plate['is_current'] ? 'table-active' : ''; ?>">
                                    <td>
                                        <strong><?= e($plate['plate_number']); ?></strong>
                                        <?php if ($plate['is_current']): ?>
                                        <span class="badge bg-success ms-1">Current</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($plate['created_at'])); ?></td>
                                    <td>
                                        <?= e($plate['assigned_by_name'] ?? 'System'); ?>
                                        <?php if (isset($plate['assigned_by_role'])): ?>
                                        <br><small class="text-muted">(<?= ucfirst($plate['assigned_by_role']); ?>)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $plate['is_current'] ? 'success' : 'secondary'; ?>">
                                            <?= $plate['is_current'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-123 display-1 text-muted"></i>
                        <p class="text-muted mt-2">No plate number history available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Status History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Status History
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($status_history) && !empty($status_history)): ?>
                    <div class="timeline">
                        <?php foreach ($status_history as $status): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">
                                        <?php
                                        $statusLabels = [
                                            'none' => 'Normal',
                                            'stolen' => 'Marked as Stolen',
                                            'no_customs_duty' => 'No Customs Duty',
                                            'changed_engine' => 'Engine Changed',
                                            'changed_color' => 'Color Changed'
                                        ];
                                        echo $statusLabels[$status['status']] ?? ucfirst($status['status']);
                                        ?>
                                    </h6>
                                    <small class="text-muted"><?= date('M j, Y g:i A', strtotime($status['changed_at'])); ?></small>
                                </div>
                                <p class="mb-1 small text-muted">
                                    Changed by: <?= e($status['changed_by_name'] ?? 'System'); ?>
                                    <?php if (isset($status['changed_by_role'])): ?>
                                    (<?= ucfirst($status['changed_by_role']); ?>)
                                    <?php endif; ?>
                                </p>
                                <?php if ($status['reason']): ?>
                                <p class="mb-0 small">Reason: <?= e($status['reason']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-info-circle display-1 text-muted"></i>
                        <p class="text-muted mt-2">No status history available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Trail -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clipboard-data me-2"></i>Recent Activity
                    </h5>
                    <small class="text-muted">Last 10 activities</small>
                </div>
                <div class="card-body">
                    <?php if (isset($vehicle['recent_activity']) && !empty($vehicle['recent_activity'])): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($vehicle['recent_activity'] as $activity): ?>
                        <div class="list-group-item px-0">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 small text-capitalize">
                                    <?= str_replace('_', ' ', $activity['action']); ?>
                                </h6>
                                <small class="text-muted"><?= date('M j, Y g:i A', strtotime($activity['created_at'])); ?></small>
                            </div>
                            <p class="mb-1 small text-muted">
                                By: <?= e($activity['user_name'] ?? 'System'); ?>
                                <?php if (isset($activity['user_role'])): ?>
                                (<?= ucfirst($activity['user_role']); ?>)
                                <?php endif; ?>
                            </p>
                            <?php if ($activity['description']): ?>
                            <p class="mb-0 small"><?= e($activity['description']); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-3">
                        <i class="bi bi-clipboard-data display-1 text-muted"></i>
                        <p class="text-muted mt-2">No recent activity found</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #0d6efd;
    border: 2px solid white;
    box-shadow: 0 0 0 3px #0d6efd;
}

.timeline-content {
    padding-bottom: 20px;
    border-left: 2px solid #e9ecef;
    padding-left: 20px;
}

.timeline-item:last-child .timeline-content {
    border-left: 2px solid transparent;
}
</style>
<?php $styles = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize lightbox for images
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'imageFadeDuration': 300
        });
    }
    
    // Print functionality
    document.getElementById('printBtn')?.addEventListener('click', function() {
        window.print();
    });
});
</script>

<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>