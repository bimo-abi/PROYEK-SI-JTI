<?php
require_once '../../autoload.php';
session_start();

use Config\Database;
$db = (new Database())->getConnection();
$nim = $_SESSION['nim'];

// Query mengambil riwayat pengajuan surat
$query = "SELECT * FROM pengajuan_surat WHERE nim = ? ORDER BY tanggal_pengajuan DESC";
$stmt = $db->prepare($query);
$stmt->execute([$nim]);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_page = 'daftar_surat';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pengajuan Surat - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>
            
            <div class="content">
                <div class="header-section">
                    <h4>Riwayat surat yang telah anda ajukan</h4>
                    <div class="filter-row">
                        <select><option>Semua jenis surat</option></select>
                        <select><option>Semua Status</option></select>
                        <input type="date">
                        <input type="text" placeholder="Cari...">
                    </div>
                </div>

                <div class="riwayat-container">
                    <?php if (count($riwayat) > 0): ?>
                        <?php foreach ($riwayat as $row): 
                            // Logika badge warna status
                            $badge_class = '';
                            $status_label = $row['status'];
                            
                            if ($row['status'] == 'disetujui') {
                                $badge_class = 'badge-terverifikasi';
                                $status_label = 'Terverifikasi';
                            } elseif ($row['status'] == 'ditolak') {
                                $badge_class = 'badge-ditolak';
                            } elseif ($row['status'] == 'diproses') {
                                $badge_class = 'badge-menunggu'; // Warna oranye
                                $status_label = 'Diproses';
                            } else {
                                $badge_class = 'badge-menunggu';
                                $status_label = 'Menunggu';
                            }
                        ?>
                            <div class="surat-item" style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #eee; border-radius: 10px; margin-bottom: 10px; background: white;">
                                <div class="file-info" style="display: flex; align-items: center; gap: 15px;">
                                    <i class="fas fa-file-pdf" style="color: #e74c3c; font-size: 1.5rem;"></i>
                                    <span style="font-size: 0.9rem; color: #333;"><?= htmlspecialchars($row['file_path']) ?></span>
                                </div>
                                <div class="action-status" style="display: flex; align-items: center; gap: 20px;">
                                    <?php if ($row['status'] == 'disetujui'): ?>
                                        <a href="../../storage/surat/<?= $row['file_path'] ?>" class="btn-unduh" download style="color: #00a2ed; text-decoration: none; font-size: 0.85rem;">
                                            <i class="fas fa-download"></i> Unduh
                                        </a>
                                    <?php endif; ?>
                                    <span class="badge <?= $badge_class ?>"><?= ucfirst($status_label) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center">Belum ada riwayat pengajuan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>