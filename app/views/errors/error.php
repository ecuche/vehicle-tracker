<?php
$hide_header = true;
ob_start();
?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="error-container py-5">
                <!-- Error Icon -->
                <div class="error-icon mb-4">
                    <?php if ($data['error_code'] === '404'): ?>
                    <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
                    <?php elseif ($error_code === '403'): ?>
                    <i class="bi bi-shield-exclamation display-1 text-danger"></i>
                    <?php elseif ($data['error_code'] === '500'): ?>
                    <i class="bi bi-gear display-1 text-secondary"></i>
                    <?php else: ?>
                    <i class="bi bi-x-circle display-1 text-danger"></i>
                    <?php endif; ?>
                </div>
                
                <!-- Error Code -->
                <h1 class="display-1 fw-bold text-primary"><?= $error_code ?></h1>
                
                <!-- Error Title -->
                <h2 class="h3 mb-3"><?= $error_title ?></h2>
                
                <!-- Error Message -->
                <p class="lead text-muted mb-4"><?= $error_message ?></p>
                
                <!-- Action Buttons -->
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="javascript:history.back()" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Go Back
                    </a>
                    
                    <a href="<?= $_ENV['APP_URL'] ?>/dashboard" class="btn btn-primary">
                        <i class="bi bi-speedometer2"></i> Go to Dashboard
                    </a>
                    
                    <a href="<?= $_ENV['APP_URL'] ?>/" class="btn btn-outline-secondary">
                        <i class="bi bi-house"></i> Home Page
                    </a>
                </div>
                
                <!-- Additional Help -->
                <div class="mt-5">
                    <p class="text-muted small">
                        If you believe this is an error, please 
                        <a href="mailto:support@vehicletracker.com" class="text-decoration-none">contact support</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php ob_start(); ?>
<style>
.error-container {
    max-width: 500px;
    margin: 0 auto;
}

.error-icon {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

@media (max-width: 768px) {
    .error-container {
        padding: 2rem 1rem;
    }
    
    .display-1 {
        font-size: 4rem;
    }
}
</style>
<?php
$styles = ob_get_clean();

include 'app/Views/layouts/main.php';
?>