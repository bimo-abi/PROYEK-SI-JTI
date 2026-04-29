<?php
class Auth {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Menyimpan data user ke session setelah login berhasil
    public function setSession($user) {
        $_SESSION['is_login'] = true;
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['nama']     = $user['nama'];
        $_SESSION['peran']    = $user['peran'];
        $_SESSION['nim_nip']  = $user['nomor_induk'];
    }

    // Proteksi halaman: Tendang ke login jika belum masuk
    public static function check() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
            header("Location: ../login.php");
            exit;
        }
    }

    // Proteksi Role: Batasi akses per halaman (Admin/Dosen/Mahasiswa)
    public static function role($role) {
        if ($_SESSION['peran'] !== $role) {
            die("<div style='color:red; font-family:sans-serif; text-align:center; margin-top:50px;'>
                    <h2>Akses Ditolak!</h2>
                    <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
                    <a href='../logout.php'>Kembali</a>
                 </div>");
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }
}