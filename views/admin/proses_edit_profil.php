<?php
require_once '../../autoload.php';
session_start();

// Proteksi: Wajib Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

use Config\Database;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = (new Database())->getConnection();
    $user_id = $_SESSION['user_id'];
    
    // Ambil data dari form
    $nama = $_POST['nama'];
    $email = $_POST['email'];

    try {
        // Mulai transaksi agar data konsisten
        $db->beginTransaction();

        // Update nama dan email di tabel pengguna
        $query = "UPDATE pengguna SET nama = ?, email = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$nama, $email, $user_id]);

        $db->commit();

        // Update data session agar nama di sidebar/topbar langsung berubah
        $_SESSION['nama'] = $nama;

        // Redirect kembali ke halaman edit dengan status sukses
        header("Location: edit_profil.php?status=success");
        exit();

    } catch (PDOException $e) {
        $db->rollBack();
        die("Gagal memperbarui profil: " . $e->getMessage());
    }
} else {
    // Jika diakses langsung tanpa POST, lempar balik ke dashboard
    header("Location: dashboard.php");
    exit();
}