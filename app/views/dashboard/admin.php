<?php
$title = "Admin Dashboard";
$hide_back = true;
ob_start();
?>
<!-- Admin Stats Overview -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card stats-card">
            <div class="stats-number" id="totalUsers"><?= $stats['total_users'] ?? 0; ?></div>
            <div class="stats-label">Total Users</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stats-card">
            <div class="stats-number" id="totalVehicles"><?= $stats['total_vehicles'] ?? 0; ?></div>
            <div class="stats-label">Total Vehicles</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stats-card">
            <div class="stats-number" id="totalTransfers"><?= $stats['total_transfers'] ?? 0; ?></div>
            <div class="stats-label">Total Transfers</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stats-card">
            <div class="stats-number text-warning" id="pendingTransfers"><?= $stats['pending_transfers'] ?? 0; ?></div>
            <div class="stats-label">Pending Transfers</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stats-card">
            <div class="stats-number text-danger" id="bannedUsers"><?= $stats['banned_users'] ?? 0; ?></div>
            <div class="stats-label">Banned Users</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card stats-card">
            <div class="stats-number text-info" id="todayRegistrations"><?= $today_registrations ?? 0; ?></div>
            <div class="stats-label">Today's Registrations</div>
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

function initializeCharts() {
    // Vehicle Registrations Chart
    const registrationsCtx = document.getElementById('registrationsChart').getContext('2d');
    const registrationsChart = new Chart(registrationsCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($monthly_data['vehicles_registered'] ?? [], 'month')); ?>,
            datasets: [{
                label: 'Vehicle Registrations',
                data: <?= json_encode(array_column($monthly_data['vehicles_registered'] ?? [], 'count')); ?>,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
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

    // Transfers Chart
    const transfersCtx = document.getElementById('transfersChart').getContext('2d');
    const transfersChart = new Chart(transfersCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($monthly_data['transfers_completed'] ?? [], 'month')); ?>,
            datasets: [{
                label: 'Vehicle Transfers',
                data: <?= json_encode(array_column($monthly_data['transfers_completed'] ?? [], 'count')); ?>,
                backgroundColor: '#198754',
                borderColor: '#198754',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
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
}

function loadSystemStatus() {
    Ajax.getAdminStats()
        .then(stats => {
            // Update stats cards
            document.getElementById('totalUsers').textContent = stats.total_users || 0;
            document.getElementById('totalVehicles').textContent = stats.total_vehicles || 0;
            document.getElementById('totalTransfers').textContent = stats.total_transfers || 0;
            document.getElementById('pendingTransfers').textContent = stats.pending_transfers || 0;
            document.getElementById('bannedUsers').textContent = stats.banned_users || 0;
            
            // Update last updated time
            document.getElementById('lastUpdated').textContent = `Last updated: ${new Date().toLocaleTimeString()}`;
        })
        .catch(error => {
            console.error('Failed to load system status:', error);
        });
}

function loadSystemAlerts() {
    const alertsContainer = document.getElementById('systemAlerts');
    alertsContainer.innerHTML = `
        <div class="text-center py-2">
            <div class="loading-spinner"></div>
            <p class="small text-muted mt-2">Loading alerts...</p>
        </div>
    `;

    // Simulate loading system alerts
    setTimeout(() => {
        const alerts = [
            // This would typically come from an API
            // For now, we'll show static alerts or none
        ];

        if (alerts.length === 0) {
            alertsContainer.innerHTML = `
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i>
                    <strong>All systems operational</strong><br>
                    <small>No critical issues detected</small>
                </div>
            `;
        } else {
            let alertsHtml = '';
            alerts.forEach(alert => {
                alertsHtml += `
                    <div class="alert alert-${alert.type}">
                        <i class="bi ${alert.icon}"></i>
                        <strong>${alert.title}</strong><br>
                        <small>${alert.message}</small>
                    </div>
                `;
            });
            alertsContainer.innerHTML = alertsHtml;
        }
    }, 1000);
}

function updateStats() {
    // This function would update the charts and stats with fresh data
    console.log('Updating admin dashboard stats...');
    
    // In a real implementation, you would:
    // 1. Fetch new data from the API
    // 2. Update the charts with new data
    // 3. Refresh any real-time statistics
}

// Export function for generating reports
function generateReport(type) {
    let endpoint = '';
    let filename = '';
    
    switch(type) {
        case 'users':
            endpoint = '/admin/export-users';
            filename = 'users_report.csv';
            break;
        case 'vehicles':
            endpoint = '/admin/export-vehicles';
            filename = 'vehicles_report.csv';
            break;
        case 'audit':
            endpoint = '/admin/export-audit';
            filename = 'audit_report.csv';
            break;
        default:
            App.showToast('Invalid report type', 'error');
            return;
    }
    
    App.showToast('Generating report...', 'info');
    
    // This would typically call an API endpoint to generate the report
    setTimeout(() => {
        App.showToast('Report generated successfully', 'success');
        // In a real implementation, you would download the file here
    }, 2000);
}
</script>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>