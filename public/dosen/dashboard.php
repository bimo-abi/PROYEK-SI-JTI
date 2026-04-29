<?php
require_once '../../app/Core/Auth.php';
require_once '../../config/Database.php';
require_once '../../app/Models/Dosen.php';

Auth::check();
Auth::role('dosen');

$db = (new Database())->getConnection();
$dosenModel = new Dosen($db);
$daftarSurat = $dosenModel->lihatSemuaSurat();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dosen Dashboard - SI-JTI</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background: #f8f9fa; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        .badge { background: #28a745; color: white; padding: 5px 10px; border-radius: 15px; font-size: 0.8em; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Monitoring Absensi Mahasiswa JTI</h2>
        <p>Login sebagai: <b>Dosen (<?= $_SESSION['nama']; ?>)</b> | <a href="../logout.php">Logout</a></p>
        <hr>

        <h3>Daftar Izin Mahasiswa (Telah Disetujui Admin)</h3>
        <table>
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Jenis Izin</th>
                    <th>Keperluan</th>
                    <th>Bukti</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($daftarSurat) > 0) : ?>
                    <?php foreach ($daftarSurat as $s) : ?>
                    <tr>
                        <td><?= $s['nomor_induk']; ?></td>
                        <td><?= $s['nama']; ?></td>
                        <td><?= $s['jenis_surat']; ?></td>
                        <td><?= $s['keperluan']; ?></td>
                        <td>
                            <a href="../../uploads/<?= $s['bukti_pendukung']; ?>" target="_blank">Unduh PDF</a>
                        </td>
                        <td><span class="badge">DISETUJUI</span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Belum ada data izin yang disetujui.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>