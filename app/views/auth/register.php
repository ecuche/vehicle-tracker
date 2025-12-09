<?php
$title = "Create Account";
$subtitle = "Register for a new account";
$footer_links = '
    <p class="mb-0">
        Already have an account? <a href="'.$_ENV['APP_URL'].'/login" class="text-decoration-none">Sign in here</a>
    </p>
';

ob_start();
?>
<form action="<?= url('register')?>" method="POST" class="auth-form" data-validate="true" id="registrationForm">
    <?php csrf_field(); ?>

     <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" 
               class="form-control <?= has_error('name') ? 'is-invalid' : ''; ?>" 
               id="name" 
               name="name" 
               value="<?= old('name'); ?>" 
               data-validation="name"
               placeholder="Enter your full name"
               required>
        <?php if (has_error('name')): ?>
        <div class="invalid-feedback"><?= get_error('name'); ?></div>
        <?php endif; ?>
    </div>
    
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" 
                   class="form-control <?= has_error('email') ? 'is-invalid' : ''; ?>" 
                   id="email" 
                   name="email" 
                   value="<?= old('email'); ?>" 
                   data-validation="email"
                   placeholder="example@domain.com"
                   required>
            <?php if (has_error('email')): ?>
            <div class="invalid-feedback"><?= get_error('email'); ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" 
                   class="form-control <?= has_error('phone') ? 'is-invalid' : ''; ?>" 
                   id="phone" 
                   name="phone" 
                   value="<?= old('phone'); ?>" 
                   data-validation="phone"
                   placeholder="e.g., 08012345678"
                   required>
            <?php if (has_error('phone')): ?>
            <div class="invalid-feedback"><?= get_error('phone'); ?></div>
            <?php endif; ?>
            <div class="form-text">Enter your Nigerian phone number</div>
        </div>

    <div class="mb-3">
        <label for="nin" class="form-label">National Identification Number (NIN)</label>
        <input type="text" 
               class="form-control <?= has_error('nin') ? 'is-invalid' : ''; ?>" 
               id="nin" 
               name="nin" 
               value="<?= old('nin'); ?>" 
               data-validation="nin"
               maxlength="11"
               placeholder="11-digit NIN"
               required>
        <?php if (has_error('nin')): ?>
        <div class="invalid-feedback"><?= get_error('nin'); ?></div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="role" class="form-label">Account Type</label>
        <select class="form-select <?= has_error('role') ? 'is-invalid' : ''; ?>" 
                id="role" 
                name="role" 
                required>
            <option value="">Select account type</option>
            <option value="driver" <?= old('role') === 'driver' ? 'selected' : ''; ?>>Driver</option>
            <option value="searcher" <?= old('role') === 'searcher' ? 'selected' : ''; ?>>Searcher</option>
        </select>
        <?php if (has_error('role')): ?>
        <div class="invalid-feedback"><?= get_error('role'); ?></div>
        <?php endif; ?>
        <div class="form-text">
            <strong>Driver:</strong> Can register and transfer vehicles<br>
            <strong>Searcher:</strong> Can search vehicle database
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" 
                   class="form-control <?= has_error('password') ? 'is-invalid' : ''; ?>" 
                   id="password" 
                   name="password" 
                   data-validation="required|password_strength"
                   required>
            <?php if (has_error('password')): ?>
            <div class="invalid-feedback"><?= get_error('password'); ?></div>
            <?php endif; ?>
            <div id="password-strength"></div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="password_confirm" class="form-label">Confirm Password</label>
            <input type="password" 
                   class="form-control <?= has_error('password_confirm') ? 'is-invalid' : ''; ?>" 
                   id="password_confirm" 
                   name="password_confirm" 
                   data-validation="required|matches:password"
                   required>
            <?php if (has_error('password_confirm')): ?>
            <div class="invalid-feedback"><?= get_error('password_confirm'); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input <?= has_error('terms') ? 'is-invalid' : ''; ?>" 
               id="terms" name="terms" required>
        <label class="form-check-label" for="terms">
            I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a>
        </label>
        <?php if (has_error('terms')): ?>
        <div class="invalid-feedback"><?= get_error('terms'); ?></div>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-person-plus"></i> Create Account
    </button>
