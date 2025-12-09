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
            echo json_encode(['error' => 'access denied']);
            exit;
        }
    }

    public function getUser(){
        $field = $this->request->post('field');
        $value = $this->request->post('value');
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

    public function deleteVehicle(){
        $vin = $this->request->post('vin');
        if($this->vehicle->softDelete(['vin'=> $vin])){
            echo json_encode(['success'=> 'vehicle has been deleted']);
        }else{
            echo json_encode(['error'=> 'could not delete Vehicle']);
            exit;
        }
    }

    public function changeCurrentPlate(){
        $this->checkRole();
        $plate_id = $this->request->post('plate_id');
        $plate = $this->plateNumber->findById($plate_id);
        if(empty($plate)){
            echo json_encode(['error'=>'provide correct vehicle Plate Number']);
            exit;
        }
        $vehicle = $this->vehicle->findById($plate['vehicle_id']);
        $this->vehicle->updateById(['current_plate'=> $plate['plate_number'], 'current_plate_id' => $plate['id']], $plate['vehicle_id']);
        $this->plateNumber->update(['is_current'=> 0],  ['vehicle_id' => $vehicle['id']]);
        $this->plateNumber->updateById(['is_current'=> 1], $plate['id']);
        $plate['success'] = true;
        echo json_encode($plate);
        exit;
    }

    public function assignNewPlate($vin){
        $this->checkRole();
        $vehicle = $this->vehicle->findByVIN($vin);
        if(empty($vehicle)){
            echo json_encode(['error'=> 'Vehicle Error']);
            exit;
        }
        $plate = $this->request->post('plate');
        if(!$this->validator->validatePlateNumber($plate)){
            echo json_encode(['error'=> 'Wrong Plate Number Format']);
            exit;
        }
        if($this->plateNumber->exists(['plate_number' => $plate])){
            echo json_encode(['error'=> 'Plate Number is already registered']);
            exit;
        }
        $note = $this->request->post('note');
        $date = $this->request->post('assign_date');
        $this->plateNumber->update(['is_current' => 0], ['vehicle_id'=> $vehicle['id']]);
        $plate = $this->plateNumber->insertAndGet(['vehicle_id'=> $vehicle['id'], 'plate_number'=> strtoupper($plate), 'note'=>$note, 'assigned_at' => $date]);
        $plate['success'] = true;   
        echo json_encode($plate);
        exit;

    } 
}
?>