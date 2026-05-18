<?php
require_once '../autoload.php';
session_start();

use Config\Database;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $db = (new Database())->getConnection();

    // Pastikan NIM tersedia
    if (!isset($_SESSION['nim'])) {
        $stmt = $db->prepare("SELECT nomor_induk FROM detail_pengguna WHERE id_pengguna = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $_SESSION['nim'] = $data['nomor_induk'];
        } else {
            die("Error: Data NIM tidak ditemukan.");
        }
    }

    $nim = $_SESSION['nim'];
    $jenis_surat = $_POST['jenis_surat'] ?? 'Umum';

    // 1. TANGKAP DATA FORM SECARA TERPISAH
    $keterangan = $_POST['keterangan'] ?? '';
    $tgl_mulai = $_POST['tgl_mulai'] ?? '';
    $tgl_selesai = $_POST['tgl_selesai'] ?? '';

    // --- Bagian variabel $keperluan di sini sudah DIHAPUS ---

    // 2. PROSES UPLOAD PDF
    $input_name = 'file_pdf';
    $file_db_name = null;

    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
        $target_dir = "../assets/uploads/pdf/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES[$input_name]["name"], PATHINFO_EXTENSION));

        if ($extension == 'pdf') {
            $file_db_name = date('Ymd_His') . "_" . $nim . "_" . str_replace(' ', '_', $jenis_surat) . "." . $extension;
            $target_file = $target_dir . $file_db_name;

            if (!move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_file)) {
                die("Gagal mengunggah berkas ke server.");
            }
        } else {
            die("Hanya format PDF yang diperbolehkan.");
        }
    } else {
        die("Error: Lampiran PDF wajib diunggah.");
    }

    // 3. EKSEKUSI DATABASE TANPA KOLOM KEPERLUAN
    try {
        // FIX: Kolom 'keperluan' dan satu tanda tanya (?) di VALUES telah dihapus dari query
        $query = "INSERT INTO pengajuan_surat (nim, jenis_surat, tgl_mulai, tgl_selesai, keterangan, file_path, status, tanggal_pengajuan) 
                  VALUES (?, ?, ?, ?, ?, ?, 'menunggu', NOW())";

        $stmt = $db->prepare($query);
        $stmt->execute([
            $nim,
            $jenis_surat,
            $tgl_mulai,    // Masuk ke kolom tgl_mulai
            $tgl_selesai,  // Masuk ke kolom tgl_selesai
            $keterangan,   // Masuk ke kolom keterangan (menggantikan peran keperluan)
            $file_db_name  // Masuk ke kolom file_path
        ]);

        header("Location: ../views/mahasiswa/daftar_pengajuan.php?status=success");
        exit();
    } catch (\PDOException $e) {
        if ($file_db_name && file_exists("../assets/uploads/pdf/" . $file_db_name)) {
            unlink("../assets/uploads/pdf/" . $file_db_name);
        }
        die("Gagal mengirim pengajuan: " . $e->getMessage());
    }
} else {
    header("Location: ../auth/login.php");
    exit();
}
