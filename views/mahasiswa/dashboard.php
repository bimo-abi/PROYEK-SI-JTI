<?php
require_once '../../autoload.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

use Config\Database;

// --- LOGIC SECTION ---
$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// 1. Fetch Profile Data
$queryProfil = "SELECT p.nama, p.email, d.nomor_induk, pr.nama_prodi, g.nama_golongan 
                FROM pengguna p
                LEFT JOIN detail_pengguna d ON p.id = d.id_pengguna
                LEFT JOIN prodi pr ON d.id_prodi = pr.id
                LEFT JOIN golongan g ON d.id_golongan = g.id
                WHERE p.id = ?";

$stmt = $db->prepare($queryProfil);
$stmt->execute([$user_id]);
$mhs = $stmt->fetch(PDO::FETCH_ASSOC);

$nim_mhs = $mhs['nomor_induk'] ?? ($_SESSION['nim'] ?? '');

// 2. Fetch Statistics
$queryStats = "SELECT 
    COUNT(*) as total,
    SUM(status = 'disetujui') as diterima,
    SUM(status = 'menunggu') as proses,
    SUM(status = 'ditolak') as ditolak
    FROM pengajuan_surat WHERE nim = ?";

$stmtStats = $db->prepare($queryStats);
$stmtStats->execute([$nim_mhs]);
$stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

$queryNotif = "SELECT pesan, created_at FROM notifikasi WHERE nim = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 5";
// $queryNotif = "SELECT id_pengajuan, jenis_surat, status, tanggal_pengajuan as created_at 
//                FROM pengajuan_surat 
//                WHERE nim = ? AND status IN ('disetujui', 'ditolak') AND is_read = 0 
//                ORDER BY tanggal_pengajuan DESC LIMIT 5";
$stmtNotif = $db->prepare($queryNotif);
$stmtNotif->execute([$nim_mhs]);
$notifs_db = $stmtNotif->fetchAll(PDO::FETCH_ASSOC);

$notifs = [];
foreach ($notifs_db as $s) {
    $is_disetujui = ($s['status'] == 'disetujui');
    $notifs[] = [
        'id_pengajuan' => $s['id_pengajuan'],
        'pesan' => "Surat " . strtoupper($s['jenis_surat']) . " telah " . ($is_disetujui ? 'disetujui' : 'ditolak') . ".",
        'created_at' => $s['created_at']
    ];
}

$current_page = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/mahasiswa_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>

        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>

            <div class="content mahasiswa-dashboard-content">
                <!-- Stats Section -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 32px;">
                    <div class="stat-card blue">
                        <i class="fas fa-file-alt"></i>
                        <span class="label">Total Pengajuan</span>
                        <span class="value"><?= $stats['total'] ?? 0 ?></span>
                    </div>
                    <div class="stat-card green">
                        <i class="fas fa-check-circle"></i>
                        <span class="label">Disetujui</span>
                        <span class="value"><?= $stats['diterima'] ?? 0 ?></span>
                    </div>
                    <div class="stat-card orange">
                        <i class="fas fa-clock"></i>
                        <span class="label">Dalam Proses</span>
                        <span class="value"><?= $stats['proses'] ?? 0 ?></span>
                    </div>
                    <div class="stat-card red">
                        <i class="fas fa-times-circle"></i>
                        <span class="label">Ditolak</span>
                        <span class="value"><?= $stats['ditolak'] ?? 0 ?></span>
                    </div>
                </div>

                <div class="main-grid">
                    <!-- Left Column: Notifications & Quick Actions -->
                    <div class="left-column">
                        <div class="section-card" style="margin-bottom: 24px;">
                            <div class="section-title">
                                <i class="fas fa-bell text-primary"></i> Notifikasi Terbaru
                            </div>
                            <div class="notif-list">
                                <?php if (!empty($notifs)): ?>
                                    <?php foreach ($notifs as $notif): ?>
                                        <div class="notif-item">
                                            <div style="display: flex; gap: 16px; align-items: flex-start;">
                                                <div style="width: 40px; height: 40px; border-radius: 12px; background: var(--background); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                                    <i class="fas fa-info-circle"></i>
                                                </div>
                                                <div style="flex: 1;">
                                                    <a href="detail_pengajuan.php?id=<?= $notif['id_pengajuan'] ?>" style="text-decoration: none; color: inherit;">
                                                        <p style="margin: 0; font-weight: 600; font-size: 0.9375rem;">
                                                            <?= htmlspecialchars($notif['pesan']) ?>
                                                        </p>
                                                    </a>
                                                    <small style="color: var(--text-muted);"><?= date('d M Y, H:i', strtotime($notif['created_at'])) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p style="text-align: center; color: var(--text-muted); padding: 20px;">Tidak ada notifikasi baru.</p>
                                <?php endif; ?>
                            </div>
                            <div style="margin-top: 24px; text-align: center;">
                                <a href="pengajuan_surat.php" class="btn-primary" style="display: inline-flex; width: auto; padding: 12px 32px;">
                                    <i class="fas fa-plus" style="margin-right: 8px;"></i> Ajukan Surat Baru
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Profile Summary -->
                    <div class="right-column">
                        <div class="profile-card-premium">
                            <div class="avatar-wrapper" style="position: relative; display: inline-block;">
                                <img src="<?= $foto_sidebar ?? '../../assets/img/profiles/avatar.jpg' ?>?t=<?= time() ?>" alt="Mhs" class="avatar">
                                <div style="position: absolute; bottom: 15px; right: 5px; width: 24px; height: 24px; background: var(--success); border: 4px solid var(--surface); border-radius: 50%;"></div>
                            </div>
                            <h3 style="margin-bottom: 4px;"><?= htmlspecialchars($mhs['nama']) ?></h3>
                            <p style="color: var(--primary); font-weight: 700; font-size: 0.875rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 24px;">Mahasiswa Aktif</p>
                            
                            <div style="text-align: left; background: var(--background); padding: 20px; border-radius: 20px;">
                                <div style="margin-bottom: 12px;">
                                    <small style="color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 0.7rem;">Program Studi</small>
                                    <p style="margin: 0; font-weight: 600; font-size: 0.9rem;"><?= htmlspecialchars($mhs['nama_prodi'] ?? 'Teknik Informatika') ?></p>
                                </div>
                                <div style="margin-bottom: 12px;">
                                    <small style="color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 0.7rem;">Nomor Induk (NIM)</small>
                                    <p style="margin: 0; font-weight: 600; font-size: 0.9rem;"><?= htmlspecialchars($mhs['nomor_induk'] ?? '-') ?></p>
                                </div>
                                <div>
                                    <small style="color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 0.7rem;">Email Institusi</small>
                                    <p style="margin: 0; font-weight: 600; font-size: 0.9rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($mhs['email'] ?? '-') ?></p>
                                </div>
                            </div>

                            <a href="edit_profil.php" class="btn-secondary" style="margin-top: 24px; width: 100%;">
                                <i class="fas fa-user-edit" style="margin-right: 8px;"></i> Perbarui Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>