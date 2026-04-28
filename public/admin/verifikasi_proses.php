<?php
require_once '../../config/Database.php';
require_once '../../app/Models/Admin.php';
require_once '../../app/Core/Auth.php';

Auth::check();
Auth::role('admin');

$db = (new Database())->getConnection();
$adminModel = new Admin($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $id_surat = $_POST['id_surat'];
        $status   = $_POST['status']; // 'disetujui' atau 'ditolak'
        $catatan  = $_POST['catatan'] ?? '';

        // Validasi sederhana
        if ($status == 'ditolak' && empty($catatan)) {
            throw new Exception("Jika menolak, alasan/catatan harus diisi!");
        }

        if ($adminModel->verifikasi($id_surat, $status, $catatan)) {
            echo "Status surat berhasil diperbarui.";
            // Nantinya bisa redirect: header("Location: dashboard.php");
        } else {
            throw new Exception("Gagal memperbarui status surat.");
        }

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}