<?php
class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        $this->host = getenv('PGHOST') ?: 'localhost';
        $this->port = getenv('PGPORT') ?: '3306';
        $this->db_name = getenv('PGDATABASE') ?: 'Grupp4';
        $this->username = getenv('PGUSER') ?: 'sqllab';
        $this->password = getenv('PGPASSWORD') ?: 'Pangolin!24';
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "pgsql:host=193.93.250.83" . $this->host . ";port=3306" . $this->port . ";dbname=wwwit-utv" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }

        return $this->conn;
    }
}
