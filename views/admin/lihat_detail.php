<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

$db = (new Database())->getConnection();
$id_pengajuan = $_GET['id'] ?? null;

// Gunakan SELECT * agar lebih fleksibel terhadap perubahan kolom
$query = "SELECT p.*, u.nama, pr.nama_prodi, g.nama_golongan, d.*
          FROM pengajuan_surat p
          LEFT JOIN pengguna u ON u.email LIKE CONCAT(p.nim, '%')
          LEFT JOIN detail_pengguna d ON p.nim = d.nomor_induk
          LEFT JOIN prodi pr ON d.id_prodi = pr.id
          LEFT JOIN golongan g ON d.id_golongan = g.id
          WHERE p.id_pengajuan = ?";

$stmt = $db->prepare($query);
$stmt->execute([$id_pengajuan]);
$surat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$surat) die("Data tidak ditemukan.");
?>

<!-- Tampilan tetap sama, pastikan variabelnya pas -->
<div class="card-verifikasi" style="background: white; padding: 40px; border-radius: 15px; max-width: 700px; margin: auto;">
    <h2 style="text-align: center;">Verifikasi Keaslian Surat</h2>
    <hr>
    <div style="display: flex; justify-content: space-between; margin-top: 30px;">
        <div>
            <p><strong>NIM :</strong><br><?= htmlspecialchars($surat['nim']) ?></p>
            <p><strong>Nama :</strong><br><?= htmlspecialchars($surat['nama'] ?? 'Mahasiswa JTI') ?></p>
            <p><strong>Prodi :</strong><br><?= htmlspecialchars($surat['nama_prodi'] ?? '-') ?></p>
            <p><strong>Golongan :</strong><br><?= htmlspecialchars($surat['nama_golongan'] ?? '-') ?></p>
            <p><strong>Semester :</strong><br><?= htmlspecialchars($surat['semester'] ?? '-') ?></p>
        </div>
        <div>
            <p><strong>Keterangan Izin :</strong><br><?= htmlspecialchars($surat['jenis_surat']) ?></p>
            <p><strong>Lampiran :</strong><br>
                <a href="../../assets/uploads/pdf/<?= $surat['file_path'] ?>" target="_blank" style="color: #00a2ed; text-decoration: none;">
                    Lihat PDF <i class="fas fa-file-pdf"></i>
                </a>
            </p>
        </div>
    </div>

    <div style="text-align: right; margin-top: 50px;">
        <form action="../../process/proses_verifikasi.php" method="POST" style="display: inline;">
            <input type="hidden" name="id_pengajuan" value="<?= $surat['id_pengajuan'] ?>">
            <button type="submit" name="status" value="ditolak" style="background: red; color: white; border: none; padding: 10px 25px; border-radius: 5px; cursor: pointer; margin-right: 10px;">Tolak</button>
            <button type="submit" name="status" value="disetujui" style="background: #00a2ed; color: white; border: none; padding: 10px 25px; border-radius: 5px; cursor: pointer;">Terima</button>
        </form>
    </div>
</div>