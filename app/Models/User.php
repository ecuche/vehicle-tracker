<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use PDO;

class User extends Model {

    protected $table = "users";

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, phone, nin, password, role, verification_token, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['nin'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
            $data['verification_token']
        ]);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByNIN($nin) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE nin = ? AND deleted_at IS NULL");
        $stmt->execute([$nin]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByPhone($phone) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE phone = ? AND deleted_at IS NULL");
        $stmt->execute([$phone]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByIdentifier($identifier) {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE (email = ? OR phone = ? OR nin = ?) 
            AND deleted_at IS NULL
        ");
        $stmt->execute([$identifier, $identifier, $identifier]);
        return $stmt->fetch();
    }

    public function updateProfilePicture($id, $profile_picture) {
        $stmt = $this->db->prepare("
            UPDATE users SET profile_picture = ?, updated_at = NOW() 
            WHERE id = ? AND deleted_at IS NULL
        ");
        return $stmt->execute([$profile_picture, $id]);
    }

    public function updatePassword($id, $password) {
        $stmt = $this->db->prepare("
            UPDATE users SET password = ?, updated_at = NOW() 
            WHERE id = ? AND deleted_at IS NULL
        ");
        return $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public function updateUser($id, $data) {
        $fields = [];
        $values = [];
        foreach ($data as $column => $value) {
            $fields[] = "$column = ?";
            $values[] = $value;
        }
        $fields[] = "updated_at = NOW()";
        $sql = "UPDATE users SET " . implode(", ", $fields) . " 
                WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $values[] = $id;
        return $stmt->execute($values);
    }


    public function verifyEmail($token) {
        $stmt = $this->db->prepare("
            UPDATE users SET email_verified = 1, email_verified_at = NOW(), verification_token = NULL, updated_at = NOW() 
            WHERE verification_token = ? AND deleted_at IS NULL
        ");
        return $stmt->execute([$token]);
    }

    public function createPasswordReset($user_id, $token, $expires_at) {
        // Delete any existing reset tokens for this user
        $this->deleteUserPasswordResets($user_id);

        $stmt = $this->db->prepare("
            INSERT INTO password_resets (user_id, token, expires_at, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$user_id, $token, $expires_at]);
    }

    public function findPasswordReset($token) {
        $stmt = $this->db->prepare("
            SELECT pr.*, u.email 
            FROM password_resets pr 
            JOIN users u ON pr.user_id = u.id 
            WHERE pr.token = ? AND u.deleted_at IS NULL
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function deletePasswordReset($token) {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE token = ?");
        return $stmt->execute([$token]);
    }

    private function deleteUserPasswordResets($user_id) {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE user_id = ?");
        return $stmt->execute([$user_id]);
    }

   public function getUsersPaginated($page = 1, $per_page = 10, $search = '') {
    $offset = ($page - 1) * $per_page;
    
    // Base query with LEFT JOIN to vehicles
    $sql = "SELECT u.*, COUNT(v.id) AS vehicle_count
            FROM users u
            LEFT JOIN vehicles v ON v.user_id = u.id AND v.deleted_at IS NULL
            WHERE u.deleted_at IS NULL";
    
    $params = [];
    
    // Search filter
    if (!empty($search)) {
        $sql .= " AND (u.email LIKE ? OR u.phone LIKE ? OR u.nin LIKE ?)";
        $search_term = "%$search%";
        $params = [$search_term, $search_term, $search_term];
    }
    
    // Group by user to make COUNT work
    $sql .= " GROUP BY u.id
              ORDER BY u.created_at DESC
              LIMIT ? OFFSET ?";
    
    $params[] = $per_page;
    $params[] = $offset;
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function getUsersCount($search = '') {
        $sql = "SELECT COUNT(*) as count FROM users WHERE deleted_at IS NULL";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (email LIKE ? OR phone LIKE ? OR nin LIKE ?)";
            $search_term = "%$search%";
            $params = [$search_term, $search_term, $search_term];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()->count;
    }

    public function searchByIdentifier($identifier) {
        $stmt = $this->db->prepare("
            SELECT id, email, phone, nin, role, profile_picture 
            FROM users 
            WHERE (email LIKE ? OR phone LIKE ? OR nin LIKE ?) 
            AND deleted_at IS NULL 
            LIMIT 10
        ");
        $search_term = "%$identifier%";
        $stmt->execute([$search_term, $search_term, $search_term]);
        return $stmt->fetchAll();
    }

    public function updateRole($user_id, $role) {
        $stmt = $this->db->prepare("
            UPDATE users SET role = ?, updated_at = NOW() 
            WHERE id = ? AND deleted_at IS NULL
        ");
        return $stmt->execute([$role, $user_id]);
    }

    public function ban($user_id) {
        $stmt = $this->db->prepare("
            UPDATE users SET is_banned = 1, updated_at = NOW() 
            WHERE id = ? AND deleted_at IS NULL
        ");
        return $stmt->execute([$user_id]);
    }

    public function unban($user_id) {
        $stmt = $this->db->prepare("
            UPDATE users SET is_banned = 0, updated_at = NOW() 
            WHERE id = ? AND deleted_at IS NULL
        ");
        return $stmt->execute([$user_id]);
    }

    public function getTotalCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE deleted_at IS NULL");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getBannedCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE is_banned = 1 AND deleted_at IS NULL");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getAdminCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getDriverCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'driver'");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getSearcherCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'searcher'");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getUserVehiclesWithHistory($user_id) {
        $stmt = $this->db->prepare("
            SELECT 
                v.*,
                vm.make,
                vm.model,
                pn.plate_number as current_plate,
                CASE 
                    WHEN v.user_id = ? THEN 'owned'
                    ELSE 'sold'
                END as ownership_status
            FROM vehicles v
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1
            WHERE v.id IN (
                SELECT vehicle_id FROM vehicles WHERE user_id = ? AND deleted_at IS NULL
                UNION
                SELECT vehicle_id FROM ownership_transfers WHERE from_user_id = ? AND status = 'accepted' AND deleted_at IS NULL
            )
            AND v.deleted_at IS NULL
            ORDER BY v.created_at DESC
        ");
        $stmt->execute([$user_id, $user_id, $user_id]);
        return $stmt->fetchAll();
    }

    public function getUserPlateNumbers($user_id) {
        $stmt = $this->db->prepare("
            SELECT DISTINCT pn.plate_number, pn.assigned_at
            FROM plate_numbers pn
            JOIN vehicles v ON pn.vehicle_id = v.id
            WHERE v.user_id = ? AND pn.deleted_at IS NULL AND v.deleted_at IS NULL
            ORDER BY pn.assigned_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function deleteUser($user_id) {
        $stmt = $this->db->prepare("
            DELETE FROM  users WHERE id = ? 
        ");
        return $stmt->execute([$user_id]);
    }

    public function updateLastLogin($user_id) {
        $stmt = $this->db->prepare("
            UPDATE users SET last_login_at = NOW() WHERE id = ?
        ");
        return $stmt->execute([$user_id]);  
    }
}
?>