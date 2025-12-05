<?php
$title = "Reset Password";
$subtitle = "Create your new password";
$footer_links = '
    <p class="mb-0">
        <a href="'. $_ENV['APP_URL'].'/login" class="text-decoration-none">Back to login</a>
    </p>
';

ob_start();
?>
<form action="<?= $_ENV['APP_URL'] ?>/reset-password/<?= $token; ?>" method="POST" class="auth-form" id="resetPasswordForm">
    <?php csrf_field(); ?>
    
    <div class="alert alert-info">
        <i class="bi bi-shield-check"></i>
        Create a new strong password for your account.
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <input type="password" 
               class="form-control <?= has_error('password') ? 'is-invalid' : ''; ?>" 
               id="password" 
               name="password" 
               data-validation="password_strength"
               required>
        <?php if (has_error('password')): ?>
        <div class="invalid-feedback"><?= get_error('password'); ?></div>
        <?php endif; ?>
        <div id="password-strength"></div>
    </div>

    <div class="mb-3">
        <label for="password_confirm" class="form-label">Confirm New Password</label>
        <input type="password" 
               class="form-control <?= has_error('password_confirm') ? 'is-invalid' : ''; ?>" 
               id="password_confirm" 
               name="password_confirm" 
               data-validation="matches:password"
               required>
        <?php if (has_error('password_confirm')): ?>
        <div class="invalid-feedback"><?= get_error('password_confirm'); ?></div>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-key"></i> Reset Password
    </button>
</form>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetPasswordForm');
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirm');
    const passwordStrengthDiv = document.getElementById('password-strength');
    
    // Real-time password strength feedback
    if (passwordField && passwordStrengthDiv) {
        passwordField.addEventListener('input', function() {
            FormValidation.validatePasswordWithFeedback(this.value, passwordStrengthDiv);
        });
    }
    
    // Real-time validation
    const fields = form.querySelectorAll('[data-validation]');
    fields.forEach(field => {
        field.addEventListener('blur', function() {
            FormValidation.validateField(this);
        });
        
        field.addEventListener('input', function() {
            FormValidation.clearFieldValidation(this);
        });
    });
});
</script>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/auth.php';
?>