<?php

class Database {
    // Properti koneksi
    private $host = "localhost";
    private $db_name = "db_jti-surat"; // Pastikan ini sama dengan di phpMyAdmin
    private $username = "root";
    private $password = ""; // Kosongkan jika pakai Laragon/XAMPP default
    public $conn;

    /**
     * Method untuk mendapatkan koneksi database
     * Menggunakan PDO (PHP Data Objects) untuk keamanan ekstra
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            
            // Mengatur mode error agar memunculkan Exception jika query bermasalah
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Set default fetch mode ke associative array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            echo "Koneksi Database Gagal: " . $e->getMessage();
        }

        return $this->conn;
    }
}