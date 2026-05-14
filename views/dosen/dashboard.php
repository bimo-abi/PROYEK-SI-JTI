<?php
require_once '../../autoload.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$current_page = 'dashboard';

use Config\Database;
$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// 1. Query Profil Dosen
$queryProfil = "SELECT p.nama, p.email, d.nomor_induk, d.foto_profil
                FROM pengguna p
                LEFT JOIN detail_pengguna d ON p.id = d.id_pengguna
                WHERE p.id = ?";
$stmt = $db->prepare($queryProfil);
$stmt->execute([$user_id]);
$dosen = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Statistik (Total Surat Berdasarkan Jenis)
$stats = [
    'sakit' => 0,
    'kampus' => 0,
    'luar_kampus' => 0
];

// Kita hitung semua surat yang sudah diverifikasi (karena dosen di halaman Data Mahasiswa hanya melihat yang terverifikasi)
$queryStats = "SELECT jenis_surat, COUNT(*) as total FROM pengajuan_surat WHERE status IN ('disetujui', 'terverifikasi') GROUP BY jenis_surat";
$resStats = $db->query($queryStats)->fetchAll(PDO::FETCH_ASSOC);
foreach ($resStats as $s) {
    $type = strtolower($s['jenis_surat']);
    if (isset($stats[$type])) {
        $stats[$type] = $s['total'];
    }
}

// 3. Query Notifikasi Baru (Belum dibaca oleh dosen)
// Kita ambil semua surat dengan status 'menunggu' sebagai notifikasi pengajuan baru
$queryNotif = "SELECT p.id_pengajuan, p.jenis_surat, p.tanggal_pengajuan, u.nama as nama_mhs 
               FROM pengajuan_surat p
               JOIN detail_pengguna d ON p.nim = d.nomor_induk
               JOIN pengguna u ON d.id_pengguna = u.id
               WHERE p.status = 'menunggu'
               ORDER BY p.tanggal_pengajuan DESC LIMIT 5";
$notifs = $db->query($queryNotif)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Dosen - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>

        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>

            <div class="content">
                <div class="main-grid">
                    <!-- Kolom Kiri: Statistik & Notif -->
                    <div class="left-column">
                        <div class="section-title"><i class="fas fa-home" style="margin-right: 10px;"></i> Dashboard Dosen</div>
                        
                        <div class="stats-grid">
                            <div class="stat-card blue"> Izin Sakit <span><?= $stats['sakit'] ?></span></div>
                            <div class="stat-card green"> Kegiatan Kampus <span><?= $stats['kampus'] ?></span></div>
                            <div class="stat-card orange"> Kegiatan Luar <span><?= $stats['luar_kampus'] ?></span></div>
                        </div>

                        <div class="notif-box" style="margin-top: 30px; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <h4 style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                                <i class="far fa-bell" style="color: #00a2ed;"></i> Notifikasi Pengajuan Baru
                            </h4>
                            <ul style="list-style: none; padding: 0;">
                                <?php if (!empty($notifs)): ?>
                                    <?php foreach ($notifs as $n): ?>
                                        <li style="padding: 12px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <a href="detail_mahasiswa.php?id=<?= $n['id_pengajuan'] ?>" style="text-decoration: none; color: #333; font-weight: bold; display: block;">
                                                    <?= htmlspecialchars($n['nama_mhs'] ?? '') ?>
                                                </a>
                                                <small style="color: #666;">Mengajukan: <?= htmlspecialchars($n['jenis_surat'] ?? '') ?></small>
                                            </div>
                                            <div style="text-align: right;">
                                                <span style="display: block; font-size: 0.75rem; color: #999;"><?= date('d M, H:i', strtotime($n['tanggal_pengajuan'])) ?></span>
                                                <span style="font-size: 0.7rem; background: #e3f2fd; color: #00a2ed; padding: 2px 8px; border-radius: 10px; font-weight: bold;">BARU</span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                    <li style="text-align: center; margin-top: 15px;">
                                        <a href="data_mahasiswa.php" style="color: #00a2ed; text-decoration: none; font-size: 0.85rem; font-weight: bold;">Lihat Semua Data <i class="fas fa-arrow-right"></i></a>
                                    </li>
                                <?php else: ?>
                                    <li style="color: #888; text-align: center; padding: 20px;">Belum ada pengajuan baru yang belum dibaca.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Info Profil -->
                    <div class="right-column">
                        <div class="profile-card">
                            <div class="avatar-wrapper">
                                <?php 
                                    $foto = !empty($dosen['foto_profil']) ? "../../assets/img/profiles/" . $dosen['foto_profil'] : "../../assets/img/profiles/avatar.jpg";
                                ?>
                                <img src="<?= $foto ?>?t=<?= time() ?>" alt="Dosen Avatar">
                            </div>
                            <p class="profile-name"><?= htmlspecialchars($dosen['nama']) ?></p>
                            <p class="profile-role">Dosen</p>
                        </div>

                        <div class="info-card">
                            <div class="info-header">
                                <h5>Info Profil</h5>
                            </div>
                            <div class="info-body">
                                <p><strong>NIP/NIDN :</strong> <?= htmlspecialchars($dosen['nomor_induk'] ?? '-') ?></p>
                                <p><strong>Email :</strong> <?= htmlspecialchars($dosen['email'] ?? '-') ?></p>
                                <p><strong>Status :</strong> Dosen Aktif</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>