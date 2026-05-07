<?php
require_once '../../autoload.php';
include '../layouts/sidebar.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['peran'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

use Config\Database;
$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// data profil admin belum masuk wak
$queryProfil = "SELECT * FROM users WHERE id = ?";

$stmt = $db->prepare($queryProfil);
$stmt->execute([$user_id]);
$mhs = $stmt->fetch(PDO::FETCH_ASSOC);

$current_page = 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Admin - SI-JTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 240px;
            height: 100vh;
            background: linear-gradient(to bottom, #000000 0%, #2c5282 100%);
            position: fixed;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            font-style: italic;
            font-weight: bold;
            font-size: 24px;
            margin-bottom: 30px;
            text-align: center;
        }

        .admin-profile {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-img {
            width: 80px;
            height: 80px;
            background-color: white;
            border-radius: 50%;
            margin: 0 auto 10px;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
        }

        .nav-item {
            background: white;
            color: black;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .nav-item i { margin-right: 10px; font-size: 18px; }

        .btn-keluar {
            margin-top: auto;
            color: #ff4d4d;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-weight: bold;
        }

        /* Main Content Styling */
        .main-content {
            margin-left: 240px;
            padding: 0;
        }

        .header-bar {
            background: linear-gradient(to right, #000000, #2c5282);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .dashboard-title {
            padding: 20px 30px;
            background-color: white;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .content-body { padding: 30px; }

        /* Card Stats Styling */
        .stat-card {
            background: white;
            border: 2px solid #00a3ff;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .stat-icon { width: 60px; }

        .stat-info h3 { margin: 0; font-size: 28px; font-weight: bold; text-align: right; }
        .stat-info p { margin: 0; font-size: 12px; color: #666; }

        /* Bottom Section */
        .bottom-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-top: 25px;
        }

        .card-panel {
            background: white;
            border-radius: 15px;
            padding: 20px;
            min-height: 300px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .notif-list {
            list-style: none;
            padding: 0;
        }

        .notif-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
            font-size: 14px;
        }

        .notif-item b { color: #333; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>SI - JTI</h2>
        <div class="admin-profile">
            <div class="profile-img"></div>
            <span>Admin</span>
        </div>
        
        <ul class="nav-menu">
            <li><a href="/surat-digital/resources/views/admin/dashboard-admin.blade.php" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="/surat-digital/resources/views/admin/surat-masuk-admin.blade.php" class="nav-item"><i class="fa-solid fa-envelope-open-text"></i> Surat Masuk</a></li>
            <li><a href="/surat-digital/resources/views/admin/riwayat-admin.blade.php" class="nav-item"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat</a></li>
        </ul>

        <a href="/surat-digital/resources/views/login.blade.php" class="btn-keluar">
            <i class="fa-solid fa-circle-left me-2"></i> Keluar
        </a>
    </div>

    <div class="main-content">
        <div class="header-bar">
            <div></div>
            <div class="text-white fw-bold"><i class="fa-solid fa-user-circle"></i> Admin</div>
        </div>

        <div class="dashboard-title">
            <i class="fa-solid fa-house me-2"></i> Dashboard
        </div>

        <div class="content-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <img src="https://cdn-icons-png.flaticon.com/512/561/561127.png" class="stat-icon" alt="icon">
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Surat izin sakit</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" class="stat-icon" alt="icon">
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Surat Izin Kegiatan Kampus</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <img src="https://cdn-icons-png.flaticon.com/512/3652/3652191.png" class="stat-icon" alt="icon">
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Surat Izin Kegiatan</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bottom-grid">
                <div class="card-panel">
                    </div>
                <div class="card-panel">
                    <h6 class="fw-bold mb-4"><i class="fa-solid fa-bell me-2"></i> Notifikasi Terbaru</h6>
                    <ul class="notif-list">
                        <li class="notif-item">1. <b>IZIN MASUK</b> – Rayhan...</li>
                        <li class="notif-item">2. <b>IZIN DITOLAK</b> – Rayh...</li>
                        <li class="notif-item">3. <b>IZIN MASUK</b> – Rayhan...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</body>
</html>