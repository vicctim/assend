<?php
namespace App\Database;

class Database {
    private static $instance = null;
    private $connection = null;
    
    private function __construct() {
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect() {
        try {
            $this->connection = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->connection->connect_error) {
                throw new \Exception("Erro na conexão: " . $this->connection->connect_error);
            }
            
            $this->connection->set_charset("utf8mb4");
        } catch (\Exception $e) {
            error_log("Erro de conexão com o banco: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function getConnection() {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }
    
    public function query($sql, $params = [], $types = '') {
        try {
            $stmt = $this->getConnection()->prepare($sql);
            
            if (!$stmt) {
                throw new \Exception("Erro na preparação da query: " . $this->getConnection()->error);
            }
            
            if (!empty($params)) {
                if (empty($types)) {
                    $types = str_repeat('s', count($params));
                }
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            return $stmt;
        } catch (\Exception $e) {
            error_log("Erro na execução da query: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function fetch($sql, $params = [], $types = '') {
        $stmt = $this->query($sql, $params, $types);
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }
    
    public function fetchAll($sql, $params = [], $types = '') {
        $stmt = $this->query($sql, $params, $types);
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
        
        $types = str_repeat('s', count($data));
        $stmt = $this->query($sql, array_values($data), $types);
        $id = $this->getConnection()->insert_id;
        $stmt->close();
        
        return $id;
    }
    
    public function update($table, $data, $where, $whereParams = [], $whereTypes = '') {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        
        $types = str_repeat('s', count($data)) . $whereTypes;
        $params = array_merge(array_values($data), $whereParams);
        
        $stmt = $this->query($sql, $params, $types);
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        return $affected;
    }
    
    public function delete($table, $where, $params = [], $types = '') {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params, $types);
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        return $affected;
    }
    
    public function beginTransaction() {
        $this->getConnection()->begin_transaction();
    }
    
    public function commit() {
        $this->getConnection()->commit();
    }
    
    public function rollback() {
        $this->getConnection()->rollback();
    }
    
    public function __destruct() {
        if ($this->connection !== null) {
            $this->connection->close();
        }
    }
} 