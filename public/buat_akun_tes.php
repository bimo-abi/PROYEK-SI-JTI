<?php
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();

// Password yang akan di-hash
$passwordAdmin = password_hash('admin123', PASSWORD_DEFAULT);
$passwordMhs = password_hash('mhs123', PASSWORD_DEFAULT);

try {
    // Buat Admin
    $db->query("INSERT INTO pengguna (nama, email, kata_sandi, peran) VALUES ('Admin JTI', 'admin@jti.com', '$passwordAdmin', 'admin')");
    
    // Buat Mahasiswa (Ingat NIM harus sesuai format: E + Angka)
    $db->query("INSERT INTO pengguna (nama, email, kata_sandi, peran) VALUES ('Budi Santoso', 'budi@jti.com', '$passwordMhs', 'mahasiswa')");
    $idMhs = $db->lastInsertId();
    $db->query("INSERT INTO detail_pengguna (id_pengguna, nomor_induk, id_prodi) VALUES ($idMhs, 'E41250001', 1)");

    echo "Akun tes berhasil dibuat!";
} catch (Exception $e) {
    echo "Gagal/Sudah ada: " . $e->getMessage();
}