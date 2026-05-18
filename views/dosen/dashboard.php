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

$queryStats = "SELECT jenis_surat, COUNT(*) as total FROM pengajuan_surat WHERE status IN ('disetujui', 'terverifikasi') GROUP BY jenis_surat";
$resStats = $db->query($queryStats)->fetchAll(PDO::FETCH_ASSOC);
foreach ($resStats as $s) {
    $type = strtolower($s['jenis_surat']);
    if ($type === 'sakit') {
        $stats['sakit'] = $s['total'];
    } elseif ($type === 'kegiatan kampus' || $type === 'kampus') {
        $stats['kampus'] = $s['total'];
    } elseif ($type === 'kegiatan luar kampus' || $type === 'luar_kampus') {
        $stats['luar_kampus'] = $s['total'];
    }
}

// 3. FIX UTAMA: Mengambil 5 surat terbaru dengan status 'terverifikasi' ATAU 'disetujui' agar sinkron dengan Data Mahasiswa
$queryNotif = "SELECT 
                    p.id_pengajuan, 
                    p.jenis_surat, 
                    p.status,
                    p.tanggal_pengajuan AS tgl_notif, 
                    u.nama as nama_mhs 
               FROM pengajuan_surat p
               JOIN detail_pengguna d ON p.nim = d.nomor_induk
               JOIN pengguna u ON d.id_pengguna = u.id
               WHERE p.status IN ('terverifikasi', 'disetujui')
               ORDER BY p.tanggal_pengajuan DESC 
               LIMIT 5";
$stmtNotif = $db->prepare($queryNotif);
$stmtNotif->execute();
$notifs = $stmtNotif->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Dosen - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/mahasiswa_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .notif-item-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            text-decoration: none;
            color: inherit;
            transition: background 0.2s, transform 0.2s;
            border-radius: 8px;
        }

        .notif-item-link:hover {
            background: #f5f9ff;
            transform: translateX(5px);
        }

        .badge-status {
            font-size: 0.65rem;
            padding: 3px 8px;
            border-radius: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .badge-verifikasi {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-disetujui {
            background: #e3f2fd;
            color: #0d47a1;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>

        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>

            <div class="content mahasiswa-dashboard-content">
                <div class="section-title" style="margin-bottom: 24px;">Dashboard Dosen</div>

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
                    <div class="left-column">
                        <div class="section-card" style="margin-bottom: 24px;">
                            <div class="section-title" style="margin-bottom: 15px;">
                                <i class="fas fa-bell text-primary"></i> Notifikasi Pengajuan Baru (Maks. 5)
                            </div>
                            <div class="notif-list">
                                <?php if (!empty($notifs)): ?>
                                    <?php foreach ($notifs as $n): ?>
                                        <a href="data_mahasiswa.php?search=<?= urlencode($n['nama_mhs'] ?? '') ?>" class="notif-item-link">
                                            <div style="display: flex; gap: 16px; align-items: center; flex: 1;">
                                                <div style="width: 40px; height: 40px; border-radius: 12px; background: #e3f2fd; display: flex; align-items: center; justify-content: center; color: #00a2ed; flex-shrink: 0;">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <div style="flex: 1;">
                                                    <p style="margin: 0; font-weight: 600; font-size: 0.9375rem; color: #333;">
                                                        <?= htmlspecialchars($n['nama_mhs'] ?? '') ?>
                                                    </p>
                                                    <small style="color: var(--text-muted); display: block; margin-top: 2px;">
                                                        Jenis: <?= htmlspecialchars($n['jenis_surat'] ?? '') ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div style="text-align: right; min-width: 110px; flex-shrink: 0;">
                                                <span style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">
                                                    <?= date('d M, H:i', strtotime($n['tgl_notif'])) ?>
                                                </span>
                                                <span class="badge-status <?= ($n['status'] ?? '') === 'disetujui' ? 'badge-disetujui' : 'badge-verifikasi' ?>">
                                                    <?= htmlspecialchars($n['status'] ?? '') ?>
                                                </span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div style="text-align: center; color: var(--text-muted); padding: 20px 0;">
                                        <i class="fas fa-check-circle" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 8px;"></i>
                                        <p style="margin: 0; font-size: 0.9rem;">Belum ada pengajuan baru.</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($notifs)): ?>
                                <div style="margin-top: 24px; text-align: center;">
                                    <a href="data_mahasiswa.php" class="btn-primary" style="display: inline-flex; width: auto; padding: 12px 32px;">
                                        Lihat Semua Data <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

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
                                    <p style="margin: 0; font-weight: 600; font-size: 0.9rem;">
                                        <?= htmlspecialchars($dosen['nomor_induk'] ?? '-') ?>
                                    </p>
                                </div>
                                <div>
                                    <small style="color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 0.7rem;">Email</small>
                                    <p style="margin: 0; font-weight: 600; font-size: 0.9rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= htmlspecialchars($dosen['email'] ?? '-') ?>
                                    </p>
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