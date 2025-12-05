<?php
namespace App\Middleware;

use App\Core\Auth;
use App\Core\Session;

class AuthMiddleware {
    private $auth;
    private $session;

    public function __construct() {
        $this->auth = new Auth();
        $this->session = new Session();
    }

    public function handle($request, $next) {
        // Check if user is authenticated
        if (!$this->auth->isLoggedIn()) {
            $this->session->setFlash('error', 'Please log in to access this page.');
            
            // Store the intended URL for redirect after login
            if ($request['url'] !== '/login' && $request['url'] !== '/register') {
                $this->session->set('intended_url', $request['url']);
            }
            
            header('Location: '.$_ENV['APP_URL'].'/login');
            exit;
        }

        // Validate session security
        if (!$this->auth->validateSession()) {
            $this->session->setFlash('error', 'Session security validation failed. Please log in again.');
            header('Location: '.$_ENV['APP_URL'].'/login');
            exit;
        }

        // Check if user account is banned
        $user = $this->auth->getUser();
        if ($user && $user->is_banned) {
            $this->auth->logout();
            $this->session->setFlash('error', 'Your account has been banned. Please contact administrator.');
            header('Location: '.$_ENV['APP_URL'].'/login');
            exit;
        }

        // Check if email is verified (except for email verification routes)
        if ($user && !$user->email_verified && !$this->isEmailVerificationRoute($request['url'])) {
            $this->session->setFlash('warning', 'Please verify your email address to access all features.');
            // Don't block access, just show warning
        }

        // Update last activity timestamp
        $this->auth->extendSession();

        return $next($request);
    }

    private function isEmailVerificationRoute($url) {
        $emailVerificationRoutes = [
            '/verify-email',
            '/logout',
            '/profile'
        ];

        foreach ($emailVerificationRoutes as $route) {
            if (strpos($url, $route) === 0) {
                return true;
            }
        }

        return false;
    }

    public static function check() {
        $middleware = new self();
        return $middleware->auth->isLoggedIn();
    }

    public static function user() {
        $middleware = new self();
        return $middleware->auth->getUser();
    }

    public static function id() {
        $middleware = new self();
        return $middleware->auth->getUserId();
    }

    public static function role() {
        $middleware = new self();
        return $middleware->auth->getUserRole();
    }
}
?>