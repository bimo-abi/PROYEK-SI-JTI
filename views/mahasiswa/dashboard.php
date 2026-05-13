<?php
require_once '../../autoload.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

use Config\Database;

$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// 1. Query Profil untuk mendapatkan data diri dan NIM
$queryProfil = "SELECT p.nama, p.email, d.nomor_induk, pr.nama_prodi, g.nama_golongan 
                FROM pengguna p
                LEFT JOIN detail_pengguna d ON p.id = d.id_pengguna
                LEFT JOIN prodi pr ON d.id_prodi = pr.id
                LEFT JOIN golongan g ON d.id_golongan = g.id
                WHERE p.id = ?";

$stmt = $db->prepare($queryProfil);
$stmt->execute([$user_id]);
$mhs = $stmt->fetch(PDO::FETCH_ASSOC);

// Fallback jika profile belum lengkap
$nim_mhs = $mhs['nomor_induk'] ?? ($_SESSION['nim'] ?? '');

// 2. Query Statistik - Disesuaikan dengan kolom 'nim' sesuai image_657c45.png
$queryStats = "SELECT 
    COUNT(*) as total,
    SUM(status = 'disetujui') as diterima,
    SUM(status = 'menunggu') as proses,
    SUM(status = 'ditolak') as ditolak
    FROM pengajuan_surat WHERE nim = ?"; // Menggunakan nim sesuai gambar database

$stmtStats = $db->prepare($queryStats);
$stmtStats->execute([$nim_mhs]);
$stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

$current_page = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>

        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>

            <div class="content">
                <div class="main-grid">
                    <!-- Kolom Kiri: Statistik & Notif -->
                    <div class="left-column">
                        <div class="section-title"><i class="icon-home"></i> Dashboard</div>
                        <div class="stats-grid">
                            <div class="stat-card blue"> Semua Pengajuan <span><?= $stats['total'] ?? 0 ?></span></div>
                            <div class="stat-card green"> Pengajuan Diterima <span><?= $stats['diterima'] ?? 0 ?></span></div>
                            <div class="stat-card orange"> Pengajuan dalam proses <span><?= $stats['proses'] ?? 0 ?></span></div>
                            <div class="stat-card red"> Pengajuan Ditolak <span><?= $stats['ditolak'] ?? 0 ?></span></div>
                        </div>

                        <div class="notif-box">
                            <h4>Notifikasi Terbaru</h4>
                            <ul>
                                <li>Surat Keterangan aktif kuliah disetujui</li>
                                <li>Profil berhasil diperbarui</li>
                            </ul>
                            <a href="pengajuan_surat.php" class="btn-ajukan" style="text-decoration: none; display: inline-block;">
                                + Ajukan Surat Baru
                            </a>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Info Mahasiswa -->
                    <div class="right-column">
                        <!-- Card Foto & Tahun -->
                        <div class="profile-card">
                            <div class="avatar-wrapper">
                                <img src="<?= $foto_sidebar ?? '../../assets/img/avatar.png' ?>?t=<?= time() ?>" alt="Mhs">
                            </div>
                            <p class="profile-name"><?= htmlspecialchars($mhs['nama']) ?></p>
                            <p class="academic-year">Tahun Akademik<br>2025/2026</p>
                        </div>

                        <!-- Card Detail Info -->
                        <div class="info-card">
                            <div class="info-header">
                                <h5>Info Mahasiswa</h5>
                            </div>
                            <div class="info-body">
                                <p><strong>Prodi :</strong> <?= htmlspecialchars($mhs['nama_prodi'] ?? 'Teknik Informatika') ?></p>
                                <p><strong>Golongan :</strong> <?= htmlspecialchars($mhs['nama_golongan'] ?? 'C') ?></p>
                                <p><strong>NIM :</strong> <?= htmlspecialchars($mhs['nomor_induk'] ?? 'E41250904') ?></p>
                                <p><strong>Email :</strong> <?= htmlspecialchars($mhs['email'] ?? '-') ?></p>

                                <a href="edit_profil.php" style="text-decoration: none;">
                                    <button class="btn-edit-profile">
                                        <i class="icon-pencil"></i> Ubah Profil
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>