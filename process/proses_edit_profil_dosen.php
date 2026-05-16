<?php
// Aktifkan pelaporan error untuk debugging jika terjadi kendala (Bisa dihapus jika sudah production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../autoload.php';
session_start();

use Config\Database;

// Pastikan request datang menggunakan metode POST dan user memiliki session valid sebagai dosen
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/auth/login.php");
    exit();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../views/auth/login.php");
    exit();
}

// Inisialisasi Koneksi Database
$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// Ambil dan bersihkan data inputan form
$nama = trim($_POST['nama']);
$email = trim($_POST['email']);

// Validasi sederhana agar inputan tidak kosong
if (empty($nama) || empty($email)) {
    header("Location: ../views/dosen/edit_profile.php?status=error");
    exit();
}

try {
    // Query update murni hanya ke tabel pengguna (nama & email) sesuai dengan skema DB aktif
    $sql = "UPDATE pengguna SET nama = ?, email = ? WHERE id = ?";
    $stmt = $db->prepare($sql);
    $result = $stmt->execute([$nama, $email, $user_id]);

    if ($result) {
        // Jika berhasil, kembalikan ke profil.php membawa status success
        header("Location: ../views/dosen/profil.php?status=success");
        exit();
    } else {
        // Jika gagal eksekusi tanpa throw error
        header("Location: ../views/dosen/edit_profile.php?status=error");
        exit();
    }

} catch (\PDOException $e) {
    // Menangkap error database dan menampilkannya agar tidak menjadi halaman putih polos
    die("Gagal memperbarui profil. Terjadi kesalahan database: " . $e->getMessage());
}