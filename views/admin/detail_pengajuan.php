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

// 2. Ambil ID dari URL
$id_pengajuan = $_GET['id'] ?? null;
if (!$id_pengajuan) {
    header("Location: surat_masuk.php");
    exit();
}
// 3. Query Data Lengkap
try {
    $query = "SELECT p.*, u.nama, u.email, dp.nomor_induk, pr.nama_prodi, g.nama_golongan, j.nama_surat 
              FROM pengajuan_surat p
              JOIN detail_pengguna dp ON p.nim = dp.nomor_induk
              JOIN pengguna u ON dp.id_pengguna = u.id
              LEFT JOIN prodi pr ON dp.id_prodi = pr.id
              LEFT JOIN golongan g ON dp.id_golongan = g.id
              LEFT JOIN jenis_surat j ON p.jenis_surat = j.kode_surat
              WHERE p.id = ?";

    $stmt = $db->prepare($query);
    $stmt->execute([$id_pengajuan]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        header("Location: surat_masuk.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Konfigurasi Layout
$current_page = 'surat_masuk';
$page_title = 'Verifikasi Pengajuan';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Verifikasi - Admin SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="margin: 0; background: #f4f7f6; font-family: 'Segoe UI', sans-serif;">
    <div class="wrapper" style="display: flex;">
        <?php include '../layouts/sidebar_admin.php'; ?>

        <div class="main-container" style="flex-grow: 1; min-height: 100vh;">
            <?php include '../layouts/topbar_admin.php'; ?>

            <div class="content" style="padding: 30px;">
                <div style="max-width: 900px; margin: 0 auto; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden;">
                    
                    <div style="background: #00a2ed; padding: 20px; color: white; text-align: center;">
                        <h3 style="margin: 0; text-transform: uppercase; letter-spacing: 1px;">Formulir Verifikasi Surat</h3>
                    </div>

                    <div style="padding: 40px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; border-bottom: 1px solid #eee; padding-bottom: 30px;">
                            <div>
                                <h5 style="color: #00a2ed; margin-bottom: 20px; border-left: 4px solid #00a2ed; padding-left: 10px;">IDENTITAS MAHASISWA</h5>
                                <p style="margin-bottom: 12px; font-size: 0.9rem;"><strong>NIM :</strong><br> <span style="color: #555;"><?= htmlspecialchars($data['nomor_induk']) ?></span></p>
                                <p style="margin-bottom: 12px; font-size: 0.9rem;"><strong>Nama Lengkap :</strong><br> <span style="color: #555;"><?= htmlspecialchars($data['nama']) ?></span></p>
                                <p style="margin-bottom: 12px; font-size: 0.9rem;"><strong>Program Studi :</strong><br> <span style="color: #555;"><?= htmlspecialchars($data['nama_prodi']) ?></span></p>
                                <p style="margin-bottom: 12px; font-size: 0.9rem;"><strong>Golongan :</strong><br> <span style="color: #555;"><?= htmlspecialchars($data['nama_golongan'] ?? '-') ?></span></p>
                            </div>

                            <div>
                                <h5 style="color: #00a2ed; margin-bottom: 20px; border-left: 4px solid #00a2ed; padding-left: 10px;">DETAIL PENGAJUAN</h5>
                                <p style="margin-bottom: 12px; font-size: 0.9rem;"><strong>Jenis Surat :</strong><br> <span style="color: #555;"><?= htmlspecialchars($data['nama_surat']) ?></span></p>
                                <p style="margin-bottom: 12px; font-size: 0.9rem;"><strong>Alasan Izin :</strong><br> <span style="color: #555;"><?= htmlspecialchars($data['alasan']) ?></span></p>
                                <p style="margin-bottom: 12px; font-size: 0.9rem;"><strong>Lampiran File :</strong><br> 
                                    <?php if($data['file_path']): ?>
                                        <a href="../../assets/uploads/surat/<?= $data['file_path'] ?>" target="_blank" style="color: #e74c3c; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 5px;">
                                            <i class="fas fa-file-pdf"></i> Buka Lampiran PDF
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #999;">Tidak ada lampiran</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center;">
                            <a href="surat_masuk.php" style="color: #666; text-decoration: none; font-size: 0.9rem;"><i class="fas fa-arrow-left"></i> Kembali</a>
                            
                            <form action="../../process/proses_status.php" method="POST" style="display: flex; gap: 15px;">
                                <input type="hidden" name="id_pengajuan" value="<?= $data['id'] ?>">
                                
                                <button type="submit" name="aksi" value="tolak" 
                                        style="background: #fdf0f0; color: #e74c3c; border: 1px solid #e74c3c; padding: 10px 25px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s;"
                                        onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?')">
                                    <i class="fas fa-times"></i> TOLAK
                                </button>

                                <button type="submit" name="aksi" value="terima" 
                                        style="background: #27ae60; color: white; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s;"
                                        onclick="return confirm('Apakah Anda yakin ingin menyetujui pengajuan ini?')">
                                    <i class="fas fa-check"></i> SETUJUI
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>