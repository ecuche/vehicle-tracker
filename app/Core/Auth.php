<?php
namespace App\Core;

use App\Models\User;
use App\Core\Request;

class Auth {
    private $session;
    private $user;
    private $db;
    private $request;

    public function __construct() {
        $this->session = new Session();
        $this->user = new User();
        $this->db = Database::getInstance();
        $this->request = new Request();
    }

    public function login($user) {
        $this->user->updateLastLogin($user['id']);
        $this->session->set('user_id', $user['id']);
        $this->session->set('user_role', $user['role']);
        $this->session->set('user_email', $user['email']);
        $this->session->set('login_time', time());
        $this->session->set('last_activity', time());
        
        // Generate new session ID to prevent fixation
        $this->session->regenerate();
        
        // Log login activity
        $this->logActivity('login');
    }

    public function logout() {
        // Log logout activity
        $this->logActivity('logout');
        
        $this->session->destroy();
    }

    public function isLoggedIn() {
        if (!$this->session->has('user_id')) {
            return false;
        }

        // Check session timeout
        if ($this->isSessionExpired()) {
            $this->logout();
            return false;
        }

        // Update last activity
        $this->session->set('last_activity', time());

        return true;
    }

    public function isAdmin() {
        return $this->isLoggedIn() && $this->session->get('user_role') === 'admin';
    }

    public function isDriver() {
        return $this->isLoggedIn() && $this->session->get('user_role') === 'driver';
    }

    public function isSearcher() {
        return $this->isLoggedIn() && $this->session->get('user_role') === 'searcher';
    }

    public function getUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $user_id = $this->session->get('user_id');
        return $this->user->findById($user_id);
    }

    public function getUserId() {
        return $this->session->get('user_id');
    }

    public function getUserRole() {
        return $this->session->get('user_role');
    }

    public function getUserEmail() {
        return $this->session->get('user_email');
    }

    private function isSessionExpired() {
        $lastActivity = $this->session->get('last_activity');
        $sessionLifetime = $_ENV['SESSION_TIMEOUT'] ?? 3600; // 1 hour default

        if (!$lastActivity) {
            return true;
        }

        return (time() - $lastActivity) > $sessionLifetime;
    }

    public function checkPermission($requiredRole) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $userRole = $this->getUserRole();

        // Define role hierarchy
        $roleHierarchy = [
            'admin' => ['admin', 'driver', 'searcher'],
            'driver' => ['driver'],
            'searcher' => ['searcher']
        ];

        return in_array($userRole, $roleHierarchy[$requiredRole] ?? []);
    }

    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            $this->session->setFlash('error', 'Please log in to access this page.');
            $this->request->redirect('login');
            exit;
        }
    }

    public function requireRole($role) {
        $this->requireAuth();

        if (!$this->checkPermission($role)) {
            $this->session->setFlash('error', 'You do not have permission to access this page.');
            $this->request->redirect('dashboard');
            exit;
        }
    }

    public function requireAdmin() {
        $this->requireRole('admin');
    }

    // Rate limiting methods
    public function isRateLimited($identifier, $maxAttempts = 5, $timeout = 900) {
        $key = 'login_attempts_' . md5($identifier);
        return !$this->session->checkRateLimit($key, $maxAttempts, $timeout);
    }

    public function recordFailedAttempt($identifier, $maxAttempts = 5, $timeout = 900) {
        $key = 'login_attempts_' . md5($identifier);
        $this->session->setRateLimit($key, $maxAttempts, $timeout);
    }

    public function resetRateLimit($identifier) {
        $key = 'login_attempts_' . md5($identifier);
        $this->session->clearRateLimit($key);
    }

    public function getRemainingAttempts($identifier) {
        $key = 'login_attempts_' . md5($identifier);
        $rateLimitData = $this->session->get('rate_limits', []);
        
        if (!isset($rateLimitData[$key])) {
            return $_ENV['LOGIN_ATTEMPTS'] ?? 5;
        }
        
        $data = $rateLimitData[$key];
        $maxAttempts = $_ENV['LOGIN_ATTEMPTS'] ?? 5;
        
        return max(0, $maxAttempts - $data['count']);
    }

    // Password verification
    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function needsRehash($hashedPassword) {
        return password_needs_rehash($hashedPassword, PASSWORD_DEFAULT);
    }

    // Session security
    public function validateSession() {
        if (!$this->isLoggedIn()) {
            return false;
        }

        // Check if user agent matches
        $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $storedUserAgent = $this->session->get('user_agent');

        if (!$storedUserAgent) {
            $this->session->set('user_agent', $currentUserAgent);
            return true;
        }

        if ($currentUserAgent !== $storedUserAgent) {
            $this->logout();
            return false;
        }

        // Check IP address (optional - can be too strict for users with dynamic IPs)
        $checkIP = $_ENV['SESSION_CHECK_IP'] ?? false;
        if ($checkIP) {
            $currentIP = $this->getClientIP();
            $storedIP = $this->session->get('user_ip');

            if (!$storedIP) {
                $this->session->set('user_ip', $currentIP);
                return true;
            }

            if ($currentIP !== $storedIP) {
                $this->logout();
                return false;
            }
        }

        return true;
    }

    private function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    // Activity logging
    private function logActivity($action) {
        $user_id = $this->getUserId();
        if (!$user_id) {
            return;
        }

        $ip_address = $this->getClientIP();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        // In a real application, you might want to log this to a database
        error_log("User activity: User {$user_id} performed {$action} from IP {$ip_address}");
    }

    // CSRF protection
    public function generateCSRFToken() {
        return $this->session->generateCSRFToken();
    }

    public function validateCSRFToken($token) {
        return $this->session->validateCSRFToken($token);
    }

    // Password reset functionality
    public function generateResetToken() {
        return bin2hex(random_bytes(32));
    }

    public function validateResetToken($token) {
        $resetRequest = $this->user->findPasswordReset($token);
        
        if (!$resetRequest) {
            return false;
        }

        if (strtotime($resetRequest->expires_at) < time()) {
            $this->user->deletePasswordReset($token);
            return false;
        }

        return $resetRequest;
    }

    // Account locking
    public function isAccountLocked($user) {
        return $user['is_banned'] == 1;
    }

    public function lockAccount($user_id) {
        return $this->user->ban($user_id);
    }

    public function unlockAccount($user_id) {
        return $this->user->unban($user_id);
    }

    // Session extension for "remember me" functionality (simplified)
    public function rememberMe() {
        $this->session->set('remember_me', true);
        // In a real implementation, you'd set a long-lived cookie
    }

    public function extendSession() {
        $this->session->extend();
    }

    // Utility methods
    public function getLoginTime() {
        return $this->session->get('login_time');
    }

    public function getSessionDuration() {
        $loginTime = $this->getLoginTime();
        return $loginTime ? time() - $loginTime : 0;
    }

    public function getSessionInfo() {
        return [
            'user_id' => $this->getUserId(),
            'user_role' => $this->getUserRole(),
            'login_time' => $this->getLoginTime(),
            'session_duration' => $this->getSessionDuration(),
            'last_activity' => $this->session->get('last_activity')
        ];
    }
}
?>