<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

// 1. Proteksi Halaman Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();

// 2. Ambil data surat yang statusnya 'proses'
try {
    $query = "SELECT p.*, u.nama, pr.nama_prodi, g.nama_golongan 
              FROM pengajuan_surat p
              JOIN detail_pengguna dp ON p.nim = dp.nomor_induk
              JOIN pengguna u ON dp.id_pengguna = u.id
              LEFT JOIN prodi pr ON dp.id_prodi = pr.id
              LEFT JOIN golongan g ON dp.id_golongan = g.id
              WHERE p.status = 'proses'
              ORDER BY p.tanggal_pengajuan ASC";

    $stmt = $db->query($query);
    $surat_masuk = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}

// Konfigurasi Layout
$current_page = 'surat_masuk';
$page_title = 'Daftar Surat Masuk';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Masuk - Admin SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="margin: 0; background: #f4f7f6; font-family: 'Segoe UI', sans-serif;">
    <div class="wrapper" style="display: flex;">
        <?php include '../layouts/sidebar_admin.php'; ?>

        <div class="main-container" style="flex-grow: 1; min-height: 100vh;">
            <?php include '../layouts/topbar_admin.php'; ?>

            <div class="content" style="padding: 30px;">
                <div class="card-container" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <div style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                        <h4 style="display: flex; align-items: center; gap: 10px; color: #333; margin: 0;">
                            <i class="fas fa-envelope-open-text" style="color: #00a2ed;"></i> Menunggu Verifikasi
                        </h4>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
                            <thead>
                                <tr style="background: #f8f9fa; text-align: left;">
                                    <th style="padding: 15px; border-bottom: 2px solid #eee; color: #666;">No</th>
                                    <th style="padding: 15px; border-bottom: 2px solid #eee; color: #666;">Nama</th>
                                    <th style="padding: 15px; border-bottom: 2px solid #eee; color: #666;">Prodi</th>
                                    <th style="padding: 15px; border-bottom: 2px solid #eee; color: #666;">Gol</th>
                                    <th style="padding: 15px; border-bottom: 2px solid #eee; color: #666;">NIM</th>
                                    <th style="padding: 15px; border-bottom: 2px solid #eee; color: #666;">Surat</th>
                                    <th style="padding: 15px; border-bottom: 2px solid #eee; color: #666; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($surat_masuk) > 0): ?>
                                    <?php $no = 1; foreach ($surat_masuk as $row): ?>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 15px;"><?= $no++ ?>.</td>
                                            <td style="padding: 15px; font-weight: 600; color: #333;"><?= htmlspecialchars($row['nama']) ?></td>
                                            <td style="padding: 15px; color: #555;"><?= htmlspecialchars($row['nama_prodi']) ?></td>
                                            <td style="padding: 15px; text-align: center;"><?= htmlspecialchars($row['nama_golongan'] ?? '-') ?></td>
                                            <td style="padding: 15px; font-family: monospace;"><?= htmlspecialchars($row['nim']) ?></td>
                                            <td style="padding: 15px; text-align: center;">
                                                <i class="fas fa-file-pdf" style="color: #e74c3c;"></i> .pdf
                                            </td>
                                            <td style="padding: 15px; text-align: center;">
                                                <a href="detail_pengajuan.php?id=<?= $row['id'] ?>" 
                                                   style="background: #00a2ed; color: white; padding: 7px 15px; border-radius: 8px; text-decoration: none; font-size: 0.8rem; font-weight: 600;">
                                                    Lihat Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 40px; color: #888; font-style: italic;">
                                            Tidak ada surat masuk yang perlu diproses.
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
</body>
</html>