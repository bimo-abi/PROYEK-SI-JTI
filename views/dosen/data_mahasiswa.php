<?php
require_once '../../autoload.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$current_page = 'data_mahasiswa';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa - Dosen SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>
            
            <div class="content">
                <div class="card-container" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h4><i class="fas fa-users" style="color: #00a2ed;"></i> Data Mahasiswa</h4>
                        <input type="text" placeholder="Cari mahasiswa..." style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; width: 250px;">
                    </div>
                    
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                                <th style="padding: 12px;">No</th>
                                <th style="padding: 12px;">Nama</th>
                                <th style="padding: 12px;">Prodi</th>
                                <th style="padding: 12px;">Gol</th>
                                <th style="padding: 12px;">NIM</th>
                                <th style="padding: 12px;">Semester</th>
                                <th style="padding: 12px; text-align: center;">Surat</th>
                                <th style="padding: 12px; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;">1</td>
                                <td style="padding: 12px;">Rayhan Riyadhul Jinan</td>
                                <td style="padding: 12px;">Teknik Informatika</td>
                                <td style="padding: 12px;">C</td>
                                <td style="padding: 12px;">E41250835</td>
                                <td style="padding: 12px;">2</td>
                                <td style="padding: 12px; text-align: center;">
                                    <a href="#" style="color: #ff4757; text-decoration: none;"><i class="fas fa-file-pdf"></i> PDF</a>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="background: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; font-size: 0.8rem; font-weight: bold;">Ditolak</span>
                                </td>
                            </tr>
                            <!-- Tambahkan data dinamis lainnya di sini -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>