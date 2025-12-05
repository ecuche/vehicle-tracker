<?php
/**
 * Vehicle Tracker - Database Configuration
 * Environment-based database configuration
 */

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

return [
    'driver' => $_ENV['DB_DRIVER'] ?? 'mysql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'database' => $_ENV['DB_NAME'] ?? 'vehicle_tracker',
    'username' => $_ENV['DB_USER'] ?? 'root',
    'password' => $_ENV['DB_PASS'] ?? '',
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    'collation' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
    'prefix' => $_ENV['DB_PREFIX'] ?? '',
    
    // PDO options
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . ($_ENV['DB_CHARSET'] ?? 'utf8mb4')
    ],
    
    // Connection pool settings (if using connection pooling)
    'pool' => [
        'min_connections' => $_ENV['DB_POOL_MIN'] ?? 1,
        'max_connections' => $_ENV['DB_POOL_MAX'] ?? 10,
        'timeout' => $_ENV['DB_POOL_TIMEOUT'] ?? 30
    ],
    
    // Read/write connections (for master-slave setup)
    'connections' => [
        'write' => [
            'host' => $_ENV['DB_WRITE_HOST'] ?? $_ENV['DB_HOST'] ?? 'localhost'
        ],
        'read' => [
            'host' => $_ENV['DB_READ_HOST'] ?? $_ENV['DB_HOST'] ?? 'localhost'
        ]
    ],
    
    // Migration settings
    'migrations' => [
        'table' => 'migrations',
        'path' => dirname(__DIR__) . '/database/migrations'
    ],
    
    // Seed settings
    'seeds' => [
        'path' => dirname(__DIR__) . '/database/seeds'
    ],
    
    // Backup settings
    'backup' => [
        'path' => dirname(__DIR__) . '/storage/backups',
        'compress' => $_ENV['DB_BACKUP_COMPRESS'] ?? true,
        'keep_days' => $_ENV['DB_BACKUP_KEEP_DAYS'] ?? 30
    ]
];