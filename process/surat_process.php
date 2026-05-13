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
    $keperluan = htmlspecialchars($_POST['keperluan']);

    // --- HANDLE FILE UPLOAD ---
    $file_db_name = null;
    // Ubah 'bukti' menjadi 'lampiran' jika di form pengajuan_surat.php name-nya adalah lampiran
    $input_name = isset($_FILES['bukti']) ? 'bukti' : 'lampiran';

    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
        // SESUAIKAN: Folder ke assets/uploads/pdf/ agar terbaca di dashboard
        $target_dir = "../assets/uploads/pdf/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES[$input_name]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($extension, $allowed)) {
            // Format Nama: 20260302_NIM_Jenis_Surat.pdf sesuai Figma
            $file_db_name = date('Ymd') . "_" . $nim . "_" . str_replace(' ', '_', $jenis_surat) . "." . $extension;
            $target_file = $target_dir . $file_db_name;

            if (!move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_file)) {
                die("Gagal mengunggah berkas ke server.");
            }
        } else {
            die("Format file tidak didukung.");
        }
    }

    // --- EKSEKUSI DATABASE ---
    try {
        // Gunakan nama kolom 'file_surat' agar sesuai dengan logika download kita
        $query = "INSERT INTO pengajuan_surat (nim, jenis_surat, keperluan, file_path, status, tanggal_pengajuan) 
                  VALUES (?, ?, ?, ?, 'menunggu', NOW())";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $nim, 
            $jenis_surat, 
            $keperluan, 
            $file_db_name // Nama file yang disimpan di folder assets/uploads/pdf/
        ]);

        header("Location: ../views/mahasiswa/daftar_pengajuan.php?status=success");
        exit();

    } catch (\PDOException $e) {
        if ($file_db_name && file_exists("../../assets/uploads/pdf/" . $file_db_name)) {
            unlink("../../assets/uploads/pdf/" . $file_db_name);
        }
        die("Gagal mengirim pengajuan: " . $e->getMessage());
    }
} else {
    header("Location: ../auth/login.php");
    exit();
}