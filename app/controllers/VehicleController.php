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
            header('Location: '.$_ENV['APP_URL'].'/dashboard');
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
                        $this->session->setFlash('success', 'Vehicle registered successfully');
                        header('Location: '.$_ENV['APP_URL'].'/vehicles');
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
        extract($data);        
        require_once 'app/Views/vehicle/register.php';
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
        $user_role = $this->auth->getUserRole();
        
        $page = $_GET['page'] ?? 1;
        $per_page = $_GET['per_page'] ?? 10;
        
        $vehicles = $this->vehicle->getUserVehiclesPaginated($user_id, $page, $per_page);
        $total_vehicles = $this->vehicle->getUserVehicleCount($user_id);
        $pending_requests = $this->transfer->getOutgoingCount($user_id);
        $incoming_requests = $this->transfer->getIncomingCount($user_id);
        $failed_sales = $this->transfer->getFailedCount($user_id);
        $sold_vehicles = $this->transfer->getSoldCount($user_id);

        
        $data = [
            'vehicles' => $vehicles,
            'pagination' => [
                'page' => $page,
                'per_page' => $per_page,
                'total' => $total_vehicles,
                'total_pages' => ceil($total_vehicles / $per_page)
            ]
        ];
        extract($data);
        require_once 'app/Views/vehicle/index.php';
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
                    'from_user_id' => $user_id,
                    'to_user_id' => $recipient->id,
                    'status' => 'pending'
                ];
                
                if ($this->transfer->create($transfer_data)) {
                    // Send email and app notification
                    sendTransferNotification($recipient->email, $vehicle, $this->auth->getUser());
                    
                    $this->session->setFlash('success', 'Transfer request sent successfully');
                    header('Location: '.$_ENV['APP_URL'].'/vehicles');
                    exit;
                } else {
                    $errors[] = 'Failed to create transfer request';
                }
            }
            $this->session->setFlash('errors', $errors);
        }
        
        header('Location: '.$_ENV['APP_URL'].'/vehicles');
        exit;
    }

    public function transferVehicle($vin) {
        $vehicle = $this->vehicle->findByVIN($vin);
        if (!$vehicle) {
            $this->session->setFlash('error', 'Vehicle not found');
            header('Location: '.$_ENV['APP_URL'].'/dashboard');
            exit;
        }

        $user_id = $this->auth->getUserId();
        require_once 'app/Views/vehicle/transfer.php';
    }

    public function handleTransfer() {
        $user_id = $this->auth->getUserId();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $transfer_id = $_POST['transfer_id'] ?? '';
            $action = $_POST['action'] ?? ''; // accept or reject
            
            $transfer = $this->transfer->findTransferById($transfer_id);
            
            if (!$transfer || $transfer->to_user_id != $user_id) {
                $this->session->setFlash('error', 'Transfer request not found');
                header('Location: '.$_ENV['APP_URL'].'/dashboard');
                exit;
            }
            
            if ($action === 'accept') {
                // Update vehicle ownership
                if ($this->vehicle->transferOwnership($transfer->vehicle_id, $user_id)) {
                    $this->transfer->updateStatus($transfer_id, 'accepted');
                    
                    // Notify previous owner
                    $previous_owner = $this->user->findById($transfer->from_user_id);
                    sendTransferAcceptedNotification($previous_owner->email, $transfer->vehicle_id);
                    
                    $this->session->setFlash('success', 'Vehicle transfer accepted successfully');
                } else {
                    $this->session->setFlash('error', 'Failed to transfer vehicle ownership');
                }
            } elseif ($action === 'reject') {
                $this->transfer->updateStatus($transfer_id, 'rejected');
                $this->session->setFlash('success', 'Vehicle transfer rejected');
            }
        }
        
        header('Location: '.$_ENV['APP_URL'].'/dashboard');
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
                header('Location: '.$_ENV['APP_URL'].'/vehicles');
                exit;
            }
            
            if (empty($plate_number)) {
                $this->session->setFlash('error', 'Plate number is required');
                header('Location: '.$_ENV['APP_URL'].'/vehicles');
                exit;
            }
            
            if ($this->vehicle->assignPlateNumber($vehicle_id, $plate_number)) {
                $this->session->setFlash('success', 'Plate number assigned successfully');
            } else {
                $this->session->setFlash('error', 'Failed to assign plate number');
            }
        }
        
        header('Location: '.$_ENV['APP_URL'].'/vehicles');
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
        if ($this->session->get('user_id') !== $vehicle['user_id']) {
            $this->session->setFlash('error', 'Access denied');
            header('Location: '.$_ENV['APP_URL'].'/dashboard');
            exit;
        }
        $vehicle = $this->vehicle->getFullProfile($vehicle['id']);
        if (!$vehicle) {
            $this->session->setFlash('error', 'Vehicle not found');
            header('Location: '.$_ENV['APP_URL'].'/search');
            exit;
        }
        extract($vehicle);
       
        require_once 'app/Views/vehicle/details.php';
    }

    public function viewVehicleHistory($vin) {   
        $vehicle = $this->vehicle->findByVIN($vin); 
        if($this->session->get('user_role') === 'driver'){
            if ($this->session->get('user_id') !== $vehicle['user_id']) {
                $this->session->setFlash('error', 'Access denied');
                header('Location: '.$_ENV['APP_URL'].'/dashboard');
                exit;
            }
        }  
        $ownership_history = $this->vehicle->getVehicleHistory($vehicle['id']);
        $plate_history = $this->vehicle->getPlateNumberHistory($vehicle['id']);
        $document_history = $this->vehicle->getDocumentHistory($vehicle['id']);
        require_once 'app/Views/vehicle/history.php';
    }
}
?>