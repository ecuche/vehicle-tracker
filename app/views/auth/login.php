<?php
$title = "Login";
$subtitle = "Sign in to your account";
$footer_links = '
    <p class="mb-2">
        Don\'t have an account? <a href="'.$_ENV['APP_URL'].'/register" class="text-decoration-none">Create one here</a>
    </p>
    <p class="mb-0">
        <a href="'.$_ENV['APP_URL'].'/forgot-password" class="text-decoration-none">Forgot your password?</a>
    </p>
';

ob_start();
?>
<form action="<?= $_ENV['APP_URL'] ?>/login" method="POST" class="auth-form">
    <?php csrf_field(); ?>
    
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

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" 
               class="form-control <?= has_error('password') ? 'is-invalid' : ''; ?>" 
               id="password" 
               data-validation="required"
               name="password" 
               required>
        <?php if (has_error('password')): ?>
        <div class="invalid-feedback"><?= get_error('password'); ?></div>
        <?php endif; ?>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">Remember me</label>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-box-arrow-in-right"></i> Sign In
    </button>
</form>
<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time validation
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('password');
    
    if (emailField) {
        emailField.addEventListener('blur', function() {
            FormValidation.validateField(this);
        });
    }
    
    if (passwordField) {
        passwordField.addEventListener('blur', function() {
            FormValidation.validateField(this);
        });
    }
});
</script>

<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/auth.php';
?>
