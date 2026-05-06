<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../autoload.php';
session_start();

use Config\Database;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

try {
    // 1. Ambil NIM dari tabel detail_pengguna
    // Pastikan kolomnya 'nomor_induk' dan 'id_pengguna' sesuai User Summary
    $query_nim = "SELECT nomor_induk FROM detail_pengguna WHERE id_pengguna = ?";
    $stmt_nim = $db->prepare($query_nim);
    $stmt_nim->execute([$user_id]);
    $res_nim = $stmt_nim->fetch(PDO::FETCH_ASSOC);

    if (!$res_nim) {
        die("Error: Data NIM tidak ditemukan di tabel detail_pengguna untuk ID Pengguna: " . $user_id);
    }

    $nim = $res_nim['nomor_induk'];

    // 2. Ambil data dari tabel pengajuan_surat sesuai gambar database
    // Kita gunakan LEFT JOIN agar jika jenis_surat tidak ada, data pengajuan tetap muncul
    $query = "SELECT p.*, j.nama_surat 
              FROM pengajuan_surat p
              LEFT JOIN jenis_surat j ON p.jenis_surat = j.kode_surat 
              WHERE p.nim = ?
              ORDER BY p.tanggal_pengajuan DESC";

    $stmt = $db->prepare($query);
    $stmt->execute([$nim]);
    $daftar_surat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
} catch (Exception $e) {
    die("General Error: " . $e->getMessage());
}

$current_page = 'daftar_pengajuan';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daftar Pengajuan - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>

            <div class="content">
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h4><i class="fas fa-list-alt"></i> Riwayat Pengajuan Surat</h4>
                        <a href="pengajuan_surat.php" style="background: #00a2ed; color: white; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 0.9rem;">
                            <i class="fas fa-plus"></i> Ajukan Surat Baru
                        </a>
                    </div>

                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #eee; text-align: left;">
                                <th style="padding: 15px;">Tanggal</th>
                                <th style="padding: 15px;">Jenis Surat</th>
                                <th style="padding: 15px;">Alasan</th>
                                <th style="padding: 15px;">Status</th>
                                <th style="padding: 15px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($daftar_surat) > 0): ?>
                                <?php foreach ($daftar_surat as $surat): ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 15px;"><?= date('d M Y', strtotime($surat['tanggal_pengajuan'])) ?></td>
                                        <td style="padding: 15px; font-weight: bold;"><?= htmlspecialchars($surat['nama_surat']) ?></td>
                                        <td style="padding: 15px;"><?= htmlspecialchars(substr($surat['alasan'], 0, 30)) ?>...</td>
                                        <td style="padding: 15px;">
                                            <?php
                                            $badge_color = '';
                                            if ($surat['status'] == 'proses') $badge_color = '#ffc107'; // Kuning
                                            elseif ($surat['status'] == 'disetujui') $badge_color = '#28a745'; // Hijau
                                            else $badge_color = '#dc3545'; // Merah
                                            ?>
                                            <span style="background: <?= $badge_color ?>; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; text-transform: capitalize;">
                                                <?= $surat['status'] ?>
                                            </span>
                                        </td>
                                        <td style="padding: 15px;">
                                            <a href="detail_pengajuan.php?id=<?= $surat['id'] ?>" style="color: #00a2ed; text-decoration: none;">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 30px; color: #999;">Belum ada riwayat pengajuan surat.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>