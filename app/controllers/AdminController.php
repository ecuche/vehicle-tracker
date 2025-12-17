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

    public function vehicles() {
        $page = $_GET['page'] ?? 1;
        $per_page = $_GET['per_page'] ?? 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $offset = ($page - 1) * $per_page;
        
        $vehicles = $this->vehicle->getVehiclesPaginated($offset, $per_page, $search, $status);
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
            $vehicle_id = $this->request->post('vehicle_id') ?? '';
            $status = $this->request->post('status') ?? '';
            
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
        $page = $this->request->get('page', 1);
        $per_page = $this->request->get('per_page', 10);
        $search = $this->request->get('search', '');
        $action = $this->request->get('action', '');
        $start_date = $this->request->get('start_date', '');
        $end_date = $this->request->get('end_date', '');
        
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

    public function manageUser($email){
        $user = is_numeric($email) ? $this->user->findById($email) : $this->user->findByEmail($email);
        if (empty($user)) {
            $this->session->setFlash('error', 'User does not exist');
            header('Location: '.$_ENV['APP_URL'].'/dashboard');
            exit;
        }
        $data = [
            'user' => $user,
            'stats' => $this->vehicle->userVehicleStats($user['id']),
        ];
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
            'stats' => $this->vehicle->vehicleStat($vehicle['id'])
        ];
        $this->view('admin/edit-vehicle', $data);
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

    public function viewAdmins(){
        $current_page = $this->request->get('page', 1);
        $per_page = $this->request->get('per_page', 10);
        $offset = ($current_page -1) * $per_page;
        $total_items = $this->user->countRow(['role'=>'admin']);
        $admins = $this->user->findPagination(['role'=> 'admin'], $per_page , $offset);
        $total_pages = ceil($total_items / $per_page);
        $stats = $this->user->roleStat('admin');
        $data = [
            'admins' => $admins, 
            'per_page' => $per_page,
            'current_page' => $current_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'stats' => $stats
        ];
        $this->view('admin/admins', $data);
    }

    public function viewDrivers(){
        $current_page = $this->request->get('page', 1);
        $per_page = $this->request->get('per_page', 10);
        $offset = ($current_page -1) * $per_page;
        $total_items = $this->user->countRow(['role'=>'driver']);
        $drivers = $this->user->getWithRoleAndVehicleStatsPagination('driver', $per_page , $offset);
        $total_pages = ceil($total_items / $per_page);
        $stats = $this->user->roleStat('driver');
        $data = [
            'drivers' => $drivers, 
            'per_page' => $per_page,
            'current_page' => $current_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'stats' => $stats
        ];
        $this->view('admin/drivers', $data);
    }

    public function viewSearchers(){
        $current_page = $this->request->get('page', 1);
        $per_page = $this->request->get('per_page', 10);
        $offset = ($current_page -1) * $per_page;
        $total_items = $this->user->countRow(['role'=>'searcher']);
        $searchers = $this->user->findPagination(['role'=> 'searcher'], $per_page , $offset);
        $total_pages = ceil($total_items / $per_page);
        $stats = $this->user->roleStat('searcher');
        $data = [
            'searchers' => $searchers, 
            'per_page' => $per_page,
            'current_page' => $current_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'stats' => $stats
        ];
        $this->view('admin/searchers', $data);
    }

    public function viewNormalVehicles(){
        $current_page = $this->request->get('page', 1);
        $per_page = $this->request->get('per_page', 10);
        $offset = ($current_page -1) * $per_page;
        $total_items = $this->vehicle->countRow(['current_status'=>'none']);
        $stats = $this->vehicle->vehiclesStat('none');
        $total_pages = ceil($total_items / $per_page);
        $vehicles = $this->vehicle->getVehiclesPaginated($offset, $per_page , 'none');
        $data = [
            'vehicles' => $vehicles, 
            'per_page' => $per_page,
            'current_page' => $current_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'stats' => $stats
        ];
        $this->view('admin/normal-vehicles', $data);
    }

    public function viewStolenVehicles(){
        $current_page = $this->request->get('page', 1);
        $per_page = $this->request->get('per_page', 10);
        $offset = ($current_page -1) * $per_page;
        $total_items = $this->vehicle->countRow(['current_status'=>'stolen']);
        $stats = $this->vehicle->vehiclesStat('stolen');
        $total_pages = ceil($total_items / $per_page);
        $vehicles = $this->vehicle->getVehiclesPaginated($offset, $per_page , 'stolen');
        $data = [
            'vehicles' => $vehicles, 
            'per_page' => $per_page,
            'current_page' => $current_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'stats' => $stats
        ];
        $this->view('admin/stolen-vehicles', $data);
    }

    public function viewNoCustomsVehicles(){
        $current_page = $this->request->get('page', 1);
        $per_page = $this->request->get('per_page', 10);
        $offset = ($current_page -1) * $per_page;
        $total_items = $this->vehicle->countRow(['current_status'=>'no_customs_duty']);
        $stats = $this->vehicle->vehiclesStat('no_customs_duty');
        $total_pages = ceil($total_items / $per_page);
        $vehicles = $this->vehicle->getVehiclesPaginated($offset, $per_page , 'no_customs_duty');
        $data = [
            'vehicles' => $vehicles, 
            'per_page' => $per_page,
            'current_page' => $current_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'stats' => $stats
        ];
        $this->view('admin/no-customs-vehicles', $data);
    }

    public function viewChangedEngineVehicles(){
        $current_page = $this->request->get('page', 1);
        $per_page = $this->request->get('per_page', 10);
        $offset = ($current_page -1) * $per_page;
        $total_items = $this->vehicle->countRow(['current_status'=>'changed_engine']);
        $stats = $this->vehicle->vehiclesStat('changed_engine');
        $total_pages = ceil($total_items / $per_page);
        $vehicles = $this->vehicle->getVehiclesPaginated($offset, $per_page , 'changed_engine');
        $data = [
            'vehicles' => $vehicles, 
            'per_page' => $per_page,
            'current_page' => $current_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'stats' => $stats
        ];
        $this->view('admin/changed-engine-vehicles', $data);
    }

    public function viewChangedColorVehicles(){
         $current_page = $this->request->get('page', 1);
        $per_page = $this->request->get('per_page', 10);
        $offset = ($current_page -1) * $per_page;
        $total_items = $this->vehicle->countRow(['current_status'=>'changed_color']);
        $stats = $this->vehicle->vehiclesStat('changed_color');
        $total_pages = ceil($total_items / $per_page);
        $vehicles = $this->vehicle->getVehiclesPaginated($offset, $per_page , 'changed_color');
        $data = [
            'vehicles' => $vehicles, 
            'per_page' => $per_page,
            'current_page' => $current_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'stats' => $stats
        ];
        $this->view('admin/changed-color-vehicles', $data);


    }
}
?>