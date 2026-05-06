<?php
require_once '../autoload.php';
session_start();

use Config\Database;

// Pastikan hanya request POST dan user sudah login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $db = (new Database())->getConnection();

    // 1. Ambil Data dari Session dan Form
    // Kita gunakan NIM karena Foreign Key di tabel pengajuan_surat merujuk ke nomor_induk
    $nim = $_SESSION['nim']; 
    $jenis_surat = $_POST['jenis_surat']; // Dari hidden input form_pengajuan.php
    $keperluan = htmlspecialchars($_POST['keperluan']);

    // 2. Handle File Upload (Bukti Pendukung)
    $file_db_name = null;
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
        $target_dir = "../storage/surat/";
        
        // Buat folder jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES["bukti"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($extension, $allowed)) {
            // Format Nama: TANGGAL_NIM_JENIS.EXT
            $file_db_name = date('Ymd_His') . "_" . $nim . "_" . $jenis_surat . "." . $extension;
            $target_file = $target_dir . $file_db_name;

            if (!move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_file)) {
                die("Gagal mengunggah berkas ke server.");
            }
        } else {
            die("Format file tidak didukung (Gunakan: JPG, PNG, atau PDF).");
        }
    }

    // 3. Eksekusi Query ke Database
    try {
        // Menggunakan nama tabel 'pengajuan_surat' sesuai SQL yang kita buat tadi
        $query = "INSERT INTO pengajuan_surat (nim, jenis_surat, keperluan, file_path, status) 
                  VALUES (?, ?, ?, ?, 'menunggu')";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $nim, 
            $jenis_surat, 
            $keperluan, 
            $file_db_name
        ]);

        // Redirect ke daftar pengajuan dengan notifikasi sukses
        header("Location: ../views/mahasiswa/daftar_pengajuan.php?status=success");
        exit();

    } catch (\PDOException $e) {
        // Jika gagal, hapus file yang sudah terlanjur diupload (opsional untuk kebersihan server)
        if ($file_db_name && file_exists("../storage/surat/" . $file_db_name)) {
            unlink("../storage/surat/" . $file_db_name);
        }
        die("Gagal mengirim pengajuan: " . $e->getMessage());
    }
} else {
    // Jika akses ilegal (bukan POST)
    header("Location: ../views/mahasiswa/dashboard.php");
    exit();
}