<?php
require_once '../../autoload.php';
session_start();
use Config\Database;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $db = (new Database())->getConnection();
    $user_id = $_SESSION['user_id'];
    
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $id_prodi = $_POST['id_prodi'];
    $id_golongan = !empty($_POST['id_golongan']) ? $_POST['id_golongan'] : null;

    try {
        $db->beginTransaction();

        // 1. Update Tabel Pengguna
        $sql_user = "UPDATE pengguna SET nama = ?, email = ? WHERE id = ?";
        $stmt_user = $db->prepare($sql_user);
        $stmt_user->execute([$nama, $email, $user_id]);

        // 2. Update Tabel Detail Pengguna
        $sql_detail = "UPDATE detail_pengguna SET id_prodi = ?, id_golongan = ? WHERE id_pengguna = ?";
        $stmt_detail = $db->prepare($sql_detail);
        $stmt_detail->execute([$id_prodi, $id_golongan, $user_id]);

        $db->commit();
        
        // Update nama di session agar topbar berubah
        $_SESSION['nama'] = $nama;

        header("Location: profil.php?status=success");
        exit();

    } catch (Exception $e) {
        $db->rollBack();
        die("Error: " . $e->getMessage());
    }
}