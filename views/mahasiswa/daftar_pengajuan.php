<?php
require_once '../../autoload.php';
session_start();

// Proteksi halaman
if (!isset($_SESSION['user_id']) || $_SESSION['peran'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

use Config\Database;
$db = (new Database())->getConnection();

// Ambil NIM dari session (pastikan NIM sudah disimpan di session saat login)
// Jika belum ada di session, kita ambil dari database berdasarkan user_id
if (!isset($_SESSION['nim'])) {
    $stmt = $db->prepare("SELECT nomor_induk FROM detail_pengguna WHERE id_pengguna = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $_SESSION['nim'] = $stmt->fetchColumn();
}

$nim_login = $_SESSION['nim'];

// Query untuk mengambil riwayat pengajuan surat mahasiswa tersebut
$query = "SELECT * FROM pengajuan_surat WHERE nim = ? ORDER BY tanggal_pengajuan DESC";
$stmt = $db->prepare($query);
$stmt->execute([$nim_login]);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_page = 'daftar_surat';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pengajuan Surat - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>

            <div class="content">
                <div class="header-section">
                    <h4>Riwayat surat yang telah anda ajukan</h4>
                    
                    <!-- Filter Bar sesuai desain image_bcf13f.png -->
                    <div class="filter-row">
                        <select name="jenis">
                            <option value="">Semua jenis surat</option>
                            <option value="sakit">Surat Izin Sakit</option>
                            <option value="kampus">Kegiatan Kampus</option>
                            <option value="luar_kampus">Kegiatan Luar Kampus</option>
                        </select>
                        <select name="status">
                            <option value="">Semua Status</option>
                            <option value="disetujui">Terverifikasi</option>
                            <option value="menunggu">Menunggu</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                        <input type="date">
                        <div class="search-box">
                            <input type="text" placeholder="Cari...">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>

                <div class="riwayat-container">
                    <?php if (count($riwayat) > 0): ?>
                        <?php foreach ($riwayat as $row): 
                            // Logika penentuan class badge dan label
                            $class_badge = '';
                            $label = '';
                            
                            if ($row['status'] == 'disetujui') {
                                $class_badge = 'badge-terverifikasi';
                                $label = 'Terverifikasi';
                            } elseif ($row['status'] == 'ditolak') {
                                $class_badge = 'badge-ditolak';
                                $label = 'Ditolak';
                            } elseif ($row['status'] == 'diproses') {
                                $class_badge = 'badge-menunggu'; // Bisa disesuaikan warnanya
                                $label = 'Diproses';
                            } else {
                                $class_badge = 'badge-menunggu';
                                $label = 'Menunggu';
                            }
                        ?>
                            <div class="surat-item">
                                <div class="file-info">
                                    <i class="fas fa-file-pdf"></i>
                                    <!-- Menampilkan nama file dari database -->
                                    <span><?= htmlspecialchars($row['file_path']) ?></span>
                                </div>
                                <div class="action-status">
                                    <!-- Link download mengarah ke folder storage -->
                                    <a href="../../storage/surat/<?= $row['file_path'] ?>" class="btn-unduh" download>
                                        <i class="fas fa-download"></i> Unduh
                                    </a>
                                    <span class="badge <?= $class_badge ?>"><?= $label ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Tampilan jika belum ada riwayat -->
                        <div class="text-center" style="padding: 50px; color: #888;">
                            <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 10px;"></i>
                            <p>Belum ada riwayat pengajuan surat.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>