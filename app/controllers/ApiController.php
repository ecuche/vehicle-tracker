<?php
namespace App\Controllers;
use App\Core\Controller;

class ApiController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function checkRole(string $role = 'driver') {
        if($role === 'admin' && !$this->auth->isAdmin() ||
            $role === 'driver' && !$this->auth->isDriver() ||
            $role === 'searcher' && !$this->auth->isSearcher()
        ) {
            $this->response->error('access denied');
        }
    }

    // public function checkUserPermission

    public function getUser(){
        $field = $this->request->post('field');
        $value = $this->request->post(name: 'value');
        $user = $this->user->findbyColumn($field, $value);
        if(!empty($field && !empty($value) && !empty($user))){
            $this->response->success($user);
        }
        $this->response->error('User Not found');
    }

    public function updateVehicle(){
        $id = $this->request->post('id');
        $vin = $this->request->post('vin');
        $vehicle_color = $this->request->post('vehicle_color');
        $current_plate = $this->request->post('current_plate');
        $vehicle_model_id  = $this->request->post('vehicle_model_id');
        $year = $this->request->post('year');
        $current_status = $this->request->post('current_status');
        $status_reason = $this->request->post('status_reason');
        $new_owner_email = $this->request->post('new_owner_email');
        $current_owner_email = $this->request->post('current_owner_email');
        $vehicle = $this->vehicle->findbyId($id);
        $user = !empty($new_owner_email) ? 
            $this->user->findByEmail($new_owner_email) : 
            $this->user->findByEmail($current_owner_email);      
        if(!empty($vehicle)){
            if(!$this->validator::validateVIN($vin)){
                $this->response->error('provide correct VIN format');
            }   
            if(empty($user)){
                $this->response->error('Driver could not be found');
            }   
            if(empty($current_plate)){
                $this->response->error('Plate Number is required');
            }
            $plate = $this->plateNumber->findbyColumn('plate_number', $current_plate);
            if(!empty($plate)){
                if($plate['id'] !== $vehicle['current_plate_id']){
                    $this->response->error('Plate Number is taken');
                }
            }
            if((($current_status === "") && ($status_reason !== "")) || 
                (($current_status !== "") && ($status_reason === "")))
            {
                $this->response->error("Change of status/Reason is required");
            }

            if(!empty($status_reason)){
                 $status =  $this->vehicleStatusHistory->insertAndGet([
                    'vehicle_id'=> $vehicle['id'],
                    'status'=> $current_status,
                    'status_reason'=>$status_reason
                ]);
            }else{
                $status = [
                    'id' => $vehicle['current_status_id'],
                    'status' => $vehicle['current_status']
                ];
            }
            if(empty($plate)){
                $this->plateNumber->update(['is_current'=> 0], ['vehicle_id'=>$vehicle['id']]);
                $plate = $this->plateNumber->insertAndGet([
                    'vehicle_id'=>$vehicle['id'],
                    'plate_number' => $current_plate
                ]);
            }else{
                $plate = [
                    'id'=>$vehicle['current_plate_id'],
                    'plate_number' => $vehicle['current_plate']
                ];
            }
            
            $data = [
                'vin' => $vin,
                'user_id' => $user['id'],
                'vehicle_model_id ' => $vehicle_model_id,
                'year' => $year,
                'color' => $vehicle_color,
                'current_status' => $status['status'],
                'current_status_id' => $status['id'],
                'current_plate' => $plate['plate_number'],
                'current_plate_id' => $plate['id']
            ];
            $this->vehicle->update($data, ['id'=>$vehicle['id']]);
            $this->response->success($data);
        }else{
            $this->response->error('Could not find Vehicle');
        }
    }

    public function deleteVehicle(){
        $vin = $this->request->post('vin');
        if($this->vehicle->softDelete(['vin'=> $vin])){
            $this->response->success();
        }else{
            $this->response->error('could not delete Vehicle');
        }
    }

    public function changeCurrentPlate(){
        $this->checkRole();
        $plate_id = $this->request->post('plate_id');
        $plate = $this->plateNumber->findById($plate_id);
        if(empty($plate)){
            $this->response->error('provide correct vehicle Plate Number');
        }
        $vehicle = $this->vehicle->findById($plate['vehicle_id']);
        $this->vehicle->updateById(['current_plate'=> $plate['plate_number'], 'current_plate_id' => $plate['id']], $plate['vehicle_id']);
        $this->plateNumber->update(['is_current'=> 0],  ['vehicle_id' => $vehicle['id']]);
        $this->plateNumber->updateById(['is_current'=> 1], $plate['id']);
        $this->response->success($plate);
    }

    public function assignNewPlate($vin){
        $this->checkRole();
        $vehicle = $this->vehicle->findByVIN($vin);
        if(empty($vehicle)){
            $this->response->error('Vehicle Error');
        }
        $plate = $this->request->post('plate');
        if(!$this->validator->validatePlateNumber($plate)){
            $this->response->error('Wrong Plate Number Format');
        }
        if($this->plateNumber->exists(['plate_number' => $plate])){
            $this->response->error('Plate Number is already registered');
        }
        $note = $this->request->post('note');
        $date = $this->request->post('assign_date');
        $this->plateNumber->update(['is_current' => 0], ['vehicle_id'=> $vehicle['id']]);
        $plate = $this->plateNumber->insertAndGet(['vehicle_id'=> $vehicle['id'], 'plate_number'=> strtoupper($plate), 'note'=>$note, 'assigned_at' => $date]);
        $this->response->success($plate);
    } 

    public function acceptTransfer($vin){
        $this->response->logInAuth();
        $vehicle = $this->vehicle->findByVIN($vin);
        $transfer = $this->transfer->findById($this->request->post('transfer_id'));
        if($vin != $this->request->post('vin') || 
            empty($vehicle) || 
            empty($this->request->post('transfer_id')) ||
            empty($transfer) ||
            !$this->auth->checkUserIdPermisson($vehicle['user_id']) ||
            !$this->auth->checkUserIdPermisson($transfer['buyer_id'])
            ){
                $this->response->error();
        }
        $this->transfer->updateAndGetById([
            'buyer_id' => $this->auth->getUserId(),
            'status' => 'completed',
            'response' => $this->request->post('notes') ?? '',
            'response_date' => date('Y-m-d H:i:s')
        ], $transfer['id']);
        $this->vehicle->updateAndGetById([
            'transfer_status'=> 'completed',
            'transfer_id' => $transfer['id'],
            'user_id' => $this->auth->getUserId()
        ], $vehicle['id']);
        $this->response->success();
    }

     public function rejectTransfer($vin){
        $this->response->logInAuth();
        $vehicle = $this->vehicle->findByVIN($vin);
        $transfer = $this->transfer->findById($this->request->post('transfer_id'));
        if($vin != $this->request->post('vin') || 
            empty($vehicle) || 
            empty($this->request->post('transfer_id')) ||
            empty($transfer) ||
            !$this->auth->checkUserIdPermisson($vehicle['user_id']) ||
            !$this->auth->checkUserIdPermisson($transfer['buyer_id'])
            ){
                $this->response->error();
        }
        $this->transfer->updateAndGetById([
            'buyer_id' => $this->auth->getUserId(),
            'status' => 'rejected',
            'response' => $this->request->post('notes') ?? '',
            'response_date' => date('Y-m-d H:i:s')
        ], $transfer['id']);
        $this->vehicle->updateAndGetById([
            'transfer_status'=> 'completed',
            'transfer_id' => $transfer['id'],
            'user_id' => $transfer['seller_id']
        ], $vehicle['id']);
        $this->response->success();
    }

       public function getVehicleModels() {
        if (empty($this->request->post('make'))) {
            $this->response->error('Unauthorized');
        }
        $make = $this->request->post('make');
        $models = $this->vehicleModel->getModelsByMake($make);
        if ($models) {
            $this->response->success($models);
        } else {
            $this->response->error('Vehicle Make not found');
        }
    }

      public function handleTransfer($vin = null) {
        $post = $this->request->jsonPost();
        $vin = $vin ?: $post['vin'];
        $seller = $this->user->findById($this->auth->getUserId());
        $buyer = $this->user->findById($post['recipient_id']);
        if (empty($post) || empty($post['vehicle_id']) || empty($post['recipient_id'])) {
            $this->response->error('Vehicle or User not found');
        }
        $vehicle = $this->vehicle->findById($post['vehicle_id']);
        if(empty($vehicle) || $vehicle['current_status'] == "stolen" || $vehicle['user_id'] != $seller['id']) {
            $this->response->error('Vehicle Validation failed');
        }
        $this->vehicle->updateById(['user_id'=> $buyer['id'], ], $vehicle['id']);
        $this->transfer->updateLast(['end_date' => date('Y-m-d H:i:s')], ['vehicle_id' => $vehicle['id']]);
        $this->transfer->update(['is_current' => 0], ['vehicle_id' => $vehicle['id']]);
        $transfer = $this->transfer->insertAndGet([
            'vehicle_id' => $vehicle['id'],
            'seller_id' => $seller['id'],
            'buyer_id' => $buyer['id'],
            'transfer_type' => $post['transfer_type'] ?? '',
            'transfer_amount' => $post['transfer_amount'] ?? '',
            'transfer_note'=> $post['transfer_note'] ?? '',
        ]);
        $this->vehicle->updateById([
            'transfer_status'=> 'pending',
            'transfer_id'=> $transfer['id']
        ], $vehicle['id']);
        $this->response->success();
    }

    
    public function getVehicleDetails($vehicle_id) {
        $vehicle = $this->vehicle->getFullDetails($vehicle_id);
        
        if ($vehicle) {
            $this->response->success($vehicle);
        } 
        $this->response->error('Vehicle not found');
    }

     

      public function checkVIN(){
        if (empty($this->request->post('vin'))) {
            $this->response->error('Unauthorized');
        }
        $vin = $this->request->post('vin');
        $vehicle = $this->vehicle->findByVIN($vin);
        if (!empty($vehicle)) {
            $this->response->success($vehicle);
        } else {
            $this->response->error('VIN not found');
        }
    }

    
    public function searchVehicle() {
        $this->response->roleAuth(['searcher', 'admin']);
        $user_role = $this->auth->getUserRole();
        $search_type = $this->request->jsonPost('type');
        $search_term = $this->request->jsonPost('term');
        
        if (empty($search_type) || empty($search_term)) {
            $this->response->error('Search type and term are required');
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
            $this->response->success( $vehicle_details);
        } else {
            $this->response->error('Vehicle not found');
        }
    }

    public function searchUser(){
        if(isset($_GET['q']) && !empty($_GET['q'])) {
            $identifier = $_GET['q'] ?? '';
            $users = $this->user->searchByIdentifier($identifier);
            if (empty($identifier) || empty($users)) {
                $this->response->error('No User Found');
            }
            $this->response->success($users);
        }
        $value = $this->request->post('value');
            $field = match (true) {
                $this->validator->validateEmail($value) => 'email',
                $this->validator->validateNIN($value) => 'nin',
                $this->validator->validatePhone($value) => 'phone',
                default => 'phone',
            };
        $user = $this->user->find([$field=>$value]);
        if(empty($user)){
            $this->response->error('User not found');
        }
        if($user['id'] === $this->auth->getUserId()){
            $this->response->error('You are this User');
        }
        $this->response->success($user);
    }

      public function getUserProfile($identifier) {
        $this->response->logInAuth();
        $this->response->roleAuth(['searcher', 'admin']);

        $user = $this->user->findByIdentifier($identifier);
        if ($user) {
            // Get user's vehicles and contact details
            $vehicles = $this->user->getUserVehiclesWithHistory($user['id']);
            $plate_numbers = $this->user->getUserPlateNumbers($user['id']);
            
            $profile_data = [
                'user' => [
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'nin' => $user['nin'],
                    'role' => $user['role'],
                    'profile_picture' => $user['profile_picture']
                ],
                'vehicles' => $vehicles,
                'plate_numbers' => $plate_numbers
            ];
            $this->response->success($profile_data);
        } else {
            $this->response->error('User not found');
        }
        exit;
    }

      public function updateUser(){
        $this->response->roleAuth(['admin']);
        $post = $this->request->jsonPost();
         if (empty($post['email'])) {
            $this->response->error('Unauthorized');
        }
        $data = [
            'name' => $post['name'],
            'role' => $post['role'],
            'is_banned' => empty($post['is_banned']) ? 0 : 1
        ]; 
        $user =  $this->user->findByEmail($post['email']);
       
        if ($this->user->updateUser($user['id'], $data)) {
            $this->response->success($user);
        } else {
            $this->response->error('User not found');
        }
    }

     public function deleteUser() {
        $this->response->roleAuth(['admin']);
        $post = $this->request->jsonPost();
        if (empty($post['email'])) {
            $this->response->error('Unauthorized');
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
            $this->response->success($user);
        } else {
            $this->response->error('User not found or could not be deleted');
        }
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
            $this->response->success($data);    
        } else {
            $this->response->error('User not found');
        }
    }

      public function toggleUserBan() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $this->request->post('user_id') ?? '';
            $action = $this->request->post('action') ?? ''; // ban or unban
            
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
        $this->response->success($stats);
    }

    
    public function getSearchHistory() {
        $user_id = $this->auth->getUserId();
        $this->response->roleAuth(['searcher', 'admin']);

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
        $this->response->success($response);
    }

    public function sendPasswordReset($email){
        $this->response->roleAuth(['admin']);
        $user = $this->user->findByEmail($email);

        if ($user) {
            do {
                $reset_token = bin2hex(random_bytes(32));
            } while ($this->user->countRow(['token'=> $reset_token], 'password_resets'));
            
            if ($this->user->createPasswordReset($user['id'], $reset_token)) {
                sendPasswordResetEmail($email, $reset_token);
                $this->response->success(['success'=> true]);
            }
        }
        $this->response->error('Could not send user reset email');
    }

    public function sendVerification($email){
        $this->response->roleAuth(['admin']);
        $user = $this->user->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->user->updateById([
                'verification_token'=> $token,
                'email_verified' => 0,
                'email_verified_at' => null
            ], $user['id']);
            sendVerificationEmail($email, $token);
            $this->response->success(['success'=> true]);
        }
        $this->response->error('Could not send user reset email');
    }


    public function verifyEmail($email){
        $this->response->roleAuth(['admin']);
        $user = $this->user->findByEmail($email);

        if ($user) {
            $this->user->updateById([
                'email_verified' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'verification_token' => null
            ], $user['id']);
            $this->response->success(['success'=> true]);
        }
        $this->response->error('Could not send user reset email');
    }


}
?>