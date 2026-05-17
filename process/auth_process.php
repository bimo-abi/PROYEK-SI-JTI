<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../autoload.php';
session_start();
date_default_timezone_set('Asia/Jakarta');

use Config\Database;
use Classes\Validator;

$db = (new Database())->getConnection();

/**
 * Fungsi Helper untuk validasi email student polije
 */
function isStudentPolijeEmail($email)
{
    return preg_match("/^[a-zA-Z0-9._%+-]+@student\.polije\.ac\.id$/", $email);
}

if (isset($_GET['action'])) {

    $project_root = str_replace('/process', '', dirname($_SERVER['PHP_SELF']));

    // --- LOGIKA REGISTRASI ---
    // --- LOGIKA REGISTRASI (FIXED FOR DOSEN EMAIL) ---
    if ($_GET['action'] == 'register') {
        $nama = Validator::sanitize($_POST['nama']);
        $peran = $_POST['role'] ?? 'mahasiswa';

        // Tentukan email berdasarkan peran yang dipilih
        if ($peran === 'dosen') {
            $email = !empty($_POST['email_dosen']) ? Validator::sanitize($_POST['email_dosen']) : '';
        } else {
            $email = !empty($_POST['email']) ? Validator::sanitize($_POST['email']) : '';
        }

        // Validasi format khusus email mahasiswa
        if ($peran === 'mahasiswa' && !isStudentPolijeEmail($email)) {
            header("Location: " . $project_root . "/views/auth/register.php?error=email_format");
            exit();
        }

        if ($peran === 'dosen') {
            // Dosen menggunakan NIP yang di-input ke field 'nip'
            $nim_nip = !empty($_POST['nip']) ? Validator::sanitize($_POST['nip']) : null;
            $id_golongan = null;
            $id_prodi = null;
        } else {
            $nim_nip = !empty($_POST['nim']) ? Validator::sanitize($_POST['nim']) : null;
            $id_golongan = !empty($_POST['id_golongan']) ? $_POST['id_golongan'] : null;
            $id_prodi = !empty($_POST['id_prodi']) ? $_POST['id_prodi'] : null;
        }

        $pass = $_POST['password'];
        $confirm_pass = $_POST['confirm_password'];

        if (strlen($pass) < 8 || strlen($pass) > 12) {
            header("Location: " . $project_root . "/views/auth/register.php?error=password_length");
            exit();
        }

        if ($pass !== $confirm_pass) {
            header("Location: " . $project_root . "/views/auth/register.php?error=password");
            exit();
        }

        try {
            $db->beginTransaction();
            $checkEmail = "SELECT COUNT(*) FROM pengguna WHERE email = ?";
            $stmtEmail = $db->prepare($checkEmail);
            $stmtEmail->execute([$email]);

            if ($stmtEmail->fetchColumn() > 0) {
                header("Location: " . $project_root . "/views/auth/register.php?error=email");
                exit();
            }

            $hashed_password = password_hash($pass, PASSWORD_BCRYPT);
            $sqlUser = "INSERT INTO pengguna (nama, email, kata_sandi, peran) VALUES (?, ?, ?, ?)";
            $stmtUser = $db->prepare($sqlUser);
            $stmtUser->execute([$nama, $email, $hashed_password, $peran]);

            $lastId = $db->lastInsertId();
            $sqlDetail = "INSERT INTO detail_pengguna (id_pengguna, id_prodi, id_golongan, nomor_induk) VALUES (?, ?, ?, ?)";
            $stmtDetail = $db->prepare($sqlDetail);
            $stmtDetail->execute([$lastId, $id_prodi, $id_golongan, $nim_nip]);

            $db->commit();
            header("Location: " . $project_root . "/views/auth/login.php?status=registered");
            exit();
        } catch (PDOException $e) {
            $db->rollBack();
            die("Registrasi Gagal: " . $e->getMessage());
        }
    }

    // --- LOGIKA LOGIN ---
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
            $_SESSION['role']    = strtolower($user['peran']);
            $_SESSION['nim']     = $user['nomor_induk'];

            $roleFolder = $_SESSION['role'];
            header("Location: " . $project_root . "/views/" . $roleFolder . "/dashboard.php");
            exit();
        } else {
            header("Location: " . $project_root . "/views/auth/login.php?status=failed");
            exit();
        }
    }

    // --- LOGIKA MINTA RESET PASSWORD ---
    if ($_GET['action'] == 'proses_lupa_password') {
        $email = Validator::sanitize($_POST['email']);

        $stmt = $db->prepare("SELECT id FROM pengguna WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expired_at = date("Y-m-d H:i:s", strtotime('+1 hour'));

            try {
                $db->beginTransaction();

                // 1. TAMBAHKAN INI: Hapus token lama jika sudah ada (mencegah Duplicate Entry)
                $deleteOld = $db->prepare("DELETE FROM token_reset_sandi WHERE email = ?");
                $deleteOld->execute([$email]);

                // 2. Baru masukkan token yang baru
                $sql = "INSERT INTO token_reset_sandi (email, token, expired_at) VALUES (?, ?, ?)";
                $stmtToken = $db->prepare($sql);
                $stmtToken->execute([$email, $token, $expired_at]);

                $db->commit();

                header("Location: " . $project_root . "/views/auth/reset_password.php?token=" . $token);
                exit();
            } catch (Exception $e) {
                $db->rollBack();
                die("Gagal memproses token: " . $e->getMessage());
            }
        } else {
            header("Location: " . $project_root . "/views/auth/lupa_password.php?status=not_found");
            exit();
        }
    }

    //LOGIKA SIMPAN PASSWORD BARU
    if ($_GET['action'] == 'simpan_password_baru') {
        $token = $_POST['token'] ?? '';
        $pass = $_POST['password'];
        $confirm_pass = $_POST['confirm_password'];

        // Validasi Panjang 8-12 Karakter
        if (strlen($pass) < 8 || strlen($pass) > 12) {
            header("Location: " . $project_root . "/views/auth/reset_password.php?token=$token&error=password_length");
            exit();
        }

        if ($pass !== $confirm_pass) {
            header("Location: " . $project_root . "/views/auth/reset_password.php?token=$token&error=password");
            exit();
        }

        // Ambil email berdasarkan token yang belum expired
        $stmt = $db->prepare("SELECT email FROM token_reset_sandi WHERE token = ? AND expired_at > NOW()");
        $stmt->execute([$token]);
        $dataToken = $stmt->fetch();

        if ($dataToken) {
            $email = $dataToken['email'];
            $hashed_password = password_hash($pass, PASSWORD_BCRYPT);

            try {
                $db->beginTransaction();
                // 1. Update password di tabel pengguna
                $update = $db->prepare("UPDATE pengguna SET kata_sandi = ? WHERE email = ?");
                $update->execute([$hashed_password, $email]);

                // 2. Hapus token agar tidak bisa dipakai lagi
                $delete = $db->prepare("DELETE FROM token_reset_sandi WHERE email = ?");
                $delete->execute([$email]);

                $db->commit();
                header("Location: " . $project_root . "/views/auth/login.php?status=reset_success");
                exit();
            } catch (Exception $e) {
                $db->rollBack();
                die("Gagal reset password: " . $e->getMessage());
            }
        } else {
            echo "<script>
                alert('Token tidak valid atau sudah kadaluarsa!');
                window.location.href = '" . $project_root . "/views/auth/login.php';
              </script>";
            exit();
        }
    }

    if (isset($_GET['action']) && $_GET['action'] == 'update_profile') {
        // Pastikan session sudah berjalan
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user_id = $_SESSION['user_id'];
        $nama = $_POST['nama'];
        $email = $_POST['email'];

        try {
            // Query update terfokus hanya pada tabel pengguna (nama dan email)
            $sql = "UPDATE pengguna SET nama = ?, email = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([$nama, $email, $user_id]);

            if ($result) {
                // Berhasil, tendang balik ke halaman profil dengan status sukses
                header("Location: ../views/dosen/profil.php?status=success");
                exit();
            } else {
                header("Location: ../views/dosen/edit_profile.php?status=error");
                exit();
            }
        } catch (\PDOException $e) {
            // Jika ada error internal database, paksa tampilkan teks errornya agar tidak jadi halaman putih
            die("Error Database saat menyimpan data: " . $e->getMessage());
        }
    }
}
