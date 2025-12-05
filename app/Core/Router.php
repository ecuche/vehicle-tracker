<?php
namespace App\Core;


class Router {
    private $routes = [];
    private $params = [];

    public function add($route, $params = []) {

    // Escape forward slashes
    $route = preg_replace('/\//', '\\/', $route);

    // Match {var}
    $route = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<\1>[A-Za-z0-9\-_]+)', $route);

    // Match {var:regex}
    $route = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*):([^\}]+)\}/', '(?P<\1>\2)', $route);

    // Add start/end + case-insensitive
    $route = '/^' . $route . '$/i';

    $this->routes[$route] = $params;
}



    public function match($url) {
        // Remove query string variables
        $url = $this->removeQueryStringVariables($url);

        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
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

    public function dispatch($url) {
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'] ?? null;
            $action = $this->params['action'] ?? 'index';

            $controller = $this->convertToStudlyCaps($controller);
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);
                $action = $this->convertToCamelCase($action);

                if (is_callable([$controller_object, $action])) {
                    // Prepare parameters: exclude internal ones
                    $params = $this->params;
                    unset($params['controller'], $params['action'], $params['namespace']);

                    // Pass all remaining route parameters
                    $controller_object->$action(...array_values($params));
                } else {
                    throw new \Exception("Method $action in controller $controller not found");
                }
            } else {
                throw new \Exception("Controller class $controller not found");
            }
        } else {
            $this->show404();
        }
    }

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

    // Load routes from configuration
    public function loadRoutes() {
        // Define routes
        $this->add('', ['controller' => 'AuthController', 'action' => 'login']);
        $this->add('/', ['controller' => 'AuthController', 'action' => 'login']);
        $this->add('login', ['controller' => 'AuthController', 'action' => 'login']);
        $this->add('logout', ['controller' => 'AuthController', 'action' => 'logout']);
        $this->add('register', ['controller' => 'AuthController', 'action' => 'register']);
        $this->add('verify-email/{token:[\da-f]+}', ['controller' => 'AuthController', 'action' => 'verifyEmail']);
        $this->add('forgot-password', ['controller' => 'AuthController', 'action' => 'forgotPassword']);
        $this->add('reset-password/{token:[\da-f]+}', ['controller' => 'AuthController', 'action' => 'resetPassword']);
        
        // Dashboard routes
        $this->add('dashboard', ['controller' => 'DashboardController', 'action' => 'index']);
        $this->add('dashboard/stats', ['controller' => 'DashboardController', 'action' => 'getDashboardStats']);
        
        // Profile routes
        $this->add('profile', ['controller' => 'ProfileController', 'action' => 'index']);
        $this->add('profile/update', ['controller' => 'ProfileController', 'action' => 'update']);
        $this->add('profile/remove-picture', ['controller' => 'ProfileController', 'action' => 'removeProfilePicture']);
        $this->add('profile/change-password', ['controller' => 'ProfileController', 'action' => 'changePassword']);
        $this->add('profile/user/{identifier:.+}', ['controller' => 'ProfileController', 'action' => 'getUserProfile']);
        
        // Vehicle routes
        $this->add('vehicles', ['controller' => 'VehicleController', 'action' => 'index']);
        $this->add('vehicles/register', ['controller' => 'VehicleController', 'action' => 'register']);
        $this->add('vehicles/transfer', ['controller' => 'VehicleController', 'action' => 'transfer']);
        $this->add('vehicles/transfer/{vin:.+}', ['controller' => 'VehicleController', 'action' => 'transferVehicle']);
        $this->add('vehicles/handle-transfer', ['controller' => 'VehicleController', 'action' => 'handleTransfer']);
        $this->add('vehicles/assign-plate', ['controller' => 'VehicleController', 'action' => 'assignPlate']);
        $this->add('vehicles/details/{id:\d+}', ['controller' => 'VehicleController', 'action' => 'getVehicleDetails']);
        $this->add('vehicles/view/{vin:.+}', ['controller' => 'VehicleController', 'action' => 'viewVehicle']);
        $this->add('vehicles/history/{vin:.+}', ['controller' => 'VehicleController', 'action' => 'viewVehicleHistory']);
        $this->add('vehicles/search-user', ['controller' => 'VehicleController', 'action' => 'searchUser']);
        
        // Search routes
        $this->add('search', ['controller' => 'SearchController', 'action' => 'index']);
        $this->add('search/vehicle', ['controller' => 'SearchController', 'action' => 'searchVehicle']);
        $this->add('search/advanced', ['controller' => 'SearchController', 'action' => 'searchVehicleAdvanced']);
        $this->add('search/vehicle-profile/{vin:.+}', ['controller' => 'SearchController', 'action' => 'getVehicleProfile']);
        $this->add('search/history', ['controller' => 'SearchController', 'action' => 'getSearchHistory']);
        $this->add('search/export', ['controller' => 'SearchController', 'action' => 'exportSearchResults']);
        
        // Admin routes
        $this->add('admin/users', ['controller' => 'AdminController', 'action' => 'users']);
        $this->add('admin/vehicles', ['controller' => 'AdminController', 'action' => 'vehicles']);
        $this->add('admin/audit', ['controller' => 'AdminController', 'action' => 'audit']);
        $this->add('admin/search-user', ['controller' => 'AdminController', 'action' => 'searchUser']);
        $this->add('admin/manage-user/{email:.+}', ['controller' => 'AdminController', 'action' => 'manageUser']);
        $this->add('admin/manage-vehicle/{vin:.+}', ['controller' => 'AdminController', 'action' => 'manageVehicle']);
        $this->add('admin/user/{id:\d+}', ['controller' => 'AdminController', 'action' => 'getUserDetails']);
        $this->add('admin/user/vehicles/{email:.+}', ['controller' => 'AdminController', 'action' => 'viewUserVehicles']);
        $this->add('admin/vehicle/users/{vin:.+}', ['controller' => 'AdminController', 'action' => 'viewVehicleUsers']);
        $this->add('admin/update-role', ['controller' => 'AdminController', 'action' => 'updateUserRole']);
        $this->add('admin/toggle-ban', ['controller' => 'AdminController', 'action' => 'toggleUserBan']);
        $this->add('admin/export-audit', ['controller' => 'AdminController', 'action' => 'exportAuditToCSV']);
        $this->add('admin/stats', ['controller' => 'AdminController', 'action' => 'getStats']);
        $this->add('admin/audit/user/{email:.+}', ['controller' => 'AdminController', 'action' => 'getUserAuditLogs']);
        
        // Error routes
        $this->add('errors/404', ['controller' => 'ErrorController', 'action' => 'notFound']);
        $this->add('errors/403', ['controller' => 'ErrorController', 'action' => 'forbidden']);
        $this->add('errors/500', ['controller' => 'ErrorController', 'action' => 'serverError']);
        
        // API routes for AJAX calls
        $this->add('api/vehicle/search', ['controller' => 'SearchController', 'action' => 'searchVehicle']);
        $this->add('api/search/vehicle', ['controller' => 'SearchController', 'action' => 'searchVehicle']);
        $this->add('api/user/search', ['controller' => 'VehicleController', 'action' => 'searchUser']);
        $this->add('api/profile/user/{identifier:.+}', ['controller' => 'ProfileController', 'action' => 'getUserProfile']);
        $this->add('api/vehicle/get-models', ['controller' => 'VehicleController', 'action' => 'getVehicleModels']);
        $this->add('api/check-vin', ['controller' => 'VehicleController', 'action' => 'checkVIN']);

        // Admin API
        $this->add('api/admin/update/user', ['controller' => 'AdminController', 'action' => 'updateUser']);
        $this->add('api/admin/delete/user', ['controller' => 'AdminController', 'action' => 'deleteUser']);
        $this->add('api/admin/update/vehicle', ['controller' => 'AdminController', 'action' => 'updateVehicle']);
        $this->add('api/admin/get-user', ['controller' => 'ApiController', 'action' => 'getUser']);





    }
}
?>