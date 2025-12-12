<?php
$title = "Audit Trail";
$actions = '
    <div class="input-group" style="width: 400px;">
        <input type="text" class="form-control" placeholder="Search Action..." id="searchInput" value="' . e($search) . '">
        <select class="form-select" style="width: auto;" id="statusFilter">
            <option value="">All Action</option>
            <option value="update_user_role" ' . ($action === 'update_user_role' ? 'selected' : '') . '>Update User Role</option>
            <option value="ban_user" ' . ($action === 'ban_user' ? 'selected' : '') . '>Ban User</option>
            <option value="unban_user" ' . ($action === 'unban_user' ? 'selected' : '') . '>Unban User</option>
            <option value="update_vehicle_status" ' . ($action === 'update_vehicle_status' ? 'selected' : '') . '>Update Vehicle Status</option>
        </select>
        <button class="btn btn-outline-secondary" type="button" id="searchButton">
            <i class="bi bi-search"></i>
        </button>
    </div>
';
ob_start();
?>
<!-- Stats Overview -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number"><?= $pagination['total']; ?></div>
            <div class="stats-label">Total Logs</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number text-primary"><?= $today_logs; ?></div>
            <div class="stats-label">Today\'s Logs</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number text-info"><?= $user_actions; ?></div>
            <div class="stats-label">User Actions</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number text-warning"><?= $admin_actions; ?></div>
            <div class="stats-label">Admin Actions</div>
        </div>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form id="dateFilterForm" class="row g-3">
            <div class="col-md-3">
                <label for="startDate" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="startDate" name="start_date" value="<?= e($start_date); ?>">
            </div>
            <div class="col-md-3">
                <label for="endDate" class="form-label">End Date</label>
                <input type="date" class="form-control" id="endDate" name="end_date" value="<?= e($end_date); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Apply Filter
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="clearDateFilter()">
                        Clear
                    </button>
                </div>
            </div>
            <div class="col-md-3 text-end">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="button" class="btn btn-success" onclick="exportAudit()">
                        <i class="bi bi-download"></i> Export CSV
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Audit Logs Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Audit Logs</h5>
        
        <div class="d-flex align-items-center">
            <span class="me-2 text-muted">Show:</span>
            <select class="form-select form-select-sm" style="width: auto;" id="perPageSelect">
                <option value="10" <?= $pagination['per_page'] == 10 ? 'selected' : ''; ?>>10</option>
                <option value="15" <?= $pagination['per_page'] == 15 ? 'selected' : ''; ?>>15</option>
                <option value="20" <?= $pagination['per_page'] == 20 ? 'selected' : ''; ?>>20</option>
                <option value="50" <?= $pagination['per_page'] == 50 ? 'selected' : ''; ?>>50</option>
            </select>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (empty($audit_logs)): ?>
        <div class="text-center py-5">
            <i class="bi bi-clipboard-data display-1 text-muted"></i>
            <h4 class="text-muted mt-3">No Audit Logs Found</h4>
            <p class="text-muted">No audit logs match your search criteria.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Record ID</th>
                        <th>IP Address</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($audit_logs as $log): ?>
                    <tr>
                        <td>
                            <small class="text-muted">
                                <?= format_date($log['created_at'], 'M j, Y'); ?><br>
                                <?= format_date($log['created_at'], 'H:i:s'); ?>
                            </small>
                        </td>
                        <td>
                            <?php if ($log['user_email']): ?>
                            <div>
                                <div><?= e($log['user_email']); ?></div>
                                <small class="text-muted"><?= e($log['user_phone']); ?></small>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">System</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= getActionBadgeColor($log['action']); ?>">
                                <?= ucwords(str_replace('_', ' ', $log['action'])); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($log['table_name']): ?>
                            <code><?= e($log['table_name']); ?></code>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($log['record_id']): ?>
                            <span class="badge bg-secondary">#<?= e($log['record_id']); ?></span>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <code><?= e($log['ip_address']); ?></code>
                        </td>
                        <td>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-info" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailsModal<?= $log['id']; ?>">
                                <i class="bi bi-info-circle"></i> Details
                            </button>
                        </td>
                    </tr>

                    <!-- Details Modal -->
                    <div class="modal fade" id="detailsModal<?= $log['id']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Audit Log Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Basic Information</h6>
                                            <dl class="row">
                                                <dt class="col-sm-4">Timestamp:</dt>
                                                <dd class="col-sm-8"><?= format_date($log['created_at'], 'Y-m-d H:i:s'); ?></dd>
                                                
                                                <dt class="col-sm-4">User:</dt>
                                                <dd class="col-sm-8"><?= $log['user_email'] ? e($log['user_email']) : '<span class="text-muted">System</span>'; ?></dd>
                                                
                                                <dt class="col-sm-4">Action:</dt>
                                                <dd class="col-sm-8"><?= ucwords(str_replace('_', ' ', $log['action'])); ?></dd>
                                                
                                                <dt class="col-sm-4">IP Address:</dt>
                                                <dd class="col-sm-8"><code><?= e($log['ip_address']); ?></code></dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Context</h6>
                                            <dl class="row">
                                                <dt class="col-sm-4">Table:</dt>
                                                <dd class="col-sm-8"><?= $log['table_name'] ? '<code>' . e($log['table_name']) . '</code>' : '<span class="text-muted">-</span>'; ?></dd>
                                                
                                                <dt class="col-sm-4">Record ID:</dt>
                                                <dd class="col-sm-8"><?= $log['record_id'] ? '#' . e($log['record_id']) : '<span class="text-muted">-</span>'; ?></dd>
                                                
                                                <dt class="col-sm-4">User Agent:</dt>
                                                <dd class="col-sm-8"><small class="text-muted"><?= e($log['user_agent']); ?></small></dd>
                                            </dl>
                                        </div>
                                    </div>
                                    
                                    <?php if ($log['old_values'] || $log['new_values']): ?>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6>Changes</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Field</th>
                                                            <th>Old Value</th>
                                                            <th>New Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $old_values = $log['old_values'] ? json_decode($log['old_values'], true) : [];
                                                        $new_values = $log['new_values'] ? json_decode($log['new_values'], true) : [];
                                                        $all_fields = array_unique(array_merge(array_keys($old_values), array_keys($new_values)));
                                                        
                                                        foreach ($all_fields as $field):
                                                            $old_value = $old_values[$field] ?? null;
                                                            $new_value = $new_values[$field] ?? null;
                                                        ?>
                                                        <tr>
                                                            <td><strong><?= e(ucwords(str_replace('_', ' ', $field))); ?></strong></td>
                                                            <td>
                                                                <?php if ($old_value !== null): ?>
                                                                <span class="text-danger"><?= e(is_array($old_value) ? json_encode($old_value) : $old_value); ?></span>
                                                                <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($new_value !== null): ?>
                                                                <span class="text-success"><?= e(is_array($new_value) ? json_encode($new_value) : $new_value); ?></span>
                                                                <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <?= pagination_links($pagination['page'], $pagination['total_pages'], '/admin/audit', $pagination['per_page']); ?>
            </div>
            <div class="text-muted">
                Page <?= $pagination['page']; ?> of <?= $pagination['total_pages']; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
