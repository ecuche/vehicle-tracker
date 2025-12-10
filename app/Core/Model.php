<?php
namespace App\Core;

use App\Core\Database;
use Illuminate\Support\Str;
use PDO;

abstract class Model {
    
    protected $db;

    protected $table;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getTable(): string{
        if($this->table !== null){
            return $this->table;
        }
        $parts = explode("\\", $this::class);
        $parts = array_pop($parts);
        $parts = preg_replace('/(?<!^)[A-Z]/', '_$0', $parts);
        return strtolower($parts).'s';
    }

    public function getDbConnection() {
        return $this->db;
    }

    public function returnObject($result) {
        return  (object)$result;
    }   

    public function insert($data, $table = null) {
        $table ??= $this->getTable();
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $stmt = $this->db->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        return $stmt->execute(array_values($data));
    }

    public function getInsertId() {
        return $this->db->lastInsertId();
    }   

    public function lastInsertId() {
        return $this->db->lastInsertId();
    }

    public function insertAndGet($data, $table = null) {
        $table ??= $this->getTable();
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $stmt = $this->db->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        $success = $stmt->execute(array_values($data));
        if (!$success) {
            return false;
        }
        $id = $this->db->lastInsertId();
        if ($id) {
            return $this->findById($id, $table);
        }
        return false;
    }

