<?php
session_start();
require_once '../autoload.php';

use Config\Database;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = (new Database())->getConnection();
    $user_id = $_SESSION['user_id'];

    // Ambil data hanya yang diizinkan (Nama & Email)
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';

    try {
        $db->beginTransaction();

        // 1. Update Tabel Pengguna (Data Utama)
        $query_user = "UPDATE pengguna SET nama = ?, email = ? WHERE id = ?";
        $stmt_user = $db->prepare($query_user);
        $stmt_user->execute([$nama, $email, $user_id]);

        // 2. Logika Pemrosesan Foto Profil
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === 0) {
            $file_tmp = $_FILES['foto_profil']['tmp_name'];
            $file_ext = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);

            // Penamaan file unik untuk menghindari cache browser
            $new_file_name = "admin_" . $user_id . "_" . time() . "." . $file_ext;
            $upload_path = "../assets/img/profiles/" . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Update kolom foto_profil di tabel detail_pengguna
                $query_foto = "UPDATE detail_pengguna SET foto_profil = ? WHERE id_pengguna = ?";
                $stmt_foto = $db->prepare($query_foto);
                $stmt_foto->execute([$new_file_name, $user_id]);

                // Update Session agar Sidebar & Topbar sinkron seketika
                $_SESSION['foto_profil'] = $new_file_name;
            }
        }

        // 3. Update Session Nama
        $_SESSION['nama'] = $nama;

        $db->commit();

        // REDIRECT OTOMATIS: Langsung ke Dashboard setelah berhasil
        header("Location: ../views/admin/dashboard.php?status=success");
        exit();
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        header("Location: ../views/admin/edit_profil.php?status=error");
        exit();
    }
}
