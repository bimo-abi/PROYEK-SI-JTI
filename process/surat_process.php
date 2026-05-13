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
    
    // Tangani data dari form pengajuan yang baru (keterangan + tanggal)
    $keterangan = $_POST['keterangan'] ?? '';
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    
    if (!empty($keterangan) && !empty($tanggal_mulai) && !empty($tanggal_selesai)) {
        $keperluan_raw = "Tanggal: " . $tanggal_mulai . " s/d " . $tanggal_selesai . "\nKeterangan: " . $keterangan;
        $keperluan = htmlspecialchars($keperluan_raw);
    } else {
        $keperluan = htmlspecialchars($_POST['keperluan'] ?? '');
    }

   $input_name = 'file_pdf';
    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
    // Lokasi folder sesuai dengan image_55f4fc.png
    $target_dir = "../assets/uploads/pdf/";
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $extension = strtolower(pathinfo($_FILES[$input_name]["name"], PATHINFO_EXTENSION));
    
    // Pastikan hanya PDF yang masuk sesuai aturan baru kamu
    if ($extension == 'pdf') {
        // Format Nama: 20260513_NIM_Jenis_Surat.pdf
        $file_db_name = date('Ymd_His') . "_" . $nim . "_" . str_replace(' ', '_', $jenis_surat) . "." . $extension;
        $target_file = $target_dir . $file_db_name;

        if (!move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_file)) {
            die("Gagal mengunggah berkas ke server.");
        }
    } else {
        die("Hanya format PDF yang diperbolehkan untuk pengajuan surat.");
    }
} else {
    // Jika file tidak ada, kirim error agar kolom file_path tidak NULL
    die("Error: Lampiran PDF wajib diunggah.");
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