<?php
require_once '../../autoload.php';
session_start();
use Config\Database;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();
$id_pengajuan = $_GET['id'] ?? null;

if (!$id_pengajuan) {
    header("Location: daftar_pengajuan.php");
    exit();
}

// Ambil detail surat
$query = "SELECT * FROM pengajuan_surat WHERE id_pengajuan = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id_pengajuan]);
$surat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$surat) {
    die("Data pengajuan tidak ditemukan.");
}

// is_read update removed due to database schema change
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Pengajuan - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>
            <div class="content">
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; max-width: 800px; margin: auto;">
                    <a href="daftar_pengajuan.php" style="text-decoration: none; color: #666; font-size: 0.9rem;">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                    
                    <h3 style="color: #333;">Detail Pengajuan Surat</h3>
                    <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 10px 0; width: 200px; color: #888;">Jenis Surat</td>
                            <td style="font-weight: bold;">: <?= htmlspecialchars($surat['jenis_surat']) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #888;">Tanggal Pengajuan</td>
                            <td>: <?= date('d F Y', strtotime($surat['tanggal_pengajuan'])) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #888;">Keperluan</td>
                            <td>: <?= htmlspecialchars($surat['keperluan'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; color: #888;">Status</td>
                            <td>: 
                                <span style="text-transform: uppercase; font-weight: bold; color: <?= ($surat['status'] == 'disetujui') ? 'green' : (($surat['status'] == 'ditolak') ? 'red' : 'orange') ?>;">
                                    <?= htmlspecialchars($surat['status']) ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>