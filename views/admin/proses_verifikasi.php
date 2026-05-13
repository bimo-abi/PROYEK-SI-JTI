<?php
require_once '../autoload.php';
$db = (new Config\Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_pengajuan'];
    $status = $_POST['status'];

    $query = "UPDATE pengajuan_surat SET status = ? WHERE id_pengajuan = ?";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$status, $id])) {
        header("Location: ../views/admin/dashboard.php?status=success");
    } else {
        echo "Gagal memperbarui status.";
    }
}