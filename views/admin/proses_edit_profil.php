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

        // 1. Tangkap NIM baru dari form
        $nim_baru = $_POST['nim'];
        $nim_lama = $_SESSION['nim']; 

        // 2. Update tabel UTAMA (pengguna)
        // Kita gunakan id sebagai acuan agar NIM (nomor_induk) bisa diubah
        $query = "UPDATE pengguna SET nama = ?, email = ?, nomor_induk = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$nama, $email, $nim_baru, $user_id]);

        // 3. Update tabel DETAIL (detail_pengguna) 
        // Sangat penting karena tabel ini menggunakan nomor_induk sebagai relasi
        $queryDetail = "UPDATE detail_pengguna SET nomor_induk = ? WHERE nomor_induk = ?";
        $stmtDetail = $db->prepare($queryDetail);
        $stmtDetail->execute([$nim_baru, $nim_lama]);

        $db->commit();

        // 4. Update session agar data terbaru langsung tampil
        $_SESSION['nama'] = $nama;
        $_SESSION['nim'] = $nim_baru;

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
