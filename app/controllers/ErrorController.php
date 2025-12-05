<?php
namespace App\Controllers;
use App\Core\Controller;

class ErrorController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function notFound() {
        http_response_code(404);
        $this->renderError('404', 'Page Not Found', 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.');
    }

    public function forbidden() {
        http_response_code(403);
        $this->renderError('403', 'Access Denied', 'You do not have permission to access this page.');
    }

    public function serverError() {
        http_response_code(500);
        $this->renderError('500', 'Server Error', 'Something went wrong on our end. Please try again later.');
    }

    public function maintenance() {
        http_response_code(503);
        $this->renderError('503', 'Maintenance Mode', 'The system is currently under maintenance. Please check back later.');
    }

    private function renderError($code, $title, $message) {

       
        $data = [
            'error_code' => $code,
            'error_title' => $title,
            'error_message' => $message,
            'is_logged_in' => $this->auth->isLoggedIn(),
            'user_role' => $this->auth->getUserRole()
        ];
        extract($data);
        // Log the error for admin review
        $this->logError($code, $title);
        $files = scandir('.');
        require_once 'app/views/errors/error.php';
    }

    private function logError($code, $title) {
        $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $requestUri = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $referrer = $_SERVER['HTTP_REFERER'] ?? 'direct';

        $logMessage = sprintf(
            "ERROR %s: %s - IP: %s - URL: %s - Referrer: %s - User-Agent: %s",
            $code,
            $title,
            $clientIP,
            $requestUri,
            $referrer,
            $userAgent
        );

        error_log($logMessage);
    }
}
?>