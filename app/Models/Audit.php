<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Audit extends Model {

    protected $table = "audit_trails";

    public function log($user_id, $action, $table_name = null, $record_id = null, $old_values = null, $new_values = null) {
        $stmt = $this->db->prepare("
            INSERT INTO audit_trails (
                user_id, action, table_name, record_id, old_values, new_values, 
                ip_address, user_agent, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        // Convert arrays to JSON if provided
        $old_values_json = $old_values ? json_encode($old_values) : null;
        $new_values_json = $new_values ? json_encode($new_values) : null;

        return $stmt->execute([
            $user_id,
            $action,
            $table_name,
            $record_id,
            $old_values_json,
            $new_values_json,
            $ip_address,
            $user_agent
        ]);
    }

    public function getAuditLogsPaginated($page = 1, $per_page = 10, $search = '', $action = '', $start_date = '', $end_date = '') {
        $offset = ($page - 1) * $per_page;
        
        $sql = "
            SELECT 
                at.*,
                u.email as user_email,
                u.phone as user_phone
            FROM audit_trails at
            LEFT JOIN users u ON at.user_id = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (
                u.email LIKE ? OR 
                u.phone LIKE ? OR 
                at.table_name LIKE ? OR
                at.action LIKE ?
            )";
            $search_term = "%$search%";
            $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
        }
        
        if (!empty($action)) {
            $sql .= " AND at.action = ?";
            $params[] = $action;
        }
        
        if (!empty($start_date)) {
            $sql .= " AND DATE(at.created_at) >= ?";
            $params[] = $start_date;
        }
        
        if (!empty($end_date)) {
            $sql .= " AND DATE(at.created_at) <= ?";
            $params[] = $end_date;
        }
        
        $sql .= " ORDER BY at.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $per_page;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTodayLogsCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM audit_trails WHERE DATE(created_at) = CURDATE()");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getAdminLogsCount() {
        $stmt = $this->db->prepare(" SELECT COUNT(DISTINCT a.user_id) AS count
                                            FROM audit_trails a
                                            JOIN users u ON a.user_id = u.id
                                            WHERE u.role = 'admin'
                                            AND DATE(a.created_at) = CURDATE()");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getDriverLogsCount() {
        $stmt = $this->db->prepare(" SELECT COUNT(DISTINCT a.user_id) AS count
                                            FROM audit_trails a
                                            JOIN users u ON a.user_id = u.id
                                            WHERE u.role = 'driver'
                                            AND DATE(a.created_at) = CURDATE()");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getSearcherLogsCount() {
        $stmt = $this->db->prepare(" SELECT COUNT(DISTINCT a.user_id) AS count
                                            FROM audit_trails a
                                            JOIN users u ON a.user_id = u.id
                                            WHERE u.role = 'searcher'
                                            AND DATE(a.created_at) = CURDATE()");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getAuditLogsCount($search = '', $action = '', $start_date = '', $end_date = '') {
        $sql = "
            SELECT COUNT(*) as count 
            FROM audit_trails at
            LEFT JOIN users u ON at.user_id = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (
                u.email LIKE ? OR 
                u.phone LIKE ? OR 
                at.table_name LIKE ? OR
                at.action LIKE ?
            )";
            $search_term = "%$search%";
            $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
        }
        
        if (!empty($action)) {
            $sql .= " AND at.action = ?";
            $params[] = $action;
        }
        
        if (!empty($start_date)) {
            $sql .= " AND DATE(at.created_at) >= ?";
            $params[] = $start_date;
        }
        
        if (!empty($end_date)) {
            $sql .= " AND DATE(at.created_at) <= ?";
            $params[] = $end_date;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()->count;
    }

    public function getAllAuditLogs($search = '', $action = '', $start_date = '', $end_date = '') {
        $sql = "
            SELECT 
                at.*,
                u.email as user_email,
                u.phone as user_phone
            FROM audit_trails at
            LEFT JOIN users u ON at.user_id = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (
                u.email LIKE ? OR 
                u.phone LIKE ? OR 
                at.table_name LIKE ? OR
                at.action LIKE ?
            )";
            $search_term = "%$search%";
            $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
        }
        
        if (!empty($action)) {
            $sql .= " AND at.action = ?";
            $params[] = $action;
        }
        
        if (!empty($start_date)) {
            $sql .= " AND DATE(at.created_at) >= ?";
            $params[] = $start_date;
        }
        
        if (!empty($end_date)) {
            $sql .= " AND DATE(at.created_at) <= ?";
            $params[] = $end_date;
        }
        
        $sql .= " ORDER BY at.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUserAuditLogs($user_id, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT * FROM audit_trails 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$user_id, $limit]);
        return $stmt->fetchAll();
    }

    public function getActions() {
        $stmt = $this->db->prepare("
            SELECT DISTINCT action 
            FROM audit_trails 
            ORDER BY action
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function cleanupOldLogs($days = 90) {
        $stmt = $this->db->prepare("
            DELETE FROM audit_trails 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        return $stmt->execute([$days]);
    }

    public function clearAllLogs() {
        $stmt = $this->db->prepare("TRUNCATE TABLE audit_trails");
        return $stmt->execute();
    }

    public function getDistinctUsersCount() {
        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT user_id) as count FROM audit_trails");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getAuditLogsByUserId($user_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM audit_trails 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
}
?>