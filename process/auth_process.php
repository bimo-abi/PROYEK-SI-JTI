<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../autoload.php';
session_start();

use Config\Database;
use Classes\Validator;

$db = (new Database())->getConnection();

if (isset($_GET['action'])) {

    // --- LOGIKA REGISTRASI ---
    if ($_GET['action'] == 'register') {
        $nama = Validator::sanitize($_POST['nama']);
        $email = Validator::sanitize($_POST['email']);
        $nim_nip = Validator::sanitize($_POST['nomor_induk']);
        $id_prodi = $_POST['id_prodi'];
        $id_golongan = $_POST['id_golongan'];
        $peran = $_POST['role'] ?? ''; // Sesuai dengan name="role" di register.php
        $pass = $_POST['password'];
        $confirm_pass = $_POST['confirm_password'];

        if ($pass !== $confirm_pass) {
            header("Location: ../views/auth/register.php?error=password");
            exit();
        }

        try {
            $db->beginTransaction();
            $hashed_password = password_hash($pass, PASSWORD_BCRYPT);

            $sqlUser = "INSERT INTO pengguna (nama, email, kata_sandi, peran) VALUES (?, ?, ?, ?)";
            $stmtUser = $db->prepare($sqlUser);
            $stmtUser->execute([$nama, $email, $hashed_password, $peran]);

            $lastId = $db->lastInsertId();

            $sqlDetail = "INSERT INTO detail_pengguna (id_pengguna, id_prodi, id_golongan, nomor_induk) VALUES (?, ?, ?, ?)";
            $stmtDetail = $db->prepare($sqlDetail);
            $stmtDetail->execute([$lastId, $id_prodi, $id_golongan, $nim_nip]);

            $db->commit();
            header("Location: ../views/auth/login.php?status=registered");
            exit();
        } catch (PDOException $e) {
            $db->rollBack();
            die("Registrasi Gagal: " . $e->getMessage());
        }
    }
    // --- LOGIKA LOGIN ADMIN & MAHASISWA ---
    if ($_GET['action'] == 'login') {
        $identifier = Validator::sanitize($_POST['identifier']);
        $password = $_POST['password'];

        $query = "SELECT p.*, d.nomor_induk FROM pengguna p 
              LEFT JOIN detail_pengguna d ON p.id = d.id_pengguna 
              WHERE p.email = ? OR d.nomor_induk = ?";

        $stmt = $db->prepare($query);
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['kata_sandi'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['role']    = strtolower($user['peran']); // 'admin' atau 'mahasiswa'
            $_SESSION['nim']     = $user['nomor_induk'];

            // Menentukan folder tujuan berdasarkan peran
            $roleFolder = $_SESSION['role'];

            // Redirect otomatis (contoh: /PROYEK-SI-JTI/views/admin/dashboard.php)
            $project_root = str_replace('/process', '', dirname($_SERVER['PHP_SELF']));
            header("Location: " . $project_root . "/views/" . $roleFolder . "/dashboard.php");
            exit();
        } else {
            $project_root = str_replace('/process', '', dirname($_SERVER['PHP_SELF']));
            header("Location: " . $project_root . "/views/auth/login.php?status=failed");
            exit();
        }
    }

    // --- LOGIKA LOGOUT ---
    if ($_GET['action'] == 'logout') {
        session_unset();
        session_destroy();
        header("Location: ../views/auth/login.php?status=logout");
        exit();
    }
}
