<?php
$title = "User Management";
$actions = '
    <div class="input-group" style="width: 300px;">
        <input type="text" class="form-control" placeholder="Search users..." id="searchInput" value="' . e($search) . '">
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
            <div class="stats-label">Total Users</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number"><?= $driver_count; ?></div>
            <div class="stats-label">Drivers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number"><?= $searcher_count; ?></div>
            <div class="stats-label">Searchers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="stats-number"><?= $admin_count; ?></div>
            <div class="stats-label">Administrators</div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">User Accounts</h5>
        
        <div class="d-flex align-items-center">
            <span class="me-2 text-muted">Show:</span>
            <select class="form-select form-select-sm me-2" style="width: auto;" id="perPageSelect">
                <option value="10" <?= $pagination['per_page'] == 10 ? 'selected' : ''; ?>>10</option>
                <option value="15" <?= $pagination['per_page'] == 15 ? 'selected' : ''; ?>>15</option>
                <option value="20" <?= $pagination['per_page'] == 20 ? 'selected' : ''; ?>>20</option>
                <option value="50" <?= $pagination['per_page'] == 50 ? 'selected' : ''; ?>>50</option>
            </select>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (empty($users)): ?>
        <div class="text-center py-5">
            <i class="bi bi-people display-1 text-muted"></i>
            <h4 class="text-muted mt-3">No Users Found</h4>
            <p class="text-muted">No users match your search criteria.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Vehicles</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= user_avatar(); ?>" 
                                        alt="Profile" 
                                        class="rounded-circle me-3" 
                                        width="40" 
                                        height="40">
                                    <div>
                                        <a href="<?= $_ENV['APP_URL']?>/admin/manage-user/<?= $user['email'] ?>" class="class">
                                            <strong><?= ucwords(e($user['name'])); ?></strong><br>
                                            <small class="text-muted">NIN: <?= e($user['nin']); ?></small>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div><?= e($user['email']); ?></div>
                                    <small class="text-muted"><?= format_phone($user['phone']); ?></small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'driver' ? 'primary' : 'info'); ?>">
                                    <?= ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['is_banned']): ?>
                                <span class="badge bg-danger">Banned</span>
                                <?php elseif (!$user['email_verified']): ?>
                                <span class="badge bg-warning">Unverified</span>
                                <?php else: ?>
                                <span class="badge bg-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= $user['vehicle_count'] ?? 0; ?></span>
                            </td>
                            <td>
                                <small class="text-muted"><?= relative_time($user['created_at']); ?></small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <?= pagination_links($pagination['page'], $pagination['total_pages'], '/admin/users', $pagination['per_page']); ?>
            </div>
            <div class="text-muted">
                Page <?= $pagination['page']; ?> of <?= $pagination['total_pages']; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- User Profile Modal -->
<div class="modal fade" id="userProfileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userProfileContent">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    
    function performSearch() {
        const query = searchInput.value.trim();
        const url = new URL(window.location);
        
        if (query) {
            url.searchParams.set('search', query);
        } else {
            url.searchParams.delete('search');
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
});
</script>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>