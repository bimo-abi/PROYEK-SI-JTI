<?php
require_once __DIR__ . '/../autoload.php';
session_start();

use Config\Database;
use Classes\Validator;

$db = (new Database())->getConnection();

if (isset($_GET['action'])) {

    // --- LOGIKA REGISTRASI ---
    if ($_GET['action'] == 'register') {
        // Sanitasi input menggunakan Validator yang sudah kita buat sebelumnya
        $nama = Validator::sanitize($_POST['nama']);
        $email = Validator::sanitize($_POST['email']);
        $nim_nip = Validator::sanitize($_POST['nomor_induk']);
        $id_prodi = $_POST['id_prodi'];
        $id_golongan = $_POST['id_golongan']; // Menangkap input golongan baru
        $peran = $_POST['peran'];
        $pass = $_POST['password'];
        $confirm_pass = $_POST['confirm_password'];

        // 1. Validasi kecocokan password
        if ($pass !== $confirm_pass) {
            header("Location: ../views/auth/register.php?error=password");
            exit();
        }

        // 2. Cek Duplikasi NIM/NIP di tabel detail_pengguna
        $checkQuery = "SELECT COUNT(*) FROM detail_pengguna WHERE nomor_induk = ?";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->execute([$nim_nip]);
        $rowCount = $checkStmt->fetchColumn();

        if ($rowCount > 0) {
            header("Location: ../views/auth/register.php?error=duplicate_nim");
            exit();
        }

        // 3. Hash password untuk keamanan
        $hashed_password = password_hash($pass, PASSWORD_BCRYPT);

        try {
            // Menggunakan Transaction agar data konsisten di dua tabel
            $db->beginTransaction();

            // INSERT ke tabel 'pengguna' (Data Akun Utama)
            $sqlUser = "INSERT INTO pengguna (nama, email, kata_sandi, peran) VALUES (?, ?, ?, ?)";
            $stmtUser = $db->prepare($sqlUser);
            $stmtUser->execute([$nama, $email, $hashed_password, $peran]);

            // Mengambil ID terakhir dari tabel pengguna untuk relasi
            $lastId = $db->lastInsertId();

            // INSERT ke tabel 'detail_pengguna' (Data Akademik + Golongan)
            $sqlDetail = "INSERT INTO detail_pengguna (id_pengguna, id_prodi, id_golongan, nomor_induk) VALUES (?, ?, ?, ?)";
            $stmtDetail = $db->prepare($sqlDetail);
            $stmtDetail->execute([$lastId, $id_prodi, $id_golongan, $nim_nip]);

            $db->commit();
            header("Location: ../views/auth/login.php?status=registered");
            exit();
        } catch (PDOException $e) {
            $db->rollBack();
            // Menampilkan pesan error teknis jika terjadi kegagalan sistem
            die("Registrasi Gagal: " . $e->getMessage());
        }
    }

    // --- LOGIKA LOGIN ---
    if ($_GET['action'] == 'login') {
        $identifier = Validator::sanitize($_POST['identifier']); // Bisa Email atau NIM
        $password = $_POST['password'];

        // Query JOIN untuk memungkinkan login via Email (tabel pengguna) atau NIM (tabel detail)
        $query = "SELECT p.* FROM pengguna p 
                  LEFT JOIN detail_pengguna d ON p.id = d.id_pengguna 
                  WHERE p.email = ? OR d.nomor_induk = ?";

        $stmt = $db->prepare($query);
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifikasi password hash
        if ($user && password_verify($password, $user['kata_sandi'])) {
            // Set session untuk identitas user yang login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['peran'] = $user['peran'];

            // Redirect otomatis ke folder dashboard sesuai role (mahasiswa/admin/dosen)
            header("Location: views/" . $user['peran'] . "/dashboard.php");
            exit();
        } else {
            // Jika gagal, kembali ke halaman login dengan status error
            header("Location: views/auth/login.php?status=failed");
            exit();
        }
    }

    // --- LOGIKA LOGOUT ---
    if ($_GET['action'] == 'logout') {
        // Mulai session jika belum ada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Hapus semua data session
        $_SESSION = [];

        // Hancurkan session
        session_destroy();

        // Redirect kembali ke halaman login
        header("Location: ../views/auth/login.php?status=logout");
        exit();
    }
}
