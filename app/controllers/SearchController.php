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


    public function getVehicleProfile($vin) {
        $this->response->roleAuth(['searcher', 'admin']);
        $vehicle = $this->vehicle->findByVIN($vin);
        if (empty($vehicle)) {
            $this->response->error('Vehicle not found');
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

  
}
?>