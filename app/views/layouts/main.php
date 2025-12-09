<!DOCTYPE html>
<html lang="en" data-theme="<?= current_theme(); ?>" appUrl="<?= url() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - '.$_ENV['APP_NAME'] : $_ENV['APP_NAME']; ?></title>
    
     <!-- Bootstrap 5 CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="<?=  url('public/assets/css/bootstrap.min.css')?>">

    <!-- Bootstrap Icons -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css"> -->
    <link rel="stylesheet" href="<?=  url('public/assets/bootstrap-icons/bootstrap-icons.css')?>">


    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?=  url('public/assets/css/style.css')?>">
    <link rel="stylesheet" href="<?=  url('public/assets/css/dark-mode.css')?>">
    <link rel="stylesheet" href="<?=  url('public/assets/css/light-mode.css')?>">

    <?php if (isset($styles)): ?>
    <?= $styles; ?>
    <?php endif; ?>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= csrf_token(); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?=  url('public/assets/images/favicon.ico')?>">
    
    <?php if (isset($meta_description)): ?>
    <meta name="description" content="<?= e($meta_description); ?>">
    <?php endif; ?>
    
    <?php if (isset($meta_keywords)): ?>
    <meta name="keywords" content="<?= e($meta_keywords); ?>">
    <?php endif; ?>

    <script>
        const appUrl = document.getElementsByTagName("html")[0].getAttribute("appUrl");
    </script>
</head>
<body class="<?= theme_class(); ?>">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?=  url('dashboard')?>">
                <i class="bi bi-car-front-fill"></i>
                Vehicle Tracker
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if ($this->auth->isLoggedIn()): ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : ''; ?>" href="<?=  url('dashboard')?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <?php if ($this->auth->isDriver() || $this->auth->isSearcher()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/vehicles') !== false ? 'active' : ''; ?>" href="<?=  url('vehicles')?>">
                            <i class="bi bi-truck"></i> My Vehicles
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($this->auth->isSearcher() || $this->auth->isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/search') !== false ? 'active' : ''; ?>" href="<?=  url('search')?>">
                            <i class="bi bi-search"></i> Search
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($this->auth->isAdmin()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= strpos($_SERVER['REQUEST_URI'], '/admin') !== false ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-shield-check"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?=  url('admin/users')?>">Users</a></li>
                            <li><a class="dropdown-item" href="<?=  url('admin/vehicles')?>">Vehicles</a></li>
                            <li><a class="dropdown-item" href="<?=  url('admin/audit')?>">Audit Trail</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
                <?php endif; ?>
                
                <ul class="navbar-nav ms-auto">
                    <?php if ($this->auth->isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="<?= user_avatar(); ?>" alt="Profile" class="rounded-circle" width="32" height="32">
                            <?= e($this->auth->getUserEmail()); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?=  url('profile')?>">
                                <i class="bi bi-person"></i> Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button class="dropdown-item theme-toggle" type="button">
                                    <i class="bi bi-moon"></i>
                                    <i class="bi bi-sun"></i>
                                    <span class="theme-text">Toggle Theme</span>
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?=  url('logout')?>">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=  url('login')?>">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=  url('register')?>">
                            <i class="bi bi-person-plus"></i> Register
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <?php if ($this->auth->isLoggedIn()): ?>
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : ''; ?>" href="<?=  url('dashboard')?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        <?php if ($this->auth->isDriver() || $this->auth->isSearcher()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/vehicles') !== false ? 'active' : ''; ?>" href="<?=  url('vehicles')?>">
                                <i class="bi bi-truck"></i> My Vehicles
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($this->auth->isSearcher() || $this->auth->isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/search') !== false ? 'active' : ''; ?>" href="<?=  url('search')?>">
                                <i class="bi bi-search"></i> Vehicle Search
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($this->auth->isAdmin()): ?>
                        <li class="nav-item">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                <span>Administration</span>
                            </h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : ''; ?>" href="<?=  url('admin/users')?>">
                                <i class="bi bi-people"></i> User Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/vehicles') !== false ? 'active' : ''; ?>" href="<?=  url('admin/vehicles')?>">
                                <i class="bi bi-truck-flatbed"></i> Vehicle Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/audit') !== false ? 'active' : ''; ?>" href="<?=  url('admin/audit')?>">
                                <i class="bi bi-clipboard-data"></i> Audit Trail
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/profile') !== false ? 'active' : ''; ?>" href="<?=  url('profile')?>">
                                <i class="bi bi-person"></i> My Profile
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <?php endif; ?>

            <!-- Main Content Area -->
            <main class="<?= $this->auth->isLoggedIn() ? 'col-md-9 ms-sm-auto col-lg-10 px-md-4' : 'col-12'; ?>">
                <!-- Flash Messages -->
                <div id="flash-messages" class="mt-3">
                    <?php
                    $flash = flash_message();
                    if ($flash): ?>
                    <div class="alert alert-<?= $flash['type']; ?> alert-dismissible fade show" role="alert">
                        <?= e($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Page Header -->
                <?php if (isset($title) && !isset($hide_header)): ?>
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= e($title); ?></h1>
                    <?php if (isset($actions)): ?>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <?= $actions; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Page Content -->
                <?= $content; ?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-dark text-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <span>&copy; <?= date('Y'); ?> Vehicle Tracker. All rights reserved.</span>
                </div>
                <div class="col-md-6 text-md-end">
                    <span>Version <?= $_ENV['APP_VERSION'] ?? '1.0.0'; ?></span>
                </div>
            </div>
        </div>
    </footer>

     <!-- JQuery JS -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
    <script src="<?=  url('public/assets/js/jquery.min.js')?>"></script>


    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="<?=  url('public/assets/js/bootstrap.bundle.min.js')?>"></script>

    <!-- Custom JS -->
    <script src="<?=  url('public/assets/js/theme.js')?>"></script>
    <script src="<?=  url('public/assets/js/validation.js')?>"></script>
    <script src="<?=  url('public/assets/js/ajax.js')?>"></script>
    <script src="<?=  url('public/assets/js/app.js')?>"></script>
    
    <!-- Page-specific JS -->
    <?php if (isset($scripts)): ?>
    <?= $scripts; ?>
    <?php endif; ?>
    
    <!-- Session timeout -->
    <script>
        document.body.setAttribute('data-session-timeout', '<?= $_ENV['SESSION_LIFETIME'] ?? 3600; ?>');
    </script>
</body>
</html>