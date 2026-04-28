<?php
class Database {
    private $host = "localhost";
    private $db_name = "db_jti-surat";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Menggunakan PDO untuk koneksi
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            // Error handling mode
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Koneksi Gagal: " . $e->getMessage();
        }
        return $this->conn;
    }
}