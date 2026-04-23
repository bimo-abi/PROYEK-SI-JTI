<?php
class User extends Model implements Authenticatable {
    protected $id;
    protected $email;
    private $password; // Encapsulation

    public function login($email, $password) {
        $query = "SELECT * FROM pengguna WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['kata_sandi'])) {
            return $user;
        }
        throw new Exception("Email atau password salah!");
    }
}

// Inheritance: Mahasiswa mewarisi User
class Mahasiswa extends User {
    public function ajukanSurat($dataSurat) {
        try {
            $query = "INSERT INTO surat (id_pemohon, id_jenis_surat, keperluan, berkas_pdf) 
                      VALUES (:id, :jenis, :keperluan, :berkas)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute($dataSurat);
        } catch (Exception $e) {
            // Exception Handling
            error_log($e->getMessage());
            return false;
        }
    }
}