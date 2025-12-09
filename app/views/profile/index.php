<?php
$title = "Profile | ".$user['name'];
ob_start();
?>
<div class="row">
    <div class="col-md-4">
        <!-- Profile Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Profile Information</h5>
            </div>
            <div class="card-body text-center">
                <img src="<?= user_avatar($user); ?>" 
                     alt="Profile Picture" 
                     class="rounded-circle mb-3" 
                     width="120" 
                     height="120">
                
                <h4><?= e($user['email']); ?></h4>
                <p class="text-muted"><?= ucfirst($user['role']); ?></p>
                
                <div class="d-grid gap-2">
                    <button type="button" 
                            class="btn btn-outline-primary btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#changePictureModal">
                        <i class="bi bi-camera"></i> Change Picture
                    </button>
                    
                    <button type="button" 
                            class="btn btn-outline-primary btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#removePictureModal"
                            <?= !$user['profile_picture'] ? 'disabled' : ''; ?>>
                        <i class="bi bi-trash"></i> Remove Picture
                    </button>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Account Details</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Email:</dt>
                    <dd class="col-sm-7"><?= e($user['email']); ?></dd>
                    
                    <dt class="col-sm-5">Phone:</dt>
                    <dd class="col-sm-7"><?= format_phone($user['phone']); ?></dd>
                    
                    <dt class="col-sm-5">NIN:</dt>
                    <dd class="col-sm-7"><?= e($user['nin']); ?></dd>
                    
                    <dt class="col-sm-5">Role:</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-primary"><?= ucfirst($user['role']); ?></span>
                    </dd>
                    
                    <dt class="col-sm-5">Member Since:</dt>
                    <dd class="col-sm-7"><?= format_date($user['created_at'], 'M j, Y'); ?></dd>
                    
                    <dt class="col-sm-5">Last Login:</dt>
                    <dd class="col-sm-7"><?= format_date($user['last_login_at'], 'M j, Y'); ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Change Password Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('profile/change-password') ?>" method="POST" id="changePasswordForm">
                    <?php csrf_field(); ?>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" 
                               class="form-control <?= has_error('current_password') ? 'is-invalid' : ''; ?>" 
                               id="current_password" 
                               name="current_password" 
                               required>
                        <?php if (has_error('current_password')): ?>
                        <div class="invalid-feedback"><?= flash_error('current_password'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" 
                               class="form-control <?= has_error('new_password') ? 'is-invalid' : ''; ?>" 
                               id="new_password" 
                               name="new_password" 
                               data-validation="password_strength"
                               required>
                        <?php if (has_error('new_password')): ?>
                        <div class="invalid-feedback"><?= flash_error('new_password'); ?></div>
                        <?php endif; ?>
                        <div id="password-strength" class="mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" 
                               class="form-control <?= has_error('confirm_password') ? 'is-invalid' : ''; ?>" 
                               id="confirm_password" 
                               name="confirm_password" 
                               data-validation="matches:new_password"
                               required>
                        <?php if (has_error('confirm_password')): ?>
                        <div class="invalid-feedback"><?= flash_error('confirm_password'); ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-key"></i> Update Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Activity Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <?php if (isset($recent_activity) && !empty($recent_activity)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recent_activity as $activity): ?>
                    <div class="list-group-item px-0">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?= e($activity['action']); ?></h6>
                            <small class="text-muted"><?= relative_time($activity['timestamp']); ?></small>
                        </div>
                        <p class="mb-1"><?= e($activity['description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted text-center mb-0">No recent activity</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Change Picture Modal -->
<div class="modal fade" id="changePictureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action=<?= url('profile/update') ?> method="POST" enctype="multipart/form-data">
                <?php csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Select Image</label>
                        <input type="file" 
                               class="form-control" 
                               id="profile_picture" 
                               name="profile_picture" 
                               accept="image/*"
                               required>
                        <div class="form-text">
                            Supported formats: JPG, PNG, GIF, WebP. Max size: 2MB.
                        </div>
                    </div>
                    <div id="image-preview" class="text-center mt-3" style="display: none;">
                        <img id="preview" class="img-thumbnail" width="150">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Picture</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Remove Picture Modal -->
<div class="modal fade" id="removePictureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remove Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove your profile picture? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action=<?= url('profile/remove-picture') ?> method="POST" class="d-inline">
                    <?php csrf_field(); ?>
                    <button type="submit" class="btn btn-danger">Remove Picture</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
// Fixed Profile Page Script with proper loading checks
document.addEventListener('DOMContentLoaded', function() {
    
    // Wait for FormValidation to be available
    function initializeProfileValidation() {
        // Password strength feedback
        const passwordField = document.getElementById('new_password');
        const passwordStrengthDiv = document.getElementById('password-strength');
        
        if (passwordField && passwordStrengthDiv) {
            passwordField.addEventListener('input', function() {
                if (window.FormValidation) {
                    window.FormValidation.validatePasswordWithFeedback(this.value, passwordStrengthDiv);
                } else {
                    console.warn('FormValidation not loaded yet');
                }
            });
        }
        
        // Image preview for profile picture
        const profilePictureInput = document.getElementById('profile_picture');
        const imagePreview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview');
        
        if (profilePictureInput) {
            profilePictureInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File size must be less than 2MB');
                        this.value = '';
                        return;
                    }
                    
                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPG, PNG, GIF, WebP)');
                        this.value = '';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.style.display = 'none';
                }
            });
        }
        
        // Form validation for password change form
        const changePasswordForm = document.getElementById('changePasswordForm');
        if (changePasswordForm && window.FormValidation) {
            const fields = changePasswordForm.querySelectorAll('[data-validation]');
            fields.forEach(field => {
                field.addEventListener('blur', function() {
                    window.FormValidation.validateField(this);
                });
                
                field.addEventListener('input', function() {
                    window.FormValidation.clearFieldValidation(this);
                });
            });
            
            // Add form submit validation
            changePasswordForm.addEventListener('submit', function(e) {
                let isValid = true;
                
                fields.forEach(field => {
                    if (!window.FormValidation.validateField(field)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    
                    // Show error toast
                    if (window.VehicleTrackerApp) {
                        window.VehicleTrackerApp.showToast('Please fix the validation errors', 'danger');
                    }
                    
                    // Scroll to first error
                    const firstError = changePasswordForm.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                }
            });
        }
    }
    
    // Check if FormValidation is already loaded
    if (window.FormValidation) {
        initializeProfileValidation();
    } else {
        // Wait for FormValidation to load (with timeout)
        let attempts = 0;
        const maxAttempts = 50; // 5 seconds max wait
        const checkInterval = setInterval(function() {
            attempts++;
            if (window.FormValidation) {
                clearInterval(checkInterval);
                initializeProfileValidation();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkInterval);
                console.error('FormValidation failed to load');
            }
        }, 100);
    }
});


</script>
<?php
$scripts = ob_get_clean();
require_once 'app/Views/layouts/main.php';
?>