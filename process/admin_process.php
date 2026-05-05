<?php
require_once '../autoload.php';
session_start();

use Config\Database;
use Classes\Admin;

if (isset($_POST['verifikasi_aksi']) && $_SESSION['peran'] == 'admin') {
    $db = (new Database())->getConnection();
    $admin = new Admin($db, $_SESSION['user_id']);
    
    $id_surat = $_POST['id_surat'];
    $status_baru = $_POST['status_aksi']; // 'disetujui' atau 'ditolak'
    $catatan = $_POST['catatan_admin'] ?? '';

    if ($admin->verifikasiSurat($id_surat, $status_baru, $catatan)) {
        header("Location: ../views/admin/daftar_surat.php?notif=updated");
    } else {
        header("Location: ../views/admin/daftar_surat.php?notif=error");
    }
}