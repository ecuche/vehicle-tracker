<?php
/**
 * Application Helper Functions
 * Various utility functions used throughout the application
 */

use App\Core\Auth;
use App\Core\Session;
use App\Core\Upload;

/**
 * Get the base URL of the application
 */
function base_url($path = '') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $base = $base ? '/' . $base : '';
    
    $url = $protocol . '://' . $host . $base;
    
    if ($path) {
        $url .= '/' . ltrim($path, '/');
    }
    
    return $url;
}

function url($link = ''){
    return base_url($link);
}


/**
 * Get the current URL
 */
function current_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    
    return $protocol . '://' . $host . $uri;
}

/**
 * Redirect to a specific URL
 */
function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Get old form input value (for form repopulation)
 */
function old($field, $default = '') {
    $session = new Session();
    $formData = $session->getFormData($field) ?? '';
    return $formData ?? $default;
}

/**
 * Check if form has errors for a specific field
 */
function has_error($field) {
    $session = new Session();
    $errors = $session->hasError($field) ?? [];
    return $errors ?? null;
}

/**
 * Get error message for a specific field
 */
function get_error($field) {
    $session = new Session();
    $errors = $session->getError($field) ?? [];
    return $errors ?? '';
}

/**
 * Display CSRF token field
 */
function csrf_field() {
    $auth = new Auth();
    $token = $auth->generateCSRFToken();
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Get CSRF token value
 */
function csrf_token() {
    $auth = new Auth();
    return $auth->generateCSRFToken();
}

/**
 * Format date for display
 */
function format_date($date, $format = 'Y-m-d H:i:s') {
    if (!$date) return '';
    
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Format date as relative time (e.g., "2 hours ago")
 */
function relative_time($date) {
    if (!$date) return '';
    
    $now = new DateTime();
    $then = new DateTime($date);
    $diff = $now->diff($then);
    
    if ($diff->y > 0) {
        return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    } elseif ($diff->m > 0) {
        return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    } elseif ($diff->d > 0) {
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    } elseif ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    } elseif ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    } else {
        return 'Just now';
    }
}

/**
 * Format file size in human-readable format
 */
function format_filesize($bytes) {
    if ($bytes == 0) return '0 B';
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $base = log($bytes, 1024);
    $unit = $units[floor($base)];
    
    return round(pow(1024, $base - floor($base)), 2) . ' ' . $unit;
}

/**
 * Get file URL from upload path
 */
function file_url($filePath) {
    if (empty($filePath)) {
        return $_ENV['APP_URL'].'/public/assets/images/default-avatar.png'; // Default avatar
    }
    
    $upload = new Upload();
    return $upload->getFileUrl($filePath);
}

/**
 * Truncate text with ellipsis
 */
function truncate($text, $length = 100, $ellipsis = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $ellipsis;
}

/**
 * Sanitize output for HTML display
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate random string
 */
