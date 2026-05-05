<?php
namespace Config;

use PDO;
use PDOException;

class Database {
    private $host = "localhost";
    private $db_name = "db_surat-jti"; 
    private $username = "root";      
    private $password = "";          
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Mengatur error mode ke Exception agar mudah didebug
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Mengatur fetch mode default ke Associative Array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Koneksi Database Gagal: " . $exception->getMessage();
        }
        return $this->conn;
    }
}