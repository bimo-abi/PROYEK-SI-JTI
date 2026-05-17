<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();
$id_pengajuan = $_GET['id'] ?? null;

if (!$id_pengajuan) {
    header("Location: daftar_pengajuan.php");
    exit();
}

// Query mengambil data surat
$query = "SELECT * FROM pengajuan_surat WHERE id_pengajuan = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id_pengajuan]);
$surat = $stmt->fetch(PDO::FETCH_ASSOC);

// if ($surat) {
//     $updateRead = $db->prepare("UPDATE pengajuan_surat SET is_read = 1 WHERE id_pengajuan = ?");
//     $updateRead->execute([$id_pengajuan]);
// }

if (!$surat) {
    die("Data pengajuan tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Pengajuan - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body style="background-color: #f4f7fa;">
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>
            <div class="content" style="padding: 30px;">
                <div class="card-container" style="background: white; padding: 40px; border-radius: 20px; max-width: 800px; margin: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">

                    <a href="daftar_pengajuan.php" style="text-decoration: none; color: #4f46e5; font-weight: 600;">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                    <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 20px 0;">

                    <h2 style="color: #1e293b; margin-bottom: 25px; font-size: 1.5rem;">Detail Pengajuan Surat</h2>

                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 15px 0; width: 220px; color: #64748b;">Jenis Surat</td>
                            <td style="font-weight: 700; color: #1e293b;">: <?= strtoupper(htmlspecialchars($surat['jenis_surat'])) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 15px 0; color: #64748b;">Tanggal Pengajuan</td>
                            <td style="color: #334155;">: <?= date('d M Y', strtotime($surat['tanggal_pengajuan'])) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 15px 0; color: #64748b;">Rentang Waktu Izin</td>
                            <td>:
                                <?php if (!empty($surat['tgl_mulai']) && !empty($surat['tgl_selesai'])): ?>
                                    <span style="background: #eef2ff; color: #4f46e5; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.9rem;">
                                        <?= date('d/m/Y', strtotime($surat['tgl_mulai'])) ?>
                                    </span>
                                    <span style="margin: 0 5px; color: #94a3b8;">s/d</span>
                                    <span style="background: #eef2ff; color: #4f46e5; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.9rem;">
                                        <?= date('d/m/Y', strtotime($surat['tgl_selesai'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #94a3b8;">: Data lama (Lihat di Keterangan)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 15px 0; color: #64748b; vertical-align: top;">Keterangan / Alasan</td>
                            <td style="color: #334155; line-height: 1.6;">: <?= htmlspecialchars($surat['keterangan'] ?? $surat['keperluan'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 15px 0; color: #64748b;">Lampiran Berkas</td>
                            <td>:
                                <?php if (!empty($surat['file_path'])): ?>
                                    <a href="../../assets/uploads/pdf/<?= $surat['file_path'] ?>" target="_blank" style="background: #f0fdf4; color: #16a34a; padding: 5px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; border: 1px solid #bbf7d0;">
                                        <i class="fas fa-file-pdf"></i> Lihat PDF
                                    </a>
                                <?php else: ?>
                                    <span style="color: #94a3b8;">Tidak ada lampiran</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 15px 0; color: #64748b;">Status Saat Ini</td>
                            <td>:
                                <?php
                                $status = strtolower($surat['status']);
                                $color = ($status == 'disetujui') ? '#10b981' : (($status == 'ditolak') ? '#ef4444' : '#f59e0b');
                                ?>
                                <span style="text-transform: uppercase; font-weight: 800; color: <?= $color ?>; letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($surat['status']) ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>