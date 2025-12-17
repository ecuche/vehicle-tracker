<?php
namespace App\Core;

class Request {
    public function __construct() {
    }

    public function get($name, $default = null) {
        return isset($_GET[$name]) ? trim($_GET[$name]) : $default;
    }

    public function post($name, $default = null) {
        return isset($_POST[$name]) ? trim($_POST[$name]) : $default;
    }

    public function jsonPost(string $name = '', $default = null) {
        $post = json_decode(file_get_contents('php://input'), true) ?? null;
        if(!empty($name)) {
            return $post[$name] ?? $default;
        }
        return  $post ?? $default;
    }

    public function cookie($name, $default = null) {
        return isset($_COOKIE[$name]) ? trim($_COOKIE[$name]) : $default;
    }

    public function server($name, $default = null) {
        return isset($_SERVER[$name]) ? trim($_SERVER[$name]) : $default;
    }

    public function session($name, $default = null) {
        return isset($_SESSION[$name]) ? trim($_SESSION[$name]) : $default;
    }

    public function env($name, $default = null) {
        return isset($_ENV[$name]) ? trim($_ENV[$name]) : $default; 
    }

    public function redirect(string $url, array $data = []): void  
    {
        $params = empty($data) ? '' : '/?' .http_build_query($data);
        header("Location: {$_ENV['APP_URL']}/{$url}".$params);
        exit;
    }

    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public function isPost() {
        return $this->getMethod() === 'POST';
    }
    
    public function isGet() {
        return $this->getMethod() === 'GET';
    }
    
    public function isPut() {
        return $this->getMethod() === 'PUT';
    }
    
    public function isDelete() {
        return $this->getMethod() === 'DELETE';
    }

    public function getQueryString() {
        return $_SERVER['QUERY_STRING'] ?? '';
    }

    public function getUri() {
        $uri = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        $uri = strtok($uri, '?');
        
        // Get base path (subdirectory) from script name
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = str_replace('\\', '/', dirname($scriptName));
        
        // Remove /public from the path since .htaccess routes through it
        if (substr($basePath, -7) === '/public') {
            $basePath = substr($basePath, 0, -7);
        }
        
        $basePath = $basePath === '/' ? '' : $basePath;
        
        // Remove base path from URI if it exists
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Remove trailing slash except for root
        $uri = rtrim($uri, '/');
        
        return $uri ?: '/';
    }
    
    public function getPath() {
        return $this->getUri();
    }

    public function input($key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function file($key) {
        return $_FILES[$key] ?? null;
    }
    
    public function hasFile($key) {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK;
    }

    public function ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }
    
    public function userAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

}
?>