    public function findFirst($where, $table = null) {
        $table ??= $this->getTable();
        if (empty($where)) {
            $stmt = $this->db->prepare("SELECT * FROM $table WHERE deleted_at IS NULL LIMIT 1");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE $whereClause AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(array_values($where));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function hardFindFirst($where, $table = null) { 
        $table ??= $this->getTable();
        if (empty($where)) {
            $stmt = $this->db->prepare("SELECT * FROM $table LIMIT 1");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE $whereClause LIMIT 1");
        $stmt->execute(array_values($where));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function check($where, $table){
        $table ??= $this->getTable();
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("SELECT 1 FROM $table WHERE $whereClause AND deleted_at IS NULL LIMIT 1 ");
        $stmt->execute(array_values($where));
        return $stmt->fetch() !== false;
    }

    public function hardCheck($where, $table){
        $table ??= $this->getTable();
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("SELECT 1 FROM $table WHERE $whereClause LIMIT 1");
        $stmt->execute(array_values($where));
        return $stmt->fetch() !== false;
    }

    public function insertGetId($data, $table = null) {
        $table ??= $this->getTable();
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $stmt = $this->db->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    public function insertMultiple($dataArray, $table = null) {
        $table ??= $this->getTable();
        if (empty($dataArray)) {
            return false;
        }
        $columns = implode(", ", array_keys($dataArray[0]));
        $placeholders = implode(", ", array_fill(0, count($dataArray[0]), "?"));
        $allPlaceholders = implode(", ", array_fill(0, count($dataArray), "($placeholders)"));
        $stmt = $this->db->prepare("INSERT INTO $table ($columns) VALUES $allPlaceholders");
        $values = [];
        foreach ($dataArray as $data) {
            $values = array_merge($values, array_values($data));
        }
        return $stmt->execute($values);
    }

    public function insertAndGetId($data, $table = null) {
        $table ??= $this->getTable();
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $stmt = $this->db->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    public function update($data, $where, $table = null) {
        $table ??= $this->getTable();
        $setClause = implode(", ", array_map(fn($col) => "$col = ?", array_keys($data)));
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("UPDATE $table SET $setClause WHERE $whereClause AND deleted_at IS NULL");
        return $stmt->execute(array_merge(array_values($data), array_values($where)));
    }

    public function updateById($data, $id, $table = null) {
        $table ??= $this->getTable();
        $setClause = implode(", ", array_map(fn($col) => "$col = ?", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE $table SET $setClause WHERE id = ? AND deleted_at IS NULL");
        return $stmt->execute(array_merge(array_values($data), [$id]));
    }

    public function updateAllFirst($data, $table = null) {
        $table ??= $this->getTable();
        $setClause = implode(", ", array_map(fn($col) => "$col = ?", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE $table SET $setClause WHERE deleted_at IS NULL ORDER BY id ASC LIMIT 1");
        return $stmt->execute(array_values($data));
    }

    public function updateAllLast($data, $table = null) {
        $table ??= $this->getTable();
        $setClause = implode(", ", array_map(fn($col) => "$col = ?", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE $table SET $setClause WHERE deleted_at IS NULL ORDER BY id DESC LIMIT 1");
        return $stmt->execute(array_values($data));
    }

    public function updateLast($data, $where, $table = null) {
        $table ??= $this->getTable();
        $setClause = implode(", ", array_map(fn($col) => "$col = ?", array_keys($data)));
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("UPDATE $table SET $setClause WHERE $whereClause AND deleted_at IS NULL ORDER BY id DESC LIMIT 1");
        return $stmt->execute(array_merge(array_values($data), array_values($where)));
    }

    public function updateFirst($data, $where, $table = null) {
        $table ??= $this->getTable();
        $setClause = implode(", ", array_map(fn($col) => "$col = ?", array_keys($data)));
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("UPDATE $table SET $setClause WHERE $whereClause AND deleted_at IS NULL ORDER BY id ASC LIMIT 1");
        return $stmt->execute(array_merge(array_values($data), array_values($where)));
    }

    public function deleteById($id, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("DELETE FROM $table WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function softDeleteById($id, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("UPDATE $table SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function delete($where, $table = null) {
        $table ??= $this->getTable();
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("DELETE FROM $table WHERE $whereClause");
        return $stmt->execute(array_values($where));
    }

    public function find($where, $table = null) {
        $table ??= $this->getTable();
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE $whereClause AND deleted_at IS NULL");
        $stmt->execute(array_values($where));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    public function findAllGroupBy($groupByColumn, $where = [], $table = null) {
        $table ??= $this->getTable();
        if (empty($where)) {
            $stmt = $this->db->prepare("SELECT * FROM $table WHERE deleted_at IS NULL GROUP BY $groupByColumn");
            $stmt->execute();
        } else {
            $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
            $stmt = $this->db->prepare("SELECT * FROM $table WHERE $whereClause AND deleted_at IS NULL GROUP BY $groupByColumn");
            $stmt->execute(array_values($where));
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hardFindAllGroupBy($groupByColumn, $where = [], $table = null) {
        $table ??= $this->getTable();
        if (empty($where)) {
            $stmt = $this->db->prepare("SELECT * FROM $table GROUP BY $groupByColumn");
            $stmt->execute();
        } else {
            $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
            $stmt = $this->db->prepare("SELECT * FROM $table WHERE $whereClause GROUP BY $groupByColumn");
            $stmt->execute(array_values($where));
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findIncludingDeleted($where, $table = null) {
        $table ??= $this->getTable();
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE $whereClause");
        $stmt->execute(array_values($where));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAllIncludingDeleted($where = [], $table = null) {
        $table ??= $this->getTable();
        if (empty($where)) {
            $stmt = $this->db->prepare("SELECT * FROM $table");
            $stmt->execute();
        } else {
            $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
            $stmt = $this->db->prepare("SELECT * FROM $table WHERE $whereClause");
            $stmt->execute(array_values($where));
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hardFind($where, $table = null) {
        $table ??= $this->getTable();
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE $whereClause");
        $stmt->execute(array_values($where));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function hardFindAll($where = [], $table = null) {
        $table ??= $this->getTable();
        if (empty($where)) {
            $stmt = $this->db->prepare("SELECT * FROM $table");
            $stmt->execute();
        } else {
            $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
            $stmt = $this->db->prepare("SELECT * FROM $table WHERE $whereClause");
            $stmt->execute(array_values($where));
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAll($where = [], $table = null) {
        $table ??= $this->getTable();
        if (empty($where)) {
            $stmt = $this->db->prepare("SELECT * FROM $table");
            $stmt->execute();
        } else {
            $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
            $stmt = $this->db->prepare("SELECT * FROM $table WHERE $whereClause AND deleted_at IS NULL");
            $stmt->execute(array_values($where));
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function rawQuery($query, $params = []) {
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function rawExecute($query, $params = []) {
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function findById(int $id, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     public function hardFindById($id, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function softDelete($where, $table = null) {
        $table ??= $this->getTable();
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("UPDATE $table SET deleted_at = NOW() WHERE $whereClause");
        return $stmt->execute(array_values($where));
    }

    public function findAllByUserId($user_id, $table = null){
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function hardFindAllByUserId($user_id, $table = null){
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function restoreById($id, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("UPDATE $table SET deleted_at = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function hardDeleteByColumn($column, $value, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("DELETE FROM $table WHERE {$column} = ?");
        return $stmt->execute([$value]);
    }

    public function softDeleteByColumn($column, $value, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("UPDATE $table SET deleted_at = NOW() WHERE {$column} = ?");
        return $stmt->execute([$value]);
    }

    public function hardDeleteByIds($ids, $table = null) {
        $table ??= $this->getTable();
        $placeholders = implode(", ", array_fill(0, count($ids), "?"));
        $stmt = $this->db->prepare("DELETE FROM $table WHERE id IN ($placeholders)");
        return $stmt->execute($ids);
    }

    public function softDeleteByIds($ids, $table = null) {
        $table ??= $this->getTable();
        $placeholders = implode(", ", array_fill(0, count($ids), "?"));
        $stmt = $this->db->prepare("UPDATE $table SET deleted_at = NOW() WHERE id IN ($placeholders)");
        return $stmt->execute($ids);
    }   

    public function restoreByColumn($column, $value, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("UPDATE $table SET deleted_at = NULL WHERE {$column} = ?");
        return $stmt->execute([$value]);
    }

    public function findAllUpdatedOn($date, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE DATE(updated_at) = ?  AND deleted_at IS NULL");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

     public function hardFindAllUpdatedOn($date, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE DATE(updated_at) = ?");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    public function findAllCreatedOn($date, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE DATE(created_at) = ?");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function hardFindAllCreatedOn($date, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE DATE(created_at) = ? AND deleted_at IS NULL");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllCreatedBefore($date, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE created_at <= ?");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllCreatedAfter($date, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE created_at >= ? AND deleted_at IS NULL");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hardFindAllCreatedAfter($date, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE created_at >= ?");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllCreatedBetween($startDate, $endDate, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE created_at >= ? AND created_at <= ? AND deleted_at IS NULL");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hardFindAllCreatedBetween($startDate, $endDate, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE created_at >= ? AND created_at <= ?");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllCreatedAt($startDate, $endDate, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE created_at BETWEEN ? AND ? AND deleted_at IS NULL");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function hardFindAllCreatedAt($startDate, $endDate, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE created_at BETWEEN ? AND ?");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllSoftDeletedByColumn($column, $value, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE {$column} = ? AND deleted_at IS NOT NULL");
        $stmt->execute([$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hardFindAllSoftDeletedByColumn($column, $value, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE {$column} = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function restore($where, $table = null) {
        $table ??= $this->getTable();
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("UPDATE $table SET deleted_at = NULL WHERE $whereClause");
        return $stmt->execute(array_values($where));
    }

    public function hardDeleteById($id, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("DELETE FROM $table WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function hardDelete($where, $table = null) {
        $table ??= $this->getTable();
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("DELETE FROM $table WHERE $whereClause");
        return $stmt->execute(array_values($where));
    }

    public function findAllIncludingSoftDeleted($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllActive($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE deleted_at IS NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllSoftDelete($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE deleted_at IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllWithLimit($limit, $offset = 0, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE deleted_at IS NULL LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hardFindAllWithLimit($limit, $offset = 0, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllByIds($ids, $table = null) {
        $table ??= $this->getTable();
        $placeholders = implode(", ", array_fill(0, count($ids), "?"));
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE id IN ($placeholders) AND deleted_at IS NULL");
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hardFindAllByIds($ids, $table = null) {
        $table ??= $this->getTable();
        $placeholders = implode(", ", array_fill(0, count($ids), "?"));
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findbyColumn($column, $value, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE {$column} = ? AND deleted_at IS NULL");
        $stmt->execute([$value]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function hardFindbyColumn($column, $value, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE {$column} = ?");
        $stmt->execute([$value]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function hardFindAllByColumn($column, $value, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE {$column} = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllByColumn($column, $value, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE {$column} = ? AND deleted_at IS NULL");
        $stmt->execute([$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findFirstRow($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAllLast($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     public function findLast($where, $table = null) {
        $table ??= $this->getTable();
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE $whereClause AND deleted_at IS NULL ORDER BY id DESC LIMIT 1");
        $stmt->execute(array_values($where));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countRow($where = [], $table = null) {
        $table ??= $this->getTable();
        if (empty($where)) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM $table WHERE deleted_at IS NULL");
            $stmt->execute();
        } else {
            $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM $table WHERE $whereClause AND deleted_at IS NULL");
            $stmt->execute(array_values($where));
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function hardCountRow($where = [], $table = null) {
        $table ??= $this->getTable();
        if (empty($where)) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM $table");
            $stmt->execute();
        } else {
            $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM $table WHERE $whereClause");
            $stmt->execute(array_values($where));
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function countAll($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM $table WHERE deleted_at IS NULL");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;   
    }

    public function hardCountAll($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM $table");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;   
    }

    public function exists(array $where, $table = null) {
        $table ??= $this->getTable();
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("SELECT 1 FROM $table WHERE $whereClause AND deleted_at IS NULL LIMIT 1 ");
        $stmt->execute(array_values($where));
        return $stmt->fetch() !== false;
    }

    public function hardExists($where, $table = null) {
        $table ??= $this->getTable();
        $whereClause = implode(" AND ", array_map(fn($col) => "$col = ?", array_keys($where)));
        $stmt = $this->db->prepare("SELECT 1 FROM $table WHERE $whereClause LIMIT 1");
        $stmt->execute(array_values($where));
        return $stmt->fetch() !== false;
    }

    public function randomRow($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE deleted_at IS NULL ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function hardRandomRow($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkSlugExists($slug, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT 1 FROM $table WHERE slug = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$slug]);
        return $stmt->fetch() !== false;
    }

    public function hardCheckSlugExists($slug, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT 1 FROM $table WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        return $stmt->fetch() !== false;
    }
    
    public function generateSlug($table = null) {
        $table ??= $this->getTable();
        do{
            $slug = $this->randomString(8);
        }while($this->checkSlugExists($slug, $table = null));
        return $slug;
    }

    public function randomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function updateAll($data, $table = null) {
        $table ??= $this->getTable();
        $setClause = implode(", ", array_map(fn($col) => "$col = ?", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE $table SET $setClause WHERE deleted_at IS NULL");
        return $stmt->execute(array_values($data));
    }

    public function hardUpdateAll($data, $table = null) {
        $table ??= $this->getTable();
        $setClause = implode(", ", array_map(fn($col) => "$col = ?", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE $table SET $setClause");
        return $stmt->execute(array_values($data));
    }

    public function deleteAll($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("DELETE FROM $table");
        return $stmt->execute();
    }

    public function softDeleteAll($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("UPDATE $table SET deleted_at = NOW()");
        return $stmt->execute();
    }

    public function restoreAll($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("UPDATE $table SET deleted_at = NULL");
        return $stmt->execute();
    }

    public function countSoftDeleted($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM $table WHERE deleted_at IS NOT NULL");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;   
    }

    public function countActive($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM $table WHERE deleted_at IS NULL");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;   
    }

    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    public function getAllSoftDeleted($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE deleted_at IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hardDeleteAll($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("DELETE FROM $table");
        return $stmt->execute();
    }

    public function hardRestoreAll($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("UPDATE $table SET deleted_at = NULL");
        return $stmt->execute();
    }

    public function hardCountSoftDeleted($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM $table WHERE deleted_at IS NOT NULL");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function hardCountActive($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM $table WHERE deleted_at IS NULL");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function hardGetAllSoftDeleted($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE deleted_at IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hardGetAllActive($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE deleted_at IS NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hardGetAll($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllActive($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE deleted_at IS NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE deleted_at IS NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    public function hardDeleteAllSoftDeleted($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("DELETE FROM $table WHERE deleted_at IS NOT NULL");
        return $stmt->execute();
    }

    public function deleteAllSoftDeleted($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("DELETE FROM $table WHERE deleted_at IS NOT NULL");
        return $stmt->execute();
    }

    public function hardDeleteAllActive($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("DELETE FROM $table WHERE deleted_at IS NULL");
        return $stmt->execute();
    }

    public function deleteAllActive($table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("DELETE FROM $table WHERE deleted_at IS NULL");
        return $stmt->execute();
    }

    public function getbyIdPagination($id, $limit, $offset = 0, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE id = ? AND deleted_at IS NULL LIMIT ? OFFSET ?");
        $stmt->execute([$id, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hardGetbyIdPagination($id, $limit, $offset = 0, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE id = ? LIMIT ? OFFSET ?");
        $stmt->execute([$id, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPagination($limit, $offset = 0, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE deleted_at IS NULL LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hardGetAllPagination($limit, $offset = 0, $table = null) {
        $table ??= $this->getTable();
        $stmt = $this->db->prepare("SELECT * FROM $table LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   
}
?>