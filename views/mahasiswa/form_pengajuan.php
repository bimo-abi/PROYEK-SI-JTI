<?php
require_once '../../autoload.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Menangkap jenis surat dari halaman sebelumnya
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : 'sakit';
$judul_halaman = "";

// Judul dinamis berdasarkan pilihan
if ($jenis == 'sakit') $judul_halaman = "Surat Izin Sakit";
elseif ($jenis == 'kampus') $judul_halaman = "Surat Izin Kegiatan Kampus";
else $judul_halaman = "Surat Izin Kegiatan Luar Kampus";

$current_page = 'pengajuan';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Form Pengajuan - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>

            <div class="content">
                <div class="form-container" style="background: white; padding: 30px; border-radius: 15px;">
                    <h3>Beranda mahasiswa (Form Pengajuan <?= $judul_halaman ?>)</h3>
                    <hr style="margin: 20px 0;">

                    <form action="../../process/surat_process.php?action=tambah" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="jenis_surat" value="<?= $jenis ?>">

                        <div style="margin-bottom: 15px;">
                            <label>Keperluan / Alasan :</label>
                            <textarea name="keperluan" rows="4" style="width: 100%; padding: 10px; border-radius: 8px;" required placeholder="Jelaskan alasan pengajuan surat..."></textarea>
                        </div>

                        <!-- Di file form_pengajuan.php atau sejenisnya -->
                        <div class="form-group">
                            <label for="file_pdf">Upload Lampiran (PDF) <span style="color:red">*</span></label>
                            <input type="file" name="file_pdf" id="file_pdf" accept="application/pdf" required>
                            <!-- <small>Hanya file .pdf yang diperbolehkan.</small> -->
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn-lanjut" style="background: #00a2ed; border: none; padding: 10px 25px; color: white; border-radius: 5px; cursor: pointer;">
                                Kirim Pengajuan
                            </button>
                            <a href="pengajuan_surat.php" style="padding: 10px 25px; background: #ccc; text-decoration: none; color: #333; border-radius: 5px;">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>