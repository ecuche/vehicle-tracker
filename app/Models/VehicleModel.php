<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class VehicleModel extends Model {

    protected $table = "vehicle_models";

    public function getAllMake() {
        $stmt = $this->db->prepare("
            SELECT DISTINCT make FROM vehicle_models 
            WHERE deleted_at IS NULL 
            ORDER BY make
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllModel() {
        $stmt = $this->db->prepare("
            SELECT id, model FROM vehicle_models 
            WHERE deleted_at IS NULL 
            ORDER BY model
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByMakeAndModel($make, $model) {
        $stmt = $this->db->prepare("
            SELECT * FROM vehicle_models 
            WHERE make = ? AND model = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$make, $model]);
        return $stmt->fetch();
    }

    public function getModelsByMake($make) {
        $stmt = $this->db->prepare("
            SELECT id, model 
            FROM vehicle_models 
            WHERE make = ? AND deleted_at IS NULL 
            ORDER BY model
        ");
        $stmt->execute([$make]);
        return $stmt->fetchAll();
    }

     public function getMakeByModel($model) {
        $stmt = $this->db->prepare("
            SELECT id, make 
            FROM vehicle_models 
            WHERE model = ? AND deleted_at IS NULL 
            ORDER BY model
        ");
        $stmt->execute([$model]);
        return $stmt->fetch();
    }

    public function create($make, $model) {
        $stmt = $this->db->prepare("
            INSERT INTO vehicle_models (make, model, created_at) 
            VALUES (?, ?, NOW())
        ");
        return $stmt->execute([$make, $model]);
    }

    public function search($query) {
        $stmt = $this->db->prepare("
            SELECT * FROM vehicle_models 
            WHERE (make LIKE ? OR model LIKE ?) 
            AND deleted_at IS NULL 
            ORDER BY make, model 
            LIMIT 10
        ");
        $search_term = "%$query%";
        $stmt->execute([$search_term, $search_term]);
        return $stmt->fetchAll();
    }
}
?>