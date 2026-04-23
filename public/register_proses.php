<?php
require_once '../config/Database.php';
require_once '../app/Utils/Validator.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = Validator::bersihkanInput($_POST['nama']);
    $nim   = Validator::bersihkanInput($_POST['nim']); // Format: E41...
    $email = Validator::bersihkanInput($_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Gunakan Validator yang kita buat di awal
    if (!Validator::validasiNomorInduk($nim, 'mahasiswa')) {
        die("Format NIM tidak valid! Gunakan format seperti E41250909.");
    }

    try {
        $db->beginTransaction();

        // 1. Insert ke tabel pengguna
        $q1 = "INSERT INTO pengguna (nama, email, kata_sandi, peran) VALUES (?, ?, ?, 'mahasiswa')";
        $stmt1 = $db->prepare($q1);
        $stmt1->execute([$nama, $email, $pass]);
        $userId = $db->lastInsertId();

        // 2. Insert ke tabel detail_pengguna (nomor_induk)
        $q2 = "INSERT INTO detail_pengguna (id_pengguna, nomor_induk) VALUES (?, ?)";
        $stmt2 = $db->prepare($q2);
        $stmt2->execute([$userId, $nim]);

        $db->commit();
        header("Location: login.php?reg=berhasil");
    } catch (Exception $e) {
        $db->rollBack();
        echo "Gagal daftar: " . $e->getMessage();
    }
}