<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();
$id_pengajuan = $_GET['id'] ?? null;

if (!$id_pengajuan) {
    header("Location: data_mahasiswa.php");
    exit();
}

// Ambil detail pengajuan lengkap dengan data mahasiswa
$query = "SELECT p.*, u.nama, u.email, pr.nama_prodi, g.nama_golongan, d.nomor_telepon, d.alamat
          FROM pengajuan_surat p
          JOIN detail_pengguna d ON p.nim = d.nomor_induk
          JOIN pengguna u ON d.id_pengguna = u.id
          LEFT JOIN prodi pr ON d.id_prodi = pr.id
          LEFT JOIN golongan g ON d.id_golongan = g.id
          WHERE p.id_pengajuan = ?";

$stmt = $db->prepare($query);
$stmt->execute([$id_pengajuan]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data tidak ditemukan.");
}

// is_read_dosen update removed due to database schema change

$current_page = 'data_mahasiswa';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Mahasiswa - Dosen SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>
            
            <div class="content">
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; max-width: 900px; margin: auto; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <a href="data_mahasiswa.php" style="text-decoration: none; color: #666; font-weight: 500;">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                        <span style="background: #e3f2fd; color: #00a2ed; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">
                            <?= strtoupper($data['status']) ?>
                        </span>
                    </div>
                    
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                        <!-- Informasi Mahasiswa -->
                        <div>
                            <h5 style="color: #00a2ed; margin-bottom: 20px; border-bottom: 2px solid #00a2ed; display: inline-block; padding-bottom: 5px;">
                                <i class="fas fa-user-graduate"></i> Informasi Mahasiswa
                            </h5>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 10px 0; color: #888; width: 120px;">Nama</td>
                                    <td style="font-weight: bold;">: <?= htmlspecialchars($data['nama'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">NIM</td>
                                    <td>: <?= htmlspecialchars($data['nim'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">Prodi</td>
                                    <td>: <?= htmlspecialchars($data['nama_prodi'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">Golongan</td>
                                    <td>: <?= htmlspecialchars($data['nama_golongan'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">Email</td>
                                    <td>: <?= htmlspecialchars($data['email'] ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Detail Pengajuan -->
                        <div>
                            <h5 style="color: #00a2ed; margin-bottom: 20px; border-bottom: 2px solid #00a2ed; display: inline-block; padding-bottom: 5px;">
                                <i class="fas fa-file-alt"></i> Detail Pengajuan
                            </h5>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 10px 0; color: #888; width: 120px;">Jenis Surat</td>
                                    <td style="font-weight: bold;">: <?= htmlspecialchars($data['jenis_surat'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">Tgl Pengajuan</td>
                                    <td>: <?= date('d F Y', strtotime($data['tanggal_pengajuan'])) ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">Keterangan</td>
                                    <td>: <?= nl2br(htmlspecialchars($data['keperluan'] ?? '-')) ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 0 0 0;" colspan="2">
                                        <?php if (!empty($data['file_path'])): ?>
                                            <a href="../../assets/uploads/pdf/<?= $data['file_path'] ?>" target="_blank" style="display: block; background: #ff4757; color: white; text-align: center; padding: 12px; border-radius: 10px; text-decoration: none; font-weight: bold;">
                                                <i class="fas fa-file-pdf"></i> Lihat Dokumen PDF
                                            </a>
                                        <?php else: ?>
                                            <div style="background: #f8f9fa; color: #999; text-align: center; padding: 12px; border-radius: 10px; border: 1px dashed #ddd;">
                                                File PDF tidak tersedia
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
