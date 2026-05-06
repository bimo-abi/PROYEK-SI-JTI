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
    $query_nim = "SELECT nomor_induk FROM detail_pengguna WHERE id_pengguna = ?";
    $stmt_nim = $db->prepare($query_nim);
    $stmt_nim->execute([$user_id]);
    $res_nim = $stmt_nim->fetch(PDO::FETCH_ASSOC);

    if (!$res_nim) {
        die("Error: Data NIM tidak ditemukan di tabel detail_pengguna.");
    }

    $nim = $res_nim['nomor_induk'];

    // 2. Ambil data dari tabel pengajuan_surat
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
}

$current_page = 'daftar_pengajuan';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengajuan - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>

            <div class="content">
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <h4 style="margin: 0; color: #333;"><i class="fas fa-list-alt" style="color: #00a2ed;"></i> Riwayat Pengajuan Surat</h4>
                        <a href="pengajuan_surat.php" style="background: #00a2ed; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: 0.3s;">
                            <i class="fas fa-plus"></i> Ajukan Surat Baru
                        </a>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                            <thead>
                                <tr style="background: #f8f9fa; border-bottom: 2px solid #eee; text-align: left;">
                                    <th style="padding: 15px; color: #666; font-size: 0.9rem;">Tanggal</th>
                                    <th style="padding: 15px; color: #666; font-size: 0.9rem;">Jenis Surat</th>
                                    <th style="padding: 15px; color: #666; font-size: 0.9rem;">Alasan</th>
                                    <th style="padding: 15px; color: #666; font-size: 0.9rem;">Status</th>
                                    <th style="padding: 15px; color: #666; font-size: 0.9rem;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($daftar_surat) > 0): ?>
                                    <?php foreach ($daftar_surat as $surat): ?>
                                        <tr style="border-bottom: 1px solid #eee; transition: 0.2s;">
                                            <td style="padding: 15px; font-size: 0.9rem;"><?= date('d M Y', strtotime($surat['tanggal_pengajuan'])) ?></td>
                                            <td style="padding: 15px; font-weight: 600; color: #333; font-size: 0.9rem;">
                                                <?= htmlspecialchars($surat['nama_surat'] ?? 'Tidak Diketahui') ?>
                                            </td>
                                            <td style="padding: 15px; color: #777; font-size: 0.85rem;">
                                                <?= htmlspecialchars(substr($surat['alasan'], 0, 40)) ?><?= strlen($surat['alasan']) > 40 ? '...' : '' ?>
                                            </td>
                                            <td style="padding: 15px;">
                                                <?php
                                                $status = strtolower($surat['status']);
                                                $bg = '#ffc107'; // Default proses (Kuning)
                                                if ($status == 'disetujui') $bg = '#28a745';
                                                elseif ($status == 'ditolak') $bg = '#dc3545';
                                                ?>
                                                <span style="background: <?= $bg ?>; color: white; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 500; text-transform: uppercase;">
                                                    <?= htmlspecialchars($status) ?>
                                                </span>
                                            </td>
                                            <td style="padding: 15px;">
                                                <a href="detail_pengajuan.php?id=<?= $surat['id'] ?>" style="color: #00a2ed; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 40px; color: #aaa; font-style: italic;">
                                            <i class="fas fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                            Belum ada riwayat pengajuan surat.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script SweetAlert2 untuk menangkap notifikasi dari URL -->
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const pesan = urlParams.get('pesan');

        if (pesan === 'terkirim') {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Pengajuan surat Anda telah berhasil dikirim.',
                icon: 'success',
                confirmButtonColor: '#00a2ed'
            });
        }
    </script>
</body>
</html>