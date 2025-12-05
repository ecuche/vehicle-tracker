<?php
/**
 * Vehicle Tracker - Application Configuration
 * Main application settings and configuration
 */

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

return [
    /*
    |--------------------------------------------------------------------------
    | Application Information
    |--------------------------------------------------------------------------
    */
    'name' => $_ENV['APP_NAME'] ?? 'Vehicle Tracker',
    'version' => $_ENV['APP_VERSION'] ?? '1.0.0',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    
    /*
    |--------------------------------------------------------------------------
    | Time and Location Configuration
    |--------------------------------------------------------------------------
    */
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'Africa/Lagos',
    'locale' => $_ENV['APP_LOCALE'] ?? 'en',
    'fallback_locale' => $_ENV['APP_FALLBACK_LOCALE'] ?? 'en',
    
    /*
    |--------------------------------------------------------------------------
    | Encryption Configuration
    |--------------------------------------------------------------------------
    */
    'encryption' => [
        'key' => $_ENV['APP_ENCRYPTION_KEY'] ?? '',
        'cipher' => $_ENV['APP_ENCRYPTION_CIPHER'] ?? 'AES-256-CBC'
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Session Configuration
    |--------------------------------------------------------------------------
    */
    'session' => [
        'driver' => $_ENV['SESSION_DRIVER'] ?? 'file',
        'lifetime' => $_ENV['SESSION_LIFETIME'] ?? 120,
        'expire_on_close' => filter_var($_ENV['SESSION_EXPIRE_ON_CLOSE'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'encrypt' => filter_var($_ENV['SESSION_ENCRYPT'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'same_site' => $_ENV['SESSION_SAME_SITE'] ?? 'lax'
    ],
    
    /*
    |--------------------------------------------------------------------------
    | File Upload Configuration
    |--------------------------------------------------------------------------
    */
    'upload' => [
        'max_size' => $_ENV['UPLOAD_MAX_SIZE'] ?? 5242880, // 5MB
        'allowed_types' => explode(',', $_ENV['UPLOAD_ALLOWED_TYPES'] ?? 'image/jpeg,image/png,image/gif,application/pdf'),
        'path' => $_ENV['UPLOAD_PATH'] ?? dirname(__DIR__) . '/public/assets/uploads',
        'url' => $_ENV['UPLOAD_URL'] ?? '/assets/uploads'
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    */
    'email' => [
        'driver' => $_ENV['MAIL_DRIVER'] ?? 'smtp',
        'host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
        'port' => $_ENV['MAIL_PORT'] ?? 587,
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'from' => [
            'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@vehicletracker.com',
            'name' => $_ENV['MAIL_FROM_NAME'] ?? 'Vehicle Tracker'
        ]
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */
    'security' => [
        'csrf_protection' => filter_var($_ENV['CSRF_PROTECTION'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'xss_protection' => filter_var($_ENV['XSS_PROTECTION'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'rate_limiting' => [
            'enabled' => filter_var($_ENV['RATE_LIMITING_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'max_attempts' => $_ENV['RATE_LIMITING_MAX_ATTEMPTS'] ?? 5,
            'decay_minutes' => $_ENV['RATE_LIMITING_DECAY_MINUTES'] ?? 1
        ]
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'driver' => $_ENV['CACHE_DRIVER'] ?? 'file',
        'path' => dirname(__DIR__) . '/storage/cache',
        'lifetime' => $_ENV['CACHE_LIFETIME'] ?? 3600
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'driver' => $_ENV['LOG_DRIVER'] ?? 'file',
        'path' => dirname(__DIR__) . '/storage/logs',
        'level' => $_ENV['LOG_LEVEL'] ?? 'error',
        'max_files' => $_ENV['LOG_MAX_FILES'] ?? 7
    ],
    
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */
    'api' => [
        'prefix' => $_ENV['API_PREFIX'] ?? 'api',
        'version' => $_ENV['API_VERSION'] ?? 'v1',
        'rate_limit' => $_ENV['API_RATE_LIMIT'] ?? 60,
        'cors' => [
            'allowed_origins' => explode(',', $_ENV['API_CORS_ALLOWED_ORIGINS'] ?? '*'),
            'allowed_methods' => explode(',', $_ENV['API_CORS_ALLOWED_METHODS'] ?? 'GET,POST,PUT,DELETE,OPTIONS'),
            'allowed_headers' => explode(',', $_ENV['API_CORS_ALLOWED_HEADERS'] ?? '*')
        ]
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */
    'features' => [
        'user_registration' => filter_var($_ENV['FEATURE_USER_REGISTRATION'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'email_verification' => filter_var($_ENV['FEATURE_EMAIL_VERIFICATION'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'password_reset' => filter_var($_ENV['FEATURE_PASSWORD_RESET'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'vehicle_transfers' => filter_var($_ENV['FEATURE_VEHICLE_TRANSFERS'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'admin_dashboard' => filter_var($_ENV['FEATURE_ADMIN_DASHBOARD'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'audit_trail' => filter_var($_ENV['FEATURE_AUDIT_TRAIL'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'dark_mode' => filter_var($_ENV['FEATURE_DARK_MODE'] ?? true, FILTER_VALIDATE_BOOLEAN)
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Vehicle Tracking Specific Settings
    |--------------------------------------------------------------------------
    */
    'vehicle_tracking' => [
        'vin_validation' => filter_var($_ENV['VIN_VALIDATION'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'plate_validation' => filter_var($_ENV['PLATE_VALIDATION'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'max_vehicles_per_user' => $_ENV['MAX_VEHICLES_PER_USER'] ?? 10,
        'transfer_cooldown_hours' => $_ENV['TRANSFER_COOLDOWN_HOURS'] ?? 24,
        'stolen_vehicle_alerts' => filter_var($_ENV['STOLEN_VEHICLE_ALERTS'] ?? true, FILTER_VALIDATE_BOOLEAN)
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Nigerian Specific Settings
    |--------------------------------------------------------------------------
    */
    'nigerian' => [
        'country_code' => '234',
        'currency' => 'NGN',
        'states' => [
            'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
            'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'Gombe', 'Imo',
            'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos',
            'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers',
            'Sokoto', 'Taraba', 'Yobe', 'Zamfara', 'FCT'
        ]
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode
    |--------------------------------------------------------------------------
    */
    'maintenance' => [
        'enabled' => filter_var($_ENV['MAINTENANCE_MODE'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'allowed_ips' => explode(',', $_ENV['MAINTENANCE_ALLOWED_IPS'] ?? ''),
        'message' => $_ENV['MAINTENANCE_MESSAGE'] ?? 'System is under maintenance. Please try again later.'
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    */
    'backup' => [
        'enabled' => filter_var($_ENV['BACKUP_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'schedule' => $_ENV['BACKUP_SCHEDULE'] ?? '0 2 * * *', // Daily at 2 AM
        'keep_days' => $_ENV['BACKUP_KEEP_DAYS'] ?? 30,
        'compress' => filter_var($_ENV['BACKUP_COMPRESS'] ?? true, FILTER_VALIDATE_BOOLEAN)
    ]
];