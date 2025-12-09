<?php
namespace App\Controllers;
use App\Core\Controller;


class VehicleController extends Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('login');
            exit;
        }
    }

    public function register() {
        $user_id = $this->auth->getUserId();
        $user_role = $this->auth->getUserRole();
        
        // Only drivers and searchers can register vehicles
        if (!in_array($user_role, ['driver', 'searcher'])) {
            $this->session->setFlash('error', 'Only drivers and searchers can register vehicles');
            $this->redirect('dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'vin' => trim($_POST['vin'] ?? ''),
                'plate_number' => trim($_POST['plate_number'] ?? ''),
                'vehicle_model_id' => $_POST['vehicle_model_id'] ?? '',
                'year' => $_POST['year'] ?? '',
                'color' => $_POST['color'] ?? '',
                'user_id' => $user_id
            ];

            $errors = $this->validateVehicleRegistration($data);
            
            if (empty($errors)) {
                // Handle file uploads
                $upload_errors = [];
                $vehicle_images = [];
                $vehicle_documents = [];
                
                // Upload vehicle images
                if (!empty($_FILES['vehicle_images'])) {
                    $images_result = $this->upload->handleVehicleImages($_FILES['vehicle_images']);
                    if (!$images_result['success']) {
                        $upload_errors[] = $images_result['error'];
                    } else {
                        $vehicle_images = $images_result['file_paths'];
                    }
                }
                
                // Upload vehicle documents
                if (!empty($_FILES['vehicle_documents'])) {
                    $docs_result = $this->upload->handleVehicleDocuments($_FILES['vehicle_documents']);
                    if (!$docs_result['success']) {
                        $upload_errors[] = $docs_result['error'];
                    } else {
                        $vehicle_documents = $docs_result['file_paths'];
                    }
                }
                
                if (empty($upload_errors)) {
                    if ($this->vehicle->create($data, $vehicle_images, $vehicle_documents)) {
                        $vehicle = $this->vehicle->find(['vin'=>$data['vin']]);
                        $status = $this->vehicleStatusHistory->insertAndGet(['vehicle_id' => $vehicle['id']]);
                        $plate = $this->plateNumber->insertAndGet(['vehicle_id' => $vehicle['id'], 'plate_number' => $data['plate_number']]);
                        $transfer = $this->transfer->insertAndGet(['vehicle_id'=> $vehicle['id'], 'buyer_id' => $user_id, 'status'=>'registration' ]);
                        $this->vehicle->updateById(['current_status' => $status['status'], 'current_status_id' => $status['id'], 'current_plate' => $plate['plate_number'], 'current_plate_id' => $plate['id']], $vehicle['id']);
                        $this->session->setFlash('success', 'Vehicle registered successfully');
                        $this->redirect('vehicles');
                        exit;
                    } else {
                        $errors[] = 'Vehicle registration failed';
                    }
                } else {
                    $errors = array_merge($errors, $upload_errors);
                }
            }
            
            $this->session->setFlash('errors', $errors);
            $this->session->setFlash('form_data', $data);
        }
        
        $vehicle_models = $this->vehicleModel->getAll();
        $vehicle_makes = $this->vehicleModel->getAllMake();
        $data = [
            'vehicle_models' => $vehicle_models,
            'vehicle_makes' => $vehicle_makes,
        ];
        $this->view('vehicle/register', $data);
    }

    private function validateVehicleRegistration($data) {
        $errors = [];

        if (!$this->validator::validateVIN($data['vin'])) {
            $errors[] = 'Invalid VIN format (must be 17 alphanumeric characters)';
        }

        if ($this->vehicle->findByVIN($data['vin'])) {
            $errors[] = 'VIN already registered';
        }

        if (empty($data['plate_number'])) {
            $errors[] = 'Plate number is required';
        }

        if (empty($data['vehicle_model_id']) || !$this->vehicleModel->findById($data['vehicle_model_id'])) {
            $errors[] = 'Invalid vehicle model';
        }

        if (empty($data['year']) || $data['year'] < 1900 || $data['year'] > date('Y') + 1) {
            $errors[] = 'Invalid year';
        }

        if (empty($data['color'])) {
            $errors[] = 'Vehicle Color is required';
        }

        return $errors;
    }

    public function index() {
        $user_id = $this->auth->getUserId();
        
        $page = $_GET['page'] ?? 1;
        $per_page = $_GET['per_page'] ?? 10;
        $total_vehicles = $this->vehicle->getUserVehicleCount($user_id);
        $data = [
            'vehicles' => $this->vehicle->getUserVehiclesPaginated($user_id, $page, $per_page),
            'pagination' => [
                'page' => $page,
                'per_page' => $per_page,
                'total' => $total_vehicles,
                'total_pages' => ceil($total_vehicles / $per_page)
            ],
            'pending_requests'=> $this->transfer->getOutgoingCount($user_id),
            'failed_sales'=> $this->transfer->getFailedCount($user_id),
            'incoming_requests' => $this->transfer->getIncomingCount($user_id),
            'total_vehicles' => $total_vehicles,
            'sold_vehicles' => $this->transfer->getSoldCount($user_id),
        ];
        $this->view('vehicle/index', $data);
    }

    public function transfer() {
        $user_id = $this->auth->getUserId();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vehicle_id = $_POST['vehicle_id'] ?? '';
            $user_identifier = trim($_POST['user_identifier'] ?? '');
            
            $errors = [];
            
            // Find vehicle
            $vehicle = $this->vehicle->findVehicleById($vehicle_id);
            if (!$vehicle || $vehicle->user_id != $user_id) {
                $errors[] = 'Vehicle not found or you are not the owner';
            }
            
            // Find recipient user
            $recipient = $this->user->findByIdentifier($user_identifier);
            if (!$recipient) {
                $errors[] = 'User not found';
            } elseif ($recipient->id == $user_id) {
                $errors[] = 'Cannot transfer vehicle to yourself';
            }
            
            if (empty($errors)) {
                // Create transfer request
                $transfer_data = [
                    'vehicle_id' => $vehicle_id,
                    'seller_id' => $user_id,
                    'buyer_id' => $recipient->id,
                    'status' => 'pending'
                ];
                
                if ($this->transfer->create($transfer_data)) {
                    // Send email and app notification
                    sendTransferNotification($recipient->email, $vehicle, $this->auth->getUser());
                    
                    $this->session->setFlash('success', 'Transfer request sent successfully');
                    $this->redirect('vehicles');
                    exit;
                } else {
                    $errors[] = 'Failed to create transfer request';
                }
            }
            $this->session->setFlash('errors', $errors);
        }
        $this->redirect('vehicles');
        exit;
    }

    public function transferVehicle($vin) {
        $vehicle = $this->vehicle->findByVIN($vin);
        if (!$vehicle) {
            $this->session->setFlash('error', 'Vehicle not found');
            $this->redirect('dashboard');
            exit;
        }
        if($vehicle['user_id'] !== $this->auth->getUserId() && !$this->auth->isAdmin()){
            $this->session->setFlash('error', 'Vehicle not found');
            $this->redirect('vehicles');
            exit;
        }
        $data = [
            'user_id' => $this->auth->getUserId(),
            'vehicle' => $vehicle
        ];
        $this->view('vehicle/transfer', $data);
    }

    public function handleTransfer($vin = null) {
        $post = json_decode(file_get_contents('php://input'), true);
        $vin = $vin ?: $post['vin'];
        $seller = $this->user->findById($this->auth->getUserId());
        $buyer = $this->user->findById($post['recipient_id']);
        if (empty($post) || empty($post['vehicle_id']) || empty($post['recipient_id'])) {
            echo json_encode(['error' => 'Vehicle or User not found']);
            exit;
        }
        $vehicle = $this->vehicle->findById($post['vehicle_id']);
        if(empty($vehicle) || $vehicle['current_status'] == "stolen" || $vehicle['user_id'] != $seller['id']) {
            echo json_encode(['error' => 'Vehicle Validation failed']);
            exit;
        }
        $this->vehicle->updateById(['user_id'=> $buyer['id']], $vehicle['id']);
        $this->transfer->updateLast(['end_date' => 'NOW()']);
        $this->transfer->update(['is_current' => 0], ['vehicle_id' => $vehicle['id']]);
        $this->transfer->insertAndGet([
            'vehicle_id' => $vehicle['id'],
            'seller_id' => $seller['id'],
            'buyer_id' => $buyer['id'],
            'transfer_type' => $post['transfer_type'] ?? '',
            'transfer_amount' => $post['transfer_amount'] ?? '',
            'transfer_note'=> $post['transfer_note'] ?? '',
        ]);
        echo json_encode(['success' => true ]);
        exit;
    }

    public function assignPlate() {
        $user_id = $this->auth->getUserId();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vehicle_id = $_POST['vehicle_id'] ?? '';
            $plate_number = trim($_POST['plate_number'] ?? '');
            
            $vehicle = $this->vehicle->findVehicleById($vehicle_id);
            
            if (!$vehicle || $vehicle->user_id != $user_id) {
                $this->session->setFlash('error', 'Vehicle not found or you are not the owner');
                $this->redirect('vehicles');
                exit;
            }
            
            if (empty($plate_number)) {
                $this->session->setFlash('error', 'Plate number is required');
                $this->redirect('vehicles');
                exit;
            }
            
            if ($this->vehicle->assignPlateNumber($vehicle_id, $plate_number)) {
                $this->session->setFlash('success', 'Plate number assigned successfully');
            } else {
                $this->session->setFlash('error', 'Failed to assign plate number');
            }
        }
        $this->redirect('vehicles');
        exit;
    }

    public function getVehicleDetails($vehicle_id) {
        $vehicle = $this->vehicle->getFullDetails($vehicle_id);
        
        if ($vehicle) {
            header('Content-Type: application/json');
            echo json_encode($vehicle);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Vehicle not found']);
        }
        exit;
    }

    public function searchUser() {
        $identifier = $_GET['q'] ?? '';
        if (empty($identifier)) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
        $users = $this->user->searchByIdentifier($identifier);
        header('Content-Type: application/json');
        echo json_encode($users);
        exit;
    }

    public function checkVIN(){
        if (empty($_POST['vin'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $vin = $_POST['vin'];
        $vehicle = $this->vehicle->findByVIN($vin);
        if ($vehicle) {
            header('Content-Type: application/json');
            echo json_encode($vehicle);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'VIN not found']);
        }
        exit;
    }

     public function getVehicleModels() {
        if (empty($_POST['make'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $make = $_POST['make'];
        $models = $this->vehicleModel->getModelsByMake($make);
        // header('Content-Type: application/json');
        if ($models) {
            echo json_encode($models);
        } else {
            echo json_encode(['error' => 'Make not found']);
        }
        exit;
    }

    public function viewVehicle($vin) {   
        $vehicle = $this->vehicle->findByVIN($vin);
        if ($this->auth->getUserId() !== $vehicle['user_id'] && !$this->auth->isAdmin() && !$this->auth->isSearcher() ) {
            $this->session->setFlash('error', 'Access denied');
            $this->redirect('dashboard');
            exit;
        }
        if (!$vehicle) {
            $this->session->setFlash('error', 'Vehicle not found');
            $this->redirect('dashboard');
            exit;
        }
        $data = [
            'vehicle' => $vehicle,
            'model' => $this->vehicleModel->findById($vehicle['vehicle_model_id']),
            'plates' => $this->plateNumber->findAll(['vehicle_id'=> $vehicle['id']]),
            'status' => $this->vehicleStatusHistory->findAll(['vehicle_id'=> $vehicle['id']]),
            'transfer' => $this->transfer->findAll(['vehicle_id'=> $vehicle['id']]),
            'user' => $this->user->findById($vehicle['user_id']),
        ];
        $this->view('vehicle/details', $data);
    }

    public function viewOwnershipHistory($vin) {  
        $vehicle = $this->vehicle->findByVIN($vin); 
        if(empty($vehicle)){
            $this->session->setFlash('error', 'Vehicle not found');
            $this->redirect('dashboard');
            exit;
        }
        if( $this->session->get('user_role') === 'driver' &&
            $this->session->get('user_id') !== $vehicle['user_id']
        ){
                $this->session->setFlash('error', 'Access denied');
                $this->redirect('dashboard');
                exit;
        }  
        $data = [
            'ownership_history' => $this->vehicle->getVehicleHistory($vehicle['id']),
            'plate_history' => $this->vehicle->getPlateNumberHistory($vehicle['id']),
            'document_history' => $this->vehicle->getDocumentHistory($vehicle['id']),
            'status_history' => $this->vehicleStatusHistory->findAll(['vehicle_id'=> $vehicle['id']]),
            'vehicle' => $vehicle
        ];
        $this->view('vehicle/history', $data);
    }

    public function viewStatusHistory($vin) {
        $this->viewOwnershipHistory($vin);  
    }
}
?>