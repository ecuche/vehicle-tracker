<?php
namespace App\Controllers;
use App\Core\Controller;

class AdminController extends Controller {

    public function __construct() {
        parent::__construct();
        
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('login');
        }
        
        if (!$this->auth->isAdmin()) {
            $this->session->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('dashboard');
        }
    }

    public function users() {
        $page = $_GET['page'] ?? 1;
        $per_page = $_GET['per_page'] ?? 10;
        $search = $_GET['search'] ?? '';
        
        $users = $this->user->getUsersPaginated($page, $per_page, $search);
        $driver_count = $this->user->getDriverCount();
        $searcher_count = $this->user->getSearcherCount();
        $admin_count = $this->user->getAdminCount();

        $total_users = $this->user->getUsersCount($search);
        
        $data = [
            'users' => $users,
            'driver_count' => $driver_count,
            'searcher_count' => $searcher_count,
            'admin_count' => $admin_count,
            'pagination' => [
                'page' => $page,
                'per_page' => $per_page,
                'total' => $total_users,
                'total_pages' => ceil($total_users / $per_page)
            ],
            'search' => $search
        ];
        $this->view('admin/users', $data);
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

    public function getUserDetails($user_id) {
        $user = $this->user->findById($user_id);
        
        if ($user) {
            $vehicles = $this->user->getUserVehiclesWithHistory($user_id);
            $contact_details = [
                'email' => $user['email'],
                'phone' => $user['phone'],
                'nin' => $user['nin']
            ];
            
            $data = [
                'user' => $user,
                'vehicles' => $vehicles,
                'contact_details' => $contact_details
            ];
            
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'User not found']);
        }
        exit;
    }

    public function updateUserRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? '';
            $new_role = $_POST['role'] ?? '';
            
            $allowed_roles = ['driver', 'searcher', 'admin'];
            
            if (!in_array($new_role, $allowed_roles)) {
                $this->session->setFlash('error', 'Invalid role');
                header("Location: {$_ENV['APP_URL']}/admin/users");
                exit;
            }
            
            if ($this->user->updateRole($user_id, $new_role)) {
                $this->audit->log(
                    $this->auth->getUserId(),
                    'update_user_role',
                    'users',
                    $user_id,
                    ['old_role' => $_POST['old_role'] ?? ''],
                    ['new_role' => $new_role]
                );
                
                $this->session->setFlash('success', 'User role updated successfully');
            } else {
                $this->session->setFlash('error', 'Failed to update user role');
            }
        }
        $this->redirect('admin/user');
    }

    public function toggleUserBan() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? '';
            $action = $_POST['action'] ?? ''; // ban or unban
            
            $user = $this->user->findById($user_id);
            
            if (!$user) {
                $this->session->setFlash('error', 'User not found');
                header("Location: {$_ENV['APP_URL']}/admin/users");
                exit;
            }
            
            if ($action === 'ban') {
                if ($this->user->ban($user_id)) {
                    $this->audit->log(
                        $this->auth->getUserId(),
                        'ban_user',
                        'users',
                        $user_id,
                        ['is_banned' => false],
                        ['is_banned' => true]
                    );
                    
                    $this->session->setFlash('success', 'User banned successfully');
                } else {
                    $this->session->setFlash('error', 'Failed to ban user');
                }
            } elseif ($action === 'unban') {
                if ($this->user->unban($user_id)) {
                    $this->audit->log(
                        $this->auth->getUserId(),
                        'unban_user',
                        'users',
                        $user_id,
                        ['is_banned' => true],
                        ['is_banned' => false]
                    );
                    
                    $this->session->setFlash('success', 'User unbanned successfully');
                } else {
                    $this->session->setFlash('error', 'Failed to unban user');
                }
            }
        }
        $this->redirect('admin/user');
    }

    public function vehicles() {
        $page = $_GET['page'] ?? 1;
        $per_page = $_GET['per_page'] ?? 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $vehicles = $this->vehicle->getVehiclesPaginated($page, $per_page, $search, $status);
        $total_vehicles = $this->vehicle->getVehiclesCount($search, $status);
        $normal_count = $this->vehicle->getNormalStatusCount();
        $stolen_count = $this->vehicle->getStolenStatusCount();
        $customs_count = $this->vehicle->getCustomsStatusCount();
        $engine_count = $this->vehicle->getEngineStatusCount();
        $color_count = $this->vehicle->getColorStatusCount();
        
        $data = [
            'vehicles' => $vehicles,
            'pagination' => [
                'page' => $page,
                'per_page' => $per_page,
                'total' => $total_vehicles,
                'total_pages' => ceil($total_vehicles / $per_page)
            ],
            'normal_count' => $normal_count,
            'stolen_count' => $stolen_count,
            'customs_count' => $customs_count,
            'engine_count' => $engine_count,
            'color_count' => $color_count,
            'search' => $search,
            'status' => $status
        ];
        $this->view('admin/vehicles', $data);
    }

    public function updateVehicleStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vehicle_id = $_POST['vehicle_id'] ?? '';
            $status = $_POST['status'] ?? '';
            
            $allowed_statuses = ['none', 'stolen', 'no_customs_duty', 'changed_engine', 'changed_color'];
            
            if (!in_array($status, $allowed_statuses)) {
                $this->session->setFlash('error', 'Invalid status');
                header("Location: {$_ENV['APP_URL']}/admin/vehicles");
                exit;
            }
            
            $vehicle = $this->vehicle->findVehicleById($vehicle_id);
            
            if (!$vehicle) {
                $this->session->setFlash('error', 'Vehicle not found');
                header("Location: {$_ENV['APP_URL']}/admin/vehicles");
                exit;
            }
            
            if ($this->vehicle->updateStatus($vehicle_id, $status)) {
                $this->audit->log(
                    $this->auth->getUserId(),
                    'update_vehicle_status',
                    'vehicles',
                    $vehicle_id,
                    ['old_status' => $vehicle->current_status],
                    ['new_status' => $status]
                );
                
                // Send email notification to vehicle owner
                $owner = $this->user->findById($vehicle->user_id);
                if ($owner) {
                    sendVehicleStatusChangeNotification($owner->email, $vehicle, $status);
                }
                
                $this->session->setFlash('success', 'Vehicle status updated successfully');
            } else {
                $this->session->setFlash('error', 'Failed to update vehicle status');
            }
        }
        $this->redirect('admin/vehicles');
    }

    public function audit() {
        $page = $_GET['page'] ?? 1;
        $per_page = $_GET['per_page'] ?? 10;
        $search = $_GET['search'] ?? '';
        $action = $_GET['action'] ?? '';
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';
        
        $audit_logs = $this->audit->getAuditLogsPaginated($page, $per_page, $search, $action, $start_date, $end_date);
        $total_logs = $this->audit->getAuditLogsCount($search, $action, $start_date, $end_date);
        $today_logs = $this->audit->getTodayLogsCount();
        $driver_actions = $this->audit->getDriverLogsCount();
        $searcher_actions = $this->audit->getSearcherLogsCount();
        $admin_actions = $this->audit->getAdminLogsCount();
        $user_actions = $driver_actions + $searcher_actions;
        $data = [
            'audit_logs' => $audit_logs,
            'pagination' => [
                'page' => $page,
                'per_page' => $per_page,
                'total' => $total_logs,
                'total_pages' => ceil($total_logs / $per_page)
            ],
            'search' => $search,
            'action' => $action,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'today_logs' => $today_logs,
            'user_actions' => $user_actions,
            'admin_actions' => $admin_actions 
        ];
        $this->view('admin/audit', $data);
    }

    public function exportAuditToCSV() {
        $search = $_GET['search'] ?? '';
        $action = $_GET['action'] ?? '';
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';
        
        $audit_logs = $this->audit->getAllAuditLogs($search, $action, $start_date, $end_date);
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="audit_trail_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, [
            'ID',
            'User ID',
            'Action',
            'Table',
            'Record ID',
            'Old Values',
            'New Values',
            'IP Address',
            'User Agent',
            'Timestamp'
        ]);
        
        // Add data
        foreach ($audit_logs as $log) {
            fputcsv($output, [
                $log->id,
                $log->user_id,
                $log->action,
                $log->table_name,
                $log->record_id,
                $log->old_values,
                $log->new_values,
                $log->ip_address,
                $log->user_agent,
                $log->created_at
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function getStats() {
        $stats = [
            'total_users' => $this->user->getTotalCount(),
            'total_vehicles' => $this->vehicle->getTotalCount(),
            'total_transfers' => $this->transfer->getTotalCount(),
            'pending_transfers' => $this->transfer->getPendingCount(),
            'banned_users' => $this->user->getBannedCount(),
            'vehicles_by_status' => $this->vehicle->getCountByStatus()
        ];
        
        header('Content-Type: application/json');
        echo json_encode($stats);
        exit;
    }

    public function manageUser($email){
        $user = is_numeric($email) ? $this->user->findById($email) : $this->user->findByEmail($email);
        if (empty($user)) {
            $this->session->setFlash('error', 'User does not exist');
            header('Location: '.$_ENV['APP_URL'].'/dashboard');
            exit;
        }
        $data['user'] = $user;
        $this->view('admin/edit-user', $data);
    }

    public function manageVehicle($vin){
        $vehicle = is_numeric($vin) ? $this->vehicle->findVehicleById($vin) : $this->vehicle->findByVIN($vin);
        if (empty($vehicle)) {
            $this->session->setFlash('error', 'Vehicle does not exist');
            header('Location: '.$_ENV['APP_URL'].'/dashboard');
            exit;
        }
        $vehicle_makes = $this->vehicleModel->findAllGroupBy('make');
        $vehicle_models = $this->vehicleModel->findAll(['make' => $vehicle['make']]);
        $user = $this->user->findById($vehicle['user_id']);
        $data = [
            'vehicle' => $vehicle,
            'user' => $user,
            'vehicle_makes' => $vehicle_makes,
            'vehicle_models' => $vehicle_models,
        ];
        $this->view('admin/edit-vehicle', $data);
    }

    public function updateUser(){
        $post = json_decode(file_get_contents("php://input"), true);
         if (empty($post['email'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $data = [
            'name' => $post['name'],
            'role' => $post['role'],
            'is_banned' => empty($post['is_banned']) ? 0 : 1
        ]; 
        $user =  $this->user->findByEmail($post['email']);
       
        if ($this->user->updateUser($user['id'], $data)) {
            $user['success'] = true;
            header('Content-Type: application/json');
            echo json_encode($user);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'User not found']);
        }
        exit;
    }

    public function deleteUser() {
        $post = json_decode(file_get_contents("php://input"), true);
        if (empty($post['email'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $user = $this->user->findByEmail($post['email']);
        if ($user && $this->user->softDeleteById($user['id'])) {
            $this->audit->log(
                $this->auth->getUserId(),
                'delete_user',
                'users',
                $user['id'],
                ['email' => $user['email']]
            );
            $user['success'] = true;
            header('Content-Type: application/json');
            echo json_encode($user);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'User not found or could not be deleted']);
        }
        exit;
    }

    public function updateVehicle(){
       exit;
    }

    public function viewVehicleUsers($vin){
        $vehicle = $this->vehicle->findByVIN($vin);
        if (empty($vehicle)) {
            $this->session->setFlash('error', 'Vehicle does not exist');
            header('Location: '.$_ENV['APP_URL'].'/dashboard');
            exit;
        }
        $current_owner = $this->vehicle->getCurrentOwner($vehicle['id']);
        $previous_owners_pagination = $this->vehicle->getPreviousOwnersPaginated($vehicle['id'], 1, 5);
        $previous_owners = $previous_owners_pagination;
        $users = array_merge([$current_owner], $previous_owners);
        $stats = [
            'total_owners' => $this->transfer->countRow(['vehicle_id'=>$vehicle['id']]),
            'transfers' => $this->transfer->countRow(['vehicle_id'=>$vehicle['id']]),
            'current_owner' => $current_owner ? 1 : 0,
            'previous_owners' => count($previous_owners)
        ];
        $previous_owners_pagination['total_pages'] = ceil($stats['total_owners'] / 5);
        $data = [
            'vehicle' => $vehicle,
            'current_owner' => $current_owner,
            'previous_owners' => $previous_owners,
            'users' => $users,
            'stats' => $stats,
            'previous_owners_pagination' => $previous_owners_pagination,
        ];
        $this->view('admin/vehicle-users', $data);
    }

    public function viewUserVehicles($email){
        $page = $this->request->get('page',1);
        $per_page = $this->request->get('per_page',10);
        $user = $this->user->findByEmail($email);
        if (empty($user)) {
            $this->session->setFlash('error', 'User does not exist');
            header('Location: '.$_ENV['APP_URL'].'/dashboard');
            exit;
        }
        $count = $this->transfer->countRow(['buyer_id'=>$user['id']]);
        $offset = ($page -1) * $per_page;
        $pagination = [
            'current_page' => $page,
            'total_pages' => ceil($count / $per_page),
        ];
        $vehicles = $this->transfer->getUserVehicleHistoryPagination($user['id'], $offset, $per_page);
        $data = [
            'user' => $user,
            'vehicles' => $vehicles,
            'pagination'=> $pagination
        ];
        $this->view('admin/user-vehicles', $data);
    }

    public function getUserAuditLogs($email){
        $user = $this->user->findByEmail($email);
        if (empty($user)) {
            $this->session->setFlash('error', 'User does not exist');
            header('Location: '.$_ENV['APP_URL'].'/dashboard');
            exit;
        }
        $audit_logs = $this->audit->getAuditLogsByUserId($user['id']);
        $data = [
            'user' => $user,
            'audit_logs' => $audit_logs,
        ];
        $this->view('admin/user-audit', $data);
    }
}
?>