<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

if (isset($_POST['upload']) && isset($_SESSION['user_id'])) {
    $db = (new Database())->getConnection();
    $user_id = $_SESSION['user_id'];

    $file_name = $_FILES['foto_profil']['name'];
    $file_tmp  = $_FILES['foto_profil']['tmp_name'];
    $file_size = $_FILES['foto_profil']['size'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // 1. Tentukan format yang dibolehkan
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    // 2. Validasi
    if (in_array($file_ext, $allowed_ext)) {
        if ($file_size < 2000000) { // Maksimal 2MB
            // Beri nama unik agar tidak bentrok (NIM_timestamp.ext)
            $new_name = $_SESSION['nim'] . "_" . time() . "." . $file_ext;
            $destination = "../../assets/img/profiles/" . $new_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                // 3. Update nama file di tabel detail_pengguna
                $sql = "UPDATE detail_pengguna SET foto_profil = ? WHERE id_pengguna = ?";
                $stmt = $db->prepare($sql);

                if ($stmt->execute([$new_name, $user_id])) {
                    header("Location: profil.php?status=photo_success");
                    $_SESSION['foto_profil'] = $new_name;

                    header("Location: profil.php?status=photo_success");
                } else {
                    echo "Gagal update database.";
                }
            } else {
                echo "Gagal mengunggah file ke folder.";
            }
        } else {
            echo "Ukuran file terlalu besar (Maks 2MB).";
        }
    } else {
        echo "Format file tidak didukung (Hanya JPG, JPEG, PNG).";
    }
}
