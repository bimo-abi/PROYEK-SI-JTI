<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

// 1. Proteksi Halaman Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();

try {
    $query = "SELECT p.*, 
                     u.nama, 
                     pr.nama_prodi, 
                     g.nama_golongan 
              FROM pengajuan_surat p
              LEFT JOIN pengguna u ON u.email LIKE CONCAT(p.nim, '%')
              LEFT JOIN detail_pengguna dp ON p.nim = dp.nomor_induk
              LEFT JOIN prodi pr ON dp.id_prodi = pr.id
              LEFT JOIN golongan g ON dp.id_golongan = g.id
              WHERE p.status IN ('disetujui', 'ditolak', 'terverifikasi')
              ORDER BY p.id_pengajuan DESC";

    $stmt = $db->query($query);
    $riwayat_surat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data riwayat: " . $e->getMessage());
}

// Konfigurasi Layout
$current_page = 'riwayat';
$page_title = 'Riwayat Verifikasi';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Admin - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6;">

    <div class="wrapper" style="display: flex;">
        <?php include '../layouts/sidebar_admin.php'; ?>

        <div class="main-container" style="flex-grow: 1; min-height: 100vh;">
            <?php include '../layouts/topbar_admin.php'; ?>

            <div class="content" style="padding: 30px;">
                <div class="card-history" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">

                    <div style="margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-history" style="color: #00a2ed; font-size: 1.5rem;"></i>
                        <h4 style="margin: 0; color: #333;">Riwayat Keputusan Surat</h4>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                            <thead>
                                <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                                    <th style="padding: 15px; color: #666; font-size: 0.9rem;">No</th>
                                    <th style="padding: 15px; color: #666; font-size: 0.9rem;">Nama Mahasiswa</th>
                                    <th style="padding: 15px; color: #666; font-size: 0.9rem;">Prodi</th>
                                    <th style="padding: 15px; color: #666; font-size: 0.9rem;">Gol</th>
                                    <th style="padding: 15px; color: #666; font-size: 0.9rem;">NIM</th>
                                    <th style="padding: 15px; color: #666; font-size: 0.9rem;">Surat</th>
                                    <th style="padding: 15px; color: #666; font-size: 0.9rem;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($riwayat_surat) > 0): ?>
                                    <?php $no = 1;
                                    foreach ($riwayat_surat as $row): ?>
                                        <tr style="border-bottom: 1px solid #eee; transition: 0.3s;">
                                            <td style="padding: 15px; font-size: 0.9rem;"><?= $no++ ?>.</td>
                                            <td style="padding: 15px; font-weight: 600; color: #333; font-size: 0.9rem;"><?= htmlspecialchars($row['nama']) ?></td>
                                            <td style="padding: 15px; font-size: 0.85rem; color: #555;"><?= htmlspecialchars($row['nama_prodi']) ?></td>
                                            <td style="padding: 15px; text-align: center; font-size: 0.85rem;"><?= htmlspecialchars($row['nama_golongan'] ?? '-') ?></td>
                                            <td style="padding: 15px; font-family: monospace; font-size: 0.9rem;"><?= htmlspecialchars($row['nim']) ?></td>
                                            <td style="padding: 15px; text-align: center;">
                                                <?php if ($row['file_path']): ?>
                                                    <a href="../../assets/uploads/pdf/<?= $row['file_path'] ?>" target="_blank" style="color: #e74c3c; text-decoration: none;">
                                                        <i class="fas fa-file-pdf"></i> .pdf
                                                    </a>
                                                <?php else: ?>
                                                    <span style="color: #ccc;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding: 15px;">
                                                <?php
                                                $status = strtolower($row['status']);

                                                // Tambahkan 'terverifikasi' ke dalam pengecekan status sukses
                                                if ($status == 'disetujui' || $status == 'terverifikasi') {
                                                    $color = '#28a745'; // Hijau
                                                    $label = 'Terverifikasi';
                                                } else {
                                                    $color = '#dc3545'; // Merah
                                                    $label = 'Ditolak';
                                                }
                                                ?>
                                                <span style="background: <?= $color ?>; color: white; padding: 5px 12px; border-radius: 5px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; display: inline-block; min-width: 90px; text-align: center;">
                                                    <?= $label ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 50px; color: #999; font-style: italic;">
                                            <i class="fas fa-history" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                                            Belum ada riwayat keputusan surat.
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
        // Notifikasi Sukses jika baru saja memproses surat
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('pesan') === 'berhasil') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Status surat telah diperbarui.',
                confirmButtonColor: '#00a2ed'
            });
        }
    </script>
</body>

</html>