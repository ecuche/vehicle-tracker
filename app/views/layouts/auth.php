<!DOCTYPE html>
<html lang="en" data-theme="<?= current_theme(); ?>" appUrl="<?= $_ENV['APP_URL'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - Vehicle Tracker' : 'Vehicle Tracker'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="<?= $_ENV['APP_URL'] ?>/public/assets/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css"> -->
    <link rel="stylesheet" href="<?= $_ENV['APP_URL'] ?>/public/assets/bootstrap-icons/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $_ENV['APP_URL'] ?>/public/assets/css/style.css">
    <link rel="stylesheet" href="<?= $_ENV['APP_URL'] ?>/public/assets/css/dark-mode.css">
    <link rel="stylesheet" href="<?= $_ENV['APP_URL'] ?>/public/assets/css/light-mode.css">

    
    <!-- Additional Styles -->
    <style>
        .auth-layout {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color) 0%, #0a58ca 100%);
            position: relative;
        }
        
        .auth-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.1;
        }
        
        .auth-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        
        .auth-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-lg);
        }
        
        .auth-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            text-decoration: none;
            margin-bottom: 1rem;
        }
        
        .auth-logo i {
            color: var(--primary-color);
        }
        
        .auth-title {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }
        
        .auth-subtitle {
            color: var(--text-secondary);
            margin-bottom: 0;
        }
        
        .auth-theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        @media (max-width: 576px) {
            .auth-container {
                padding: 10px;
            }
            
            .auth-card {
                padding: 1.5rem;
            }
        }
    </style>

    <?php if (isset($styles)): ?>
    <?= $styles; ?>
    <?php endif; ?>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= csrf_token(); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= $_ENV['APP_URL'] ?>/public/assets/images/favicon.ico">
    <script>
        const appUrl = document.getElementsByTagName("html")[0].getAttribute("appUrl");
    </script>
</head>
<body class="auth-layout <?= theme_class(); ?>">
    <!-- Background -->
    <div class="auth-background"></div>
    
    <!-- Main Content -->
    <div class="auth-container">
        <div class="auth-card">
            <!-- Header -->
            <div class="auth-header text-center mb-4">
                <a href="/" class="auth-logo">
                    <i class="bi bi-car-front-fill"></i>
                    <span>Vehicle Tracker</span>
                </a>
                <h1 class="auth-title"><?= isset($title) ? $title : 'Welcome'; ?></h1>
                <?php if (isset($subtitle)): ?>
                <p class="auth-subtitle"><?= e($subtitle); ?></p>
                <?php endif; ?>
            </div>

            <!-- Flash Messages -->
            <div id="flash-messages">
                <?php
                $flash = flash_message();
                if ($flash): ?>
                <div class="alert alert-<?= $flash['type']; ?> alert-dismissible fade show" role="alert">
                    <?= e($flash['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Content -->
            <div class="auth-content">
                <?= $content; ?>
            </div>

            <!-- Footer -->
            <div class="auth-footer text-center mt-4">
                <?php if (isset($footer_links)): ?>
                <div class="auth-links">
                    <?= $footer_links; ?>
                </div>
                <?php endif; ?>
                <div class="text-muted small mt-3">
                    &copy; <?= date('Y'); ?> Vehicle Tracker. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Toggle -->
    <div class="auth-theme-toggle">
        <button class="btn btn-sm btn-outline-secondary theme-toggle" type="button">
            <i class="bi bi-moon"></i>
            <i class="bi bi-sun"></i>
        </button>
    </div>

    
    <!-- JQuery JS -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
    <script src="<?= $_ENV['APP_URL'] ?>/public/assets/js/jquery.min.js"></script>


    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="<?= $_ENV['APP_URL'] ?>/public/assets/js/bootstrap.bundle.min.js"></script>


    <!-- Custom JS -->
    <script src="<?= $_ENV['APP_URL'] ?>/public/assets/js/theme.js"></script>
    <script src="<?= $_ENV['APP_URL'] ?>/public/assets/js/validation.js"></script>
    <script src="<?= $_ENV['APP_URL'] ?>/public/assets/js/ajax.js"></script>
    <!-- <script src="<?= $_ENV['APP_URL'] ?>/public/assets/js/app.js"></script> -->

     <!-- Page-specific JS -->
    <?php if (isset($scripts)): ?>
    <?= $scripts; ?>
    <?php endif; ?>
    
</body>
</html>