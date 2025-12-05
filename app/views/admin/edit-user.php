<?php
$title = "Edit User - " . ($user['name'] ?? 'Unknown User');
ob_start();
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('admin/users') ?>">User Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit User</li>
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
                                <i class="bi bi-person-gear me-2"></i>Edit User
                            </h1>
                            <p class="text-muted mb-0">
                                Update user information and permissions
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php if ($user['is_banned']): ?>
                            <span class="badge bg-danger fs-6">
                                <i class="bi bi-ban me-1"></i> Banned
                            </span>
                            <?php else: ?>
                            <span class="badge bg-success fs-6">
                                <i class="bi bi-check-circle me-1"></i> Active
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Edit Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil-square me-2"></i>User Information
                    </h5>
                </div>
                <div class="card-body">
                    <form id="editUserForm" method="POST" action="<?= url('api/admin/update/user') ?>/">
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Basic Information</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <strong>Full Name</strong>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= e($user['name'] ?? ''); ?>" required>
                                    <div class="form-text">User's full legal name</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <strong>Email Address</strong>
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= e($user['email'] ?? ''); ?>" required readonly>
                                    <div class="form-text text-warning">
                                        <i class="bi bi-lock me-1"></i>Email cannot be changed
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Contact Information</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">
                                        <strong>Phone Number</strong>
                                    </label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?= e($user['phone'] ?? ''); ?>" readonly>
                                    <div class="form-text text-warning">
                                        <i class="bi bi-lock me-1"></i>Phone number cannot be changed
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nin" class="form-label">
                                        <strong>National Identification Number (NIN)</strong>
                                    </label>
                                    <input type="text" class="form-control" id="nin" name="nin" 
                                           value="<?= e($user['nin'] ?? ''); ?>" readonly maxlength="11">
                                    <div class="form-text text-warning">
                                        <i class="bi bi-lock me-1"></i>NIN cannot be changed
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Account Settings</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">
                                        <strong>User Role</strong>
                                    </label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="driver" <?= ($user['role'] ?? '') === 'driver' ? 'selected' : ''; ?>>Driver</option>
                                        <option value="searcher" <?= ($user['role'] ?? '') === 'searcher' ? 'selected' : ''; ?>>Searcher</option>
                                        <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                                    </select>
                                    <div class="form-text">Determine user permissions and access levels</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">
                                        <strong>Account Status</strong>
                                    </label>
                                    <div class="form-control bg-dark">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="is_banned" name="is_banned" 
                                                   <?= ($user['is_banned'] ?? false) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_banned">
                                                <span id="statusText">
                                                    <?= ($user['is_banned'] ?? false) ? 'Banned' : 'Active'; ?>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-text">Toggle to ban/unban this user</div>
                                </div>
                            </div>
                        </div>

                        <!-- Verification Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3">Verification Status</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Email Verification</strong>
                                    </label>
                                    <div class="form-control bg-dark">
                                        <?php if ($user['email_verified_at']): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i> Verified
                                        </span>
                                        <small class="text-muted ms-2">
                                            <?= date('M j, Y g:i A', strtotime($user['email_verified_at'])); ?>
                                        </small>
                                        <?php else: ?>
                                        <span class="badge bg-warning">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Not Verified
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Account Created</strong>
                                    </label>
                                    <div class="form-control bg-dark">
                                        <?= date('M j, Y g:i A', strtotime($user['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="<?= $_ENV['APP_URL'] ?>/admin/users" class="btn btn-secondary me-md-2">
                                        <i class="bi bi-arrow-left me-1"></i> Back to Users
                                    </a>
                                    <button type="button" class="btn btn-outline-danger me-md-2" 
                                            onclick="showDeleteConfirmation()">
                                        <i class="bi bi-trash me-1"></i> Delete User
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="bi bi-check-circle me-1"></i> Update User
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- User Summary & Actions -->
        <div class="col-lg-4">
            <!-- User Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-badge me-2"></i>User Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="user-avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="bi bi-person"></i>
                        </div>
                        <h5 class="mt-3 mb-1"><?= e($user['name']); ?></h5>
                        <p class="text-muted mb-2"><?= e($user['email']); ?></p>
                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'searcher' ? 'info' : 'primary'); ?>">
                            <?= ucfirst($user['role']); ?>
                        </span>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Vehicles Owned</span>
                            <span class="badge bg-primary rounded-pill" id="vehiclesCount">
                                <?= $user_stats['vehicles_owned'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Vehicles Sold</span>
                            <span class="badge bg-secondary rounded-pill" id="soldCount">
                                <?= $user_stats['vehicles_sold'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Transfers Made</span>
                            <span class="badge bg-info rounded-pill" id="transfersCount">
                                <?= $user_stats['transfers_made'] ?? 0; ?>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Last Login</span>
                            <small class="text-muted">
                                <?= $user['last_login_at'] ? date('M j, Y', strtotime($user['last_login_at'])) : 'Never'; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= url('admin/user/vehicles/'.$user['email']); ?>" 
                           class="btn btn-outline-primary text-start">
                            <i class="bi bi-truck me-2"></i> View Vehicles
                        </a>
                        <a href="<?= url('admin/audit/user/'.$user['email']); ?>" 
                           class="btn btn-outline-info text-start" >
                            <i class="bi bi-clock-history me-2"></i> View Activity
                        </a>
                        <?php if (!$user['email_verified_at']): ?>
                        <button type="button" class="btn btn-outline-warning text-start" 
                                onclick="verifyUserEmail()">
                            <i class="bi bi-envelope-check me-2"></i> Verify Email
                        </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-outline-secondary text-start" 
                                onclick="sendPasswordReset()">
                            <i class="bi bi-key me-2"></i> Send Password Reset
                        </button>
                    </div>
                </div>
            </div>

           
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm User Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>This action cannot be undone!</strong>
                </div>
                <p>You are about to permanently delete the user account for:</p>
                <div class="card bg-dark">
                    <div class="card-body">
                        <strong><?= e($user['name']); ?></strong><br>
                        <small class="text-muted"><?= e($user['email']); ?></small>
                    </div>
                </div>
                <p class="mt-3 text-danger">
                    <strong>Warning:</strong> This will delete all user data including:
                </p>
                <ul class="text-danger small">
                    <li>User profile information</li>
                    <li>Vehicle ownership records</li>
                    <li>Transfer history</li>
                    <li>All associated data</li>
                </ul>
                <div class="mb-3">
                    <label for="confirmDelete" class="form-label">
                        Type <strong>DELETE</strong> to confirm:
                    </label>
                    <input type="text" class="form-control" id="confirmDelete" 
                           placeholder="Type DELETE here">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                    <i class="bi bi-trash me-1"></i> Delete User
                </button>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?> 

<style>
.user-avatar {
    background: linear-gradient(135deg, #0d6efd, #0dcaf0);
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.form-check-input:checked#is_banned {
    background-color: #dc3545;
    border-color: #dc3545;
}

.list-group-item {
    border: none;
    padding-left: 0;
    padding-right: 0;
}

.card .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}
</style>

<?php $styles = ob_get_clean(); ?>
<?php ob_start(); ?> 

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeEditUserForm();
});

function initializeEditUserForm() {
    const form = document.getElementById('editUserForm');
    const banSwitch = document.getElementById('is_banned');
    const statusText = document.getElementById('statusText');
    const confirmDeleteInput = document.getElementById('confirmDelete');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    // Ban switch handler
    banSwitch.addEventListener('change', function() {
        statusText.textContent = this.checked ? 'Banned' : 'Active';
    });

    // Delete confirmation handler
    confirmDeleteInput.addEventListener('input', function() {
        confirmDeleteBtn.disabled = this.value !== 'DELETE';
    });

    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitUserUpdate();
    });

    // Set up delete confirmation
    confirmDeleteBtn.addEventListener('click', deleteUser);
}

