<?php
require_once '../../autoload.php';
session_start();

// Proteksi halaman
if (!isset($_SESSION['user_id']) || $_SESSION['peran'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

use Config\Database;
use Classes\Mahasiswa;

$db = (new Database())->getConnection();
// Mengambil data mahasiswa berdasarkan ID di session
$user = new Mahasiswa($db, $_SESSION['user_id']);
$stats = $user->getStatusSurat();
?>

<!-- Di bagian HTML Anda, ganti angka statis dengan ini: -->
<div class="card">
    <h3>Total Pengajuan</h3>
    <p><?php echo $stats['total']; ?></p>
</div>
<div class="card">
    <h3>Surat Disetujui</h3>
    <p><?php echo $stats['sukses']; ?></p>
</div>