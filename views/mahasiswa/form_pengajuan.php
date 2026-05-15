<?php
require_once '../../autoload.php';
session_start();
$hari_ini = date('Y-m-d');

use Config\Database;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();

// Pastikan NIM tersedia
if (!isset($_SESSION['nim'])) {
    $stmt = $db->prepare("SELECT nomor_induk FROM detail_pengguna WHERE id_pengguna = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data) {
        $_SESSION['nim'] = $data['nomor_induk'];
    }
}

$nim = $_SESSION['nim'] ?? '';
$nama = $_SESSION['nama'] ?? '';

$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : 'sakit';
$judul_halaman = "";

if ($jenis == 'sakit') $judul_halaman = "Surat Izin Sakit";
elseif ($jenis == 'kampus') $judul_halaman = "Surat Izin Kegiatan Kampus";
else $judul_halaman = "Surat Izin Kegiatan Luar Kampus";

$current_page = 'pengajuan';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Pengajuan - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .form-label {
            font-weight: 500;
            color: #333;
            display: flex;
            justify-content: space-between;
        }

        .form-input {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            width: 100%;
            outline: none;
            font-family: inherit;
        }

        .form-input[readonly] {
            background-color: #f8fafc;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>
            <div class="content">
                <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px; margin: auto;">
                    <h3 style="font-weight: 800; font-size: 1.1rem; margin-bottom: 30px; text-transform: uppercase; letter-spacing: 0.5px;">FORM PENGAJUAN SURAT (<?= strtoupper($judul_halaman) ?>)</h3>

                    <form action="../../process/surat_process.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="jenis_surat" value="<?= $jenis ?>">

                        <div style="display: grid; grid-template-columns: 140px 1fr; align-items: center; margin-bottom: 15px; gap: 15px;">
                            <label class="form-label">Nama <span>:</span></label>
                            <input type="text" class="form-input" value="<?= htmlspecialchars($nama) ?>" readonly>
                        </div>

                        <div style="display: grid; grid-template-columns: 140px 1fr; align-items: center; margin-bottom: 15px; gap: 15px;">
                            <label class="form-label">NIM <span>:</span></label>
                            <input type="text" class="form-input" value="<?= htmlspecialchars($nim) ?>" readonly>
                        </div>

                        <div style="display: grid; grid-template-columns: 140px 1fr; align-items: start; margin-bottom: 25px; gap: 15px;">
                            <label class="form-label" style="padding-top: 10px;">Keterangan <span>:</span></label>
                            <textarea name="keterangan" class="form-input" rows="4" required placeholder="Tuliskan alasan detail izin anda..." style="resize: vertical;"></textarea>
                        </div>

                        <div class="form-group" style="display: flex; align-items: center; margin-bottom: 20px;">
                            <label style="width: 150px; color: #334155;">Tanggal Mulai</label>
                            <span style="margin-right: 10px;">:</span>
                            <input type="date" name="tgl_mulai" class="form-control"
                                value="<?= date('Y-m-d') ?>"
                                readonly
                                style="background-color: #f1f5f9; cursor: not-allowed; width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        </div>

                        <div class="form-group" style="display: flex; align-items: center; margin-bottom: 20px;">
                            <label style="width: 150px; color: #334155;">Tanggal Selesai</label>
                            <span style="margin-right: 10px;">:</span>
                            <input type="date" name="tgl_selesai" class="form-control"
                                min="<?= date('Y-m-d') ?>"
                                required
                                style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        </div>

                        <div style="display: grid; grid-template-columns: 140px 1fr; align-items: start; margin-bottom: 25px; gap: 15px;">
                            <label class="form-label" style="padding-top: 10px;">Upload Bukti <span>:</span></label>
                            <div style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 8px; background: white; overflow: hidden; width: 100%;">
                                <label for="file_pdf" style="background: #f8f9fa; padding: 10px 15px; border-right: 1px solid #ccc; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.9rem; color: #333; margin: 0; min-width: 120px; justify-content: center;">
                                    <i class="fa-solid fa-folder"></i> Pilih File
                                </label>
                                <input type="file" id="file_pdf" name="file_pdf" accept="application/pdf" required style="display: none;" onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : 'Format PDF (Maks 2MB)'">
                                <span id="file-name" style="padding: 10px 15px; color: #999; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex-grow: 1;">Format PDF (Maks 2MB)</span>
                            </div>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <button type="submit" style="background: #4f46e5; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: 600;">Kirim Pengajuan</button>
                            <a href="pengajuan_surat.php" style="padding: 12px 30px; background: #e2e8f0; text-decoration: none; color: #475569; border-radius: 8px; font-weight: 600;">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputMulai = document.getElementById('tgl_mulai');
            const inputSelesai = document.getElementById('tgl_selesai');

            function updateMinSelesai() {
                inputSelesai.min = inputMulai.value;
                if (inputSelesai.value < inputMulai.value) {
                    inputSelesai.value = inputMulai.value;
                }
            }

            inputMulai.addEventListener('change', updateMinSelesai);

            // Set default selesai 3 hari dari mulai
            let d = new Date();
            d.setDate(d.getDate() + 3);
            inputSelesai.value = d.toISOString().split('T')[0];
            updateMinSelesai();
        });
    </script>
</body>

</html>