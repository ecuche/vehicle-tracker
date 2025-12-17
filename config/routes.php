<?php
/**
 * Application Routes
 * 
 * Define all application routes here.
 * The $router variable is automatically available from Router::loadRoutes()
 * 
 * Route Patterns:
 * - {id} - matches alphanumeric, hyphens, underscores (default)
 * - {id:[0-9]+} - matches only digits
 * - {slug:[a-z0-9-]+} - matches lowercase letters, numbers, hyphens
 * - {any:.*} - matches anything
 */

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Controllers\VehicleController;
use App\Controllers\AdminController;
use App\Controllers\SearchController;
use App\Controllers\ErrorController;
use App\Controllers\ApiController;

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

// Home & Landing
$router->get('', [DashboardController::class, 'index']);
$router->get('/', [DashboardController::class, 'index']);
$router->get('/home', [DashboardController::class, 'index']);

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout']);

// Password Reset
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->get('/reset-password/{token:[a-f0-9]{64}}', [AuthController::class, 'showResetPassword']);
$router->post('/reset-password/{token:[a-f0-9]{64}}', [AuthController::class, 'resetPassword']);

// Email Verification
$router->get('/verify-email/{token:[a-f0-9]{64}}', [AuthController::class, 'verifyEmail']);

// ============================================================================
// DASHBOARD ROUTES
// ============================================================================

$this->get('dashboard', [dashboardController::class, 'index']);

$this->post('dashboard/stats', [dashboardController::class, 'getDashboardStats']);

// ============================================================================
// PROFILE ROUTES
// ============================================================================

$this->get('profile', [ProfileController::class, 'index']);

$this->post('profile/remove-picture', [ProfileController::class, 'removeProfilePicture']);
$this->post('profile/change-password', [ProfileController::class, 'changePassword']);
$this->post('profile/update', [ProfileController::class, 'update']);

// ============================================================================
// VEHICLE ROUTES
// ============================================================================

$this->get('vehicles', [VehicleController::class, 'index']);
$this->get('vehicles/register', [VehicleController::class, 'viewVehicleRegister']);
$this->get('vehicles/transfer/{vin:[0-9A-HJ-NPR-Z]{17}}', [VehicleController::class, 'transferVehicle']);
$this->get('vehicles/view/{vin:[0-9A-HJ-NPR-Z]{17}}', [VehicleController::class, 'viewVehicle']);
$this->get('vehicles/history/{vin:[0-9A-HJ-NPR-Z]{17}}', [VehicleController::class, 'viewVehicleHistory']);
$this->get('vehicles/view/ownership-history/{vin:[0-9A-HJ-NPR-Z]{17}}', [VehicleController::class, 'viewOwnershipHistory']);
$this->get('vehicles/view/status-history/{vin:[0-9A-HJ-NPR-Z]{17}}', [VehicleController::class, 'viewStatusHistory']);
$this->get('vehicles/completed-transfers', [VehicleController::class, 'completedTransfers']);
$this->get('vehicles/incoming-transfers', [VehicleController::class, 'IncomingTransfers']);
$this->get('vehicles/outgoing-transfers', [VehicleController::class, 'outgoingTransfers']);
$this->get('vehicles/pending-transfer/{vin:[0-9A-HJ-NPR-Z]{17}}', [VehicleController::class, 'pendingTransfers']);

$this->post('vehicles/register', [VehicleController::class, 'register']);
$this->post('vehicles/transfer', [VehicleController::class, 'transfer']);
$this->post('vehicles/handle-transfer', [VehicleController::class, 'handleTransfer']);
$this->post('vehicles/assign-plate', [VehicleController::class, 'assignPlate']);

// ============================================================================
// SEARCH ROUTES
// ============================================================================

$this->get('search', [SearchController::class, 'index']);
$this->get('search/vehicle-profile/{vin:[0-9A-HJ-NPR-Z]{17}}', [SearchController::class, 'getVehicleProfile']);

// ============================================================================
// ERROR ROUTES
// ============================================================================

$router->get('errors/403', [ErrorController::class, 'forbidden']);
$router->get('errors/404', [ErrorController::class, 'notFound']);
$router->get('errors/500', [ErrorController::class, 'serverError']);

// ============================================================================
// ADMIN ROUTES
// ============================================================================

 // Admin routes
$this->get('admin/users', [AdminController::class, 'users']);
$this->get('admin/vehicles', [AdminController::class, 'vehicles']);
$this->get('admin/audit', [AdminController::class, 'audit']);
$this->get('admin/manage-user/{email:[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}}', [AdminController::class, 'manageUser']);
$this->get('admin/manage-vehicle/{vin:[0-9A-HJ-NPR-Z]{17}}', [AdminController::class, 'manageVehicle']);
$this->get('admin/user/vehicles/{email:[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}}', [AdminController::class, 'viewUserVehicles']);
$this->get('admin/vehicle/users/{vin:[0-9A-HJ-NPR-Z]{17}}', [AdminController::class, 'viewVehicleUsers']);
$this->get('admin/audit/user/{email:[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}}', [AdminController::class, 'getUserAuditLogs']);



