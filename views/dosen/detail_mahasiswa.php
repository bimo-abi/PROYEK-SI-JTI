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

try {
    // FIXED: Query disesuaikan 100% dengan screenshot skema database phpMyAdmin kamu
    $query = "SELECT p.*, u.nama, u.email, d.nomor_induk, pr.nama_prodi
              FROM pengajuan_surat p
              JOIN detail_pengguna d ON p.nim = d.nomor_induk
              JOIN pengguna u ON d.id_pengguna = u.id
              LEFT JOIN prodi pr ON d.id_prodi = pr.id
              WHERE p.id_pengajuan = ?";

    $stmt = $db->prepare($query);
    $stmt->execute([$id_pengajuan]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // if ($data) {
    //     // Tandai notifikasi sudah dibaca oleh dosen
    //     $updateRead = $db->prepare("UPDATE pengajuan_surat SET is_read_dosen = 1 WHERE id_pengajuan = ?");
    //     $updateRead->execute([$id_pengajuan]);
    // }

    if (!$data) {
        die("Data pengajuan tidak ditemukan.");
    }
} catch (\PDOException $e) {
    die("Gagal memuat data detail: " . $e->getMessage());
}

$current_page = 'data_mahasiswa';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Mahasiswa - Dosen SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body style="background-color: #f4f7fa;">
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>

            <div class="content" style="padding: 30px;">
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; max-width: 900px; margin: auto; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <a href="data_mahasiswa.php" style="text-decoration: none; color: #4f46e5; font-weight: 600;">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                        <?php
                        $status_color = '#00a2ed';
                        $status_bg = '#e3f2fd';
                        $status_text = strtoupper($data['status'] ?? 'PENDING');

                        if ($status_text === 'DISETUJUI' || $status_text === 'TERVERIFIKASI') {
                            $status_color = '#10b981';
                            $status_bg = '#ecfdf5';
                        } elseif ($status_text === 'DITOLAK') {
                            $status_color = '#ef4444';
                            $status_bg = '#fef2f2';
                        }
                        ?>
                        <span style="background: <?= $status_bg ?>; color: <?= $status_color ?>; padding: 6px 18px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; letter-spacing: 0.5px;">
                            <?= $status_text ?>
                        </span>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 20px 0;">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                        <div>
                            <h5 style="color: #4f46e5; font-size: 1.1rem; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #e0e7ff; display: inline-block; padding-bottom: 5px; font-weight: 700;">
                                <i class="fas fa-user-graduate"></i> Informasi Mahasiswa
                            </h5>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 12px 0; color: #64748b; width: 120px; font-size: 0.95rem;">Nama</td>
                                    <td style="font-weight: 600; color: #1e293b;">: <?= htmlspecialchars($data['nama'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; color: #64748b; font-size: 0.95rem;">NIM</td>
                                    <td style="color: #334155; font-weight: 600;">: <?= htmlspecialchars($data['nim'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; color: #64748b; font-size: 0.95rem;">Prodi</td>
                                    <td style="color: #334155;">: <?= htmlspecialchars($data['nama_prodi'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; color: #64748b; font-size: 0.95rem;">Email</td>
                                    <td style="color: #334155;">: <?= htmlspecialchars($data['email'] ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>

                        <div>
                            <h5 style="color: #4f46e5; font-size: 1.1rem; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #e0e7ff; display: inline-block; padding-bottom: 5px; font-weight: 700;">
                                <i class="fas fa-file-alt"></i> Detail Pengajuan
                            </h5>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 12px 0; color: #64748b; width: 120px; font-size: 0.95rem;">Jenis Surat</td>
                                    <td style="font-weight: bold; color: #1e293b; text-transform: uppercase;">: <?= htmlspecialchars($data['jenis_surat'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; color: #64748b; font-size: 0.95rem;">Tgl Diajukan</td>
                                    <td style="color: #334155;">:
                                        <?= !empty($data['tanggal_pengajuan']) ? date('d F Y', strtotime($data['tanggal_pengajuan'])) : '-' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; color: #64748b; font-size: 0.95rem;">Rentang Waktu</td>
                                    <td style="color: #334155; font-size: 0.9rem;">:
                                        <?php if (!empty($data['tgl_mulai']) && !empty($data['tgl_selesai'])): ?>
                                            <span style="background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-weight: 600; color: #4f46e5;"><?= date('d/m/Y', strtotime($data['tgl_mulai'])) ?></span> s/d
                                            <span style="background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-weight: 600; color: #4f46e5;"><?= date('d/m/Y', strtotime($data['tgl_selesai'])) ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; color: #64748b; font-size: 0.95rem; vertical-align: top;">Keterangan</td>
                                    <td style="color: #334155; line-height: 1.5;">: <?= nl2br(htmlspecialchars($data['keterangan'] ?? '-')) ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 25px 0 0 0;" colspan="2">
                                        <?php if (!empty($data['file_path'])): ?>
                                            <a href="../../assets/uploads/pdf/<?= htmlspecialchars($data['file_path']) ?>" target="_blank" style="display: block; background: #ef4444; color: white; text-align: center; padding: 13px; border-radius: 12px; text-decoration: none; font-weight: bold; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2); transition: 0.3s;">
                                                <i class="fas fa-file-pdf"></i> Lihat Dokumen PDF
                                            </a>
                                        <?php else: ?>
                                            <div style="background: #f8fafc; color: #94a3b8; text-align: center; padding: 13px; border-radius: 12px; border: 1px dashed #cbd5e1; font-weight: 500;">
                                                <i class="fas fa-info-circle"></i> Berkas berkas PDF tidak dilampirkan
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