</form>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const passwordConfirmField = document.getElementById('password_confirm');
    const passwordStrengthDiv = document.getElementById('password-strength');
    const passwordField = document.getElementById('password');
    const strengthDiv = document.getElementById('password-strength');

    if (passwordField && strengthDiv && window.FormValidation) {
        passwordField.addEventListener('input', function() {
            const result = FormValidation.validatePasswordWithFeedback(this.value);
            
            strengthDiv.innerHTML = '';
            strengthDiv.className = 'mt-2';

            if (this.value === '') {
                strengthDiv.innerHTML = '';
                return;
            }

            if (result.valid) {
                strengthDiv.innerHTML = '<small class="text-success">Strong password!</small>';
            } else {
                strengthDiv.className += ' text-danger';
                strengthDiv.innerHTML = result.feedback.map(msg => `<small>• ${msg}</small><br>`).join('');
            }
        });
    }
    
    // Real-time password strength feedback
    if (passwordField && passwordStrengthDiv) {
        passwordField.addEventListener('input', function() {
            FormValidation.validatePasswordWithFeedback(this.value, passwordStrengthDiv);
        });
    }
    
    // Real-time validation for all fields
    const fields = form.querySelectorAll('[data-validation]');
    fields.forEach(field => {
        field.addEventListener('blur', function() {
            FormValidation.validateField(this);
        });
        
        field.addEventListener('input', function() {
            FormValidation.clearFieldValidation(this);
        });
    });
    
   // Phone number formatting
const phoneField = document.getElementById('phone');

if (phoneField) {
    phoneField.addEventListener('input', function (e) {
        let raw = e.target.value.replace(/\D/g, ''); // remove all non-digits
        let formatted = '';

        // ---- CASE 1: Local Nigerian Number (starts with 0) ----
        if (raw.startsWith('0')) {
            raw = raw.slice(0, 11); // max 11 digits

            // Format: 0801 234 5678
            if (raw.length <= 4) {
                formatted = raw;
            } else if (raw.length <= 7) {
                formatted = raw.slice(0, 4) + ' ' + raw.slice(4);
            } else {
                formatted = raw.slice(0, 4) + ' ' + raw.slice(4, 7) + ' ' + raw.slice(7);
            }
        }

        // ---- CASE 2: International Nigerian Number (234XXXXXXXXXX) ----
        else if (raw.startsWith('234')) {
            raw = raw.slice(0, 13); // 234 + 10 digits

            // Format: 234 801 234 5678
            if (raw.length <= 3) {
                formatted = raw;
            } else if (raw.length <= 6) {
                formatted = raw.slice(0, 3) + ' ' + raw.slice(3);
            } else if (raw.length <= 9) {
                formatted = raw.slice(0, 3) + ' ' + raw.slice(3, 6) + ' ' + raw.slice(6);
            } else {
                formatted = raw.slice(0, 3) + ' ' + raw.slice(3, 6) + ' ' + raw.slice(6, 9) + ' ' + raw.slice(9);
            }
        }

        // ---- CASE 3: +234XXXXXXXXXX ----
        else if (e.target.value.startsWith('+')) {
            // Keep + in display, but work with digits only
            raw = raw.slice(0, 13); // 234 + 10 digits

            if (raw.length === 0) {
                formatted = '+';
            } else if (raw.length <= 3) {
                formatted = '+234'.slice(0, raw.length + 1);
            } else if (raw.length <= 6) {
                formatted = '+234 ' + raw.slice(3);
            } else if (raw.length <= 9) {
                formatted = '+234 ' + raw.slice(3, 6) + ' ' + raw.slice(6);
            } else {
                formatted = '+234 ' + raw.slice(3, 6) + ' ' + raw.slice(6, 9) + ' ' + raw.slice(9);
            }
        }

        // ---- Default (no valid prefix yet) ----
        else {
            formatted = raw; // allow user to type until matched
        }

        e.target.value = formatted;
    });
}


});
</script>
<?php
$scripts = ob_get_clean();
include 'app/Views/layouts/auth.php';
?>