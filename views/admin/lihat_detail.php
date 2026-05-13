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

// Query untuk mengambil data lengkap pengajuan dan mahasiswa
$query = "SELECT p.*, u.nama, u.email, pr.nama_prodi, g.nama_golongan, d.nomor_telepon, d.alamat
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

$current_page = 'surat_masuk';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Surat - Admin SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_admin.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar_admin.php'; ?>
            
            <div class="content">
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 1000px; margin: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <h3 style="margin: 0; color: #333;"><i class="fas fa-clipboard-check" style="color: #00a2ed;"></i> Detail Verifikasi Surat</h3>
                        <a href="surat_masuk.php" style="text-decoration: none; color: #666; font-size: 0.9rem;"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </div>
                    
                    <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 30px;">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                        <!-- Bagian Kiri: Data Mahasiswa -->
                        <div class="info-section">
                            <h5 style="color: #00a2ed; margin-bottom: 20px; border-bottom: 2px solid #00a2ed; display: inline-block; padding-bottom: 5px;">
                                <i class="fas fa-user-graduate"></i> Identitas Mahasiswa
                            </h5>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 10px 0; color: #888; width: 140px;">Nama Lengkap</td>
                                    <td style="font-weight: 600;">: <?= htmlspecialchars($surat['nama'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">NIM</td>
                                    <td style="font-family: monospace; font-size: 1rem;">: <?= htmlspecialchars($surat['nim'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">Prodi</td>
                                    <td>: <?= htmlspecialchars($surat['nama_prodi'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">Golongan</td>
                                    <td>: <?= htmlspecialchars($surat['nama_golongan'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">No. Telepon</td>
                                    <td>: <?= htmlspecialchars($surat['nomor_telepon'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">Email</td>
                                    <td>: <?= htmlspecialchars($surat['email'] ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Bagian Kanan: Detail Pengajuan -->
                        <div class="info-section">
                            <h5 style="color: #00a2ed; margin-bottom: 20px; border-bottom: 2px solid #00a2ed; display: inline-block; padding-bottom: 5px;">
                                <i class="fas fa-file-alt"></i> Detail Pengajuan Surat
                            </h5>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 10px 0; color: #888; width: 140px;">Kategori Surat</td>
                                    <td style="font-weight: 600; text-transform: uppercase;">: <?= htmlspecialchars($surat['jenis_surat'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">Tanggal Pengajuan</td>
                                    <td>: <?= date('d F Y, H:i', strtotime($surat['tanggal_pengajuan'])) ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #888;">Keperluan / Ket.</td>
                                    <td style="background: #fcfcfc; padding: 10px; border-radius: 8px; border: 1px solid #f0f0f0; line-height: 1.5;">
                                        <?= nl2br(htmlspecialchars($surat['keperluan'] ?? '-')) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 0 0 0;" colspan="2">
                                        <label style="color: #888; font-size: 0.8rem; display: block; margin-bottom: 10px;">Dokumen Pendukung (PDF):</label>
                                        <a href="../../assets/uploads/pdf/<?= $surat['file_path'] ?>" target="_blank" style="display: flex; align-items: center; justify-content: center; gap: 10px; background: #e3f2fd; color: #00a2ed; text-decoration: none; padding: 15px; border-radius: 12px; font-weight: bold; border: 2px dashed #00a2ed;">
                                            <i class="fas fa-file-pdf" style="font-size: 1.5rem;"></i> LIHAT LAMPIRAN SURAT (PDF)
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Tombol Keputusan -->
                    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #eee; display: flex; justify-content: center; gap: 20px;">
                        <form action="../../process/proses_verifikasi.php" method="POST" style="width: 100%; display: flex; justify-content: center; gap: 20px;">
                            <input type="hidden" name="id_pengajuan" value="<?= $surat['id_pengajuan'] ?>">
                            
                            <button type="submit" name="status" value="ditolak" style="flex: 1; max-width: 200px; background: #ff4757; color: white; border: none; padding: 15px; border-radius: 12px; cursor: pointer; font-weight: bold; font-size: 1rem; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <i class="fas fa-times-circle"></i> TOLAK SURAT
                            </button>
                            
                            <button type="submit" name="status" value="disetujui" style="flex: 1; max-width: 200px; background: #2ed573; color: white; border: none; padding: 15px; border-radius: 12px; cursor: pointer; font-weight: bold; font-size: 1rem; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <i class="fas fa-check-circle"></i> TERIMA SURAT
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>