function str_random($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

/**
 * Check if string contains another string
 */
// function str_contains($haystack, $needle) {
//     return strpos($haystack, $needle) !== false;
// }

/**
 * Convert string to slug
 */
function str_slug($string, $separator = '-') {
    // Convert to lowercase
    $string = strtolower($string);
    
    // Replace non-alphanumeric characters with separator
    $string = preg_replace('/[^a-z0-9]+/', $separator, $string);
    
    // Trim separators from both ends
    $string = trim($string, $separator);
    
    return $string;
}

/**
 * Get user avatar URL
 */
function user_avatar($user = null) {
    if (!$user) {
        $auth = new Auth();
        $user = $auth->getUser();
    }
    
    if ($user && $user['profile_picture']) {
        return file_url($user['profile_picture']);
    }
    
    return url('/public/assets/images/default-avatar.png');
}

/**
 * Check if current user has role
 */
function has_role($role) {
    $auth = new Auth();
    return $auth->getUserRole() === $role;
}

/**
 * Check if current user is admin
 */
function is_admin() {
    $auth = new Auth();
    return $auth->isAdmin();
}

/**
 * Check if current user is driver
 */
function is_driver() {
    $auth = new Auth();
    return $auth->isDriver();
}

/**
 * Check if current user is searcher
 */
function is_searcher() {
    $auth = new Auth();
    return $auth->isSearcher();
}

/**
 * Get flash message and display it
 */
function flash_message($type = null) {
    $session = new Session();
    
    if ($type) {
        return $session->getFlash($type);
    }
    
    // Check for any flash messages
    $types = ['success', 'error', 'warning', 'info'];
    foreach ($types as $flashType) {
        if ($session->hasFlash($flashType)) {
            return [
                'type' => $flashType,
                'message' => $session->getFlash($flashType)
            ];
        }
    }
    
    return null;
}

/**
 * Display pagination links
 */
function pagination_links($currentPage, $totalPages, $url, $perPage = null) {
    if ($totalPages <= 1) return '';
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        $prevUrl = $url . '?page=' . ($currentPage - 1);
        if ($perPage) $prevUrl .= '&per_page=' . $perPage;
        $html .= '<li class="page-item"><a class="page-link" href="' .$_ENV['APP_URL'] .$prevUrl . '">Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    for ($page = $startPage; $page <= $endPage; $page++) {
        $pageUrl = $url . '?page=' . $page;
        if ($perPage) $pageUrl .= '&per_page=' . $perPage;
        
        if ($page == $currentPage) {
            $html .= '<li class="page-item active"><span class="page-link">' . $page . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' .$_ENV['APP_URL']. $pageUrl . '">' . $page . '</a></li>';
        }
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $nextUrl = $url . '?page=' . ($currentPage + 1);
        if ($perPage) $nextUrl .= '&per_page=' . $perPage;
        $html .= '<li class="page-item"><a class="page-link" href="' .$_ENV['APP_URL']. $nextUrl . '">Next</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Get vehicle status badge
 */
function vehicle_status_badge($status) {
    $statusClasses = [
        'none' => 'bg-secondary',
        'stolen' => 'bg-danger',
        'no_customs_duty' => 'bg-warning',
        'changed_engine' => 'bg-info',
        'changed_color' => 'bg-primary'
    ];
    
    $statusLabels = [
        'none' => 'Normal',
        'stolen' => 'Stolen',
        'no_customs_duty' => 'No Customs Duty',
        'changed_engine' => 'Changed Engine',
        'changed_color' => 'Changed Color'
    ];
    
    $class = $statusClasses[$status] ?? 'bg-secondary';
    $label = $statusLabels[$status] ?? ucfirst($status);
    
    return '<span class="badge ' . $class . '">' . $label . '</span>';
}

/**
 * Get transfer status badge
 */
function transfer_status_badge($status) {
    $statusClasses = [
        'pending' => 'bg-warning',
        'accepted' => 'bg-success',
        'rejected' => 'bg-danger'
    ];
    
    $class = $statusClasses[$status] ?? 'bg-secondary';
    $label = ucfirst($status);
    
    return '<span class="badge ' . $class . '">' . $label . '</span>';
}

/**
 * Format Nigerian phone number for display
 */
function format_phone($phone) {
    if (empty($phone)) return '';
    
    // Remove all non-digit characters
    $clean = preg_replace('/\D/', '', $phone);
    
    // Format based on length
    if (strlen($clean) === 11 && substr($clean, 0, 1) === '0') {
        // 08012345678 -> 0801 234 5678
        return substr($clean, 0, 4) . ' ' . substr($clean, 4, 3) . ' ' . substr($clean, 7, 4);
    } elseif (strlen($clean) === 13 && substr($clean, 0, 3) === '234') {
        // 2348012345678 -> 0801 234 5678
        return '0' . substr($clean, 3, 3) . ' ' . substr($clean, 6, 3) . ' ' . substr($clean, 9, 4);
    }
    
    // Return original if format doesn't match
    return $phone;
}

/**
 * Get current theme (dark/light mode)
 */
function current_theme() {
    $session = new Session();
    return $session->get('theme', 'dark'); // Default to dark mode
}

/**
 * Get theme class for body
 */
function theme_class() {
    return 'theme-' . current_theme();
}

/**
 * Debug function (for development)
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Debug function without dying
 */
function dump($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    return null;
}


// Helper functions
function getEventColor($eventType) {
    $colors = [
        'registration' => 'primary',
        'transfer' => 'success',
        'status_change' => 'warning',
        'plate_assignment' => 'info',
        'document_upload' => 'secondary',
        'image_upload' => 'dark',
        'update' => 'light'
    ];
    return $colors[$eventType] ?? 'secondary';
}

function getEventIcon($eventType) {
    $icons = [
        'registration' => 'bi-plus-circle',
        'transfer' => 'bi-arrow-left-right',
        'status_change' => 'bi-info-circle',
        'plate_assignment' => 'bi-123',
        'document_upload' => 'bi-file-earmark',
        'image_upload' => 'bi-image',
        'update' => 'bi-pencil'
    ];
    return $icons[$eventType] ?? 'bi-circle';
}

function formatEventType($eventType) {
    return ucwords(str_replace('_', ' ', $eventType));
}

function getStatusColor($status) {
    $colors = [
        'none' => 'success',
        'stolen' => 'danger',
        'no_customs_duty' => 'warning',
        'changed_engine' => 'info',
        'changed_color' => 'secondary'
    ];
    return $colors[$status] ?? 'secondary';
}

function getStatusIcon($status) {
    $icons = [
        'none' => 'check-circle',
        'stolen' => 'exclamation-triangle',
        'no_customs_duty' => 'shield-exclamation',
        'changed_engine' => 'gear',
        'changed_color' => 'palette'
    ];
    return $icons[$status] ?? 'question-circle';
}

function formatStatus($status) {
    $labels = [
        'none' => 'Normal',
        'stolen' => 'Stolen',
        'no_customs_duty' => 'No Customs Duty',
        'changed_engine' => 'Changed Engine',
        'changed_color' => 'Changed Color'
    ];
    return $labels[$status] ?? ucfirst($status);
}

function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

// Helper function to build pagination URLs
function buildPageUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '/admin/users/' . ($GLOBALS['user']['id'] ?? '') . '/vehicles?' . http_build_query($params);
}

// Helper function to build previous owners pagination URLs
function buildPreviousOwnersPageUrl($page) {
    $params = $_GET;
    $params['previous_page'] = $page;
    return '/admin/vehicles/' . ($GLOBALS['vehicle']['id'] ?? '') . '/users?' . http_build_query($params);
}

// Helper functions for PHP
function getActionBadgeClass($action) {
    $actionClasses = [
        'login' => 'login',
        'logout' => 'logout',
        'failed_login' => 'failed_login',
        'register' => 'register',
        'update_profile' => 'update_profile',
        'change_password' => 'change_password',
        'vehicle_register' => 'vehicle_register',
        'vehicle_transfer' => 'vehicle_transfer',
        'document_upload' => 'document_upload',
        'status_change' => 'status_change'
    ];
    return $actionClasses[$action] ?? 'secondary';
}

function formatAction($action) {
    return ucwords(str_replace('_', ' ', $action));
}

function getStatusBadgeClass($status) {
    $statusClasses = [
        'success' => 'success',
        'failed' => 'danger',
        'warning' => 'warning',
        'error' => 'danger'
    ];
    return $statusClasses[$status] ?? 'secondary';
}

function formatTableName($tableName) {
    $tableNames = [
        'users' => 'Users',
        'vehicles' => 'Vehicles',
        'transfers' => 'Transfers',
        'documents' => 'Documents',
        'plate_numbers' => 'Plate Numbers',
        'audit_logs' => 'Audit Logs'
    ];
    return $tableNames[$tableName] ?? ucfirst($tableName);
}

function truncateUserAgent($userAgent, $length = 50) {
    if (strlen($userAgent) <= $length) {
        return $userAgent;
    }
    return substr($userAgent, 0, $length) . '...';
}

// Helper function to build pagination URLs
function buildPageUrlAudit($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '/admin/users/' . ($GLOBALS['user']['id'] ?? '') . '/audit?' . http_build_query($params);
}
?>