function submitUserUpdate() {
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitBtn');

    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Updating...';

    fetch(form.action, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            App.showToast('User updated successfully!', 'success');
            // Reload page to show changes
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.error || 'Update failed');
        }
    })
    .catch(error => {
        console.error('Update error:', error);
        App.showToast(error.message, 'error');
        
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Update User';
    });
}

function showDeleteConfirmation() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function deleteUser() {
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitBtn');
    
    // Disable button and show loading
    confirmDeleteBtn.disabled = true;
    confirmDeleteBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1"></div> Deleting...';

    fetch(appUrl + `/api/admin/delete/user`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        console.log(data)
        if (data.success) {
            App.showToast('User deleted successfully!', 'success');
            
            // Close modal and redirect
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            setTimeout(() => {
                window.location.href = appUrl + '/admin/users';
            }, 1500);
        } else {
            throw new Error(data.error || 'Delete failed');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        App.showToast(error.message, 'error');
        
        // Re-enable button
        confirmDeleteBtn.disabled = false;
        confirmDeleteBtn.innerHTML = '<i class="bi bi-trash me-1"></i> Delete User';
    });
}

function banUser() {
    if (confirm('Are you sure you want to ban this user? They will not be able to access their account.')) {
        fetch(`/admin/users/<?= $user['id']; ?>/ban`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                App.showToast('User banned successfully!', 'success');
                window.location.reload();
            } else {
                throw new Error(data.error || 'Ban failed');
            }
        })
        .catch(error => {
            console.error('Ban error:', error);
            App.showToast(error.message, 'error');
        });
    }
}

function unbanUser() {
    if (confirm('Are you sure you want to unban this user? They will be able to access their account again.')) {
        fetch(`/admin/users/<?= $user['id']; ?>/unban`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                App.showToast('User unbanned successfully!', 'success');
                window.location.reload();
            } else {
                throw new Error(data.error || 'Unban failed');
            }
        })
        .catch(error => {
            console.error('Unban error:', error);
            App.showToast(error.message, 'error');
        });
    }
}

function verifyUserEmail() {
    if (confirm('Manually verify this user\'s email address?')) {
        fetch(`/admin/users/<?= $user['id']; ?>/verify-email`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                App.showToast('Email verified successfully!', 'success');
                window.location.reload();
            } else {
                throw new Error(data.error || 'Verification failed');
            }
        })
        .catch(error => {
            console.error('Verification error:', error);
            App.showToast(error.message, 'error');
        });
    }
}

function sendPasswordReset() {
    if (confirm('Send password reset email to this user?')) {
        fetch(`/admin/users/<?= $user['id']; ?>/send-password-reset`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                App.showToast('Password reset email sent!', 'success');
            } else {
                throw new Error(data.error || 'Failed to send reset email');
            }
        })
        .catch(error => {
            console.error('Password reset error:', error);
            App.showToast(error.message, 'error');
        });
    }
}
</script>

<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/main.php';
?>
