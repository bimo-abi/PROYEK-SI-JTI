<?php
require_once '../../autoload.php';
session_start();

// 1. Keamanan: Cek login & peran
if (!isset($_SESSION['user_id']) || $_SESSION['peran'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

// Penanda halaman aktif di sidebar
$current_page = 'pengajuan';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pengajuan Surat - SI-JTI</title>
    <!-- Pastikan file CSS dashboard tetap dipanggil di sini -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <!-- Panggil Sidebar (Sama seperti dashboard) -->
        <?php include '../layouts/sidebar.php'; ?>

        <div class="main-container">
            <!-- Panggil Topbar (Sama seperti dashboard) -->
            <?php include '../layouts/topbar.php'; ?>

            <div class="content">
                <div class="section-title"><i class="icon-envelope"></i> Pengajuan Surat</div>
                
                <div class="selection-box text-center">
                    <h3>Pilih Jenis Surat</h3>
                    <p>Silakan pilih jenis surat yang ingin Anda ajukan</p>
                    
                    <form action="form_pengajuan.php" method="GET" class="surat-grid-wrapper">
                        <div class="options-container">
                            <!-- Opsi Sakit -->
                            <label class="surat-option">
                                <input type="radio" name="jenis" value="sakit" required>
                                <div class="option-card">
                                    <img src="../../assets/img/icon-sakit.png" alt="Sakit">
                                    <p>Surat Izin Sakit</p>
                                </div>
                            </label>

                            <!-- Opsi Kampus -->
                            <label class="surat-option">
                                <input type="radio" name="jenis" value="kampus">
                                <div class="option-card">
                                    <img src="../../assets/img/icon-kampus.png" alt="Kampus">
                                    <p>Izin Kegiatan Kampus</p>
                                </div>
                            </label>

                            <!-- Opsi Luar -->
                            <label class="surat-option">
                                <input type="radio" name="jenis" value="luar_kampus">
                                <div class="option-card">
                                    <img src="../../assets/img/icon-luar.png" alt="Luar">
                                    <p>Surat Izin Kegiatan</p>
                                </div>
                            </label>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn-lanjutkan">Lanjutkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>