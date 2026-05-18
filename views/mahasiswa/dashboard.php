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

$notifications = [];
if (!empty($nim_mhs)) {
    try {
        $queryNotifDirect = "SELECT id_pengajuan, jenis_surat, status, tanggal_pengajuan 
                             FROM pengajuan_surat 
                             WHERE nim = ? 
                             ORDER BY tanggal_pengajuan DESC LIMIT 5";
        $stmtNotifDirect = $db->prepare($queryNotifDirect);
        $stmtNotifDirect->execute([$nim_mhs]);
        $surat_notif = $stmtNotifDirect->fetchAll(PDO::FETCH_ASSOC);

        // Tukar data surat menjadi format teks notifikasi yang dinamik
        foreach ($surat_notif as $s) {
            $statusText = strtolower($s['status']);
            $judul = "Pembaruan Status Surat";

            if ($statusText === 'menunggu') {
                $pesan = "Surat '" . htmlspecialchars($s['jenis_surat']) . "' telah dihantar & sedang menunggu verifikasi.";
                $icon = "fa-clock";
                $color = "#eab308"; // Kuning
            } elseif ($statusText === 'terverifikasi' || $statusText === 'disetujui') {
                $pesan = "done! Surat '" . htmlspecialchars($s['jenis_surat']) . "' anda telah DISETUJUI.";
                $icon = "fa-check-circle";
                $color = "#16a34a"; // Hijau
            } elseif ($statusText === 'ditolak') {
                $pesan = "Maaf, surat '" . htmlspecialchars($s['jenis_surat']) . "' anda telah DITOLAK.";
                $icon = "fa-times-circle";
                $color = "#dc2626"; // Merah
            } else {
                $pesan = "Surat '" . htmlspecialchars($s['jenis_surat']) . "' berstatus " . htmlspecialchars($s['status']) . ".";
                $icon = "fa-info-circle";
                $color = "#2563eb"; // Biru
            }

            $notifications[] = [
                'judul'      => $judul,
                'pesan'      => $pesan,
                'created_at' => $s['tanggal_pengajuan'],
                'icon'       => $icon,
                'color'      => $color
            ];
        }
    } catch (PDOException $e) {
        // Log error jika ada masalah query
    }
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
                            <div class="notification-container">
                                <?php if (!empty($notifications)): ?>
                                    <?php foreach ($notifications as $n): ?>
                                        <div class="notification-item" style="display: flex; gap: 15px; padding: 12px; border-bottom: 1px solid #f1f5f9; align-items: center;">
                                            <div class="notif-icon" style="color: <?= $n['color'] ?>; font-size: 1.2rem;">
                                                <i class="fas <?= $n['icon'] ?>"></i>
                                            </div>
                                            <div class="notif-content" style="flex: 1;">
                                                <h5 style="margin: 0; font-size: 0.9rem; font-weight: 600; color: #1e293b;"><?= htmlspecialchars($n['judul']) ?></h5>
                                                <p style="margin: 2px 0 0; font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($n['pesan']) ?></p>
                                            </div>
                                            <div class="notif-time">
                                                <small style="color: #94a3b8; font-size: 0.7rem;"><?= date('d M', strtotime($n['created_at'])) ?></small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div style="text-align: center; padding: 20px; color: #94a3b8;">
                                        <i class="fas fa-bell-slash" style="font-size: 1.5rem; margin-bottom: 8px;"></i>
                                        <p style="margin: 0; font-size: 0.85rem;">Belum ada pembaruan notifikasi surat.</p>
                                    </div>
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