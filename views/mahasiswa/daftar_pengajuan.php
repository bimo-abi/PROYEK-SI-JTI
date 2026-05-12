<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];
try {
    // 1. Ambil NIM dari tabel detail_pengguna
    $query_nim = "SELECT nomor_induk FROM detail_pengguna WHERE id_pengguna = ?";
    $stmt_nim = $db->prepare($query_nim);
    $stmt_nim->execute([$user_id]);
    $res_nim = $stmt_nim->fetch(PDO::FETCH_ASSOC);

    if (!$res_nim) {
        die("Error: Data NIM tidak ditemukan.");
    }

    $nim = $res_nim['nomor_induk'];

    // 2. Ambil data pengajuan - Menggunakan 'file_surat' sesuai perubahan di surat_process.php
    $query = "SELECT * FROM pengajuan_surat WHERE nim = ? ORDER BY tanggal_pengajuan DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$nim]);
    $daftar_surat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

$current_page = 'daftar_pengajuan';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daftar Pengajuan - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>

        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>

            <div class="content">
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <h4 style="margin: 0; color: #333;"><i class="fas fa-list-alt" style="color: #00a2ed;"></i> Riwayat Pengajuan Surat</h4>
                        <a href="pengajuan_surat.php" style="background: #00a2ed; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 0.85rem;">
                            <i class="fas fa-plus"></i> Ajukan Surat Baru
                        </a>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa; border-bottom: 2px solid #eee; text-align: left;">
                                    <th style="padding: 15px;">Tanggal</th>
                                    <th style="padding: 15px;">Jenis Surat</th>
                                    <th style="padding: 15px;">Keperluan</th>
                                    <th style="padding: 15px;">Status</th>
                                    <th style="padding: 15px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($daftar_surat) > 0): ?>
                                    <?php foreach ($daftar_surat as $surat): ?>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 15px;"><?= date('d M Y', strtotime($surat['tanggal_pengajuan'])) ?></td>
                                            <td style="padding: 15px; font-weight: 600;">
                                                <?= htmlspecialchars($surat['jenis_surat'] ?? 'Tidak Diketahui') ?>
                                            </td>
                                            <td style="padding: 15px; color: #777;">
                                                <?php
                                                $text = $surat['keperluan'] ?? '-';
                                                echo htmlspecialchars(substr($text, 0, 40));
                                                if (strlen($text) > 40) echo '...';
                                                ?>
                                            </td>
                                            <td style="padding: 15px;">
                                                <?php
                                                $status = strtolower($surat['status'] ?? 'menunggu');
                                                // Warna label sesuai Figma: Menunggu (Kuning), Terverifikasi (Biru), Ditolak (Merah)
                                                $bg = ($status == 'disetujui' || $status == 'terverifikasi') ? '#00a2ed' : (($status == 'ditolak') ? '#dc3545' : '#ffc107');
                                                ?>
                                                <span style="background: <?= $bg ?>; color: white; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; text-transform: uppercase;">
                                                    <?= htmlspecialchars($status) ?>
                                                </span>
                                            </td>
                                            <td style="padding: 15px; display: flex; gap: 10px;">
                                                <a href="detail_pengajuan.php?id=<?= $surat['id'] ?>" style="color: #666; text-decoration: none; font-weight: 600; font-size: 0.85rem;">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>

                                                <?php if (!empty($surat['file_surat'])): ?>
                                                    <a href="../../assets/uploads/pdf/<?= $surat['file_surat'] ?>" 
                                                       style="color: #00a2ed; text-decoration: none; font-weight: 600; font-size: 0.85rem;" download>
                                                        <i class="fas fa-download"></i> Unduh
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 40px; color: #aaa;">
                                            Belum ada riwayat pengajuan surat.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'success') {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Pengajuan surat Anda telah berhasil dikirim.',
                icon: 'success',
                confirmButtonColor: '#00a2ed'
            });
        }
    </script>
</body>
</html>