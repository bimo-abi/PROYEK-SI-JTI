<?php
session_start();
require_once '../config/Database.php';
require_once '../app/Models/Surat.php';

// Proteksi: Hanya mahasiswa yang bisa akses
if (!isset($_SESSION['peran']) || $_SESSION['peran'] !== 'mahasiswa') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$suratModel = new Surat($db);
$jenisSurat = $suratModel->getJenisSurat();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Pengajuan Surat Izin</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="surat_proses.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label">Jenis Surat</label>
                        <select name="id_jenis_surat" class="form-select" required>
                            <option value="">-- Pilih Jenis Surat --</option>
                            <?php foreach ($jenisSurat as $js): ?>
                                <option value="<?= $js['id'] ?>"><?= $js['nama_surat'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keperluan / Alasan</label>
                        <textarea name="keperluan" class="form-control" rows="4" placeholder="Contoh: Mengikuti lomba tingkat nasional di Jakarta..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Berkas Pendukung (Wajib PDF)</label>
                        <input type="file" name="berkas_pdf" class="form-control" accept="application/pdf" required>
                        <div class="form-text text-danger">*Maksimal ukuran file 2MB dalam format .pdf</div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end">
                        <a href="dashboard.php" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="alert alert-info">
            <h5>Informasi Pengajuan</h5>
            <ol class="small">
                <li>Pilih jenis surat yang sesuai (Sakit/Kegiatan Kampus/Luar Kampus).</li>
                <li>Lampirkan bukti berupa surat dokter atau surat undangan kegiatan dalam bentuk <strong>PDF</strong>.</li>
                <li>Admin akan meninjau pengajuan Anda dalam 1x24 jam.</li>
                <li>Dosen dapat melihat status izin Anda melalui dashboard mereka.</li>
            </ol>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>