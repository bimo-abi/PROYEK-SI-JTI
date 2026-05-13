<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();
$current_page = 'data_mahasiswa';

// Ambil data pengajuan yang sudah diproses (Disetujui/Ditolak/Terverifikasi)
$query = "SELECT p.*, u.nama, pr.nama_prodi, g.nama_golongan, d.semester
          FROM pengajuan_surat p
          JOIN detail_pengguna d ON p.nim = d.nomor_induk
          JOIN pengguna u ON d.id_pengguna = u.id
          LEFT JOIN prodi pr ON d.id_prodi = pr.id
          LEFT JOIN golongan g ON d.id_golongan = g.id
          WHERE p.status IN ('disetujui', 'ditolak', 'terverifikasi')
          ORDER BY p.tanggal_pengajuan DESC";

$stmt = $db->query($query);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa - Dosen SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>
            
            <div class="content">
                <div class="card-container" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h4><i class="fas fa-users" style="color: #00a2ed;"></i> Data Mahasiswa</h4>
                        <input type="text" id="searchInput" placeholder="Cari mahasiswa..." style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; width: 250px;">
                    </div>
                    
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                                <th style="padding: 12px;">No</th>
                                <th style="padding: 12px;">Nama</th>
                                <th style="padding: 12px;">Prodi</th>
                                <th style="padding: 12px;">Gol</th>
                                <th style="padding: 12px;">NIM</th>
                                <th style="padding: 12px;">Semester</th>
                                <th style="padding: 12px; text-align: center;">Surat</th>
                                <th style="padding: 12px; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($riwayat) > 0): ?>
                                <?php $no = 1; foreach ($riwayat as $row): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 12px;"><?= $no++ ?></td>
                                    <td style="padding: 12px; font-weight: 500;"><?= $row['nama'] ?></td>
                                    <td style="padding: 12px;"><?= $row['nama_prodi'] ?? '-' ?></td>
                                    <td style="padding: 12px;"><?= $row['nama_golongan'] ?? '-' ?></td>
                                    <td style="padding: 12px;"><?= $row['nim'] ?></td>
                                    <td style="padding: 12px;"><?= $row['semester'] ?></td>
                                    <td style="padding: 12px; text-align: center;">
                                        <?php if (!empty($row['file_path'])): ?>
                                            <a href="../../assets/uploads/pdf/<?= $row['file_path'] ?>" target="_blank" style="color: #ff4757; text-decoration: none; font-weight: bold;">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #ccc;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px; text-align: center;">
                                        <?php 
                                            $status = strtolower($row['status']);
                                            $is_ok = ($status == 'disetujui' || $status == 'terverifikasi');
                                            $color = $is_ok ? '#28a745' : '#dc3545';
                                            $label = $is_ok ? 'VERIFIKASI' : 'DITOLAK';
                                        ?>
                                        <span style="background: <?= $color ?>; color: white; padding: 5px 12px; border-radius: 5px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">
                                            <?= $label ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 20px; color: #999;">Belum ada riwayat data mahasiswa.</td>
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