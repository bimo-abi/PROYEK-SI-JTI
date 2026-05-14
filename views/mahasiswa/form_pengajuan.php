<?php
require_once '../../autoload.php';
session_start();

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
    <!-- FONT AWESOME CDN -->
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
            background-color: #fff;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>

            <div class="content">
                <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px;">
                    <h3 style="font-weight: 800; font-size: 1.1rem; margin-bottom: 30px; text-transform: uppercase; letter-spacing: 0.5px;">FORM PENGAJUAN SURAT (<?= strtoupper($judul_halaman) ?>)</h3>
                    
                    <form action="../../process/surat_process.php?action=tambah" method="POST" enctype="multipart/form-data">
                        
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
                            <textarea name="keterangan" class="form-input" rows="4" required style="resize: vertical;"></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 140px 1fr; align-items: center; margin-bottom: 15px; gap: 15px;">
                                <label class="form-label">Tanggal Mulai <span>:</span></label>
                            <div style="width: 200px;">
                                <input type="date" 
                                    name="tanggal_mulai" 
                                    id="tanggal_mulai"
                                    class="form-input" 
                                    value="<?= date('Y-m-d'); ?>" 
                                    min="<?= date('Y-m-d'); ?>"
                                    required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 140px 1fr; align-items: center; margin-bottom: 30px; gap: 15px;">
                            <label class="form-label">Tanggal Selesai <span>:</span></label>
                            <div style="width: 200px;">
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-input" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 140px 1fr; align-items: start; margin-bottom: 25px; gap: 15px;">
                            <label class="form-label" style="padding-top: 10px;">Upload Bukti <span>:</span></label>
                            <div style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 8px; background: white; overflow: hidden; width: 100%;">
                                <label for="file_pdf" style="background: #f8f9fa; padding: 10px 15px; border-right: 1px solid #ccc; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.9rem; color: #333; margin: 0; min-width: 120px; justify-content: center;">
                                    <i class="fa-solid fa-folder"></i> Upload File
                                </label>
                                <input type="file" id="file_pdf" name="file_pdf" accept="application/pdf" required style="display: none;" onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : 'Format file dalam bentuk PDF (Maks 2MB)'">
                                <span id="file-name" style="padding: 10px 15px; color: #999; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex-grow: 1;">Format file dalam bentuk PDF (Maks 2MB)</span>
                            </div>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <button type="submit" style="background: #00a2ed; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: 600;">Kirim Pengajuan</button>
                            <a href="pengajuan_surat.php" style="padding: 12px 30px; background: #ccc; text-decoration: none; color: #333; border-radius: 8px; font-weight: 600;">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script>
    const inputMulai = document.getElementById('tanggal_mulai');
    const inputSelesai = document.getElementById('tanggal_selesai');

    function hitungTanggalSelesai() {
        if (inputMulai.value) {
            let date = new Date(inputMulai.value);
            
            // Tambahkan 3 hari
            date.setDate(date.getDate() + 3);
            
            // Format ke YYYY-MM-DD agar bisa dibaca input date
            let year = date.getFullYear();
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let day = String(date.getDate()).padStart(2, '0');
            
            let hasil = `${year}-${month}-${day}`;
            
            inputSelesai.value = hasil;
            // Opsional: set minimal tanggal selesai agar tidak bisa backdate
            inputSelesai.min = inputMulai.value;
        }
    }

    // Jalankan fungsi saat pertama kali halaman dibuka
    window.onload = hitungTanggalSelesai;

    // Jalankan fungsi setiap kali Tanggal Mulai diubah
    inputMulai.addEventListener('change', hitungTanggalSelesai);
</script>
</body>

</html>