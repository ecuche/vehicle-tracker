<?php
$title = "Admins Management - Admin";
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('admin/dashboard'); ?>">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('admin/users'); ?>">User Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Administrators</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-shield-lock me-2 text-danger"></i>Admins Management
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
                        <li><a href="<?= url("admin/view/admins?page={$current_page}&per_page={$option}") ?>" class="dropdown-item <?= $per_page == $option ? 'active' : ''; ?>" href="#"><?= $option; ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <!-- Refresh Button -->
            <a href="<?= url('admin/view/admins'); ?>" class="btn btn-sm btn-outline-primary">
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
                                Total Admins
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
                                Active Admins
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
                                Unverified Admins
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
                                Banned Admins
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

    <!-- Admins Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-danger">
                <i class="bi bi-shield-lock me-2"></i>Administrators List
                <span class="badge bg-danger ms-2"><?= $total_items ?? 0; ?> admins</span>
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
            <?php if (!empty($admins)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Admin Details</th>
                            <th>Contact Information</th>
                            <th>Access Level</th>
                            <th>verified</th>
                            <th>Banned</th>
                            <th>Activity Information</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                        <tr class="active">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <?php if (!empty($admin['profile_picture'])): ?>
                                        <img src="<?= htmlspecialchars($admin['profile_picture']); ?>" 
                                             alt="Profile" 
                                             class="rounded-circle" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-shield-lock fs-5"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($admin['name']); ?></strong>
                                        <br>
                                        <small class="text-muted">Joined since: <?= date('M j, Y', strtotime($admin['created_at'])); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <strong>Email:</strong> <?= htmlspecialchars($admin['email']); ?><br>
                                    <strong>Phone:</strong> <?= htmlspecialchars(formatPhoneNumber($admin['phone'])); ?><br>
                                    <strong>NIN:</strong> <?= htmlspecialchars($admin['nin']); ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-super me-1">Admin</span>
                            </td>
                             <td>
                                <?= getEmailVerificationStatus($admin['email_verified']); ?><br>
                                <small class="text-muted">
                                    <?php if ($admin['email_verified']): ?>
                                    <i class="bi bi-check-circle text-success"></i> Email Verified<br>
                                    <?php else: ?>
                                    <i class="bi bi-x-circle text-danger"></i> Email Not Verified<br>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                 <?= getBanStatus($admin['is_banned']); ?><br>
                                <small class="text-muted">
                                    <?php if ($admin['is_banned']): ?>
                                    <i class="bi bi-x-circle text-danger"></i> Admin is Banned<br>
                                    <?php else: ?>
                                    <i class="bi bi-check-circle text-success"></i> Admin is Active<br>
                                    <?php endif; ?>
                                </small>

                            </td>
                            <td>
                                <small class="text-muted">
                                    <strong>Last Login:</strong> 
                                    <?php if ($admin['last_login_at']): ?>
                                        <?= relative_time($admin['last_login_at']); ?>
                                    <?php else: ?>
                                        Never
                                    <?php endif; ?>
                                    <br>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('admin/manage-user/' . $admin['email']); ?>" 
                                       class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($admin['id'] == $_SESSION['user_id']): ?>
                                    <button type="button" class="btn btn-outline-secondary disabled">
                                        <i class="bi bi-person"></i> You
                                    </button>
                                    <?php endif; ?>
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
                    <i class="bi bi-shield-lock display-4 mb-3 text-danger"></i>
                    <h4>No Administrators Found</h4>
                    <p>No administrators found matching your criteria.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Activity Charts -->
    <?php if (!empty($chart_data)): ?>
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-graph-up me-2"></i>Admin Activity (Last 7 Days)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-pie-chart me-2"></i>Admin Levels Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="levelsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean();
ob_start();
?>

<style>
.bg-danger { background-color: #dc3545 !important; }
.border-left-danger { border-left-color: #dc3545 !important; }
.text-danger { color: #dc3545 !important; }

.table-super-row { background-color: rgba(220, 53, 69, 0.05) !important; }
.table-regular-row { background-color: rgba(13, 110, 253, 0.05) !important; }
.table-limited-row { background-color: rgba(255, 193, 7, 0.05) !important; }
.table-suspended-row { background-color: rgba(108, 117, 125, 0.05) !important; }

.badge-super { background-color: #dc3545 !important; }
.badge-regular { background-color: #0d6efd !important; }
.badge-limited { background-color: #ffc107 !important; color: #000 !important; }
</style>

<?php 
$styles = ob_get_clean();
ob_start();
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($chart_data)): ?>
    // Activity Chart
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_data['days'] ?? []); ?>,
            datasets: [{
                label: 'Admin Actions',
                data: <?= json_encode($chart_data['actions'] ?? []); ?>,
                backgroundColor: '#dc3545',
                borderColor: '#dc3545',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Levels Chart
    const levelsCtx = document.getElementById('levelsChart').getContext('2d');
    new Chart(levelsCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chart_data['level_labels'] ?? []); ?>,
            datasets: [{
                data: <?= json_encode($chart_data['level_data'] ?? []); ?>,
                backgroundColor: ['#dc3545', '#0d6efd', '#ffc107'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    <?php endif; ?>
});
</script>

<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php'; 
?>