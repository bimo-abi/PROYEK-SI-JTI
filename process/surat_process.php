<?php
require_once '../autoload.php';
session_start();

use Config\Database;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $db = (new Database())->getConnection();

    $id_pemohon = $_SESSION['user_id'];
    $id_jenis = $_POST['id_jenis_surat'];
    $keperluan = htmlspecialchars($_POST['keperluan']);

    // Default file name
    $file_name = null;

    // Handle File Upload
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
        $target_dir = "../assets/uploads/";
        $extension = pathinfo($_FILES["bukti"]["name"], PATHINFO_EXTENSION);
        $file_name = "BUKTI_" . time() . "." . $extension;
        $target_file = $target_dir . $file_name;

        // Validasi Ekstensi
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        if (in_array(strtolower($extension), $allowed)) {
            move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_file);
        }
    }

    // ... kode koneksi sebelumnya ...

    if (isset($_POST['ajukan_surat'])) {
        $id_pemohon = $_SESSION['user_id'];
        $id_jenis = $_POST['id_jenis_surat']; // Diambil dari <select> jenis surat
        $keperluan = htmlspecialchars($_POST['keperluan']);

        // Logic upload file bukti (Pastikan folder assets/uploads ada!)
        $nama_file = "";
        if (!empty($_FILES['bukti']['name'])) {
            $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
            $nama_file = "SURAT_" . time() . "." . $ext;
            move_uploaded_file($_FILES['bukti']['tmp_name'], "../assets/uploads/" . $nama_file);
        }

        try {
            $query = "INSERT INTO surat (id_pemohon, id_jenis_surat, keperluan, bukti_pendukung, status) 
                  VALUES (?, ?, ?, ?, 'tertunda')";
            $stmt = $db->prepare($query);
            $stmt->execute([$id_pemohon, $id_jenis, $keperluan, $nama_file]);

            header("Location: ../views/mahasiswa/dashboard.php?status=success");
        } catch (\PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    try {
        $query = "INSERT INTO surat (id_pemohon, id_jenis_surat, keperluan, bukti_pendukung, status) 
                  VALUES (?, ?, ?, ?, 'tertunda')";
        $stmt = $db->prepare($query);
        $stmt->execute([$id_pemohon, $id_jenis, $keperluan, $file_name]);

        header("Location: ../views/mahasiswa/riwayat.php?status=success");
    } catch (\PDOException $e) {
        die("Gagal mengirim surat: " . $e->getMessage());
    }
}
