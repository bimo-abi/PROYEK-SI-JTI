<?php
namespace Classes;

abstract class User {
    protected $db;
    protected $id;
    protected $nama;
    protected $email;
    protected $hashed_password; // TAMBAHKAN: Untuk keperluan verifikasi login & ubah password

    public function __construct($db) {
        $this->db = $db;
    }

    // Encapsulation: Getter untuk Nama
    public function getNama() {
        return $this->nama;
    }

    // --- TAMBAHKAN FUNGSI INI ---
    // Logika ini ditaruh di Parent Class agar bisa digunakan oleh Admin, Dosen, dan Mahasiswa
    public function changePassword($oldPass, $newPass) {
        // 1. Verifikasi password lama dengan hash dari database
        if (password_verify($oldPass, $this->hashed_password)) {
            
            // 2. Hash password baru (Security: BCRYPT)
            $safePass = password_hash($newPass, PASSWORD_BCRYPT);
            
            // 3. Update ke Database menggunakan PDO
            try {
                $query = "UPDATE pengguna SET kata_sandi = :pass WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':pass', $safePass);
                $stmt->bindParam(':id', $this->id);
                return $stmt->execute();
            } catch (\PDOException $e) {
                // Exception Handling jika database error
                return false;
            }
        }
        return false;
    }

    // Polymorphism: Dashboard akan berbeda untuk setiap role
    abstract public function dashboard();

    // Destructor untuk membersihkan memori jika perlu
    public function __destruct() {
        // Logika penutupan
    }
}