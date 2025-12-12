<?php

require_once(dirname(__FILE__) ."/app/core/Router.php");
// In a test file temporarily
$router = new App\Core\Router();
$router->loadRoutes();

foreach ($router->getRoutes() as $pattern => $params) {
    if (strpos($pattern, 'vehicle-profile') !== false) {
        echo "Pattern: " . htmlspecialchars($pattern) . "<br>";
        if (preg_match($pattern, 'search/vehicle-profile/WBADO5C56LG435427')) {
            echo "✓ Pattern matches!<br>";
        } else {
            echo "✗ Pattern does NOT match<br>";
        }
    }
}