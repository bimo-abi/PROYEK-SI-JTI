<?php
require_once '../../autoload.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Dashboard Dosen</title>

    <link rel="stylesheet" href="../../assets/css/dashboard.css">

</head>

<body>

<div class="wrapper">

    <!-- Sidebar -->
    <?php include '../layouts/sidebar_dosen.php'; ?>

    <div class="main-container">

        <!-- Topbar -->
        <?php include '../layouts/topbar_dosen.php'; ?>

        <!-- Content -->
        <div class="content">

            <div class="main-grid">

                <!-- LEFT -->
                <div class="left-column">

                    <div class="section-title">
                        Dashboard
                    </div>

                    <!-- Statistik -->
                    <div class="stats-grid">

                        <div class="stat-card blue">
                            Surat Izin Sakit
                            <span>12</span>
                        </div>

                        <div class="stat-card green">
                            Izin Kegiatan Kampus
                            <span>8</span>
                        </div>

                        <div class="stat-card orange">
                            Izin Kegiatan Luar
                            <span>5</span>
                        </div>

                        <div class="stat-card red">
                            Menunggu Verifikasi
                            <span>3</span>
                        </div>

                    </div>

                    <!-- Notifikasi -->
                    <div class="notif-box">

                        <h4>Notifikasi Terbaru</h4>

                        <ul>

                            <li>
                                Rayhan mengajukan surat izin sakit
                            </li>

                            <li>
                                Vira mengajukan kegiatan kampus
                            </li>

                            <li>
                                2 surat menunggu verifikasi
                            </li>

                        </ul>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="right-column">

                    <!-- Profile -->
                    <div class="profile-card">

                        <div class="avatar-wrapper">
                            <img src="../../assets/img/avatar.png" alt="Dosen">
                        </div>

                        <p class="profile-name">
                            DOSEN
                        </p>

                        <p class="academic-year">
                            Sistem Informasi Surat
                        </p>

                    </div>

                    <!-- Info -->
                    <div class="info-card">

                        <div class="info-header">
                            <h5>Informasi Dosen</h5>
                        </div>

                        <div class="info-body">

                            <p><strong>Status :</strong> Aktif</p>

                            <p><strong>Role :</strong> Dosen</p>

                            <p><strong>Total Surat :</strong> 25</p>

                            <p><strong>Verifikasi :</strong> 18 selesai</p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>