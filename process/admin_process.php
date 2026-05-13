<?php
require_once '../autoload.php';
session_start();

use Config\Database;
use Classes\Admin;

$db = (new Database())->getConnection();
$action = $_GET['action'] ?? '';

// --- LOGIKA UPDATE PROFIL (DOSEN & ADMIN) ---
if ($action == 'update_profile') {
    $id_pengguna = $_SESSION['user_id'];
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $email = $_POST['email'] ?? '';
    $nomor_telepon = $_POST['nomor_telepon'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $foto_lama = $_SESSION['foto_profil'] ?? '';

    try {
        $db->beginTransaction();

        // 1. Update Tabel Pengguna (Nama & Email)
        $queryUser = "UPDATE pengguna SET nama = ?, email = ? WHERE id = ?";
        $stmtUser = $db->prepare($queryUser);
        $stmtUser->execute([$nama, $email, $id_pengguna]);

        // 2. Handle Upload Foto
        $foto_final = $foto_lama;
        // ... (foto handling remains the same)
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
            $target_dir = "../assets/img/profiles/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $file_extension = pathinfo($_FILES["foto_profil"]["name"], PATHINFO_EXTENSION);
            $new_filename = "profile_" . $id_pengguna . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $new_filename;
            if (move_uploaded_file($_FILES["foto_profil"]["tmp_name"], $target_file)) {
                $foto_final = $new_filename;
                if ($foto_lama && !in_array($foto_lama, ['avatar.png', 'avatar.jpg']) && file_exists($target_dir . $foto_lama)) {
                    unlink($target_dir . $foto_lama);
                }
            }
        }

        // 3. Update Tabel Detail_Pengguna (Foto, Telepon, Alamat)
        // NIP tidak diupdate agar tetap sesuai data di database
        $queryDetail = "UPDATE detail_pengguna SET foto_profil = ?, nomor_telepon = ?, alamat = ? WHERE id_pengguna = ?";
        $stmtDetail = $db->prepare($queryDetail);
        $stmtDetail->execute([$foto_final, $nomor_telepon, $alamat, $id_pengguna]);

        // 4. Update Session agar tampilan langsung berubah
        $_SESSION['nama'] = $nama;
        $_SESSION['foto_profil'] = $foto_final;

        $db->commit();
        
        // Redirect kembali ke halaman profil dengan status sukses
        $redirect = ($_SESSION['role'] == 'dosen') ? '../views/dosen/profil.php' : '../views/admin/edit_profile.php';
        header("Location: " . $redirect . "?status=success");
        exit();

    } catch (Exception $e) {
        $db->rollBack();
        die("Terjadi kesalahan: " . $e->getMessage());
    }
}

// --- LOGIKA VERIFIKASI SURAT (KODE LAMA KAMU) ---
if (isset($_POST['verifikasi_aksi']) && $_SESSION['role'] == 'admin') {
    $admin = new Admin($db, $_SESSION['user_id']);
    
    $id_surat = $_POST['id_surat'];
    $status_baru = $_POST['status_aksi'];
    $catatan = $_POST['catatan_admin'] ?? '';

    if ($admin->verifikasiSurat($id_surat, $status_baru, $catatan)) {
        header("Location: ../views/admin/daftar_surat.php?notif=updated");
    } else {
        header("Location: ../views/admin/daftar_surat.php?notif=error");
    }
    exit();
}