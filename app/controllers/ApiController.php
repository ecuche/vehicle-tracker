<?php
namespace App\Controllers;
use App\Core\Controller;

class ApiController extends Controller {

    public function __construct() {
        parent::__construct();
        
        if (!$this->auth->isAdmin()) {
            echo json_encode(['error' => 'access denied']);
            exit;
        }
    }

    public function getUser(){
        $field = $_POST['field'];
        $value = $_POST['value'];
        $user = $this->user->findbyColumn($field, $value);
        if(!empty($field && !empty($value) && !empty($user))){
            echo json_encode($user);
        }
        exit;
    }

    public function updateVehicle(){
        $id = $_POST['id'];
        $vin = $_POST['vin'];
        $current_plate = $_POST['current_plate'];
        $vehicle_model_id  = $_POST['vehicle_model_id'];
        $year = $_POST['year'];
        $current_status = $_POST['current_status'];
        $status_reason = $_POST['status_reason'];
        $new_owner_email = $_POST['new_owner_email'];
        $current_owner_email = $_POST['current_owner_email'];

        $vehicle = $this->vehicle->findbyId( $id);
        $plate = $this->plateNumber->findbyColumn(['plate_number'], $current_plate);
        if(!empty($new_owner_email)){
            $user = $this->user->findByEmail($new_owner_email);
        }else{
            $user = $this->user->findByEmail($current_owner_email);
        }

        if(!empty($plate)){
            if($plate['id'] !== $vehicle['current_plate_id']){
                echo json_encode(['error'=>'Plate Number is taken']);
                exit;
            }
        }else{
            $plate = $this->plateNumber->insertAndGet([
                'vehicle_id'=>$vehicle['id'],
                'plate_number' => $current_plate
            ]);
        }
        if(!empty($current_status) && empty($status_reason)){
            echo json_encode(['error'=>'Change of status Reason is required']);
            exit;
        }else{
            $status_id = $this->vehicleStatusHistory->insertAndGetId([
                'vehicle_id'=> $vehicle['id'],
                'status'=> $current_status,
                'status_reason'=>$status_reason
            ]);
        }

        
        

    }
}
?>