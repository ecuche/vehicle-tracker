<?php
$title = "Admin Dashboard";
$hide_back = true;
ob_start();
?>
<!-- Admin Stats Overview -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number text-success" id="totalUsers"><?= $stats['total_users'] ?? 0; ?></div>
            <div class="stats-label">Total Users</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number text-info" id="totalVehicles"><?= $stats['total_vehicles'] ?? 0; ?></div>
            <div class="stats-label">Total Vehicles</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number text-danger" id="totalTransfers"><?= $stats['total_transfers'] ?? 0; ?></div>
            <div class="stats-label">Total Transfers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number text-warning" id="pendingTransfers"><?= $stats['total_status'] ?? 0; ?></div>
            <div class="stats-label">Total Status Change </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Charts Section -->
    <div class="col-lg-8">
        <!-- Vehicle Registrations Chart -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Vehicle Registrations (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="registrationsChart" height="250"></canvas>
            </div>
        </div>

        <!-- Transfers Chart -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Vehicle Transfers (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="transfersChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions & System Status -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= $_ENV['APP_URL'] ?>/admin/users" class="btn btn-outline-primary text-start">
                        <i class="bi bi-people me-2"></i> User Management
                    </a>
                    <a href="<?= $_ENV['APP_URL'] ?>/admin/vehicles" class="btn btn-outline-info text-start">
                        <i class="bi bi-truck me-2"></i> Vehicle Management
                    </a>
                    <a href="<?= $_ENV['APP_URL'] ?>/admin/audit" class="btn btn-outline-secondary text-start">
                        <i class="bi bi-clipboard-data me-2"></i> Audit Trail
                    </a>
                    <a href="<?= $_ENV['APP_URL'] ?>/search" class="btn btn-outline-success text-start">
                        <i class="bi bi-search me-2"></i> Vehicle Search
                    </a>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">System Status</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Database</span>
                        <span class="badge bg-success" id="dbStatus">Online</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Email Service</span>
                        <span class="badge bg-success" id="emailStatus">Online</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>File Uploads</span>
                        <span class="badge bg-success" id="uploadStatus">Online</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Server Load</span>
                        <span class="badge bg-success" id="serverLoad">Normal</span>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <small class="text-muted" id="lastUpdated">
                        Last updated: <?= date('H:i:s'); ?>
                    </small>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Admin Activity</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_activity)): ?>
                <p class="text-muted text-center mb-0">No recent activity</p>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recent_activity as $activity): ?>
                    <div class="list-group-item px-0">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1 small"><?= e($activity['action']); ?></h6>
                            <small class="text-muted"><?= relative_time($activity['timestamp']); ?></small>
                        </div>
                        <p class="mb-1 small text-muted"><?= e($activity['description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- System Alerts -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">System Alerts</h5>
            </div>
            <div class="card-body">
                <div id="systemAlerts">
                    <!-- Alerts will be loaded here -->
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadSystemAlerts()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh Alerts
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vehicle Status Distribution -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Vehicle Status Distribution</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <?php if (isset($stats['vehicles_by_status']) && !empty($stats['vehicles_by_status'])): ?>
                    <?php foreach ($stats['vehicles_by_status'] as $status): ?>
                    <div class="col-md-2 mb-3">
                        <div class="card bg-light">
                            <div class="card-body py-3">
                                <div class="stats-number"><?= $status->count; ?></div>
                                <div class="stats-label small">
                                    <?php 
                                    $statusLabels = [
                                        'none' => 'Normal',
                                        'stolen' => 'Stolen',
                                        'no_customs_duty' => 'No Customs',
                                        'changed_engine' => 'Changed Engine',
                                        'changed_color' => 'Changed Color'
                                    ];
                                    echo $statusLabels[$status->current_status] ?? ucfirst($status->current_status);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="col-12">
                        <p class="text-muted text-center mb-0">No vehicle status data available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts
    initializeCharts();
    
    // Load system status
    loadSystemStatus();
    
    // Load system alerts
    loadSystemAlerts();
    
    // Refresh data every 60 seconds
    setInterval(() => {
        loadSystemStatus();
        loadSystemAlerts();
        updateStats();
    }, 60000);
});

</script>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>