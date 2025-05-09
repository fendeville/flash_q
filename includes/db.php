<?php
require_once 'config.php';

class Database {
    private $conn;
    
    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function select($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function selectOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function insert($table, $data) {
        $keys = array_keys($data);
        $fields = "`" . implode("`, `", $keys) . "`";
        $placeholders = ":" . implode(", :", $keys);
        
        $sql = "INSERT INTO `{$table}` ({$fields}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        return $this->conn->lastInsertId();
    }
    
    public function update($table, $data, $where) {
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "`{$key}` = :{$key}";
        }
        
        $sql = "UPDATE `{$table}` SET " . implode(", ", $sets) . " WHERE {$where}";
        $stmt = $this->query($sql, $data);
        return $stmt->rowCount();
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
}

$db = new Database();
?>