$this->get('admin/vehicles/normal', [AdminController::class, 'viewNormalVehicles']);
$this->get('admin/vehicles/stolen', [AdminController::class, 'viewStolenVehicles']);
$this->get('admin/vehicles/no-customs', [AdminController::class, 'viewNoCustomsVehicles']);
$this->get('admin/vehicles/changed-engine', [AdminController::class, 'viewChangedEngineVehicles']);
$this->get('admin/vehicles/changed-color', [AdminController::class, 'viewChangedColorVehicles']);
$this->get('admin/view/drivers', [AdminController::class, 'viewDrivers']);
$this->get('admin/view/admins', [AdminController::class, 'viewAdmins']);
$this->get('admin/view/searchers', [AdminController::class, 'viewSearchers']);

// ============================================================================
// API ROUTES (JSON responses)
// ============================================================================

// Vehicle API 
$this->post('api/vehicle/get-models', [ApiController::class, 'getVehicleModels']);
$this->post('api/vehicle/transfer-ownership/{vin:[0-9A-HJ-NPR-Z]{17}}', [ApiController::class, 'handleTransfer']);
$this->post('api/vehicle/assign-new-plate/{vin:[0-9A-HJ-NPR-Z]{17}}', [ApiController::class, 'assignNewPlate']);
$this->post('api/vehicle/change-current-plate', [ApiController::class, 'changeCurrentPlate']);
$this->post('api/vehicle/search', [ApiController::class, 'searchVehicle']);

$this->post('api/vehicles/details/{vin:[0-9A-HJ-NPR-Z]{17}}', [ApiController::class, 'getVehicleDetails']);
$this->post('api/vehicles/accept-transfer/{vin:[0-9A-HJ-NPR-Z]{17}}', [ApiController::class, 'acceptTransfer']);
$this->post('api/vehicles/reject-transfer/{vin:[0-9A-HJ-NPR-Z]{17}}', [ApiController::class, 'rejectTransfer']);

$this->post('api/user/search', [ApiController::class, 'searchUser']);
$this->post('api/check-vin', [ApiController::class, 'checkVIN']);

// Search API
$this->post('api/search/vehicle', [ApiController::class, 'searchVehicle']);
$this->post('api/search/user', [ApiController::class, 'searchUser']);

// Profile API
$this->post('api/profile/user/{identifier:.+}', [ApiController::class, 'getUserProfile']);

// Admin API
$this->post('api/admin/update/user', [ApiController::class, 'updateUser']);
$this->post('api/admin/delete/user', [ApiController::class, 'deleteUser']);
$this->post('api/admin/update/vehicle', [ApiController::class, 'updateVehicle']);
$this->post('api/admin/get-user', [ApiController::class, 'getUser']);
$this->post('api/admin/delete/vehicle', [ApiController::class, 'deleteVehicle']);
$this->post('api/admin/send-password-reset/{email:[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}}', [ApiController::class, 'sendPasswordReset']);
$this->post('api/admin/verify-email/{email:[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}}', [ApiController::class, 'verifyEmail']);
$this->post('api/admin/send-verification/{email:[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}}', [ApiController::class, 'sendVerification']);



$this->get('admin/toggle-ban', [ApiController::class, 'toggleUserBan']);
$this->get('admin/export-audit', [ApiController::class,  'exportAuditToCSV']);
$this->get('admin/stats', [ApiController::class, 'getStats']);
$this->get('admin/user/{id:\d+}', [ApiController::class, 'getUserDetails']);
$this->get('admin/search-user', [ApiController::class, 'searchUser']);

$this->get('search/vehicle', [ApiController::class, 'searchVehicle']);
$this->get('search/history', [ApiController::class, 'getSearchHistory']);



// ============================================================================
// ADVANCED ROUTE EXAMPLES
// ============================================================================

/*
// UUID pattern (for future use)
$router->get('/vehicles/{uuid:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', 
    [VehicleController::class, 'showByUuid']);

// Date pattern (YYYY-MM-DD)
$router->get('/reports/{date:[0-9]{4}-[0-9]{2}-[0-9]{2}}', 
    [AdminController::class, 'dailyReport']);

// Year and month pattern
$router->get('/archive/{year:[0-9]{4}}/{month:[0-9]{2}}', 
    [VehicleController::class, 'archive']);

// Slug pattern (lowercase with hyphens)
$router->get('/blog/{slug:[a-z0-9\-]+}', 
    [BlogController::class, 'show']);

// Optional parameters (handled in controller)
$router->get('/vehicles/page/{page:[0-9]+}', 
    [VehicleController::class, 'index']);

// Multiple parameters with different patterns
$router->get('/user/{userId:[0-9]+}/vehicle/{vehicleId:[0-9]+}', 
    [VehicleController::class, 'userVehicle']);

// Catch-all pattern (use with caution - should be last)
$router->get('/docs/{path:.*}', 
    [DocsController::class, 'show']);
*/

// ============================================================================
// WILDCARD CATCH-ALL (must be last)
// ============================================================================

// Uncomment if you want to handle all unmatched routes
// $router->any('/{path:.*}', [ErrorController::class, 'notFound']);