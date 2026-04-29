<?php
require_once __DIR__ . '/../Core/Model.php';

class Pengguna extends Model {
    protected $table = 'pengguna';

    // Logika Login Universal
    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['kata_sandi'])) {
            return $user;
        }
        return false;
    }

    // Fungsi simpan data (digunakan saat registrasi & init_db)
    public function save($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (nama, email, kata_sandi, peran, nomor_induk, id_prodi) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $stmt->execute([
            $data['nama'], 
            $data['email'], 
            $hashed_password, 
            $data['peran'], 
            $data['nomor_induk'],
            $data['id_prodi']
        ]);
    }
}