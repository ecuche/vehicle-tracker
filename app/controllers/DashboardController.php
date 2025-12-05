<?php
namespace App\Controllers;
use App\Core\Controller;

class DashboardController extends Controller {

    public function __construct() {
        parent::__construct();
        
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('login');
            exit;
        }
    }

    public function index() {
        $user_id = $this->auth->getUserId();
        $user_role = $this->auth->getUserRole();
        
        switch ($user_role) {
            case 'driver':
                $this->driverDashboard($user_id);
                break;
            case 'searcher':
                $this->searcherDashboard($user_id);
                break;
            case 'admin':
                $this->adminDashboard();
                break;
            default:
                $this->session->setFlash('error', 'Invalid user role');
                $this->redirect('logout');
                exit;
        }
    }

    private function driverDashboard($user_id) {
        // Get driver's current vehicles
        $vehicles = $this->vehicle->getUserVehicles($user_id, true);
        
        // Get pending transfer requests
        $pending_transfers = $this->transfer->getPendingTransfers($user_id);
        
        // Get recent transfers
        $recent_transfers = $this->transfer->getUserTransfers($user_id, 5);
        $incoming_requests = $this->transfer->getIncomingCount($user_id);
        $failed_sales = $this->transfer->getFailedCount($user_id);
        $sold_vehicles = $this->transfer->getSoldCount($user_id);
        
        $data = [
            'vehicles' => $vehicles,
            'pending_transfers' => $pending_transfers,
            'recent_transfers' => $recent_transfers,
            'total_vehicles' => count($vehicles),
            'pending_requests' => count($pending_transfers),
            'incoming_requests' => $incoming_requests,
            'failed_sales' => $failed_sales,
            'sold_vehicles' => $sold_vehicles
        ];
        require_once 'app/Views/dashboard/driver.php';
        $this->view('dashboard/driver', $data);
    }

    private function searcherDashboard($user_id) {
        // For searcher, dashboard is the search page
        $recent_searches = $this->vehicle->getRecentSearches($user_id, 5);
        $total_vehicles = $this->vehicle->getUserVehicleCount($user_id);
        $pending_requests = $this->transfer->getOutgoingCount($user_id);
        $incoming_requests = $this->transfer->getIncomingCount($user_id);
        $failed_sales = $this->transfer->getFailedCount($user_id);
        $sold_vehicles = $this->transfer->getSoldCount($user_id);
        $data = [
            'recent_searches' => $recent_searches
        ];
        extract($data);
        require_once 'app/Views/dashboard/searcher.php';
    }

    private function adminDashboard() {
        // Admin dashboard with charts and statistics
        $stats = $this->getAdminStats();
        $monthly_data = $this->getMonthlyData();
        
        $data = [
            'stats' => $stats,
            'monthly_data' => $monthly_data
        ];
        $this->view('dashboard/admin', $data);
       
    }

    private function getAdminStats() {
        return [
            'total_users' => $this->user->getTotalCount(),
            'total_vehicles' => $this->vehicle->getTotalCount(),
            'total_transfers' => $this->transfer->getTotalCount(),
            'pending_transfers' => $this->transfer->getPendingCount(),
            'banned_users' => $this->user->getBannedCount()
        ];
    }

    private function getMonthlyData() {
        // Get data for charts - vehicles registered and transfers per month
        return [
            'vehicles_registered' => $this->vehicle->getMonthlyRegistrations(),
            'transfers_completed' => $this->transfer->getMonthlyTransfers()
        ];
    }

    public function getDashboardStats() {
        if (!$this->auth->isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $user_id = $this->auth->getUserId();
        $user_role = $this->auth->getUserRole();
        
        $stats = [];
        
        switch ($user_role) {
            case 'driver':
                $stats = [
                    'total_vehicles' => $this->vehicle->getUserVehicleCount($user_id),
                    'pending_transfers' => $this->transfer->getPendingCount($user_id)
                ];
                break;
            case 'admin':
                $stats = $this->getAdminStats();
                break;
        }
        
        header('Content-Type: application/json');
        echo json_encode($stats);
        exit;
    }
}
?>