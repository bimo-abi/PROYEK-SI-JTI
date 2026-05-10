<?php
require_once '../../autoload.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

use Config\Database;
$db = (new Database())->getConnection();

try {
    // Query untuk mengambil data pengajuan mahasiswa beserta detail profilnya
    $query = "SELECT p.*, u.nama, pr.nama_prodi, g.nama_golongan, d.nomor_induk 
              FROM pengajuan_surat p
              JOIN pengguna u ON p.nim = (SELECT nomor_induk FROM detail_pengguna WHERE id_pengguna = u.id)
              LEFT JOIN detail_pengguna d ON u.id = d.id_pengguna
              LEFT JOIN prodi pr ON d.id_prodi = pr.id
              LEFT JOIN golongan g ON d.id_golongan = g.id
              WHERE p.status = 'menunggu'
              ORDER BY p.tanggal_pengajuan ASC";
    
    $stmt = $db->query($query);
    $daftar_surat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Surat Masuk - Admin SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_admin.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar_admin.php'; ?>
            <div class="content">
                <div class="card-container" style="background: white; padding: 25px; border-radius: 15px;">
                    <h4><i class="fas fa-envelope-open-text"></i> Menunggu Verifikasi</h4>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        <thead>
                            <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                                <th style="padding: 12px;">Nama Mahasiswa</th>
                                <th style="padding: 12px;">NIM</th>
                                <th style="padding: 12px;">Jenis Surat</th>
                                <th style="padding: 12px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($daftar_surat) > 0): ?>
                                <?php foreach ($daftar_surat as $s): ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 12px;"><?= htmlspecialchars($s['nama']) ?></td>
                                        <td style="padding: 12px;"><?= htmlspecialchars($s['nomor_induk']) ?></td>
                                        <td style="padding: 12px;"><?= htmlspecialchars($s['jenis_surat']) ?></td>
                                        <td style="padding: 12px;">
                                            <a href="detail_verifikasi.php?id=<?= $s['id'] ?>" style="color: #00a2ed; text-decoration: none; font-weight: bold;">
                                                <i class="fas fa-search"></i> Periksa
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 30px; color: #999;">Tidak ada surat masuk yang perlu diproses.</td>
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