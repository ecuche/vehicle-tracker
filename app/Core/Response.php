<?php 

namespace App\Core;
use App\Core\Auth;

Class Response {

    protected $auth;
    public function __construct() {
        $this->auth = new Auth();
    }

    public function authRedirect() {
        if ($this->auth->isLoggedIn()) {
            $this->redirect("dashboard");
            exit;
        }
    }

    public function logInAuth(){
        if (!$this->auth->isLoggedIn()) {
            $this->roleAuth();
        }
    }

    public function roleAuth(array $roles = ['driver']){
         $user_role = $this->auth->getUserRole();
        
        if (!in_array($user_role, $roles)) {
            $this->redirect('dashboard');
        }
    }

    public function redirect(string $url, array $data = []): void  
    {
        header('Content-Type: application/json');
        json_encode(['error' => 'Access denied']);
        $params = empty($data) ? '' : '/?' .http_build_query($data);
        header("Location: {$_ENV['APP_URL']}/{$url}".$params);
        exit;
    }

     protected function view(string $template, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../views/{$template}.php";
    }

    public function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function error(string $message = 'Error', int $code = 500): void
    {
        http_response_code($code);
        $this->json(['error' => $message]);
    }

    public function data($data = null, int $code = 200){
        http_response_code($code);
        $this->json(['data' => $data]);
    }

    public function success(array|bool $data = null, int $code = 200): void
    {
        http_response_code($code);
        $data['success'] = true;
        $this->json($data);
    }

    public function validationError(array $errors): void
    {
        http_response_code(422);
        $this->json(['errors' => $errors]);
    }

    public function notFound(string $message = 'Not Found'): void
    {
        http_response_code(404);
        $this->json(['error' => $message]);
    }

    public function unauthorized(string $message = 'Unauthorized'): void
    {
        http_response_code(401);
        $this->json(['error' => $message]);
    }

    public function forbidden(string $message = 'Forbidden'): void
    {
        http_response_code(403);
        $this->json(['error' => $message]);
    }

    public function badRequest(string $message = 'Bad Request'): void
    {
        http_response_code(400);
        $this->json(['error' => $message]);
    }

    public function internalServerError(string $message = 'Internal Server Error'): void
    {
        http_response_code(500);
        $this->json(['error' => $message]);
    }

    public function serviceUnavailable(string $message = 'Service Unavailable'): void
    {
        http_response_code(503);
        $this->json(['error' => $message]);
    }

    public function gatewayTimeout(string $message = 'Gateway Timeout'): void
    {
        http_response_code(504);
        $this->json(['error' => $message]);
    }

    public function tooManyRequests(string $message = 'Too Many Requests'): void
    {
        http_response_code(429);
        $this->json(['error' => $message]);
    }


}