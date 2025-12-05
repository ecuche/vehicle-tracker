<?php
$title = "User Audit - " . ($user['name'] ?? 'Unknown User');
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/users">User Management</a></li>
            <li class="breadcrumb-item"><a href="/admin/users/<?php echo $user['id']; ?>">User Profile</a></li>
            <li class="breadcrumb-item active" aria-current="page">User Audit Trail</li>
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
                                <i class="bi bi-clipboard-data me-2"></i>User Audit Trail
                            </h1>
                            <p class="text-muted mb-0">
                                Complete audit history for <?php echo e($user['name']); ?> (<?php echo e($user['email']); ?>)
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge bg-primary fs-6">
                                <i class="bi bi-clock-history me-1"></i>
                                <?php echo $stats['total_activities'] ?? 0; ?> Activities
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="user-avatar-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="bi bi-person fs-1"></i>
                            </div>
                            <h5><?php echo e($user['name']); ?></h5>
                            <p class="text-muted mb-0"><?php echo e($user['email']); ?></p>
                            <span class="badge bg-info mt-1"><?php echo ucfirst($user['role']); ?></span>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-primary"><?php echo $stats['today_activities'] ?? 0; ?></div>
                                            <div class="text-muted">Today</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-success"><?php echo $stats['this_week_activities'] ?? 0; ?></div>
                                            <div class="text-muted">This Week</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-info"><?php echo $stats['this_month_activities'] ?? 0; ?></div>
                                            <div class="text-muted">This Month</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body text-center">
                                            <div class="display-6 text-warning"><?php echo $stats['failed_logins'] ?? 0; ?></div>
                                            <div class="text-muted">Failed Logins</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex">
                                        <a href="/admin/users/<?php echo $user['id']; ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-left me-1"></i> Back to User
                                        </a>
                                        <button type="button" class="btn btn-outline-success" onclick="exportUserAudit()">
                                            <i class="bi bi-download me-1"></i> Export Audit
                                        </button>
                                        <button type="button" class="btn btn-outline-warning" onclick="clearUserAudit()">
                                            <i class="bi bi-trash me-1"></i> Clear Old Logs
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="refreshAudit()">
                                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filter Audit Logs</h5>
                </div>
                <div class="card-body">
                    <form id="auditFilterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="action" class="form-label">Action Type</label>
                                    <select class="form-select" id="action" name="action">
                                        <option value="">All Actions</option>
                                        <option value="login">Login</option>
                                        <option value="logout">Logout</option>
                                        <option value="failed_login">Failed Login</option>
                                        <option value="register">Registration</option>
                                        <option value="update_profile">Profile Update</option>
                                        <option value="change_password">Password Change</option>
                                        <option value="vehicle_register">Vehicle Registration</option>
                                        <option value="vehicle_transfer">Vehicle Transfer</option>
                                        <option value="document_upload">Document Upload</option>
                                        <option value="status_change">Status Change</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Status</option>
                                        <option value="success">Success</option>
                                        <option value="failed">Failed</option>
                                        <option value="warning">Warning</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date_from" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date_to" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           placeholder="Search in details, IP address, user agent...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid gap-2 d-md-flex">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-filter me-1"></i> Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Audit Logs</h5>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <select class="form-select form-select-sm" id="perPage" onchange="changePerPage(this.value)">
                                <option value="20">20 per page</option>
                                <option value="50">50 per page</option>
                                <option value="100">100 per page</option>
                                <option value="200">200 per page</option>
                            </select>
                        </div>
                        <div class="text-muted small">
                            Showing <?php echo $pagination['start']; ?>-<?php echo $pagination['end']; ?> of <?php echo $pagination['total']; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($audit_logs)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-clipboard-data display-1 text-muted"></i>
                        <h5 class="text-muted mt-3">No Audit Logs Found</h5>
                        <p class="text-muted">No audit logs match your filter criteria.</p>
                        <button type="button" class="btn btn-primary mt-2" onclick="resetFilters()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Filters
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Action</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($audit_logs as $log): ?>
                                <tr>
                                    <td>
                                        <small>
                                            <?php echo date('M j, Y', strtotime($log['created_at'])); ?><br>
                                            <?php echo date('g:i A', strtotime($log['created_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo getActionBadgeClass($log['action']); ?>">
                                            <?php echo formatAction($log['action']); ?>
                                        </span>
                                        <?php if ($log['table_name']): ?>
                                        <br>
                                        <small class="text-muted">on <?php echo formatTableName($log['table_name']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo getStatusBadgeClass($log['status']); ?>">
                                            <?php echo ucfirst($log['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?php if ($log['description']): ?>
                                            <div><?php echo e($log['description']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($log['old_values'] || $log['new_values']): ?>
                                            <button type="button" class="btn btn-sm btn-outline-info mt-1" 
                                                    onclick="viewChanges(<?php echo $log['id']; ?>)">
                                                <i class="bi bi-arrows-collapse"></i> View Changes
                                            </button>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <code class="small"><?php echo e($log['ip_address']); ?></code>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo truncateUserAgent($log['user_agent']); ?></small>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                onclick="viewAuditDetails(<?php echo $log['id']; ?>)">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                    <nav aria-label="Audit logs pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $pagination['current_page'] == 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo buildPageUrlAudit($pagination['current_page'] - 1); ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <?php if ($i == 1 || $i == $pagination['total_pages'] || abs($i - $pagination['current_page']) <= 2): ?>
                                    <li class="page-item <?php echo $pagination['current_page'] == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo buildPageUrlAudit($i); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php elseif (abs($i - $pagination['current_page']) == 3): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $pagination['current_page'] == $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo buildPageUrlAudit($pagination['current_page'] + 1); ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Statistics -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Activity Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Activity Distribution</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Action Type</th>
                                            <th>Count</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($activity_stats['by_action'] as $action): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-<?php echo getActionBadgeClass($action['action']); ?>">
                                                    <?php echo formatAction($action['action']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $action['count']; ?></td>
                                            <td>
                                                <?php 
                                                $percentage = $activity_stats['total'] > 0 ? ($action['count'] / $activity_stats['total']) * 100 : 0;
                                                ?>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-<?php echo getActionBadgeClass($action['action']); ?>" 
                                                         role="progressbar" style="width: <?php echo $percentage; ?>%;">
                                                        <?php echo round($percentage, 1); ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Status Distribution</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Count</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($activity_stats['by_status'] as $status): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-<?php echo getStatusBadgeClass($status['status']); ?>">
                                                    <?php echo ucfirst($status['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $status['count']; ?></td>
                                            <td>
                                                <?php 
                                                $percentage = $activity_stats['total'] > 0 ? ($status['count'] / $activity_stats['total']) * 100 : 0;
                                                ?>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-<?php echo getStatusBadgeClass($status['status']); ?>" 
                                                         role="progressbar" style="width: <?php echo $percentage; ?>%;">
                                                        <?php echo round($percentage, 1); ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6>Activity Timeline</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Time Period</th>
                                                    <th>Activities</th>
                                                    <th>Trend</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($activity_stats['by_hour'] as $hour): ?>
                                                <tr>
                                                    <td><?php echo $hour['hour']; ?>:00 - <?php echo $hour['hour']; ?>:59</td>
                                                    <td><?php echo $hour['count']; ?></td>
                                                    <td>
                                                        <?php 
                                                        $max = max(array_column($activity_stats['by_hour'], 'count'));
                                                        $width = $max > 0 ? ($hour['count'] / $max) * 100 : 0;
                                                        ?>
                                                        <div class="progress" style="height: 10px;">
                                                            <div class="progress-bar bg-info" 
                                                                 role="progressbar" style="width: <?php echo $width; ?>%;">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audit Details Modal -->
<div class="modal fade" id="auditDetailsModal" tabindex="-1" aria-labelledby="auditDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="auditDetailsModalLabel">Audit Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="auditDetailsContent">
                <!-- Audit details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printAuditDetails()">
                    <i class="bi bi-printer me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Changes Modal -->
<div class="modal fade" id="changesModal" tabindex="-1" aria-labelledby="changesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changesModalLabel">Change Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="changesContent">
                <!-- Changes will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Clear Logs Confirmation Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearLogsModalLabel">Clear Old Audit Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>This action will permanently delete old audit logs!</strong>
                </div>
                <p>You are about to clear audit logs older than the specified period.</p>
                
                <div class="mb-3">
                    <label for="clear_period" class="form-label">Clear logs older than:</label>
                    <select class="form-select" id="clear_period">
                        <option value="30">30 days</option>
                        <option value="90">90 days</option>
                        <option value="180">180 days</option>
                        <option value="365">1 year</option>
                    </select>
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Approximately <span id="logsToDelete">0</span> logs will be deleted.
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirm_clear" required>
                    <label class="form-check-label" for="confirm_clear">
                        I understand this action cannot be undone
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmClearBtn" disabled onclick="confirmClearLogs()">
                    <i class="bi bi-trash me-1"></i> Clear Logs
                </button>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
ob_start();
?>
<style>
.user-avatar-lg {
    width: 100px;
    height: 100px;
}

.progress {
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    border-radius: 4px;
    color: white;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Action badge colors */
.bg-login { background-color: #198754; }
.bg-logout { background-color: #6c757d; }
.bg-failed_login { background-color: #dc3545; }
.bg-register { background-color: #0d6efd; }
.bg-update_profile { background-color: #0dcaf0; }
.bg-change_password { background-color: #ffc107; }
.bg-vehicle_register { background-color: #6610f2; }
.bg-vehicle_transfer { background-color: #20c997; }
.bg-document_upload { background-color: #fd7e14; }
.bg-status_change { background-color: #d63384; }
</style>

<?php
$styles = ob_get_clean();
ob_start();
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeUserAudit();
});

function initializeUserAudit() {
    // Filter form submission
    const filterForm = document.getElementById('auditFilterForm');
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        applyFilters();
    });

    // Date validation
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    
    if (dateFrom && dateTo) {
        dateFrom.addEventListener('change', function() {
            if (dateTo.value && this.value > dateTo.value) {
                dateTo.value = this.value;
            }
        });
        
        dateTo.addEventListener('change', function() {
            if (dateFrom.value && this.value < dateFrom.value) {
                dateFrom.value = this.value;
            }
        });
    }

    // Clear logs confirmation
    const confirmClear = document.getElementById('confirm_clear');
    const confirmClearBtn = document.getElementById('confirmClearBtn');
    const clearPeriod = document.getElementById('clear_period');
    
    if (confirmClear && confirmClearBtn) {
        confirmClear.addEventListener('change', function() {
            confirmClearBtn.disabled = !this.checked;
        });
        
        clearPeriod.addEventListener('change', updateLogsToDelete);
        updateLogsToDelete();
    }
}

function applyFilters() {
    const form = document.getElementById('auditFilterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    for (const [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    
    // Reload page with filters
    window.location.href = `/admin/users/<?php echo $user['id']; ?>/audit?${params.toString()}`;
}

function resetFilters() {
    window.location.href = `/admin/users/<?php echo $user['id']; ?>/audit`;
}

function changePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.set('page', 1); // Reset to first page
    window.location.href = url.toString();
}

function refreshAudit() {
    window.location.reload();
}

function exportUserAudit() {
    VehicleTrackerApp.showToast('Preparing audit export...', 'info');
    
    const url = new URL(window.location.href);
    url.pathname = `/admin/users/<?php echo $user['id']; ?>/audit/export`;
    
    window.location.href = url.toString();
}

function clearUserAudit() {
    const modal = new bootstrap.Modal(document.getElementById('clearLogsModal'));
    modal.show();
}

function updateLogsToDelete() {
    const period = document.getElementById('clear_period').value;
    
    // Fetch estimated count
    fetch(`/admin/users/<?php echo $user['id']; ?>/audit/old-count?days=${period}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('logsToDelete').textContent = data.count || 0;
        })
        .catch(error => {
            console.error('Error fetching log count:', error);
            document.getElementById('logsToDelete').textContent = 'unknown';
        });
}

function confirmClearLogs() {
    const period = document.getElementById('clear_period').value;
    const confirmBtn = document.getElementById('confirmClearBtn');
    
    if (!confirm('Are you sure you want to permanently delete these logs?')) {
        return;
    }
    
    // Disable button and show loading
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Clearing...';
    
    fetch(`/admin/users/<?php echo $user['id']; ?>/audit/clear`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            days: period
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            VehicleTrackerApp.showToast('Old logs cleared successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('clearLogsModal')).hide();
            
            // Reload page after delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.error || 'Failed to clear logs');
        }
    })
    .catch(error => {
        console.error('Clear logs error:', error);
        VehicleTrackerApp.showToast(error.message, 'error');
        
        // Re-enable button
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = '<i class="bi bi-trash me-1"></i> Clear Logs';
    });
}

function viewAuditDetails(logId) {
    const modal = new bootstrap.Modal(document.getElementById('auditDetailsModal'));
    const modalContent = document.getElementById('auditDetailsContent');
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <p class="text-muted mt-2">Loading audit details...</p>
        </div>
    `;
    
    fetch(`/admin/audit/${logId}/details`)
        .then(response => response.json())
        .then(data => {
            modalContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Basic Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Log ID:</strong></td>
                                <td>${data.id}</td>
                            </tr>
                            <tr>
                                <td><strong>Action:</strong></td>
                                <td><span class="badge bg-${getActionBadgeClass(data.action)}">${formatAction(data.action)}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td><span class="badge bg-${getStatusBadgeClass(data.status)}">${data.status}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Table:</strong></td>
                                <td>${data.table_name || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Record ID:</strong></td>
                                <td>${data.record_id || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Timestamp:</strong></td>
                                <td>${new Date(data.created_at).toLocaleString()}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Technical Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>IP Address:</strong></td>
                                <td><code>${data.ip_address}</code></td>
                            </tr>
                            <tr>
                                <td><strong>User Agent:</strong></td>
                                <td><small>${data.user_agent || 'N/A'}</small></td>
                            </tr>
                            <tr>
                                <td><strong>Location:</strong></td>
                                <td>${data.location || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Device:</strong></td>
                                <td>${data.device || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Browser:</strong></td>
                                <td>${data.browser || 'N/A'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                ${data.description ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Description</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0">${data.description}</p>
                            </div>
                        </div>
                    </div>
                </div>
                ` : ''}
                ${data.old_values || data.new_values ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Data Changes</h6>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="viewChanges(${data.id})">
                            <i class="bi bi-arrows-collapse me-1"></i> View Changes
                        </button>
                    </div>
                </div>
                ` : ''}
            `;
        })
        .catch(error => {
            console.error('Error loading audit details:', error);
            modalContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load audit details
                </div>
            `;
        });
    
    modal.show();
}

function viewChanges(logId) {
    const modal = new bootstrap.Modal(document.getElementById('changesModal'));
    const modalContent = document.getElementById('changesContent');
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <p class="text-muted mt-2">Loading change details...</p>
        </div>
    `;
    
    fetch(`/admin/audit/${logId}/changes`)
        .then(response => response.json())
        .then(data => {
            let changesHtml = '';
            
            if (data.old_values && data.new_values) {
                const oldValues = JSON.parse(data.old_values);
                const newValues = JSON.parse(data.new_values);
                const allFields = new Set([...Object.keys(oldValues), ...Object.keys(newValues)]);
                
                changesHtml = `
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Old Value</th>
                                    <th>New Value</th>
                                    <th>Change</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                allFields.forEach(field => {
                    const oldVal = oldValues[field] !== undefined ? oldValues[field] : '<em>None</em>';
                    const newVal = newValues[field] !== undefined ? newValues[field] : '<em>None</em>';
                    const changed = oldValues[field] !== newValues[field];
                    
                    changesHtml += `
                        <tr class="${changed ? 'table-warning' : ''}">
                            <td><strong>${field}</strong></td>
                            <td>${oldVal}</td>
                            <td>${newVal}</td>
                            <td>${changed ? '<span class="badge bg-warning">Changed</span>' : '<span class="badge bg-success">Unchanged</span>'}</td>
                        </tr>
                    `;
                });
                
                changesHtml += `
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                changesHtml = `
                    <div class="text-center py-4">
                        <i class="bi bi-info-circle display-1 text-muted"></i>
                        <p class="text-muted mt-2">No change details available</p>
                    </div>
                `;
            }
            
            modalContent.innerHTML = changesHtml;
        })
        .catch(error => {
            console.error('Error loading changes:', error);
            modalContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load change details
                </div>
            `;
        });
    
    modal.show();
}

function printAuditDetails() {
    const modalContent = document.getElementById('auditDetailsContent');
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Audit Log Details</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; }
                .badge { padding: 4px 8px; border-radius: 4px; color: white; }
                .bg-success { background-color: #198754; }
                .bg-danger { background-color: #dc3545; }
                .bg-warning { background-color: #ffc107; color: #000; }
                code { background-color: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
            </style>
        </head>
        <body>
            <h2>Audit Log Details</h2>
            ${modalContent.innerHTML}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Helper functions for JavaScript
function getActionBadgeClass(action) {
    const actionClasses = {
        'login': 'login',
        'logout': 'logout',
        'failed_login': 'failed_login',
        'register': 'register',
        'update_profile': 'update_profile',
        'change_password': 'change_password',
        'vehicle_register': 'vehicle_register',
        'vehicle_transfer': 'vehicle_transfer',
        'document_upload': 'document_upload',
        'status_change': 'status_change'
    };
    return actionClasses[action] || 'secondary';
}

function getStatusBadgeClass(status) {
    const statusClasses = {
        'success': 'success',
        'failed': 'danger',
        'warning': 'warning',
        'error': 'danger'
    };
    return statusClasses[status] || 'secondary';
}

function formatAction(action) {
    return action.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}
</script>


<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>