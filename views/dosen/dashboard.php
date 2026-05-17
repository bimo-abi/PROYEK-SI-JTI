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
    <link rel="stylesheet" href="../../assets/css/mahasiswa_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>

        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>

            <div class="content mahasiswa-dashboard-content">
                <div class="section-title" style="margin-bottom: 24px;">Dashboard Dosen</div>
                
                <!-- Stats Section -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 32px;">
                    <div class="stat-card blue">
                        <i class="fas fa-procedures"></i>
                        <span class="label">Izin Sakit</span>
                        <span class="value"><?= $stats['sakit'] ?></span>
                    </div>
                    <div class="stat-card green">
                        <i class="fas fa-university"></i>
                        <span class="label">Kegiatan Kampus</span>
                        <span class="value"><?= $stats['kampus'] ?></span>
                    </div>
                    <div class="stat-card orange">
                        <i class="fas fa-globe"></i>
                        <span class="label">Kegiatan Luar</span>
                        <span class="value"><?= $stats['luar_kampus'] ?></span>
                    </div>
                </div>

                <div class="main-grid">
                    <!-- Kolom Kiri: Notif -->
                    <div class="left-column">
                        <div class="section-card" style="margin-bottom: 24px;">
                            <div class="section-title">
                                <i class="fas fa-bell text-primary"></i> Notifikasi Pengajuan Baru
                            </div>
                            <div class="notif-list">
                                <?php if (!empty($notifs)): ?>
                                    <?php foreach ($notifs as $n): ?>
                                        <div class="notif-item" style="display: flex; justify-content: space-between; align-items: center;">
                                            <div style="display: flex; gap: 16px; align-items: flex-start; flex: 1;">
                                                <div style="width: 40px; height: 40px; border-radius: 12px; background: var(--background); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                                    <i class="fas fa-info-circle"></i>
                                                </div>
                                                <div style="flex: 1;">
                                                    <a href="detail_mahasiswa.php?id=<?= $n['id_pengajuan'] ?>" style="text-decoration: none; color: inherit;">
                                                        <p style="margin: 0; font-weight: 600; font-size: 0.9375rem;">
                                                            <?= htmlspecialchars($n['nama_mhs'] ?? '') ?>
                                                        </p>
                                                    </a>
                                                    <small style="color: var(--text-muted);">Mengajukan: <?= htmlspecialchars($n['jenis_surat'] ?? '') ?></small>
                                                </div>
                                            </div>
                                            <div style="text-align: right; min-width: 100px;">
                                                <span style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;"><?= date('d M, H:i', strtotime($n['tanggal_pengajuan'])) ?></span>
                                                <span style="font-size: 0.7rem; background: #e3f2fd; color: #00a2ed; padding: 4px 8px; border-radius: 10px; font-weight: bold;">BARU</span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div style="text-align: center; color: var(--text-muted); padding: 20px;">
                                        <p>Belum ada pengajuan baru yang belum dibaca.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($notifs)): ?>
                            <div style="margin-top: 24px; text-align: center;">
                                <a href="data_mahasiswa.php" class="btn-primary" style="display: inline-flex; width: auto; padding: 12px 32px;">
                                    Lihat Semua Data <i class="fas fa-arrow-right" style="margin-left: 8px; margin-right: 0;"></i>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Info Profil -->
                    <div class="right-column">
                        <div class="profile-card-premium">
                            <div class="avatar-wrapper" style="position: relative; display: inline-block;">
                                <?php 
                                    $foto = !empty($dosen['foto_profil']) ? "../../assets/img/profiles/" . $dosen['foto_profil'] : "../../assets/img/profiles/avatar.jpg";
                                ?>
                                <img src="<?= $foto ?>?t=<?= time() ?>" alt="Dosen Avatar" class="avatar">
                                <div style="position: absolute; bottom: 15px; right: 5px; width: 24px; height: 24px; background: var(--success); border: 4px solid var(--surface); border-radius: 50%;"></div>
                            </div>
                            <h3 style="margin-bottom: 4px;"><?= htmlspecialchars($dosen['nama']) ?></h3>
                            <p style="color: var(--primary); font-weight: 700; font-size: 0.875rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 24px;">Dosen Aktif</p>
                            
                            <div style="text-align: left; background: var(--background); padding: 20px; border-radius: 20px;">
                                <div style="margin-bottom: 12px;">
                                    <small style="color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 0.7rem;">NIP/NIDN</small>
                                    <p style="margin: 0; font-weight: 600; font-size: 0.9rem;"><?= htmlspecialchars($dosen['nomor_induk'] ?? '-') ?></p>
                                </div>
                                <div>
                                    <small style="color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 0.7rem;">Email</small>
                                    <p style="margin: 0; font-weight: 600; font-size: 0.9rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($dosen['email'] ?? '-') ?></p>
                                </div>
                            </div>
                            
                            <a href="profil.php" class="btn-secondary" style="margin-top: 24px; width: 100%;">
                                <i class="fas fa-user-edit" style="margin-right: 8px;"></i> Edit Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>