function exportAudit() {
    const search = document.getElementById('searchInput').value;
    const action = document.getElementById('actionFilter').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    const filters = {};
    if (search) filters.search = search;
    if (action) filters.action = action;
    if (startDate) filters.start_date = startDate;
    if (endDate) filters.end_date = endDate;
    
    Ajax.exportAuditToCSV(filters);
}

function clearDateFilter() {
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('dateFilterForm').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const actionFilter = document.getElementById('actionFilter');
    const searchButton = document.getElementById('searchButton');
    const dateFilterForm = document.getElementById('dateFilterForm');
    
    function performSearch() {
        const search = searchInput.value.trim();
        const action = actionFilter.value;
        const url = new URL(window.location);
        
        if (search) {
            url.searchParams.set('search', search);
        } else {
            url.searchParams.delete('search');
        }
        
        if (action) {
            url.searchParams.set('action', action);
        } else {
            url.searchParams.delete('action');
        }
        
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }
    
    if (searchButton) {
        searchButton.addEventListener('click', performSearch);
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
    
    if (actionFilter) {
        actionFilter.addEventListener('change', performSearch);
    }
    
    // Date filter form
    if (dateFilterForm) {
        dateFilterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });
    }
    
    // Pagination per page change
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('per_page', this.value);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        });
    }
    
    // Set end date to today if not set
    const endDate = document.getElementById('endDate');
    if (endDate && !endDate.value) {
        endDate.value = new Date().toISOString().split('T')[0];
    }
});
</script>

<?php
// Helper function to determine badge color based on action
function getActionBadgeColor($action) {
    $colors = [
        'create' => 'success',
        'update' => 'primary',
        'delete' => 'danger',
        'login' => 'info',
        'logout' => 'secondary',
        'register' => 'success',
        'transfer' => 'warning',
        'ban' => 'danger',
        'unban' => 'success',
        'change_role' => 'info'
    ];
    
    return $colors[$action] ?? 'secondary';
}
?>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>