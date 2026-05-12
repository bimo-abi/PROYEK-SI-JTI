<?php
require_once '../../autoload.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$current_page = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/dashboard_dosen.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

            <!-- Card Statistik -->
            <div class="card-container">

                <!-- Card 1 -->
                <div class="card-stat">
                    <img src="../../assets/img/mail.png" alt="Mail">

                    <div class="card-text">
                        <h2>0</h2>
                        <p>Surat izin sakit</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="card-stat">
                    <img src="../../assets/img/mail.png" alt="Mail">

                    <div class="card-text">
                        <h2>0</h2>
                        <p>Surat izin kegiatan kampus</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="card-stat">
                    <img src="../../assets/img/mail.png" alt="Mail">

                    <div class="card-text">
                        <h2>0</h2>
                        <p>Surat izin kegiatan luar</p>
                    </div>
                </div>

            </div>

            <!-- Bottom -->
            <div class="bottom-container">

                <!-- Box Besar -->
                <div class="big-box">

                </div>

                <!-- Notifikasi -->
                <div class="notif-box">

                    <h3>
                        <i class="far fa-bell"></i>
                        Notifikasi Terbaru
                    </h3>

                    <div class="notif-item"></div>
                    <div class="notif-item"></div>
                    <div class="notif-item"></div>
                    <div class="notif-item"></div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>