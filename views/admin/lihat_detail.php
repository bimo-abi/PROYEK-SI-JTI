<?php
require_once '../../autoload.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

use Config\Database;

$db = (new Database())->getConnection();
$id_pengajuan = $_GET['id'] ?? null;

if (!$id_pengajuan) {
    header("Location: surat_masuk.php");
    exit();
}

try {
    $query = "SELECT p.*, u.nama, u.email, pr.nama_prodi, g.nama_golongan 
              FROM pengajuan_surat p
              JOIN detail_pengguna d ON p.nim = d.nomor_induk
              JOIN pengguna u ON d.id_pengguna = u.id
              LEFT JOIN prodi pr ON d.id_prodi = pr.id
              LEFT JOIN golongan g ON d.id_golongan = g.id
              WHERE p.id_pengajuan = ?";

    $stmt = $db->prepare($query);
    $stmt->execute([$id_pengajuan]);
    $surat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$surat) die("Data tidak ditemukan.");
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$current_page = 'surat_masuk';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Verifikasi - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .btn-action {
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            filter: brightness(1.1);
        }

        .info-label {
            color: #64748b;
            font-size: 0.8rem;
            letter-spacing: 1px;
            margin-bottom: 10px;
            border-bottom: 2px solid #f0f7ff;
            padding-bottom: 5px;
            text-transform: uppercase;
        }

        .data-value {
            color: #1e293b;
            font-weight: 600;
        }

        .badge-date {
            background: #eef2ff;
            color: #4f46e5;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
        }
    </style>
</head>

<body style="background-color: #f4f7fa; font-family: 'Inter', sans-serif;">
    <div class="wrapper">
        <?php include '../layouts/sidebar_admin.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar_admin.php'; ?>

            <div class="content" style="padding: 30px;">
                <div class="card-container" style="background: white; padding: 40px; border-radius: 20px; max-width: 850px; margin: auto; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">

                    <a href="surat_masuk.php" style="text-decoration: none; color: #4f46e5; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>

                    <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 25px 0;">

                    <h2 style="color: #1e293b; margin-bottom: 30px; font-size: 1.5rem; display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-file-signature" style="color: #4f46e5;"></i> Detail Verifikasi Surat
                    </h2>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 35px;">
                        <div>
                            <h5 class="info-label">Data Mahasiswa</h5>
                            <p style="margin: 8px 0; font-size: 0.95rem;">Nama: <span class="data-value"><?= htmlspecialchars($surat['nama']) ?></span></p>
                            <p style="margin: 8px 0; font-size: 0.95rem;">NIM: <span class="data-value"><?= htmlspecialchars($surat['nim']) ?></span></p>
                            <p style="margin: 8px 0; font-size: 0.95rem;">Prodi: <span class="data-value"><?= htmlspecialchars($surat['nama_prodi'] ?? '-') ?></span></p>
                        </div>

                        <div>
                            <h5 class="info-label">Info Pengajuan</h5>
                            <p style="margin: 8px 0; font-size: 0.95rem;">Jenis Surat: <span class="data-value" style="text-transform: uppercase; color: #4f46e5;"><?= htmlspecialchars($surat['jenis_surat']) ?></span></p>
                            <p style="margin: 8px 0; font-size: 0.95rem;">Tgl Diajukan: <span class="data-value"><?= date('d M Y', strtotime($surat['tanggal_pengajuan'])) ?></span></p>
                        </div>
                    </div>

                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                        <tr>
                            <td style="padding: 15px 0; width: 220px; color: #64748b;">Rentang Waktu Izin</td>
                            <td class="data-value">:
                                <?php if (!empty($surat['tgl_mulai']) && !empty($surat['tgl_selesai'])): ?>
                                    <span class="badge-date"><?= date('d/m/Y', strtotime($surat['tgl_mulai'])) ?></span>
                                    <span style="margin: 0 5px; color: #94a3b8; font-weight: normal;">s/d</span>
                                    <span class="badge-date"><?= date('d/m/Y', strtotime($surat['tgl_selesai'])) ?></span>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-weight: normal;">Tidak ditentukan</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding: 15px 0; color: #64748b; vertical-align: top;">Keterangan / Alasan</td>
                            <td style="color: #334155; line-height: 1.6;">:
                                <span style="font-weight: 500;"><?= htmlspecialchars($surat['keterangan'] ?? $surat['keperluan'] ?? $surat['alasan'] ?? '-') ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding: 15px 0; color: #64748b;">Lampiran Berkas</td>
                            <td>:
                                <?php if (!empty($surat['file_path'])): ?>
                                    <a href="../../assets/uploads/pdf/<?= $surat['file_path'] ?>" target="_blank" style="background: #f0fdf4; color: #16a34a; padding: 6px 14px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 700; border: 1px solid #bbf7d0; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-file-pdf"></i> Lihat Berkas PDF
                                    </a>
                                <?php else: ?>
                                    <span style="color: #94a3b8;">Tidak ada lampiran</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>

                    <div style="margin-top: 40px; border-top: 1px solid #f1f5f9; padding-top: 30px;">
                        <form action="../../process/proses_verifikasi.php" method="POST" style="display: flex; gap: 20px; width: 100%;">
                            <input type="hidden" name="id_pengajuan" value="<?= $surat['id_pengajuan'] ?>">

                            <button type="submit" name="status" value="ditolak" class="btn-action" style="flex: 1; background: #ff4757; color: white;">
                                <i class="fas fa-times-circle"></i> Tolak Pengajuan
                            </button>

                            <button type="submit" name="status" value="disetujui" class="btn-action" style="flex: 1; background: #22c55e; color: white;">
                                <i class="fas fa-check-circle"></i> Setujui Surat
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>