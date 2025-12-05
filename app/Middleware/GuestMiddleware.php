<?php
namespace App\Middleware;

use App\Core\Auth;
use App\Core\Session;

class GuestMiddleware {
    private $auth;
    private $session;

    public function __construct() {
        $this->auth = new Auth();
        $this->session = new Session();
    }

    public function handle($request, $next) {
        // Check if user is already authenticated
        if ($this->auth->isLoggedIn()) {
            // Get intended URL or default to dashboard
            $intendedUrl = $this->session->get('intended_url', '/dashboard');
            $this->session->remove('intended_url');
            
            $this->session->setFlash('info', 'You are already logged in.');
            header('Location: ' . $intendedUrl);
            exit;
        }

        // Additional guest-specific checks
        $this->performGuestSecurityChecks();

        // Apply rate limiting to guest routes
        if (!$this->checkRateLimiting($request['url'])) {
            $this->session->setFlash('error', 'Too many requests. Please try again later.');
            header('Location: /login');
            exit;
        }

        return $next($request);
    }

    private function performGuestSecurityChecks() {
        // Check for suspicious activity patterns
        if ($this->detectSuspiciousActivity()) {
            $this->logSuspiciousActivity();
            // You might want to implement additional measures here,
            // like temporarily blocking the IP or requiring CAPTCHA
        }

        // Validate CSRF token for POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRFToken();
        }
    }

    private function checkRateLimiting($url) {
        $clientIP = $this->getClientIP();
        
        // Different rate limits for different routes
        $rateLimits = [
            '/login' => ['attempts' => 5, 'timeframe' => 900],    // 5 attempts per 15 minutes
            '/register' => ['attempts' => 3, 'timeframe' => 3600], // 3 attempts per hour
            '/forgot-password' => ['attempts' => 3, 'timeframe' => 3600] // 3 attempts per hour
        ];

        foreach ($rateLimits as $route => $limit) {
            if (strpos($url, $route) === 0) {
                $key = 'guest_' . $route . '_' . md5($clientIP);
                return $this->session->checkRateLimit($key, $limit['attempts'], $limit['timeframe']);
            }
        }

        return true;
    }

    private function detectSuspiciousActivity() {
        $clientIP = $this->getClientIP();
        
        // Check for rapid successive requests
        $lastRequestTime = $this->session->get('last_guest_request');
        $currentTime = time();
        
        if ($lastRequestTime && ($currentTime - $lastRequestTime) < 1) {
            // Less than 1 second between requests - suspicious
            return true;
        }
        
        $this->session->set('last_guest_request', $currentTime);

        // Check for multiple failed attempts from same IP
        $failedAttempts = $this->session->get('failed_attempts_' . md5($clientIP), 0);
        if ($failedAttempts > 10) {
            return true;
        }

        return false;
    }

    private function logSuspiciousActivity() {
        $clientIP = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $requestedUrl = $_SERVER['REQUEST_URI'] ?? 'unknown';

        error_log("SUSPICIOUS ACTIVITY: Guest access from IP: {$clientIP}, User-Agent: {$userAgent}, URL: {$requestedUrl}");

        // In production, you might want to:
        // - Log to a security database
        // - Send alert to administrators
        // - Implement IP blocking for repeated offenses
    }

    private function validateCSRFToken() {
        $csrfToken = $_POST['csrf_token'] ?? '';
        
        if (!$this->auth->validateCSRFToken($csrfToken)) {
            $this->session->setFlash('error', 'Invalid security token. Please try again.');
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
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

    public static function check() {
        $middleware = new self();
        return !$middleware->auth->isLoggedIn();
    }

    // Method to increment failed attempts (for login, registration, etc.)
    public static function recordFailedAttempt($action) {
        $middleware = new self();
        $clientIP = $middleware->getClientIP();
        
        $key = 'failed_attempts_' . md5($clientIP);
        $currentAttempts = $middleware->session->get($key, 0);
        $middleware->session->set($key, $currentAttempts + 1);

        // Also record per-action attempts for rate limiting
        $actionKey = 'guest_' . $action . '_' . md5($clientIP);
        $middleware->session->setRateLimit($actionKey, 1, 3600); // Track attempts
    }

    // Method to clear failed attempts (on successful action)
    public static function clearFailedAttempts() {
        $middleware = new self();
        $clientIP = $middleware->getClientIP();
        
        $key = 'failed_attempts_' . md5($clientIP);
        $middleware->session->remove($key);
    }

    // Method to check if CAPTCHA should be required
    public static function requiresCaptcha() {
        $middleware = new self();
        $clientIP = $middleware->getClientIP();
        
        $failedAttempts = $middleware->session->get('failed_attempts_' . md5($clientIP), 0);
        return $failedAttempts >= 3; // Require CAPTCHA after 3 failed attempts
    }

    // Method to generate and store CAPTCHA data
    public static function generateCaptcha() {
        $middleware = new self();
        
        // Simple CAPTCHA implementation
        $captchaText = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
        $middleware->session->set('captcha_text', $captchaText);
        $middleware->session->set('captcha_generated', time());
        
        return $captchaText;
    }

    // Method to validate CAPTCHA
    public static function validateCaptcha($userInput) {
        $middleware = new self();
        
        $storedCaptcha = $middleware->session->get('captcha_text');
        $generatedTime = $middleware->session->get('captcha_generated');
        
        // CAPTCHA expires after 10 minutes
        if (!$storedCaptcha || !$generatedTime || (time() - $generatedTime) > 600) {
            $middleware->session->remove('captcha_text');
            $middleware->session->remove('captcha_generated');
            return false;
        }
        
        $isValid = strtoupper($userInput) === $storedCaptcha;
        
        // Clear CAPTCHA after validation (whether successful or not)
        $middleware->session->remove('captcha_text');
        $middleware->session->remove('captcha_generated');
        
        return $isValid;
    }
}
?>