<?php
namespace App\Core;

class Session {
    private $sessionLifetime;

    public function __construct() {
        // Load environment variables
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        $this->sessionLifetime = $_ENV['SESSION_TIMEOUT'] ?? 3600; // 1 hour default

        $this->init();
    }

    private function init() {

         if (session_status() === PHP_SESSION_NONE) {
            // Session configuration
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.gc_maxlifetime', $this->sessionLifetime);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_only_cookies', 1);

            session_name('VEHICLE_TRACKER_SESSION');

            session_start();
        }

        // Regenerate session ID periodically to prevent fixation
        if (!isset($_SESSION['last_regeneration'])) {
            $this->regenerate();
        } elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
            $this->regenerate();
        }

        // Check session expiration
        $this->checkTimeout();
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            return true;
        }
        return false;
    }

    public function destroy() {
        session_unset();
        session_destroy();
        session_write_close();
        
        // Clear session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
    }

    public function regenerate() {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }

    public function setFlash($key, $value) {
        $_SESSION['flash'][$key] = $value;
    }

    public function getFlash($key) {
        if (isset($_SESSION['flash'][$key])) {
            $value = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $value;
        }
        return null;
    }

    public function hasFlash($key) {
        return isset($_SESSION['flash'][$key]);
    }

    public function keepFlash($key) {
        if (isset($_SESSION['flash'][$key])) {
            $_SESSION['flash'][$key . '_keep'] = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
        }
    }

    public function restoreFlash() {
        if (isset($_SESSION['flash'])) {
            foreach ($_SESSION['flash'] as $key => $value) {
                if (strpos($key, '_keep') !== false) {
                    $originalKey = str_replace('_keep', '', $key);
                    $_SESSION['flash'][$originalKey] = $value;
                    unset($_SESSION['flash'][$key]);
                }
            }
        }
    }

    public function setError($key, $message) {
        $_SESSION['errors'][$key] = $message;
    }

    public function     getError($key) {
        return $_SESSION['errors'][$key] ?? null;
    }

    public function hasError($key) {
        return isset($_SESSION['errors'][$key]);
    }

    public function clearErrors() {
        unset($_SESSION['errors']);
    }

    public function keepErrors() {
        if (isset($_SESSION['errors'])) {
            $_SESSION['errors_keep'] = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }
    }

    public function restoreErrors() {
        if (isset($_SESSION['errors_keep'])) {
            $_SESSION['errors'] = $_SESSION['errors_keep'];
            unset($_SESSION['errors_keep']);
        }
    }

    public function setFormData($key, $message){
         $_SESSION['form_data'][$key] = $message;
    }

    public function getFormData($key){
        return $_SESSION['form_data'][$key] ?? null;
    }

    public function hasFormData($key) {
        return isset($_SESSION['form_data'][$key]);
    }

    public function clearFormData() {
        unset($_SESSION['form_data']);
    }

    public function keepFormData() {
        if (isset($_SESSION['form_data'])) {
            $_SESSION['form_data_keep'] = $_SESSION['form_data'];
            unset($_SESSION['form_data']);
        }
    }

    public function restoreFormData() {
        if (isset($_SESSION['form_data_keep'])) {
            $_SESSION['form_data'] = $_SESSION['form_data_keep'];
            unset($_SESSION['form_data_keep']);
        }
    }

    private function checkTimeout() {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $this->sessionLifetime)) {
            // Session expired
            $this->destroy();
            header('Location: '.$_ENV['APP_URL'].'/login?expired=1');
            exit;
        }
        
        $_SESSION['last_activity'] = time();
    }

    public function extend() {
        $_SESSION['last_activity'] = time();
    }

    public function getSessionId() {
        return session_id();
    }

    public function getSessionData() {
        return $_SESSION;
    }

    public function cleanupOldSessions() {
        // This would typically be called by a cron job
        // In a production environment, you might want to implement
        // a more sophisticated session cleanup mechanism
    }

    // CSRF protection methods
    public function generateCSRFToken() {
        $token = bin2hex(random_bytes(32));
        $this->set('csrf_token', $token);
        $this->set('csrf_token_time', time());
        return $token;
    }

    public function validateCSRFToken($token) {
        $storedToken = $this->get('csrf_token');
        $tokenTime = $this->get('csrf_token_time');
        
        if (!$storedToken || !$tokenTime) {
            return false;
        }
        
        // Token expires after 1 hour
        if (time() - $tokenTime > 3600) {
            $this->remove('csrf_token');
            $this->remove('csrf_token_time');
            return false;
        }
        
        return hash_equals($storedToken, $token);
    }

    // Rate limiting methods
    public function setRateLimit($key, $limit, $timeout) {
        $rateLimitData = $this->get('rate_limits', []);
        $rateLimitData[$key] = [
            'count' => 1,
            'first_attempt' => time(),
            'timeout' => $timeout
        ];
        $this->set('rate_limits', $rateLimitData);
    }

    public function checkRateLimit($key, $limit, $timeout) {
        $rateLimitData = $this->get('rate_limits', []);
        
        if (!isset($rateLimitData[$key])) {
            return true; // No rate limit set
        }
        
        $data = $rateLimitData[$key];
        
        // Check if timeout period has passed
        if (time() - $data['first_attempt'] > $timeout) {
            unset($rateLimitData[$key]);
            $this->set('rate_limits', $rateLimitData);
            return true;
        }
        
        // Check if limit exceeded
        if ($data['count'] >= $limit) {
            return false;
        }
        
        // Increment count
        $rateLimitData[$key]['count']++;
        $this->set('rate_limits', $rateLimitData);
        
        return true;
    }

    public function clearRateLimit($key) {
        $rateLimitData = $this->get('rate_limits', []);
        unset($rateLimitData[$key]);
        $this->set('rate_limits', $rateLimitData);
    }

    public function __destruct() {
        // Restore flash messages that need to be kept
        $this->restoreFlash();
    }
}
?>