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

// Ambil data profil lengkap
$queryProfil = "SELECT p.nama, p.email, d.nomor_induk, pr.nama_prodi, g.nama_golongan 
                FROM pengguna p
                LEFT JOIN detail_pengguna d ON p.id = d.id_pengguna
                LEFT JOIN prodi pr ON d.id_prodi = pr.id
                LEFT JOIN golongan g ON d.id_golongan = g.id
                WHERE p.id = ?";

$stmt = $db->prepare($queryProfil);
$stmt->execute([$user_id]);
$mhs = $stmt->fetch(PDO::FETCH_ASSOC);

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
                    <div class="left-column">
                        <div class="section-title">Dashboard</div>
                        <div class="stats-grid">
                            <div class="stat-card blue"> Semua Pengajuan <span>0</span> </div>
                            <div class="stat-card green"> Diterima <span>0</span> </div>
                            <div class="stat-card orange"> Diproses <span>0</span> </div>
                            <div class="stat-card red"> Ditolak <span>0</span> </div>
                        </div>

                        <div class="notif-box">
                            <h4>Notifikasi Terbaru</h4>
                            <ul>
                                <li>Selamat datang di SI-JTI, <?= htmlspecialchars($mhs['nama']) ?>!</li>
                            </ul>
                            <a href="pengajuan_surat.php" class="btn-ajukan">+ Ajukan Surat</a>
                        </div>
                    </div>

                    <div class="right-column">
                        <div class="profile-card">
                            <div class="avatar-wrapper">
                                <img src="../../assets/img/avatar.png" alt="Avatar">
                            </div>
                            <p class="profile-name"><?= htmlspecialchars($mhs['nama']) ?></p>
                        </div>

                        <div class="info-card">
                            <div class="info-body">
                                <p><strong>Prodi :</strong> <?= htmlspecialchars($mhs['nama_prodi'] ?? '-') ?></p>
                                <p><strong>Golongan :</strong> <?= htmlspecialchars($mhs['nama_golongan'] ?? '-') ?></p>
                                <p><strong>NIM :</strong> <?= htmlspecialchars($mhs['nomor_induk'] ?? '-') ?></p>
                                <a href="edit_profil.php" class="btn-edit-profile">Ubah Profil</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>