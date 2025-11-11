<?php
class Database {
    private $conn;

    public function getConnection() {
        $host = '193.93.250.83/wwwit-utv/';
        $port = '3306';
        $db_name = 'KravION';
        $username = 'sqllab';
        $password = 'Armadillo#2025';

        try {
            $dsn = "mysql:dbname={$db_name};host={$host};port={$port}";
            $conn = new PDO($dsn, $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $conn;
        } catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
    }
}
