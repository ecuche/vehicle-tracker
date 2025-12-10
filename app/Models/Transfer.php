<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Transfer extends Model{

    protected $table = "ownership_transfers";

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO ownership_transfers (vehicle_id, seller_id, buyer_id, status, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([
            $data['vehicle_id'],
            $data['seller_id'],
            $data['buyer_id'],
            $data['status']
        ]);
    }

    public function findTransferById($id) {
        $stmt = $this->db->prepare("
            SELECT ot.*, v.vin, v.current_status, u_from.email as from_email, u_to.email as to_email
            FROM ownership_transfers ot
            LEFT JOIN vehicles v ON ot.vehicle_id = v.id
            LEFT JOIN users u_from ON ot.seller_id = u_from.id
            LEFT JOIN users u_to ON ot.buyer_id = u_to.id
            WHERE ot.id = ? AND ot.deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
            LEFT JOIN users u_from ON ot.seller_id = u_from.id
            WHERE ot.buyer_id = ? AND ot.status = 'pending' 
            AND ot.deleted_at IS NULL
            ORDER BY ot.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            LEFT JOIN users u_from ON ot.seller_id = u_from.id
            LEFT JOIN users u_to ON ot.buyer_id = u_to.id
            WHERE (ot.seller_id = ? OR ot.buyer_id = ?) 
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
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            $sql .= " AND buyer_id = ?";
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
                AND ot.buyer_id = ?
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
                AND ot.seller_id = ?
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
            $sql .= " AND seller_id = ?";
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            LEFT JOIN users u_from ON ot.seller_id = u_from.id
            LEFT JOIN users u_to ON ot.buyer_id = u_to.id
            WHERE ot.vehicle_id = ? AND ot.deleted_at IS NULL
            ORDER BY ot.created_at DESC
        ");
        $stmt->execute([$vehicle_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserVehicleHistoryPagination($user_id, $offset = 0, $limit =10) {
        $stmt = $this->db->prepare("
        SELECT
            ot.*,
            v.*,
            vm.*
            FROM ownership_transfers ot
            JOIN vehicles v ON ot.vehicle_id = v.id
            JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            WHERE ot.buyer_id = ? AND ot.deleted_at IS NULL 
            ORDER BY ot.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$user_id, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function getVehicleHistoryAndOwners(int $vehicle_id) {
        $vehicle_stmt = $this->db->prepare("
            SELECT 
                ot.*,
                u.*
            FROM ownership_transfers ot
            JOIN users u ON ot.buyer_id = u.id
            WHERE ot.vehicle_id = ? AND ot.deleted_at IS NULL 
            ORDER BY ot.id DESC
        ");
        $vehicle_stmt->execute([$vehicle_id]);
        return $vehicle_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIncomingTransfersPagination($offset = 0, $limit = 10, $user_id = null) {
        $user_id ??= $_SESSION['user_id'];
        $stmt = $this->db->prepare("
             SELECT 
                ot.*,
                u.*,
                v.*,
                m.*,
                pn.*
            FROM ownership_transfers ot
            JOIN users u ON ot.seller_id = u.id
            JOIN vehicles v ON ot.vehicle_id = v.id
            JOIN vehicle_models m ON v.vehicle_model_id = m.id
            JOIN plate_numbers pn ON v.current_plate_id = pn.id
            WHERE ot.buyer_id = ? AND status = 'pending' AND ot.deleted_at IS NULL 
            ORDER BY ot.id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$user_id, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOutgoingTransfersPagination($offset = 0, $limit = 10, $user_id = null) {
        $user_id ??= $_SESSION['user_id'];
        $stmt = $this->db->prepare("
             SELECT 
                ot.*,
                u.*,
                v.*,
                m.*,
                pn.*
            FROM ownership_transfers ot
            JOIN users u ON ot.seller_id = u.id
            JOIN vehicles v ON ot.vehicle_id = v.id
            JOIN vehicle_models m ON v.vehicle_model_id = m.id
            JOIN plate_numbers pn ON v.current_plate_id = pn.id
            WHERE ot.seller_id = ? AND status = 'pending' AND ot.deleted_at IS NULL 
            ORDER BY ot.id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$user_id, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompletedTransfersPagination($offset = 0, $limit = 10, $user_id = null) {
        $user_id ??= $_SESSION['user_id'];
        $stmt = $this->db->prepare("
             SELECT 
                ot.*,
                us.name as seller_name, us.email as seller_email, us.phone as seller_phone, us.nin as seller_nin,
                ub.name as buyer_name, ub.email as buyer_email, ub.phone as buyer_phone, ub.nin as buyer_nin,
                v.*,
                m.*,
                pn.*
            FROM ownership_transfers ot
            JOIN users us ON ot.seller_id = us.id
            JOIN users ub ON ot.buyer_id = ub.id
            JOIN vehicles v ON ot.vehicle_id = v.id
            JOIN vehicle_models m ON v.vehicle_model_id = m.id
            JOIN plate_numbers pn ON v.current_plate_id = pn.id
            WHERE (ot.seller_id = ? OR ot.buyer_id = ?) AND status = 'completed' AND ot.deleted_at IS NULL 
            ORDER BY ot.id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$user_id, $user_id, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function getCompletedTransfersByDate($start_date, $end_date, $user_id = null) {
        $user_id ??= $_SESSION['user_id'];
        $stmt = $this->db->prepare("
            SELECT 
                ot.*,
                us.name as seller_name, us.email as seller_email, us.phone as seller_phone, us.nin as seller_nin,
                ub.name as buyer_name, ub.email as buyer_email, ub.phone as buyer_phone, ub.nin as buyer_nin,
                v.*,
                m.*,
                pn.*
            FROM ownership_transfers ot
            JOIN users us ON ot.seller_id = us.id
            JOIN users ub ON ot.buyer_id = ub.id
            JOIN vehicles v ON ot.vehicle_id = v.id
            JOIN vehicle_models m ON v.vehicle_model_id = m.id
            JOIN plate_numbers pn ON v.current_plate_id = pn.id
            WHERE (ot.seller_id = ? OR ot.buyer_id = ?)
                AND status = 'completed'
                AND ot.deleted_at IS NULL
                AND ot.created_at BETWEEN ? AND ?
            ORDER BY ot.id DESC
        ");
        $stmt->execute([$user_id, $user_id, $start_date, $end_date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function countIncomingTransfers($user_id = null) {
        $user_id ??= $_SESSION['user_id'];
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM ownership_transfers ot
            WHERE ot.buyer_id = ? AND status = 'pending' AND ot.deleted_at IS NULL 
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch()->count;
    }

    public function countOutgoingTransfers($user_id = null) {
        $user_id ??= $_SESSION["user_id"];
        $stmt = $this->db->prepare("
        SELECT COUNT(*) as count 
            FROM ownership_transfers ot
            WHERE ot.seller_id = ? AND status = 'pending' 
            AND ot.deleted_at IS NULL 
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch()->count;
    }

     public function countCompletedTransfers($user_id = null) {
        $user_id ??= $_SESSION["user_id"];
        $stmt = $this->db->prepare("
        SELECT COUNT(*) as count 
            FROM ownership_transfers ot
            WHERE ot.buyer_id = ?
            AND status = 'completed' AND ot.deleted_at IS NULL 
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch()->count;
    }

    public function countRejectedTransfers($user_id = null) {
        $user_id ??= $_SESSION["user_id"];
        $stmt = $this->db->prepare("
         SELECT COUNT(*) as count 
            FROM ownership_transfers ot
            WHERE (ot.seller_id = ? OR ot.buyer_id = ?) 
            AND status = 'rejected' AND ot.deleted_at IS NULL
        ");
        $stmt->execute([$user_id, $user_id]);   
        return $stmt->fetch()->count;
    }

    public function countTotalTransfers($user_id = null) {
        $user_id ??= $_SESSION["user_id"];
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM ownership_transfers ot
            WHERE (ot.seller_id = ? OR ot.buyer_id = ?) 
             AND ot.deleted_at IS NULL
        ");
        $stmt->execute([$user_id, $user_id]);
        return $stmt->fetch()->count;
    }

    public function stats($user_id = null) {
        $user_id ??= $_SESSION["user_id"];
        $stats = [
                'total'=> $this->countTotalTransfers($user_id),
                'pending' => $this->countIncomingTransfers($user_id),
                'accepted' => $this->countCompletedTransfers($user_id),
                'rejected' => $this->countRejectedTransfers($user_id)
        ];
        return $stats;
    }
}
?>