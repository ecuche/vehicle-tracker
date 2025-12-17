<?php
$title = "Forgot Password";
$subtitle = "Enter your email to reset your password";
$footer_links = '
    <p class="mb-0">
        <a href="'.$_ENV['APP_URL'].'/login" class="text-decoration-none">Back to login</a>
    </p>
';

ob_start();
?>
<form action="<?= $_ENV['APP_URL'] ?>/forgot-password" method="POST" class="auth-form">
    <?php csrf_field(); ?>
    
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        Enter your email address and we'll send you a link to reset your password.
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" 
               class="form-control <?= has_error('email') ? 'is-invalid' : ''; ?>" 
               id="email" 
               name="email" 
               value="<?= old('email'); ?>" 
               data-validation="email"
               required>
        <?php if (has_error('email')): ?>
        <div class="invalid-feedback"><?= get_error('email'); ?></div>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-envelope"></i> Send Reset Link
    </button>
</form>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailField = document.getElementById('email');
    
    if (emailField) {
        emailField.addEventListener('blur', function() {
            FormValidation.validateField(this);
        });
    }
});
</script>
<?php $scripts = ob_get_clean(); ?>
<?php
include 'app/Views/layouts/auth.php';
?>