<?php
require_once '../../autoload.php';
session_start();
use Config\Database;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_pengajuan'])) {
    $db = (new Database())->getConnection();
    
    $id = $_POST['id_pengajuan'];
    $status_baru = $_POST['status']; // 'disetujui' atau 'ditolak'

    try {
        $query = "UPDATE pengajuan_surat SET status = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$status_baru, $id]);

        header("Location: surat_masuk.php?verif=success");
    } catch (PDOException $e) {
        die("Gagal update status: " . $e->getMessage());
    }
}