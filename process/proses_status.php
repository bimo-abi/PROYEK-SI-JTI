<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

// Pastikan hanya admin yang bisa mengakses file ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pengajuan'])) {
    $db = (new Database())->getConnection();
    
    $id = $_POST['id_pengajuan'];
    $aksi = $_POST['aksi']; // Isinya 'terima' atau 'tolak'
    
    // Tentukan status berdasarkan tombol yang diklik
    $status_baru = ($aksi === 'terima') ? 'disetujui' : 'ditolak';
    
    try {
        $query = "UPDATE pengajuan_surat SET status = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$status_baru, $id])) {
            // Jika berhasil, arahkan ke halaman riwayat dengan notifikasi sukses
            header("Location: ../views/admin/riwayat.php?status=updated");
            exit();
        } else {
            header("Location: ../views/admin/surat_masuk.php?status=error");
            exit();
        }
    } catch (PDOException $e) {
        die("Gagal memperbarui status: " . $e->getMessage());
    }
} else {
    // Jika mencoba akses langsung tanpa POST
    header("Location: ../views/admin/surat_masuk.php");
    exit();
}