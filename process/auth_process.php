<?php
require_once '../autoload.php';
session_start();

use Config\Database;
use Classes\Validator;

$db = (new Database())->getConnection();

if (isset($_GET['action'])) {

    // --- LOGIKA REGISTRASI ---
    // --- LOGIKA REGISTRASI ---
    if ($_GET['action'] == 'register') {
        $nama = Validator::sanitize($_POST['nama']);
        $email = $_POST['email'];
        $nim_nip = $_POST['nomor_induk'];
        $id_prodi = $_POST['id_prodi'];
        $peran = $_POST['peran'];
        $pass = $_POST['password'];
        $confirm_pass = $_POST['confirm_password'];

        if ($pass !== $confirm_pass) {
            header("Location: ../views/auth/register.php?error=password");
            exit();
        }

        // --- CEK DUPLIKASI NIM ---
        // Kita gunakan COUNT(*) agar tidak bergantung pada nama kolom ID tertentu
        $checkQuery = "SELECT COUNT(*) FROM detail_pengguna WHERE nomor_induk = ?";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->execute([$nim_nip]);
        $rowCount = $checkStmt->fetchColumn(); // Mengambil hasil jumlah baris

        if ($rowCount > 0) {
            // Jika NIM ditemukan, kirim kembali ke register dengan pesan error
            header("Location: ../views/auth/register.php?error=duplicate_nim");
            exit();
        }

        $hashed_password = password_hash($pass, PASSWORD_BCRYPT);

        try {
            $db->beginTransaction();

            $sqlUser = "INSERT INTO pengguna (nama, email, kata_sandi, peran) VALUES (?, ?, ?, ?)";
            $stmtUser = $db->prepare($sqlUser);
            $stmtUser->execute([$nama, $email, $hashed_password, $peran]);

            $lastId = $db->lastInsertId();

            $sqlDetail = "INSERT INTO detail_pengguna (id_pengguna, id_prodi, nomor_induk) VALUES (?, ?, ?)";
            $stmtDetail = $db->prepare($sqlDetail);
            $stmtDetail->execute([$lastId, $id_prodi, $nim_nip]);

            $db->commit();
            header("Location: ../views/auth/login.php?status=registered");
        } catch (PDOException $e) {
            $db->rollBack();
            die("Registrasi Gagal: " . $e->getMessage());
        }
    }

    // --- LOGIKA LOGIN ---
    if ($_GET['action'] == 'login') {
        $identifier = $_POST['identifier'];
        $password = $_POST['password'];

        // Query JOIN untuk mengecek email di tabel pengguna ATAU NIM di tabel detail_pengguna
        $query = "SELECT p.* FROM pengguna p 
                  LEFT JOIN detail_pengguna d ON p.id = d.id_pengguna 
                  WHERE p.email = ? OR d.nomor_induk = ?";

        $stmt = $db->prepare($query);
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['kata_sandi'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['peran'] = $user['peran'];

            // Redirect otomatis ke dashboard sesuai peran
            header("Location: views/" . $user['peran'] . "/dashboard.php");
            exit();
        } else {
            header("Location: views/auth/login.php?status=failed");
            exit();
        }
    }
}
