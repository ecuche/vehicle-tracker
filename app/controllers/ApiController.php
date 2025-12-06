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
            exit;
        }
        exit;
    }

    public function updateVehicle(){
        $id = $_POST['id'];
        $vin = $_POST['vin'];
        $vehicle_color = $_POST['vehicle_color'];
        $current_plate = $_POST['current_plate'];
        $vehicle_model_id  = $_POST['vehicle_model_id'];
        $year = $_POST['year'];
        $current_status = $_POST['current_status'];
        $status_reason = $_POST['status_reason'];
        $new_owner_email = $_POST['new_owner_email'];
        $current_owner_email = $_POST['current_owner_email'];
        $vehicle = $this->vehicle->findbyId($id);
        $user = !empty($new_owner_email) ? 
            $this->user->findByEmail($new_owner_email) : 
            $this->user->findByEmail($current_owner_email);      
        if(!empty($vehicle)){
            if(!$this->validator::validateVIN($vin)){
                echo json_encode(['error'=>'provide correct VIN format']);
                exit;
            }   
            if(empty($user)){
                echo json_encode(['error'=>'Driver could not be found']);
                    exit;
            }   
            if(empty($current_plate)){
                echo json_encode(['error'=>'Plate Number is required']);
                exit;
            }
            $plate = $this->plateNumber->findbyColumn('plate_number', $current_plate);
            if(!empty($plate)){
                if($plate['id'] !== $vehicle['current_plate_id']){
                    echo json_encode(['error'=>'Plate Number is taken']);
                    exit;
                }
            }
            if((($current_status === "") && ($status_reason !== "")) || 
                (($current_status !== "") && ($status_reason === "")))
            {
                echo json_encode(['error'=>'Change of status/Reason is required']);
                exit;
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
            $data['success'] = 'Vehicle updated Successfully';
            echo json_encode($data);
            exit;

        }else{
            echo json_encode(['error'=>'Could not find Vehicle']);
            exit;

        }
    }
}
?>