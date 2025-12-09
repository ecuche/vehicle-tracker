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

    public function file($name, $default = null) {
        return isset($_FILES[$name]) ? trim($_FILES[$name]) : $default;
    }
}
?>