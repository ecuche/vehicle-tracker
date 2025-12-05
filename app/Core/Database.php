<?php
namespace App\Core;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database {
    private static $instance = null;
    private $connection;
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;

    private function __construct() {
        // Load environment variables
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? 'vehicle_tracker';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
        $this->charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        $this->connect();
    }

    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            
            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ]);

            // Set timezone
            $this->connection->exec("SET time_zone = '+01:00'"); // WAT timezone

        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        // Reconnect if connection is lost
        try {
            $this->connection->query('SELECT 1');
        } catch (PDOException $e) {
            $this->connect();
        }
        
        return $this->connection;
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollBack() {
        return $this->connection->rollBack();
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    // Helper methods for common operations
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->connection->prepare($sql);
        
        return $stmt->execute($data);
    }

    public function update($table, $data, $where) {
        $set = '';
        foreach (array_keys($data) as $column) {
            $set .= "{$column} = :{$column}, ";
        }
        $set = rtrim($set, ', ');

        $where_clause = '';
        $where_params = [];
        foreach ($where as $column => $value) {
            $where_clause .= "{$column} = :where_{$column} AND ";
            $where_params["where_{$column}"] = $value;
        }
        $where_clause = rtrim($where_clause, ' AND ');

        $sql = "UPDATE {$table} SET {$set} WHERE {$where_clause}";
        $stmt = $this->connection->prepare($sql);
        
        return $stmt->execute(array_merge($data, $where_params));
    }

    public function delete($table, $where) {
        $where_clause = '';
        $params = [];
        foreach ($where as $column => $value) {
            $where_clause .= "{$column} = :{$column} AND ";
            $params[$column] = $value;
        }
        $where_clause = rtrim($where_clause, ' AND ');

        $sql = "DELETE FROM {$table} WHERE {$where_clause}";
        $stmt = $this->connection->prepare($sql);
        
        return $stmt->execute($params);
    }

    public function softDelete($table, $where) {
        return $this->update($table, ['deleted_at' => date('Y-m-d H:i:s')], $where);
    }

    public function select($table, $columns = '*', $where = [], $orderBy = '', $limit = '') {
        $sql = "SELECT {$columns} FROM {$table}";
        $params = [];

        if (!empty($where)) {
            $where_clause = '';
            foreach ($where as $column => $value) {
                $where_clause .= "{$column} = :{$column} AND ";
                $params[$column] = $value;
            }
            $where_clause = rtrim($where_clause, ' AND ');
            $sql .= " WHERE {$where_clause}";
        }

        if (!empty($orderBy)) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if (!empty($limit)) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function tableExists($table) {
        try {
            $result = $this->connection->query("SELECT 1 FROM {$table} LIMIT 1");
            return $result !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Backup and maintenance methods
    public function backup($filepath) {
        try {
            $tables = $this->connection->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            $backup = "";
            foreach ($tables as $table) {
                $backup .= "-- Table: $table\n";
                
                // Table structure
                $create_table = $this->connection->query("SHOW CREATE TABLE $table")->fetch();
                $backup .= $create_table->{'Create Table'} . ";\n\n";
                
                // Table data
                $rows = $this->connection->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $columns = implode("`, `", array_keys($row));
                    $values = implode("', '", array_map([$this->connection, 'quote'], $row));
                    $backup .= "INSERT INTO `$table` (`$columns`) VALUES ('$values');\n";
                }
                $backup .= "\n";
            }
            
            return file_put_contents($filepath, $backup) !== false;
        } catch (PDOException $e) {
            error_log("Backup failed: " . $e->getMessage());
            return false;
        }
    }

    public function optimizeTables() {
        try {
            $tables = $this->connection->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($tables as $table) {
                $this->connection->exec("OPTIMIZE TABLE $table");
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Table optimization failed: " . $e->getMessage());
            return false;
        }
    }

    public function rowCount($table, $where = []) {
        $sql = "SELECT COUNT(*) as count FROM {$table}";
        $params = [];

        if (!empty($where)) {
            $where_clause = '';
            foreach ($where as $column => $value) {
                $where_clause .= "{$column} = :{$column} AND ";
                $params[$column] = $value;
            }
            $where_clause = rtrim($where_clause, ' AND ');
            $sql .= " WHERE {$where_clause}";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result ? (int)$result->count : 0;
    }

    public function escapeString($string) {
        return $this->connection->quote($string);
    }   

    public function getDatabaseName() {
        return $this->db_name;
    }

    public function getDatabaseVersion() {
        return $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    public function getDriverName() {
        return $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    public function isConnected() {
        try {
            $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function clearTable($table) {
        try {
            $this->connection->exec("TRUNCATE TABLE {$table}");
            return true;
        } catch (PDOException $e) {
            error_log("Clear table failed: " . $e->getMessage());
            return false;
        }
    }

    public function getTables() {
        try {
            $tables = $this->connection->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            return $tables;
        } catch (PDOException $e) {
            error_log("Get tables failed: " . $e->getMessage());
            return [];
        }
    }

    public function getTableColumns($table) {
        try {
            $columns = $this->connection->query("DESCRIBE {$table}")->fetchAll(PDO::FETCH_COLUMN);
            return $columns;
        } catch (PDOException $e) {
            error_log("Get table columns failed: " . $e->getMessage());
            return [];
        }
    }

    public function createDatabase($db_name) {
        try {
            $this->connection->exec("CREATE DATABASE IF NOT EXISTS {$db_name} CHARACTER SET {$this->charset} COLLATE {$this->charset}_general_ci");
            return true;
        } catch (PDOException $e) {
            error_log("Create database failed: " . $e->getMessage());
            return false;
        }
    }

    public function dropDatabase($db_name) {
        try {
            $this->connection->exec("DROP DATABASE IF EXISTS {$db_name}");
            return true;
        } catch (PDOException $e) {
            error_log("Drop database failed: " . $e->getMessage());
            return false;
        }
    }

    public function renameTable($old_name, $new_name) {
        try {
            $this->connection->exec("RENAME TABLE {$old_name} TO {$new_name}");
            return true;
        } catch (PDOException $e) {
            error_log("Rename table failed: " . $e->getMessage());
            return false;
        }
    }

    public function addColumn($table, $column_definition) {
        try {
            $this->connection->exec("ALTER TABLE {$table} ADD COLUMN {$column_definition}");
            return true;
        } catch (PDOException $e) {
            error_log("Add column failed: " . $e->getMessage());
            return false;
        }
    }

    public function __destruct() {
        $this->connection = null;
    }
}
?>