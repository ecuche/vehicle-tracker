<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

use App\Core\Auth;
use PDO;

class SearchHistory extends Model {

    private $auth;

    protected $table = "search_history";

   

    public function userSearchHistoryPagination($offset = 0, $limit = 5, $user_id = null) {
        $user_id ??= $_SESSION['user_id'];
        $stmt = $this->db->prepare("
            SELECT 
                sh.*,
                v.*
            FROM search_history sh
            JOIN vehicles v ON sh.vehicle_id = v.id
            WHERE sh.user_id = ? AND sh.deleted_at IS NULL 
            ORDER BY sh.id DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([$user_id, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function userSearchCount($user_id = null) {
        $user_id ??= $_SESSION["user_id"];
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM search_history
            WHERE user_id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
}
?>