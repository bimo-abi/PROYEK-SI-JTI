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

    $allowed_ext = ['jpg', 'jpeg', 'png'];

    if (in_array($file_ext, $allowed_ext)) {
        if ($file_size < 2000000) {
            $new_name = $user_id . "_" . time() . "." . $file_ext;
            $destination = "../../assets/img/profiles/" . $new_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                $sql = "UPDATE detail_pengguna SET foto_profil = ? WHERE id_pengguna = ?";
                $stmt = $db->prepare($sql);

                if ($stmt->execute([$new_name, $user_id])) {
                    $_SESSION['foto_profil'] = $new_name;
                    header("Location: profil.php?status=success");
                    exit();
                } else {
                    header("Location: profil.php?status=error");
                    exit();
                }
            }
        }
    }
}
header("Location: profil.php");
exit();
