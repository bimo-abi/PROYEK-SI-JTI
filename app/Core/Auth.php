<?php
class Auth {
    /**
     * Memulai session jika belum dimulai
     */
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Menyimpan data user ke session setelah login berhasil
     */
    public function setSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['peran']   = $user['peran'];
        $_SESSION['is_login'] = true;
    }

    /**
     * Mengecek apakah user sudah login
     */
    public static function check() {
        if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
            header("Location: login.php");
            exit;
        }
    }

    /**
     * Proteksi halaman berdasarkan peran (Admin/Dosen/Mahasiswa)
     */
    public static function role($role) {
        if ($_SESSION['peran'] !== $role) {
            die("Akses Ditolak: Anda bukan " . $role);
        }
    }

    /**
     * Logout dan hapus semua data session
     */
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }
}