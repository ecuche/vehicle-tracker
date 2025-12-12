<?php
namespace App\Controllers;
use App\Core\Controller;

class SearchController extends Controller {

    public function __construct() {
        parent::__construct();
        
        if (!$this->auth->isLoggedIn()) {
            $this->redirect("login");
            exit;
        }
    }

    public function index() {
        $user_role = $this->auth->getUserRole();
        
        // Only searcher and admin can access search page
        if (!in_array($user_role, ['searcher', 'admin'])) {
            $this->session->setFlash('error', 'Access denied');
            $this->redirect('dashboard');
            exit;
        }
        $data = [
            'search_history' => $this->searchHistory->userSearchHistoryPagination(),
            'search_count' => $this->searchHistory->userSearchCount(),
        ];
        $this->view('search/index', $data);
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

        if ($search_type === 'vin') {
            $vehicle = $this->vehicle->findByVIN($search_term);
        } elseif ($search_type === 'plate') {
            $vehicle = $this->vehicle->findByPlateNumber($search_term);
        }
        if ($vehicle) {
            $vehicle_details = $this->vehicle->getFullDetails($vehicle['id']);
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

    public function getVehicleProfile($vin) {
        $user_role = $this->auth->getUserRole();
        
        if (!in_array($user_role, ['searcher', 'admin'])) {
            $this->session->setFlash('error', 'Access denied');
            $this->redirect('dashboard');
            exit;
        }
        $vehicle = $this->vehicle->findByVIN($vin);
        if (!$vehicle) {
            $this->session->setFlash('error', 'Vehicle not found');
            $this->redirect('search');
            exit;
        }
        $user = $this->user->findById($vehicle['user_id']);  
        $owners = $this->transfer->getVehicleHistoryAndOwners($vehicle['id']); 
        $plates = $this->plateNumber->findAll(['vehicle_id'=>$vehicle['id']]);
        $statuses = $this->vehicleStatusHistory->findAll(['vehicle_id'=>$vehicle['id']]);
        $documents = $this->vehicle->findAll(['vehicle_id'=>$vehicle['id']], 'vehicle_documents');
        $images = $this->vehicle->findAll(['vehicle_id'=>$vehicle['id']], 'vehicle_images');
       
        
        $data = [
            'vehicle' => $vehicle,
            'user' => $user,
            'owners' => $owners,
            'plates'=> $plates,
            'statuses'=> $statuses,
            'documents'=> $documents,
            'images'=> $images
        ];
        $this->view('search/vehicle_profile', $data);
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

    public function searchUser(){
        $value = $this->request->post('value');
            $field = match (true) {
                $this->validator->validateEmail($value) => 'email',
                $this->validator->validateNIN($value) => 'nin',
                $this->validator->validatePhone($value) => 'phone',
                default => 'phone',
            };
        $users = $this->user->findAll([$field=>$value]);
        if(empty($users)){
            echo json_encode(['error' => 'User not found']);    
            exit;
        }
        if($users[0]['id'] === $this->auth->getUserId()){
            echo json_encode(['error' => 'You are this User']);    
            exit;
        }
        $users[0]['success'] = true;   
        echo json_encode($users);    
        exit;
    }
}
?>