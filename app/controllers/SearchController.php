<?php
namespace App\Controllers;
use App\Core\Controller;

class SearchController extends Controller {

    public function __construct() {
        parent::__construct();
        
        if (!$this->auth->isLoggedIn()) {
            header('Location: '.$_ENV['APP_URL'].'/login');
            exit;
        }
    }

    public function index() {
        $user_role = $this->auth->getUserRole();
        
        // Only searcher and admin can access search page
        if (!in_array($user_role, ['searcher', 'admin'])) {
            $this->session->setFlash('error', 'Access denied');
            header('Location: '.$_ENV['APP_URL'].'/dashboard');
            exit;
        }
        require_once 'app/Views/search/index.php';
    }

    public function searchVehicle() {
        $user_role = $this->auth->getUserRole();
        
        if (!in_array($user_role, ['searcher', 'admin'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Access denied']);
            exit;
        }


        $search_type = $_GET['type'] ?? ''; // 'vin' or 'plate'
        $search_term = trim($_GET['term'] ?? '');

        
        if (empty($search_type) || empty($search_term)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Search type and term are required']);
            exit;
        }

        $vehicle = null;
        
        if ($search_type === 'vin') {
            $vehicle = $this->vehicle->findByVIN($search_term);
        } elseif ($search_type === 'plate') {
            $vehicle = $this->vehicle->findByPlateNumber($search_term);
        }
        
        if ($vehicle) {
            // Get full vehicle details including ownership history and plate history
            $vehicle_details = $this->vehicle->getFullDetails($vehicle['id']);
            
            // Log the search for audit purposes
            if ($user_role === 'searcher') {
                $this->vehicle->logSearch($this->auth->getUserId(), $search_type, $search_term, $vehicle['id']);
            }
            header('Content-Type: application/json');
            echo json_encode($vehicle_details);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Vehicle not found']);
        }
        exit;
    }

    public function searchVehicleAdvanced() {
        $user_role = $this->auth->getUserRole();
        
        if (!in_array($user_role, ['searcher', 'admin'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        $filters = [
            'vin' => $_GET['vin'] ?? '',
            'plate_number' => $_GET['plate_number'] ?? '',
            'make' => $_GET['make'] ?? '',
            'model' => $_GET['model'] ?? '',
            'year' => $_GET['year'] ?? '',
            'status' => $_GET['status'] ?? '',
            'owner_identifier' => $_GET['owner_identifier'] ?? ''
        ];

        $page = $_GET['page'] ?? 1;
        $per_page = $_GET['per_page'] ?? 10;

        $vehicles = $this->vehicle->advancedSearch($filters, $page, $per_page);
        $total_vehicles = $this->vehicle->advancedSearchCount($filters);
        
        $response = [
            'vehicles' => $vehicles,
            'pagination' => [
                'page' => $page,
                'per_page' => $per_page,
                'total' => $total_vehicles,
                'total_pages' => ceil($total_vehicles / $per_page)
            ]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function getVehicleProfile($vin) {
        $user_role = $this->auth->getUserRole();
        
        if (!in_array($user_role, ['searcher', 'admin'])) {
            $this->session->setFlash('error', 'Access denied');
            header('Location: '.$_ENV['APP_URL'].'/dashboard');
            exit;
        }
        $vehicle = $this->vehicle->findByVIN($vin);   
        $vehicle = $this->vehicle->getFullProfile($vehicle['id']);
        if (!$vehicle) {
            $this->session->setFlash('error', 'Vehicle not found');
            header('Location: '.$_ENV['APP_URL'].'/search');
            exit;
        }
        extract($vehicle);
       
        require_once 'app/Views/search/vehicle_profile.php';
    }

    public function getSearchHistory() {
        $user_id = $this->auth->getUserId();
        $user_role = $this->auth->getUserRole();
        
        if ($user_role !== 'searcher') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        $page = $_GET['page'] ?? 1;
        $per_page = $_GET['per_page'] ?? 10;
        
        $search_history = $this->vehicle->getSearchHistory($user_id, $page, $per_page);
        $total_searches = $this->vehicle->getSearchHistoryCount($user_id);
        
        $response = [
            'search_history' => $search_history,
            'pagination' => [
                'page' => $page,
                'per_page' => $per_page,
                'total' => $total_searches,
                'total_pages' => ceil($total_searches / $per_page)
            ]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function exportSearchResults() {
        $user_role = $this->auth->getUserRole();
        
        if (!in_array($user_role, ['searcher', 'admin'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        $filters = [
            'vin' => $_GET['vin'] ?? '',
            'plate_number' => $_GET['plate_number'] ?? '',
            'make' => $_GET['make'] ?? '',
            'model' => $_GET['model'] ?? '',
            'year' => $_GET['year'] ?? '',
            'status' => $_GET['status'] ?? '',
            'owner_identifier' => $_GET['owner_identifier'] ?? ''
        ];

        $vehicles = $this->vehicle->advancedSearch($filters, 1, 1000); // Export max 1000 records
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="vehicle_search_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, [
            'VIN',
            'Plate Number',
            'Make',
            'Model',
            'Year',
            'Current Status',
            'Current Owner',
            'Owner Email',
            'Owner Phone',
            'Registration Date'
        ]);
        
        // Add data
        foreach ($vehicles as $vehicle) {
            fputcsv($output, [
                $vehicle->vin,
                $vehicle->current_plate_number,
                $vehicle->make,
                $vehicle->model,
                $vehicle->year,
                $vehicle->current_status,
                $vehicle->owner_name,
                $vehicle->owner_email,
                $vehicle->owner_phone,
                $vehicle->created_at
            ]);
        }
        
        fclose($output);
        exit;
    }
}
?>