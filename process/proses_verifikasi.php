<?php
require_once '../autoload.php';
use Config\Database;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = (new Database())->getConnection();
    $id = $_POST['id_pengajuan'];
    $status = $_POST['status']; // 'terverifikasi' atau 'ditolak'

    $query = "UPDATE pengajuan_surat SET status = ? WHERE id_pengajuan = ?";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$status, $id])) {
        header("Location: ../views/admin/surat_masuk.php?msg=success");
    } else {
        echo "Gagal memperbarui status.";
    }
}