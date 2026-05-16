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
        .form-grid {
            display: grid;
            grid-template-columns: 140px 1fr;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
        }

        .form-label {
            font-weight: 500;
            color: #333;
            display: flex;
            justify-content: space-between;
            font-size: 0.95rem;
        }

        .form-input {
            padding: 11px 15px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            width: 100%;
            outline: none;
            font-family: inherit;
            font-size: 0.95rem;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }

        .form-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-input[readonly] {
            background-color: #f1f5f9;
            color: #64748b;
            border-color: #e2e8f0;
            cursor: not-allowed;
        }
    </style>
</head>

<body style="background-color: #f4f7fa;">
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>
            <div class="content" style="padding: 30px;">
                <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.03); max-width: 800px; margin: auto;">
                    <h3 style="font-weight: 800; font-size: 1.1rem; margin-top: 0; margin-bottom: 35px; text-transform: uppercase; letter-spacing: 0.5px; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
                        FORM PENGAJUAN SURAT (<?= strtoupper($judul_halaman) ?>)
                    </h3>

                    <form action="../../process/surat_process.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="jenis_surat" value="<?= $jenis ?>">

                        <div class="form-grid">
                            <label class="form-label">Nama <span>:</span></label>
                            <input type="text" class="form-input" value="<?= htmlspecialchars($nama) ?>" readonly>
                        </div>

                        <div class="form-grid">
                            <label class="form-label">NIM <span>:</span></label>
                            <input type="text" class="form-input" value="<?= htmlspecialchars($nim) ?>" readonly>
                        </div>

                        <div class="form-grid" style="align-items: start;">
                            <label class="form-label" style="padding-top: 10px;">Keterangan <span>:</span></label>
                            <textarea name="keterangan" class="form-input" rows="4" required placeholder="Tuliskan alasan detail izin anda..." style="resize: vertical;"></textarea>
                        </div>

                        <div class="form-grid">
                            <label class="form-label">Tanggal Mulai <span>:</span></label>
                            <input type="date" name="tgl_mulai" id="tgl_mulai"
                                value="<?= date('Y-m-d'); ?>" readonly class="form-input">
                        </div>

                        <div class="form-grid">
                            <label class="form-label">Tanggal Selesai <span>:</span></label>
                            <input type="date" name="tgl_selesai" id="tgl_selesai" required class="form-input">
                        </div>

                        <div class="form-grid" style="align-items: start; margin-bottom: 30px;">
                            <label class="form-label" style="padding-top: 10px;">Upload Bukti <span>:</span></label>
                            <div style="display: flex; align-items: center; border: 1px solid #cbd5e1; border-radius: 8px; background: white; overflow: hidden; width: 100%;">
                                <label for="file_pdf" style="background: #f8f9fa; padding: 11px 15px; border-right: 1px solid #cbd5e1; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.9rem; color: #333; margin: 0; min-width: 120px; justify-content: center;">
                                    <i class="fa-solid fa-folder"></i> Pilih File
                                </label>
                                <input type="file" id="file_pdf" name="file_pdf" accept="application/pdf" required style="display: none;" onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : 'Format PDF (Maks 2MB)'">
                                <span id="file-name" style="padding: 11px 15px; color: #94a3b8; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex-grow: 1;">Format PDF (Maks 2MB)</span>
                            </div>
                        </div>

                        <div style="display: flex; gap: 12px; margin-left: 155px;">
                            <button type="submit" style="background: #4f46e5; color: white; border: none; padding: 12px 28px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.95rem; transition: background 0.2s;">Kirim Pengajuan</button>
                            <a href="pengajuan_surat.php" style="padding: 12px 28px; background: #e2e8f0; text-decoration: none; color: #475569; border-radius: 8px; font-weight: 600; font-size: 0.95rem; transition: background 0.2s;">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputMulai = document.getElementById('tgl_mulai');
            const inputSelesai = document.getElementById('tgl_selesai');

            const tglMulaiVal = inputMulai.value;

            if (tglMulaiVal) {
                inputSelesai.min = tglMulaiVal;

                let dMax = new Date(tglMulaiVal);
                dMax.setDate(dMax.getDate() + 3);
                const maxDateString = dMax.toISOString().split('T')[0];

                inputSelesai.max = maxDateString;
                inputSelesai.value = maxDateString;
            }

            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const start = new Date(inputMulai.value);
                    const end = new Date(inputSelesai.value);

                    const selisihWaktu = end.getTime() - start.getTime();
                    const selisihHari = Math.ceil(selisihWaktu / (1000 * 3600 * 24));

                    if (selisihHari > 3 || end < start) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Perizinan Melebihi Batas!',
                            text: 'Sesuai aturan sistem JTI, maksimal izin berturut-turut adalah 3 hari dari tanggal mulai.',
                            confirmButtonColor: '#4f46e5'
                        });
                    }
                });
            }
        });
    </script>
</body>

</html>