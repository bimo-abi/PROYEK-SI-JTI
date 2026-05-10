<?php
require_once '../autoload.php';
session_start();

use Config\Database;

// Pastikan hanya request POST dan user sudah login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $db = (new Database())->getConnection();

    // --- VALIDASI SESSION NIM ---
    // Jika NIM tidak ada di session, kita coba ambil manual dari database 
    // Ini adalah "Jaring Pengaman" jika user lupa logout-login
    if (!isset($_SESSION['nim'])) {
        $stmt = $db->prepare("SELECT nomor_induk FROM detail_pengguna WHERE id_pengguna = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $data = $stmt->fetch();
        if ($data) {
            $_SESSION['nim'] = $data['nomor_induk'];
        } else {
            die("Error: Data NIM tidak ditemukan. Silakan hubungi admin atau login ulang.");
        }
    }

    // 1. Ambil Data
    $nim = $_SESSION['nim']; 
    $jenis_surat = $_POST['jenis_surat'] ?? 'Umum'; 
    $keperluan = htmlspecialchars($_POST['keperluan']);

    // 2. Handle File Upload (Bukti Pendukung)
    $file_db_name = null;
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
        // Gunakan realpath atau path yang konsisten
        $target_dir = __DIR__ . "/../storage/surat/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES["bukti"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($extension, $allowed)) {
            // Format Nama: TANGGAL_NIM_JENIS.EXT
            $file_db_name = date('Ymd_His') . "_" . $nim . "_" . str_replace(' ', '_', $jenis_surat) . "." . $extension;
            $target_file = $target_dir . $file_db_name;

            if (!move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_file)) {
                die("Gagal mengunggah berkas ke server. Pastikan folder storage memiliki izin akses.");
            }
        } else {
            die("Format file tidak didukung (Gunakan: JPG, PNG, atau PDF).");
        }
    }

    // 3. Eksekusi Query ke Database
    try {
        // Sesuai dengan struktur tabel SI-JTI
        $query = "INSERT INTO pengajuan_surat (nim, jenis_surat, keperluan, file_path, status, tanggal_pengajuan) 
                  VALUES (?, ?, ?, ?, 'menunggu', NOW())";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $nim, 
            $jenis_surat, 
            $keperluan, 
            $file_db_name
        ]);

        // Redirect menggunakan Path Otomatis (agar tidak 404)
        $project_root = str_replace('/process', '', dirname($_SERVER['PHP_SELF']));
        header("Location: " . $project_root . "/views/mahasiswa/daftar_pengajuan.php?status=success");
        exit();

    } catch (\PDOException $e) {
        // Hapus file jika query database gagal
        if ($file_db_name && file_exists(__DIR__ . "/../storage/surat/" . $file_db_name)) {
            unlink(__DIR__ . "/../storage/surat/" . $file_db_name);
        }
        die("Gagal mengirim pengajuan: " . $e->getMessage());
    }
} else {
    header("Location: ../views/auth/login.php");
    exit();
}