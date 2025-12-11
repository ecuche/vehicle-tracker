<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Validator;
use App\Core\Database;
use PDO;

class Vehicle extends Model {

    protected $table = "vehicles";
    protected $validation;

    public function __construct(){
        parent::__construct();
        $this->validation = new Validator();
    }

    public function create($data, $images = [], $documents = []) {
        $this->db->beginTransaction();

        try {
            // Insert vehicle
            $stmt = $this->db->prepare("
                INSERT INTO vehicles (vin, user_id, vehicle_model_id, year, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $data['vin'],
                $data['user_id'],
                $data['vehicle_model_id'],
                $data['year']
            ]);

            $vehicle_id = $this->db->lastInsertId();

            // Insert initial plate number
            $plate_stmt = $this->db->prepare("
                INSERT INTO plate_numbers (vehicle_id, plate_number, is_current, assigned_at) 
                VALUES (?, ?, 1, NOW())
            ");
            $plate_stmt->execute([$vehicle_id, $data['plate_number']]);

            // Insert vehicle images
            if (!empty($images)) {
                $image_stmt = $this->db->prepare("
                    INSERT INTO vehicle_images (vehicle_id, image_path, is_primary, created_at) 
                    VALUES (?, ?, ?, NOW())
                ");
                foreach ($images as $index => $image_path) {
                    $image_stmt->execute([
                        $vehicle_id,
                        $image_path,
                        $index === 0 ? 1 : 0 // First image is primary
                    ]);
                }
            }

            // Insert vehicle documents
            if (!empty($documents)) {
                $doc_stmt = $this->db->prepare("
                    INSERT INTO vehicle_documents (vehicle_id, document_type, file_path, created_at) 
                    VALUES (?, ?, ?, NOW())
                ");
                foreach ($documents as $doc_path) {
                    $doc_stmt->execute([
                        $vehicle_id,
                        'registration', // Default document type
                        $doc_path
                    ]);
                }
            }

            $this->db->commit();
            return $vehicle_id;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function findByVIN($vin) {
        $stmt = $this->db->prepare("
            SELECT v.*, vm.make, vm.model, pn.plate_number as current_plate_number
            FROM vehicles v
            JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1
            WHERE v.vin = ? AND v.deleted_at IS NULL
        ");
        $stmt->execute([$vin]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findVehicleById($id) {
        $stmt = $this->db->prepare("
            SELECT v.*, vm.make, vm.model, pn.plate_number as current_plate_number
            FROM vehicles v
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1
            WHERE v.id = ? AND v.deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByPlateNumber($plate_number) {
        $stmt = $this->db->prepare("
            SELECT v.*, vm.make, vm.model, pn.plate_number as current_plate_number
            FROM vehicles v
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1
            WHERE pn.plate_number = ? AND v.deleted_at IS NULL AND pn.deleted_at IS NULL
        ");
        $stmt->execute([$plate_number]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserVehicles($user_id, $current_only = true) {
        $sql = "
            SELECT 
                v.*,
                vm.make,
                vm.model,
                pn.plate_number as current_plate_number,
                COUNT(DISTINCT vi.id) as image_count,
                COUNT(DISTINCT vd.id) as document_count
            FROM vehicles v
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1
            LEFT JOIN vehicle_images vi ON v.id = vi.vehicle_id AND vi.deleted_at IS NULL
            LEFT JOIN vehicle_documents vd ON v.id = vd.vehicle_id AND vd.deleted_at IS NULL
            WHERE v.user_id = ? AND v.deleted_at IS NULL AND transfer_status = 'completed'
        ";

        if ($current_only) {
            $sql .= " AND v.user_id = ?";
        }

        $sql .= " GROUP BY v.id ORDER BY v.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($current_only ? [$user_id, $user_id] : [$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserVehiclesPaginated($user_id, $page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        $stmt = $this->db->prepare("
            SELECT 
                v.*,
                vm.make,
                vm.model,
                pn.plate_number as current_plate_number,
                COUNT(DISTINCT vi.id) as image_count,
                COUNT(DISTINCT vd.id) as document_count
            FROM vehicles v
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1
            LEFT JOIN vehicle_images vi ON v.id = vi.vehicle_id AND vi.deleted_at IS NULL
            LEFT JOIN vehicle_documents vd ON v.id = vd.vehicle_id AND vd.deleted_at IS NULL
            WHERE v.user_id = ? AND v.transfer_status = 'completed' AND v.deleted_at IS NULL
            GROUP BY v.id 
            ORDER BY v.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$user_id, $per_page, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserVehicleCount($user_id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM vehicles 
            WHERE user_id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch()->count;
    }

    public function transferOwnership($vehicle_id, $new_user_id) {
        $this->db->beginTransaction();

        try {
            // Update vehicle owner
            $stmt = $this->db->prepare("
                UPDATE vehicles SET user_id = ?, updated_at = NOW() 
                WHERE id = ? AND deleted_at IS NULL
            ");
            $stmt->execute([$new_user_id, $vehicle_id]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function assignPlateNumber($vehicle_id, $plate_number) {
        $this->db->beginTransaction();

        try {
            // Mark current plate as not current
            $update_stmt = $this->db->prepare("
                UPDATE plate_numbers SET is_current = 0, updated_at = NOW() 
                WHERE vehicle_id = ? AND is_current = 1 AND deleted_at IS NULL
            ");
            $update_stmt->execute([$vehicle_id]);

            // Insert new plate number
            $insert_stmt = $this->db->prepare("
                INSERT INTO plate_numbers (vehicle_id, plate_number, is_current, assigned_at) 
                VALUES (?, ?, 1, NOW())
            ");
            $insert_stmt->execute([$vehicle_id, $plate_number]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getFullDetails($vehicle_id) {
        // Get vehicle basic info
        $add = $this->validation->validateVIN(vin: $vehicle_id) ?  "  WHERE v.vin = ? AND v.deleted_at IS NULL " : " WHERE v.id = ? AND v.deleted_at IS NULL ";
        $sql = "SELECT 
                v.*,
                vm.make,
                vm.model,
                u.email as owner_email,
                u.phone as owner_phone,
                u.nin as owner_nin,
                pn.plate_number as current_plate_number
            FROM vehicles v
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN users u ON v.user_id = u.id
            LEFT JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1 {$add}";

        $vehicle_stmt = $this->db->prepare($sql);
        $vehicle_stmt->execute([$vehicle_id]);
        $vehicle = $vehicle_stmt->fetch(PDO::FETCH_ASSOC);
        $vehicle_id = $vehicle["id"];

        if (!$vehicle) {
            return null;
        }

        // Get ownership history
        $history_stmt = $this->db->prepare("
            SELECT 
                ot.*,
                u_from.email as from_user_email,
                u_to.email as to_user_email,
                u_from.phone as from_user_phone,
                u_to.phone as to_user_phone
            FROM ownership_transfers ot
            LEFT JOIN users u_from ON ot.seller_id = u_from.id
            LEFT JOIN users u_to ON ot.buyer_id = u_to.id
            WHERE ot.vehicle_id = ? AND ot.deleted_at IS NULL
            ORDER BY ot.created_at DESC
        ");
        $history_stmt->execute([$vehicle_id]);
        $ownership_history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get plate number history
        $plate_stmt = $this->db->prepare("
            SELECT * FROM plate_numbers 
            WHERE vehicle_id = ? AND deleted_at IS NULL 
            ORDER BY assigned_at DESC
        ");
        $plate_stmt->execute([$vehicle_id]);
        $plate_history = $plate_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get vehicle images
        $images_stmt = $this->db->prepare("
            SELECT * FROM vehicle_images 
            WHERE vehicle_id = ? AND deleted_at IS NULL 
            ORDER BY is_primary DESC, created_at ASC
        ");
        $images_stmt->execute([$vehicle_id]);
        $images = $images_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get vehicle documents
        $docs_stmt = $this->db->prepare("
            SELECT * FROM vehicle_documents 
            WHERE vehicle_id = ? AND deleted_at IS NULL 
            ORDER BY created_at DESC
        ");
        $docs_stmt->execute([$vehicle_id]);
        $documents = $docs_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get current owner
        $owner_stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE id = ?
        ");
        $owner_stmt->execute([$vehicle['user_id']]);
        $current_owner = $owner_stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'vehicle' => $vehicle,
            'ownership_history' => $ownership_history,
            'plate_history' => $plate_history,
            'images' => $images,
            'documents' => $documents,
            'current_owner' => $current_owner
        ];
    }

    public function getFullProfile($vehicle_id) {
        $details = $this->getFullDetails($vehicle_id);
        
        if ($details) {
            // Get status history
            $status_stmt = $this->db->prepare("
                SELECT * FROM vehicle_status_history 
                WHERE vehicle_id = ? 
                ORDER BY created_at DESC
            ");
            $status_stmt->execute([$vehicle_id]);
            $details['status_history'] = $status_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $details;
    }

    public function getVehicleHistoryAndOwners($vehicle_id) {
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

    public function updateStatus($vehicle_id, $status) {
        $this->db->beginTransaction();

        try {
            // Update current status
            $update_stmt = $this->db->prepare("
                UPDATE vehicles SET current_status = ?, updated_at = NOW() 
                WHERE id = ? AND deleted_at IS NULL
            ");
            $update_stmt->execute([$status, $vehicle_id]);

            // Log status change
            $log_stmt = $this->db->prepare("
                INSERT INTO vehicle_status_history (vehicle_id, status, created_at) 
                VALUES (?, ?, NOW())
            ");
            $log_stmt->execute([$vehicle_id, $status]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getVehiclesPaginated($page = 1, $per_page = 10, $search = '', $status = '') {
        $offset = ($page - 1) * $per_page;
        
        $sql = "
            SELECT 
                v.*,
                vm.make,
                vm.model,
                u.email as owner_email,
                u.phone as owner_phone,
                pn.plate_number as current_plate_number
            FROM vehicles v
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN users u ON v.user_id = u.id
            LEFT JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1
            WHERE v.deleted_at IS NULL
        ";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (v.vin LIKE ? OR pn.plate_number LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $search_term = "%$search%";
            $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
        }
        
        if (!empty($status)) {
            $sql .= " AND v.current_status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY v.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $per_page;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVehiclesCount($search = '', $status = '') {
        $sql = "
            SELECT COUNT(*) as count 
            FROM vehicles v
            LEFT JOIN users u ON v.user_id = u.id
            LEFT JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1
            WHERE v.deleted_at IS NULL
        ";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (v.vin LIKE ? OR pn.plate_number LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $search_term = "%$search%";
            $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
        }
        
        if (!empty($status)) {
            $sql .= " AND v.current_status = ?";
            $params[] = $status;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()->count;
    }

    public function advancedSearch($filters, $page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        $sql = "
            SELECT 
                v.*,
                vm.make,
                vm.model,
                u.email as owner_email,
                u.phone as owner_phone,
                u.nin as owner_nin,
                pn.plate_number as current_plate_number
            FROM vehicles v
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN users u ON v.user_id = u.id
            LEFT JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1
            WHERE v.deleted_at IS NULL
        ";
        
        $params = [];
        
        if (!empty($filters['vin'])) {
            $sql .= " AND v.vin LIKE ?";
            $params[] = "%{$filters['vin']}%";
        }
        
        if (!empty($filters['plate_number'])) {
            $sql .= " AND pn.plate_number LIKE ?";
            $params[] = "%{$filters['plate_number']}%";
        }
        
        if (!empty($filters['make'])) {
            $sql .= " AND vm.make LIKE ?";
            $params[] = "%{$filters['make']}%";
        }
        
        if (!empty($filters['model'])) {
            $sql .= " AND vm.model LIKE ?";
            $params[] = "%{$filters['model']}%";
        }
        
        if (!empty($filters['year'])) {
            $sql .= " AND v.year = ?";
            $params[] = $filters['year'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND v.current_status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['owner_identifier'])) {
            $sql .= " AND (u.email LIKE ? OR u.phone LIKE ? OR u.nin LIKE ?)";
            $owner_term = "%{$filters['owner_identifier']}%";
            $params = array_merge($params, [$owner_term, $owner_term, $owner_term]);
        }
        
        $sql .= " ORDER BY v.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $per_page;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function advancedSearchCount($filters) {
        $sql = "
            SELECT COUNT(*) as count 
            FROM vehicles v
            LEFT JOIN vehicle_models vm ON v.vehicle_model_id = vm.id
            LEFT JOIN users u ON v.user_id = u.id
            LEFT JOIN plate_numbers pn ON v.id = pn.vehicle_id AND pn.is_current = 1
            WHERE v.deleted_at IS NULL
        ";
        
        $params = [];
        
        if (!empty($filters['vin'])) {
            $sql .= " AND v.vin LIKE ?";
            $params[] = "%{$filters['vin']}%";
        }
        
        if (!empty($filters['plate_number'])) {
            $sql .= " AND pn.plate_number LIKE ?";
            $params[] = "%{$filters['plate_number']}%";
        }
        
        if (!empty($filters['make'])) {
            $sql .= " AND vm.make LIKE ?";
            $params[] = "%{$filters['make']}%";
        }
        
        if (!empty($filters['model'])) {
            $sql .= " AND vm.model LIKE ?";
            $params[] = "%{$filters['model']}%";
        }
        
        if (!empty($filters['year'])) {
            $sql .= " AND v.year = ?";
            $params[] = $filters['year'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND v.current_status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['owner_identifier'])) {
            $sql .= " AND (u.email LIKE ? OR u.phone LIKE ? OR u.nin LIKE ?)";
            $owner_term = "%{$filters['owner_identifier']}%";
            $params = array_merge($params, [$owner_term, $owner_term, $owner_term]);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()->count;
    }

    public function logSearch($user_id, $search_type, $search_term, $vehicle_id = null) {
        $stmt = $this->db->prepare("
            INSERT INTO search_history (user_id, search_type, search_term, vehicle_id, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$user_id, $search_type, $search_term, $vehicle_id]);
    }

    public function getSearchHistory($user_id, $page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        $stmt = $this->db->prepare("
            SELECT sh.*, v.vin, v.current_status
            FROM search_history sh
            LEFT JOIN vehicles v ON sh.vehicle_id = v.id
            WHERE sh.user_id = ?
            ORDER BY sh.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$user_id, $per_page, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSearchHistoryCount($user_id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM search_history 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch()->count;
    }

    public function getRecentSearches($user_id, $limit = 5) {
        $stmt = $this->db->prepare("
            SELECT sh.*, v.vin, v.current_status
            FROM search_history sh
            LEFT JOIN vehicles v ON sh.vehicle_id = v.id
            WHERE sh.user_id = ?
            ORDER BY sh.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$user_id, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM vehicles WHERE deleted_at IS NULL");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getNormalStatusCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM vehicles WHERE current_status = 'none'");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getStolenStatusCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM vehicles WHERE current_status = 'stolen'");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getCustomsStatusCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM vehicles WHERE current_status = 'no_customs_duty'");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getEngineStatusCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM vehicles WHERE current_status = 'changed_engine'");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getColorStatusCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM vehicles WHERE current_status = 'changed_color'");
        $stmt->execute();
        return $stmt->fetch()->count;
    }

    public function getCountByStatus() {
        $stmt = $this->db->prepare("
            SELECT current_status, COUNT(*) as count 
            FROM vehicles 
            WHERE deleted_at IS NULL 
            GROUP BY current_status
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthlyRegistrations() {
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM vehicles 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            AND deleted_at IS NULL
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVehicleHistory($vehicle_id) {
        $stmt = $this->db->prepare("
            SELECT 
                ot.*,
                u.* 
            FROM ownership_transfers ot
            JOIN users u ON ot.buyer_id = u.id
            WHERE ot.vehicle_id = ? AND ot.deleted_at IS NULL
            ORDER BY ot.created_at DESC
        ");
        $stmt->execute([$vehicle_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVehicleUserHistory($vehicle_id) {
        $stmt = $this->db->prepare("
            SELECT 
                ot.*,
                u_from.email as from_user_email,
                u_to.email as to_user_email,
                u_from.phone as from_user_phone,
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

    public function getPlateNumberHistory($vehicle_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM plate_numbers 
            WHERE vehicle_id = ? AND deleted_at IS NULL 
            ORDER BY assigned_at DESC
        ");
        $stmt->execute([$vehicle_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDocumentHistory($vehicle_id) {
        $stmt = $this->db->prepare("
            SELECT
            vd.*,
            u.*
            FROM vehicle_documents vd
            JOIN users u ON vd.uploader_id = u.id
            WHERE vd.vehicle_id = ? AND vd.deleted_at IS NULL 
            ORDER BY vd.created_at DESC
        ");
        $stmt->execute([$vehicle_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVehicleUsers($vehicle_id) {
        $stmt = $this->db->prepare("
           SELECT
                ot.*,
                from_user.*,
                from_user.id          AS user_id,
                from_user.created_at  AS user_created_at,
                from_user.updated_at  AS user_updated_at,
                from_user.deleted_at  AS user_deleted_at
            FROM ownership_transfers ot
            LEFT JOIN users from_user ON ot.seller_id = from_user.id
            WHERE ot.vehicle_id = ? AND ot.deleted_at IS NULL
            ORDER BY ot.created_at DESC;
        ");
        $stmt->execute([$vehicle_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurrentOwner($vehicle_id) {
        $stmt = $this->db->prepare("
            SELECT u.*
            FROM vehicles v
            JOIN users u ON v.user_id = u.id
            JOIN ownership_transfers ot ON v.id = ot.vehicle_id
            WHERE v.id = ? AND v.deleted_at IS NULL
        ");
        $stmt->execute([$vehicle_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getPreviousOwners($vehicle_id) {
        $stmt = $this->db->prepare("
            SELECT
                ot.*,
                u.*,
                u.id          AS user_id,
                u.created_at  AS user_created_at,
                u.updated_at  AS user_updated_at,
                u.deleted_at  AS user_deleted_at
            FROM ownership_transfers ot
            JOIN users u ON ot.seller_id = u.id
            WHERE ot.vehicle_id = ? AND ot.deleted_at IS NULL
            ORDER BY ot.created_at DESC;
        ");

        $stmt->execute([$vehicle_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateVehicle($vehicle_id, $data) {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $vehicle_id;
        $sql = "UPDATE vehicles SET " . implode(", ", $fields) . ", updated_at = NOW() WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getPreviousOwnersPaginated($vehicle_id, $page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        $stmt = $this->db->prepare("
            SELECT
                ot.*,
                u.*,
                u.id          AS user_id,
                u.created_at  AS user_created_at,
                u.updated_at  AS user_updated_at,
                u.deleted_at  AS user_deleted_at
                 FROM ownership_transfers ot
            JOIN users u ON ot.seller_id = u.id
            WHERE ot.vehicle_id = ? AND ot.deleted_at IS NULL
            ORDER BY ot.created_at DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([$vehicle_id, $per_page, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>