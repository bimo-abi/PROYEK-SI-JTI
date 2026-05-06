<?php
require_once '../../autoload.php';
include '../layouts/sidebar.php';
session_start();

// Keamanan: Cek login & peran
if (!isset($_SESSION['user_id']) || $_SESSION['peran'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

use Config\Database;
$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// Ambil data profil (untuk box info mahasiswa di kanan)
$queryProfil = "SELECT p.nama, p.email, d.nomor_induk, pr.nama_prodi, g.nama_golongan 
                FROM pengguna p
                JOIN detail_pengguna d ON p.id = d.id_pengguna
                JOIN prodi pr ON d.id_prodi = pr.id
                JOIN golongan g ON d.id_golongan = g.id
                WHERE p.id = ?";
$stmt = $db->prepare($queryProfil);
$stmt->execute([$user_id]);
$mhs = $stmt->fetch(PDO::FETCH_ASSOC);

// Penanda halaman aktif di sidebar
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
        <!-- Panggil Sidebar -->
        <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

        <div class="main-container">
            <!-- Panggil Topbar -->
            <?php include '../layout/topbar.php'; ?>

            <div class="content">
                <div class="main-grid">
                    
                    <!-- Kolom Kiri: Statistik & Notif -->
                    <div class="left-column">
                        <div class="section-title"><i class="icon-home"></i> Dashboard</div>
                        
                        <div class="stats-grid">
                            <div class="stat-card blue"> Semua Pengajuan <span>0</span> </div>
                            <div class="stat-card green"> Pengajuan Diterima <span>0</span> </div>
                            <div class="stat-card orange"> Pengajuan dalam proses <span>0</span> </div>
                            <div class="stat-card red"> Pengajuan Ditolak <span>0</span> </div>
                            <div class="stat-card sky"> Surat Masuk <span>0</span> </div>
                        </div>

                        <div class="notif-box">
                            <h4>Notifikasi Terbaru</h4>
                            <ul>
                                <li>Surat Keterangan aktif kuliah disetujui</li>
                                <li>Profil berhasil diperbarui</li>
                            </ul>
                            <button class="btn-ajukan">+ Ajukan Surat Baru</button>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Info Mahasiswa -->
                    <div class="right-column">
                        <div class="profile-card">
                            <img src="../../assets/img/avatar.png" alt="Mhs">
                            <p>---------</p>
                            <p>Tahun Akademik 2025/2026</p>
                        </div>

                        <div class="info-box">
                            <h5>Info Mahasiswa</h5>
                            <p>Prodi : <?= $mhs['nama_prodi'] ?></p>
                            <p>Golongan : <?= $mhs['nama_golongan'] ?></p>
                            <p>NIM : <?= $mhs['nomor_induk'] ?></p>
                            <p>Email : <?= $mhs['email'] ?></p>
                            <button class="btn-edit">Ubah Profil</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>