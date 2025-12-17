<?php
namespace App\Core;

class Router {
    private $routes = [];
    private $params = [];
    private $namedRoutes = [];

    /**
     * Add a GET route
     */
    public function get($route, $params = []) {
        $this->addRoute($route, $params, 'GET');
    }

    /**
     * Add a POST route
     */
    public function post($route, $params = []) {
        $this->addRoute($route, $params, 'POST');
    }

    /**
     * Add a PUT route
     */
    public function put($route, $params = []) {
        $this->addRoute($route, $params, 'PUT');
    }

    /**
     * Add a DELETE route
     */
    public function delete($route, $params = []) {
        $this->addRoute($route, $params, 'DELETE');
    }

    /**
     * Add a PATCH route
     */
    public function patch($route, $params = []) {
        $this->addRoute($route, $params, 'PATCH');
    }

    /**
     * Add a route that matches any HTTP method
     */
    public function any($route, $params = []) {
        $this->addRoute($route, $params, 'ANY');
    }

    /**
     * Add a route with specific HTTP methods
     */
    public function register($methods, $route, $params = []) {
        if (is_string($methods)) {
            $methods = [$methods];
        }
        foreach ($methods as $method) {
            $this->addRoute($route, $params, strtoupper($method));
        }
    }

    /**
     * Core method to add a route
     */
    private function addRoute($route, $params = [], $method = 'GET') {
        // Handle array notation [Controller::class, 'method']
        if (is_array($params) && isset($params[0]) && is_string($params[0])) {
            $controllerClass = $params[0];
            $action = $params[1] ?? 'index';
            
            // Extract controller name from class
            $controllerName = $this->extractControllerName($controllerClass);
            
            $params = [
                'controller' => $controllerName,
                'action' => $action,
                'class' => $controllerClass
            ];
        }

        // Store original route for named routes
        $originalRoute = $route;

        // Parse route pattern
        $route = $this->parseRoute($route);

        // Add start/end anchors and flags
        $flags = isset($params['flags']) ? $params['flags'] : '';
        if (isset($params['flags'])) {
            unset($params['flags']);
        }

        $pattern = '/^' . $route . '$/' . $flags;
        
        // Store route with method
        $routeKey = $method . ':' . $pattern;
        $this->routes[$routeKey] = $params;

        // Store named route if provided
        if (isset($params['name'])) {
            $this->namedRoutes[$params['name']] = $originalRoute;
        }
    }

    /**
     * Parse route pattern to regex
     */
    private function parseRoute($route) {
        // Ensure route starts with /
        if ($route !== '/' && !empty($route) && $route[0] !== '/') {
            $route = '/' . $route;
        }

        // Manual parser to handle {var:regex} with nested braces
        $output = '';
        $i = 0;
        $len = strlen($route);
        
        while ($i < $len) {
            // Check for {var:regex} pattern
            if ($route[$i] === '{' && preg_match('/\{([a-zA-Z_][a-zA-Z0-9_]*):/', substr($route, $i), $m)) {
                $varName = $m[1];
                $i += strlen($m[0]);
                
                // Find matching closing brace
                $braceCount = 1;
                $patternStart = $i;
                
                while ($i < $len && $braceCount > 0) {
                    if ($route[$i] === '{') {
                        $braceCount++;
                    } elseif ($route[$i] === '}') {
                        $braceCount--;
                    }
                    if ($braceCount > 0) {
                        $i++;
                    }
                }
                
                $pattern = substr($route, $patternStart, $i - $patternStart);
                $output .= '(?P<' . $varName . '>' . $pattern . ')';
                $i++;
            } else {
                $output .= $route[$i];
                $i++;
            }
        }
        
        $route = $output;

        // Escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Match {var} - default to alphanumeric, hyphens, and underscores
        $route = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<\1>[A-Za-z0-9\-_]+)', $route);

        return $route;
    }

    /**
     * Extract controller name from class string
     */
    private function extractControllerName($class) {
        // Remove namespace
        $parts = explode('\\', $class);
        $className = end($parts);
        
        // Remove "Controller" suffix if present
        if (substr($className, -10) === 'Controller') {
            $className = substr($className, 0, -10);
        }
        
        return $className;
    }

    /**
     * Match URL against routes
     */
    public function match($url, $method = null) {
        $url = $this->removeQueryStringVariables($url);
        
        if ($method === null) {
            $method = $_SERVER['REQUEST_METHOD'];
        }
        $method = strtoupper($method);

        foreach ($this->routes as $routeKey => $params) {
            list($routeMethod, $pattern) = explode(':', $routeKey, 2);
            
            // Check if method matches (or route accepts any method)
            if ($routeMethod !== 'ANY' && $routeMethod !== $method) {
                continue;
            }

            if (preg_match($pattern, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    /**
     * Dispatch route to controller
     */
    public function dispatch($url) {
        $url = $this->removeQueryStringVariables($url);
        $method = $_SERVER['REQUEST_METHOD'];

        if ($this->match($url, $method)) {
            $controller = $this->params['controller'] ?? null;
            $action = $this->params['action'] ?? 'index';

            // Use full class if provided
            if (isset($this->params['class'])) {
                $controllerClass = $this->params['class'];
            } else {
                $controller = $this->convertToStudlyCaps($controller);
                $controllerClass = $this->getNamespace() . $controller;
            }

            if (class_exists($controllerClass)) {
                $controller_object = new $controllerClass($this->params);
                $action = $this->convertToCamelCase($action);

                if (is_callable([$controller_object, $action])) {
                    // Prepare parameters
                    $params = $this->params;
                    unset($params['controller'], $params['action'], $params['namespace'], $params['class'], $params['name']);

                    $controller_object->$action(...array_values($params));
                } else {
                    throw new \Exception("Method $action in controller $controllerClass not found");
                }
            } else {
                throw new \Exception("Controller class $controllerClass not found");
            }
        } else {
            $this->show404();
        }
    }

    /**
     * Load routes from external file
     */
    public function loadRoutes($file = null) {
        if ($file === null) {
            $file = CONFIG_PATH . '/routes.php';
        }

        if (!file_exists($file)) {
            throw new \Exception("Routes file not found: $file");
        }

        // Make $router available in the routes file
        $router = $this;
        require $file;
    }

    /**
     * Generate URL from named route
     */
    public function route($name, $params = []) {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Named route not found: $name");
        }

        $route = $this->namedRoutes[$name];
        
        foreach ($params as $key => $value) {
            $route = preg_replace('/\{' . $key . '(:.*?)?\}/', $value, $route);
        }

        return $route;
    }

    // Helper methods (unchanged)
    protected function convertToStudlyCaps($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    protected function convertToCamelCase($string) {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    protected function removeQueryStringVariables($url) {
        if ($url === '') {
            return '';
        }
        $parts = explode('?', $url, 2);
        return $parts[0];
    }

    protected function getNamespace() {
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }

        return $namespace;
    }

    public function getParams() {
        return $this->params;
    }

    public function getRoutes() {
        return $this->routes;
    }

    private function show404() {
        http_response_code(404);
        $errorController = new \App\Controllers\ErrorController();
        $errorController->notFound();
        exit;
    }

    private function show403() {
        http_response_code(403);
        $errorController = new \App\Controllers\ErrorController();
        $errorController->forbidden();
        exit;
    }

    private function show500() {
        http_response_code(500);
        $errorController = new \App\Controllers\ErrorController();
        $errorController->serverError();
        exit;
    }
}
?>