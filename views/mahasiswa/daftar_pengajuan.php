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

    // 2. Ambil data pengajuan - Menggunakan kolom terbaru
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
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Pengajuan - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .btn-action {
            padding: 5px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .btn-detail {
            background: #eef2ff;
            color: #4f46e5;
            border: 1px solid #c7d2fe;
        }

        .btn-detail:hover {
            background: #4f46e5;
            color: white;
        }

        .btn-download {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
            margin-left: 5px;
        }

        .btn-download:hover {
            background: #16a34a;
            color: white;
        }
    </style>
</head>

<body style="background-color: #f4f7fa;">
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>
            <div class="content">
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <h4 style="margin: 0; color: #1e293b;"><i class="fas fa-list-alt" style="color: #4f46e5; margin-right: 10px;"></i> Riwayat Pengajuan Surat</h4>
                        <a href="pengajuan_surat.php" style="background: #4f46e5; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                            <i class="fas fa-plus"></i> Ajukan Surat Baru
                        </a>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                                    <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Tanggal</th>
                                    <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Jenis Surat</th>
                                    <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Keterangan</th>
                                    <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                                    <th style="padding: 15px; color: #64748b; font-size: 0.85rem; text-transform: uppercase;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($daftar_surat) > 0): ?>
                                    <?php foreach ($daftar_surat as $surat): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.3s;">
                                            <td style="padding: 15px; color: #334155;"><?= date('d M Y', strtotime($surat['tanggal_pengajuan'])) ?></td>
                                            <td style="padding: 15px; font-weight: 600; color: #1e293b;">
                                                <?= strtoupper(htmlspecialchars($surat['jenis_surat'] ?? 'Tidak Diketahui')) ?>
                                            </td>
                                            <td style="padding: 15px; color: #64748b; font-size: 0.9rem;">
                                                <?php
                                                // Gunakan kolom keterangan jika ada, jika kosong gunakan keperluan (data lama)
                                                $text = !empty($surat['keterangan']) ? $surat['keterangan'] : ($surat['keperluan'] ?? '-');
                                                echo htmlspecialchars(substr($text, 0, 50));
                                                if (strlen($text) > 50) echo '...';
                                                ?>
                                            </td>
                                            <td style="padding: 15px;">
                                                <?php
                                                $status = strtolower($surat['status'] ?? 'menunggu');
                                                $bg = ($status == 'disetujui') ? '#10b981' : (($status == 'ditolak') ? '#ef4444' : '#f59e0b');
                                                ?>
                                                <span style="background: <?= $bg ?>; color: white; padding: 5px 12px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">
                                                    <?= htmlspecialchars($status) ?>
                                                </span>
                                            </td>
                                            <td style="padding: 15px;">
                                                <div style="display: flex; align-items: center;">
                                                    <a href="detail_pengajuan.php?id=<?= $surat['id_pengajuan'] ?>" class="btn-action btn-detail">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                    <?php if (!empty($surat['file_path'])): ?>
                                                        <a href="../../assets/uploads/pdf/<?= $surat['file_path'] ?>" download class="btn-action btn-download">
                                                            <i class="fas fa-download"></i> PDF
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 50px; color: #94a3b8;">
                                            <i class="fas fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
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
                confirmButtonColor: '#4f46e5'
            });
        }
    </script>
</body>

</html>