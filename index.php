<?php
/**
 * Vehicle Tracker - Main Entry Point
 * Bootstrap the application and handle all requests
 */

ob_start(); // Start output buffering early

// -----------------------------------------------------------------------------
// ERROR REPORTING CONFIGURATION
// -----------------------------------------------------------------------------

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

// -----------------------------------------------------------------------------
// PATH CONSTANTS
// -----------------------------------------------------------------------------

define('APP_START', microtime(true));
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('CONFIG_PATH', BASE_PATH . '/config');
define('UPLOAD_PATH', PUBLIC_PATH . '/assets/uploads');

// Ensures cross-platform path compatibility
function normalize_path($path)
{
    return rtrim(str_replace(['\\', '//'], '/', $path), '/');
}

// -----------------------------------------------------------------------------
// REQUIRED DIRECTORIES
// -----------------------------------------------------------------------------

$requiredDirs = [
    STORAGE_PATH . '/cache',
    STORAGE_PATH . '/logs',
    STORAGE_PATH . '/sessions',
    STORAGE_PATH . '/backups',
    UPLOAD_PATH . '/profiles',
    UPLOAD_PATH . '/vehicles/images',
    UPLOAD_PATH . '/vehicles/documents'
];

foreach ($requiredDirs as $dir) {
    $dir = normalize_path($dir);

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// -----------------------------------------------------------------------------
// AUTOLOADER
// -----------------------------------------------------------------------------

require_once BASE_PATH . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;
use App\Core\Session;
use App\Core\Database;

// -----------------------------------------------------------------------------
// LOAD ENVIRONMENT
// -----------------------------------------------------------------------------

try {
    $dotenv = Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();

    $dotenv->required([
        'DB_HOST',
        'DB_NAME',
        'DB_USER'
    ])->notEmpty();

} catch (Throwable $e) {
    http_response_code(500);
    error_log("ENV load error: " . $e->getMessage());
    die("Application configuration error (.env missing or invalid).");
}

// Switch error mode based on APP_DEBUG
if (!empty($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// -----------------------------------------------------------------------------
// MAINTENANCE MODE
// -----------------------------------------------------------------------------

if (($_ENV['MAINTENANCE_MODE'] ?? 'false') === 'true') {

    $allowedIPs = array_filter(array_map('trim', explode(
        ',', 
        $_ENV['MAINTENANCE_ALLOWED_IPS'] ?? ''
    )));

    $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    if (!in_array('*', $allowedIPs) && !in_array($clientIP, $allowedIPs)) {

        http_response_code(503);
        header('Retry-After: 3600');

        $message = $_ENV['MAINTENANCE_MESSAGE'] ?? 'System is under maintenance.';

        // Handle AJAX
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => $message, 'maintenance' => true]);
        } else {
            include BASE_PATH . '/public/errors/maintenance.php';
        }

        exit;
    }
}

// -----------------------------------------------------------------------------
// SESSION
// -----------------------------------------------------------------------------

try {
    $session = new Session();

    // CSRF init
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }

} catch (Throwable $e) {
    error_log("Session init failed: " . $e->getMessage());
    http_response_code(500);
    die("Session initialization failed.");
}

// -----------------------------------------------------------------------------
// DATABASE
// -----------------------------------------------------------------------------

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $conn->query('SELECT 1');
} catch (Throwable $e) {
    error_log("DB error: " . $e->getMessage());
    http_response_code(500);

    if (!empty($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
        die("Database error: " . $e->getMessage());
    }

    die("Database connection failed.");
}

// -----------------------------------------------------------------------------
// ROUTING
// -----------------------------------------------------------------------------

try {
    $url = !empty($_SERVER['HTTPS'])  ? "https://" : "http://";
    $url .= $_SERVER['HTTP_HOST'];
    $url .= $_SERVER['REQUEST_URI'];
    $url = str_replace($_ENV['APP_URL']."/", '', $url) ?? '/';

    
    $url = filter_var($url, FILTER_SANITIZE_URL);
    $url = rtrim($url, '/') ?: '/';

    $router = new Router();
    $router->loadRoutes();

    $router->dispatch($url);

} catch (Throwable $e) {
    error_log("Routing error: " . $e->getMessage());
    http_response_code(500);

    if (!empty($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
        echo "<pre>";
        echo "Routing error: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString();
        echo "</pre>";
        exit;
    }

    header("Location: {$_ENV['APP_URL']}/errors/500");
    exit;
}

// -----------------------------------------------------------------------------
// ERROR HANDLERS
// -----------------------------------------------------------------------------

set_error_handler(function ($errno, $errstr, $file, $line) {

    if (!(error_reporting() & $errno)) {
        return false; // respect "@" suppression
    }

    $types = [
        E_ERROR => "Error",
        E_WARNING => "Warning",
        E_PARSE => "Parse Error",
        E_NOTICE => "Notice",
        E_DEPRECATED => "Deprecated"
    ];

    $type = $types[$errno] ?? "Unknown";

    error_log("[$type] $errstr in $file:$line");

    return true;
});


set_exception_handler(function ($ex) {

    error_log("Uncaught exception: " . $ex->getMessage());

    if (!empty($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
        echo "<pre>";
        echo $ex->getMessage() . "\n";
        echo $ex->getFile() . ":" . $ex->getLine() . "\n";
        echo $ex->getTraceAsString();
        echo "</pre>";
    } else {
        http_response_code(500);
        header("Location: {$_ENV['APP_URL']}/errors/500");
    }
});


register_shutdown_function(function () {

    $err = error_get_last();

    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {

        error_log("Fatal error: {$err['message']} in {$err['file']}:{$err['line']}");

        if (empty($_ENV['APP_DEBUG']) || $_ENV['APP_DEBUG'] !== 'true') {
            http_response_code(500);
            header("Location: {$_ENV['APP_URL']}/errors/500");
        }
    }
});

// -----------------------------------------------------------------------------
// FINISH
// -----------------------------------------------------------------------------

ob_end_flush();

if (!empty($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
    $ms = round((microtime(true) - APP_START) * 1000, 2);
    error_log("Request took {$ms}ms");
}

