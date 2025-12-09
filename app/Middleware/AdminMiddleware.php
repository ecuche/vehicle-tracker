<?php
namespace App\Middleware;

use App\Core\Auth;
use App\Core\Session;

class AdminMiddleware {
    private $auth;
    private $session;

    public function __construct() {
        $this->auth = new Auth();
        $this->session = new Session();
    }

    public function handle($request, $next) {
        // First check if user is authenticated
        if (!$this->auth->isLoggedIn()) {
            $this->session->setFlash('error', 'Please log in to access this page.');
            header('Location: '.$_ENV['APP_URL'].'/login');
            exit;
        }

        // Check if user has admin role
        if (!$this->auth->isAdmin()) {
            $this->session->setFlash('error', 'Access denied. Administrator privileges required.');
            
            // Log unauthorized access attempt
            $this->logUnauthorizedAccess();
            
            // Redirect based on user role
            $userRole = $this->auth->getUserRole();
            switch ($userRole) {
                case 'driver':
                case 'searcher':
                    header('Location: '.$_ENV['APP_URL'].'/dashboard');
                    break;
                default:
                    header('Location: '.$_ENV['APP_URL'].'/login');
                    break;
            }
            exit;
        }

        // Additional admin-specific security checks
        if (!$this->passesAdminSecurityChecks()) {
            $this->session->setFlash('error', 'Security check failed. Please contact system administrator.');
            header('Location: '.$_ENV['APP_URL'].'/login');
            exit;
        }

        // Check if admin account is active and not banned
        $user = $this->auth->getUser();
        if ($user && $user['is_banned']) {
            $this->auth->logout();
            $this->session->setFlash('error', 'Your administrator account has been suspended.');
            header('Location: '.$_ENV['APP_URL'].'/login');
            exit;
        }

        // Log admin access for audit trail
        $this->logAdminAccess($request['url']);

        return $next($request);
    }

    private function passesAdminSecurityChecks() {
        // Check if session is valid
        if (!$this->auth->validateSession()) {
            return false;
        }

        // Check if user agent matches
        $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $storedUserAgent = $this->session->get('user_agent');
        
        if ($storedUserAgent && $currentUserAgent !== $storedUserAgent) {
            return false;
        }

        // Additional security checks can be added here
        // For example: IP whitelisting, two-factor authentication, etc.

        return true;
    }

    private function logUnauthorizedAccess() {
        $user = $this->auth->getUser();
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $requestedUrl = $_SERVER['REQUEST_URI'] ?? 'unknown';

        error_log("UNAUTHORIZED ACCESS: User {$user['email']} (ID: {$user['id']}) attempted to access admin area from IP: {$ipAddress}, URL: {$requestedUrl}");

        // In a production environment, you might want to log this to a security database
        // or send an alert to administrators
    }

    private function logAdminAccess($url) {
        $user = $this->auth->getUser();
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Log sensitive admin actions for audit trail
        $sensitiveActions = [
            '/admin/users',
            '/admin/vehicles',
            '/admin/audit',
            '/admin/update-role',
            '/admin/toggle-ban',
            '/admin/update-vehicle-status'
        ];

        foreach ($sensitiveActions as $action) {
            if (strpos($url, $action) === 0) {
                error_log("ADMIN ACTION: User {$user['email']} accessed {$url} from IP: {$ipAddress}");
                break;
            }
        }
    }

    public static function check() {
        $middleware = new self();
        return $middleware->auth->isAdmin();
    }

    public static function requireAdmin() {
        $middleware = new self();
        
        if (!$middleware->auth->isLoggedIn()) {
            $middleware->session->setFlash('error', 'Please log in to access this page.');
            header('Location: '.$_ENV['APP_URL'].'/login');
            exit;
        }

        if (!$middleware->auth->isAdmin()) {
            $middleware->session->setFlash('error', 'Access denied. Administrator privileges required.');
            header('Location: '.$_ENV['APP_URL'].'/dashboard');
            exit;
        }
    }

    // Method to check if current user can manage specific resources
    public static function canManageUser($targetUserId) {
        $middleware = new self();
        
        if (!$middleware->auth->isAdmin()) {
            return false;
        }

        // Admins cannot manage themselves (for certain actions)
        $currentUserId = $middleware->auth->getUserId();
        return $currentUserId != $targetUserId;
    }

    // Method to check if current user can perform dangerous operations
    public static function canPerformDangerousOperation() {
        $middleware = new self();
        
        if (!$middleware->auth->isAdmin()) {
            return false;
        }

        // Additional checks for dangerous operations
        // For example: require recent password confirmation
        $lastPasswordConfirm = $middleware->session->get('last_password_confirm');
        if (!$lastPasswordConfirm || (time() - $lastPasswordConfirm) > 1800) { // 30 minutes
            return false;
        }

        return true;
    }

    // Method to confirm password for sensitive operations
    public static function confirmPassword($password) {
        $middleware = new self();
        $user = $middleware->auth->getUser();
        
        if ($user && $middleware->auth->verifyPassword($password, $user['password'])) {
            $middleware->session->set('last_password_confirm', time());
            return true;
        }
        
        return false;
    }
}
?>