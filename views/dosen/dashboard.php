<?php
require_once '../../autoload.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$current_page = 'dashboard';
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
                </div>
            </div>
        </div>
    </div>
</body>
</html>