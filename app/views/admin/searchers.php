<?php
$title = "Searchers Management - Admin";
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('admin/dashboard'); ?>">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('admin/users'); ?>">User Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Searchers</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-search me-2 text-success"></i>Searchers Management
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
                        <li><a href="<?= url("admin/view/searchers?page={$current_page}&per_page={$option}") ?>" class="dropdown-item <?= $per_page == $option ? 'active' : ''; ?>" href="#"><?= $option; ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <!-- Refresh Button -->
            <a href="<?= url('admin/view/searchers'); ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
     <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Searchers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['total'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fs-2 text-gray-300"></i>
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
                                Active Searchers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['active'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check fs-2 text-gray-300"></i>
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
                                Unverified Searchers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['unverified'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-dash fs-2 text-gray-300"></i>
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
                                Banned Searchers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['banned'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-slash fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Searchers Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="bi bi-search me-2"></i>Searchers List
                <span class="badge bg-success ms-2"><?= $total_items ?? 0; ?> users</span>
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
            <?php if (!empty($searchers)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User Details</th>
                            <th>Contact Information</th>
                            <th>verification Status</th>
                            <th>Ban Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($searchers as $user): ?>
                        <tr class="">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <?php if (!empty($user['profile_picture'])): ?>
                                        <img src="<?= htmlspecialchars($user['profile_picture']); ?>" 
                                             alt="Profile" 
                                             class="rounded-circle" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-person fs-5"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($user['name']); ?></strong><br>
                                        <small class="text-muted">Joined: <?= date('M j, Y', strtotime($user['created_at'])); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <strong>Email:</strong> <?= htmlspecialchars($user['email']); ?><br>
                                    <strong>Phone:</strong> <?= htmlspecialchars(formatPhoneNumber($user['phone'])); ?><br>
                                    <strong>NIN:</strong> <?= htmlspecialchars($user['nin']); ?>
                                </small>
                            </td>
                           <td>
                                <?= getEmailVerificationStatus($user['email_verified']); ?><br>
                                <small class="text-muted">
                                    <?php if ($user['email_verified']): ?>
                                    <i class="bi bi-check-circle text-success"></i> Email Verified<br>
                                    <?php else: ?>
                                    <i class="bi bi-x-circle text-danger"></i> Email Not Verified<br>
                                    <?php endif; ?>
                                    
                                    <?php if ($user['last_login_at']): ?>
                                    Last Login: <?= relative_time($user['last_login_at']); ?>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                 <?= getBanStatus($user['is_banned']); ?><br>
                                <small class="text-muted">
                                    <?php if ($user['email_verified']): ?>
                                    <i class="bi bi-check-circle text-success"></i> Email Verified<br>
                                    <?php else: ?>
                                    <i class="bi bi-x-circle text-danger"></i> Email Not Verified<br>
                                    <?php endif; ?>
                                    
                                    <?php if ($user['last_login_at']): ?>
                                    Last Login: <?= relative_time($user['last_login_at']); ?>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('admin/manage-user/' . $user['email']); ?>" 
                                       class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
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
                    <i class="bi bi-search display-4 mb-3 text-success"></i>
                    <h4>No Searchers Found</h4>
                    <p>No searchers found matching your criteria.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Charts -->
    <?php if (!empty($chart_data)): ?>
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-bar-chart me-2"></i>Searchers Registration Trend
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="registrationsChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-pie-chart me-2"></i>Searchers by Status
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="100"></canvas>
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
.bg-success { background-color: #198754 !important; }
.border-left-success { border-left-color: #198754 !important; }
.text-success { color: #198754 !important; }

.table-success-row { background-color: rgba(25, 135, 84, 0.05) !important; }
.table-danger-row { background-color: rgba(220, 53, 69, 0.05) !important; }
.table-warning-row { background-color: rgba(255, 193, 7, 0.05) !important; }
</style>

<?php 
$styles = ob_get_clean(); 
ob_start(); 
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// JavaScript functions for searcher management

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($chart_data)): ?>
    // Registration Chart
    const regCtx = document.getElementById('registrationsChart').getContext('2d');
    new Chart(regCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_data['monthly_labels'] ?? []); ?>,
            datasets: [{
                label: 'New Searchers',
                data: <?= json_encode($chart_data['registrations'] ?? []); ?>,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chart_data['status_labels'] ?? []); ?>,
            datasets: [{
                data: <?= json_encode($chart_data['status_data'] ?? []); ?>,
                backgroundColor: ['#198754', '#6c757d', '#dc3545', '#ffc107'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    <?php endif; ?>

    // Ban confirmation
    document.getElementById('confirmBan').addEventListener('click', function() {
        const userId = document.getElementById('banUserId').value;
        const reason = document.getElementById('banReason').value;
        const duration = document.getElementById('banDuration').value;
        
        if (!reason.trim()) {
            alert('Please provide a ban reason.');
            return;
        }
        
        fetch(`/api/admin/users/ban/${userId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                reason: reason,
                duration: duration,
                _token: '<?= csrf_token(); ?>' 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User banned successfully!');
                window.location.reload();
            } else {
                alert(data.message || 'Failed to ban user.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error. Please try again.');
        });
    });
});
</script>

<?php 
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php'; 
?>