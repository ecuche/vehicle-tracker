<?php
// Simple debug without routing through the app

// Load composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Create router and load routes
$router = new \App\Core\Router();
$router->loadRoutes();

// Find the vehicle-profile route
echo "<h2>All Routes with 'vehicle-profile':</h2>";
foreach ($router->getRoutes() as $pattern => $params) {
    if (strpos($pattern, 'vehicle-profile') !== false) {
        echo "<p><strong>Pattern:</strong> " . htmlspecialchars($pattern) . "</p>";
        echo "<p><strong>Action:</strong> " . $params['action'] . "</p>";
        
        // Test with valid uppercase VIN
        $testUrl = 'search/vehicle-profile/JTDBL40E09J002168';
        echo "<p><strong>Test URL (uppercase):</strong> " . $testUrl . "</p>";
        if (preg_match($pattern, $testUrl, $matches)) {
            echo "<p style='color: green;'>✓ MATCHES</p>";
        } else {
            echo "<p style='color: red;'>✗ DOES NOT MATCH</p>";
        }
        
        // Test with lowercase VIN
        $testUrl2 = 'search/vehicle-profile/jtdbl40e09j002168';
        echo "<p><strong>Test URL (lowercase):</strong> " . $testUrl2 . "</p>";
        if (preg_match($pattern, $testUrl2, $matches)) {
            echo "<p style='color: green;'>✓ MATCHES</p>";
        } else {
            echo "<p style='color: red;'>✗ DOES NOT MATCH</p>";
        }
        
        // Test with mixed case VIN
        $testUrl3 = 'search/vehicle-profile/JTdBL40E09J002168';
        echo "<p><strong>Test URL (mixed case):</strong> " . $testUrl3 . "</p>";
        if (preg_match($pattern, $testUrl3, $matches)) {
            echo "<p style='color: green;'>✓ MATCHES</p>";
        } else {
            echo "<p style='color: red;'>✗ DOES NOT MATCH</p>";
        }
        
        echo "<hr>";
    }
}
?>
