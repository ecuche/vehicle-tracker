<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Transfer extends Model{

    protected $table = "ownership_transfers";

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO ownership_transfers (vehicle_id, from_user_id, to_user_id, status, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([
            $data['vehicle_id'],
            $data['from_user_id'],
            $data['to_user_id'],
            $data['status']
        ]);
    }

    public function findTransferById($id) {
        $stmt = $this->db->prepare("
            SELECT ot.*, v.vin, v.current_status, u_from.email as from_email, u_to.email as to_email
            FROM ownership_transfers ot
            LEFT JOIN vehicles v ON ot.vehicle_id = v.id
            LEFT JOIN users u_from ON ot.from_user_id = u_from.id
            LEFT JOIN users u_to ON ot.to_user_id = u_to.id
            WHERE ot.id = ? AND ot.deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getPendingTransfers($user_id) {
        $stmt = $this->db->prepare("
            SELECT 
                ot.*,
                v.vin,
                v.year,
                vm.make,
                vm.model,
                pn.plate_number as current_plate_number,
                u_from.email as from_user_email,
                u_from.phone as from_user_phone
            FROM ownership_transfers ot
            LEFT JOIN vehicles v ON ot.vehicle_id = v.id
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1
            LEFT JOIN users u_from ON ot.from_user_id = u_from.id
            WHERE ot.to_user_id = ? AND ot.status = 'pending' 
            AND ot.deleted_at IS NULL
            ORDER BY ot.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getUserTransfers($user_id, $limit = null) {
        $sql = "
            SELECT 
                ot.*,
                v.vin,
                vm.make,
                vm.model,
                pn.plate_number as current_plate_number,
                u_from.email as from_user_email,
                u_to.email as to_user_email
            FROM ownership_transfers ot
            LEFT JOIN vehicles v ON ot.vehicle_id = v.id
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1
            LEFT JOIN users u_from ON ot.from_user_id = u_from.id
            LEFT JOIN users u_to ON ot.to_user_id = u_to.id
            WHERE (ot.from_user_id = ? OR ot.to_user_id = ?) 
            AND ot.deleted_at IS NULL
            ORDER BY ot.created_at DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id, $user_id, $limit]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id, $user_id]);
        }
        
        return $stmt->fetchAll();
    }

    public function updateStatus($transfer_id, $status) {
        $stmt = $this->db->prepare("
            UPDATE ownership_transfers SET status = ?, updated_at = NOW() 
            WHERE id = ? AND deleted_at IS NULL
        ");
        return $stmt->execute([$status, $transfer_id]);
    }

    public function getTotalCount() {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM ownership_transfers 
            WHERE deleted_at IS NULL
        ");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getPendingCount($user_id = null) {
        $sql = "SELECT COUNT(*) as count FROM ownership_transfers WHERE status = 'pending' AND deleted_at IS NULL";
        
        if ($user_id) {
            $sql .= " AND to_user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetch()->count;
    }

    public function getFailedCount(int $user_id){

        $sql = "SELECT COUNT(*) AS count
                FROM ownership_transfers ot
                JOIN vehicles v ON ot.vehicle_id = v.id
                WHERE v.user_id = ?
                AND ot.status = 'rejected'
                AND ot.deleted_at IS NULL
                AND v.deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch()->count;
    }
    
    public function getIncomingCount(int $user_id){

        $sql = "SELECT COUNT(*) AS count
                FROM ownership_transfers ot
                JOIN vehicles v ON ot.vehicle_id = v.id
                WHERE v.user_id = ?
                AND ot.status = 'pending'
                AND ot.to_user_id = ?
                AND ot.deleted_at IS NULL
                AND v.deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $user_id]);
        return $stmt->fetch()->count;
    }

     public function getOutgoingCount(int $user_id){

        $sql = "SELECT COUNT(*) AS count
                FROM ownership_transfers AS ot
                JOIN vehicles AS v 
                ON ot.vehicle_id = v.id
                WHERE v.user_id = ?
                AND ot.from_user_id = ?
                AND ot.status = 'pending'
                AND ot.deleted_at IS NULL
                AND v.deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $user_id]);
        return $stmt->fetch()->count;
    }

    public function getSoldCount($user_id = null) {
        $sql = "SELECT COUNT(*) as count FROM ownership_transfers WHERE status = 'accepted' AND deleted_at IS NULL";
        
        if ($user_id) {
            $sql .= " AND from_user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetch()->count;
    }

    public function getMonthlyTransfers() {
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM ownership_transfers 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            AND status = 'accepted'
            AND deleted_at IS NULL
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTransferHistory($vehicle_id) {
        $stmt = $this->db->prepare("
            SELECT 
                ot.*,
                u_from.email as from_user_email,
                u_from.phone as from_user_phone,
                u_to.email as to_user_email,
                u_to.phone as to_user_phone
            FROM ownership_transfers ot
            LEFT JOIN users u_from ON ot.from_user_id = u_from.id
            LEFT JOIN users u_to ON ot.to_user_id = u_to.id
            WHERE ot.vehicle_id = ? AND ot.deleted_at IS NULL
            ORDER BY ot.created_at DESC
        ");
        $stmt->execute([$vehicle_id]);
        return $stmt->fetchAll();
    }
}
?>