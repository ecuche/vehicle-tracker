<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class PlateNumber extends Model {

    protected $table = "plate_numbers";

    public function create($vehicle_id, $plate_number) {
        $stmt = $this->db->prepare("
            INSERT INTO plate_numbers (vehicle_id, plate_number, is_current, assigned_at) 
            VALUES (?, ?, 1, NOW())
        ");
        return $stmt->execute([$vehicle_id, $plate_number]);
    }

    public function findByPlateNumber($plate_number) {
        $stmt = $this->db->prepare("
            SELECT pn.*, v.vin, v.user_id, vm.make, vm.model
            FROM plate_numbers pn
            LEFT JOIN vehicles v ON pn.vehicle_id = v.id
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            WHERE pn.plate_number = ? AND pn.deleted_at IS NULL AND v.deleted_at IS NULL
        ");
        $stmt->execute([$plate_number]);
        return $stmt->fetch();
    }

    public function getCurrentPlateNumber($vehicle_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM plate_numbers 
            WHERE vehicle_id = ? AND is_current = 1 AND deleted_at IS NULL
        ");
        $stmt->execute([$vehicle_id]);
        return $stmt->fetch();
    }

    public function getPlateHistory($vehicle_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM plate_numbers 
            WHERE vehicle_id = ? AND deleted_at IS NULL 
            ORDER BY assigned_at DESC
        ");
        $stmt->execute([$vehicle_id]);
        return $stmt->fetchAll();
    }

    public function updateCurrentPlate($vehicle_id, $new_plate_number) {
        $this->db->beginTransaction();

        try {
            // Mark current plate as not current
            $update_stmt = $this->db->prepare("
                UPDATE plate_numbers SET is_current = 0, updated_at = NOW() 
                WHERE vehicle_id = ? AND is_current = 1 AND deleted_at IS NULL
            ");
            $update_stmt->execute([$vehicle_id]);

            // Insert new plate number as current
            $insert_stmt = $this->db->prepare("
                INSERT INTO plate_numbers (vehicle_id, plate_number, is_current, assigned_at) 
                VALUES (?, ?, 1, NOW())
            ");
            $insert_stmt->execute([$vehicle_id, $new_plate_number]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function isPlateNumberUnique($plate_number, $exclude_vehicle_id = null) {
        $sql = "
            SELECT COUNT(*) as count 
            FROM plate_numbers 
            WHERE plate_number = ? AND is_current = 1 AND deleted_at IS NULL
        ";
        
        $params = [$plate_number];
        
        if ($exclude_vehicle_id) {
            $sql .= " AND vehicle_id != ?";
            $params[] = $exclude_vehicle_id;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()->count == 0;
    }

    public function getPlateNumbersByUser($user_id) {
        $stmt = $this->db->prepare("
            SELECT DISTINCT pn.plate_number, pn.assigned_at, v.vin, vm.make, vm.model
            FROM plate_numbers pn
            LEFT JOIN vehicles v ON pn.vehicle_id = v.id
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            WHERE v.user_id = ? AND pn.deleted_at IS NULL AND v.deleted_at IS NULL
            ORDER BY pn.assigned_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function searchPlateNumbers($query, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT pn.*, v.vin, vm.make, vm.model, u.email as owner_email
            FROM plate_numbers pn
            LEFT JOIN vehicles v ON pn.vehicle_id = v.id
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN users u ON v.user_id = u.id
            WHERE pn.plate_number LIKE ? 
            AND pn.deleted_at IS NULL 
            AND v.deleted_at IS NULL
            ORDER BY pn.assigned_at DESC
            LIMIT ?
        ");
        $search_term = "%$query%";
        $stmt->execute([$search_term, $limit]);
        return $stmt->fetchAll();
    }

    public function softDeleteUser($plate_id) {
        return $this->softDeleteById($plate_id);
    }

    public function getTotalCount() {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM plate_numbers 
            WHERE deleted_at IS NULL
        ");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getRecentAssignments($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT 
                pn.*,
                v.vin,
                vm.make,
                vm.model,
                u.email as owner_email
            FROM plate_numbers pn
            LEFT JOIN vehicles v ON pn.vehicle_id = v.id
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN users u ON v.user_id = u.id
            WHERE pn.deleted_at IS NULL AND v.deleted_at IS NULL
            ORDER BY pn.assigned_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
?>