<?php
require_once __DIR__ . '/../Core/Model.php';

class Pengguna extends Model {
    // Properti dilindungi (Encapsulation)
    protected $id;
    protected $nama;
    protected $email;
    protected $table = "pengguna";

    /**
     * Mencari data pengguna berdasarkan ID
     */
    public function find($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Implementasi fungsi abstract save dari Core/Model.php
     * Digunakan untuk registrasi atau tambah user baru oleh admin
     */
    public function save($data) {
        $query = "INSERT INTO " . $this->table . " (nama, email, kata_sandi, peran, nomor_induk) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        
        // Enkripsi password menggunakan hash (Keamanan)
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $stmt->execute([
            $data['nama'], 
            $data['email'], 
            $hashed_password, 
            $data['peran'], 
            $data['nomor_induk']
        ]);
    }

    /**
     * Fungsi Login Utama
     * Memverifikasi email dan password yang sudah di-hash
     */
    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika user ditemukan dan password cocok
        if ($user && password_verify($password, $user['kata_sandi'])) {
            return $user; // Mengembalikan array data user
        }
        
        return false; // Login gagal
    }
}