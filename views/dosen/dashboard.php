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

// Query Profil Dosen
$queryProfil = "SELECT p.nama, p.email, d.nomor_induk
                FROM pengguna p
                LEFT JOIN detail_pengguna d ON p.id = d.id_pengguna
                WHERE p.id = ?";
$stmt = $db->prepare($queryProfil);
$stmt->execute([$user_id]);
$dosen = $stmt->fetch(PDO::FETCH_ASSOC);

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
                            <div class="stat-card blue"> Surat Izin Sakit <span>0</span></div>
                            <div class="stat-card green"> Kegiatan Kampus <span>0</span></div>
                            <div class="stat-card orange"> Kegiatan Luar <span>0</span></div>
                        </div>

                        <div class="notif-box" style="margin-top: 30px;">
                            <h4 style="display: flex; align-items: center; gap: 10px;">
                                <i class="far fa-bell" style="color: #00a2ed;"></i> Notifikasi Terbaru
                            </h4>
                            <ul>
                                <li style="color: #888; margin-top: 10px;">Belum ada notifikasi terbaru.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Info Profil -->
                    <div class="right-column">
                        <div class="profile-card">
                            <div class="avatar-wrapper">
                                <img src="<?= $foto_sidebar ?? '../../assets/img/avatar.png' ?>?t=<?= time() ?>" alt="Dosen Avatar">
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