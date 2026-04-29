<?php
require_once '../../app/Core/Auth.php';
require_once '../../config/Database.php';
require_once '../../app/Models/Admin.php';

Auth::check();
Auth::role('admin');

$db = (new Database())->getConnection();
$adminModel = new Admin($db);
$daftarSurat = $adminModel->getAllSurat();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin Dashboard - SI-JTI</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background: #f4f7f6; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #343a40; color: white; }
        .status { font-weight: bold; padding: 5px 10px; border-radius: 4px; font-size: 0.8em; }
        .tertunda { background: #ffc107; color: #000; }
        .disetujui { background: #28a745; color: #fff; }
        .ditolak { background: #dc3545; color: #fff; }
        .btn-verif { text-decoration: none; background: #007bff; color: white; padding: 5px 10px; border-radius: 3px; font-size: 0.9em; }
    </style>
</head>
<body>
    <h2>Panel Kelola Surat JTI</h2>
    <p>Selamat Datang, <b><?= $_SESSION['nama']; ?></b> | <a href="../logout.php">Logout</a></p>
    <hr>

    <table>
        <thead>
            <tr>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Jenis Surat</th>
                <th>Bukti (PDF)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($daftarSurat as $s) : ?>
            <tr>
                <td><?= $s['nim']; ?></td>
                <td><?= $s['nama_mahasiswa']; ?></td>
                <td><?= $s['jenis_surat']; ?></td>
                <td>
                    <a href="../../uploads/<?= $s['bukti_pendukung']; ?>" target="_blank">Lihat PDF</a>
                </td>
                <td>
                    <span class="status <?= $s['status']; ?>"><?= strtoupper($s['status']); ?></span>
                </td>
                <td>
                    <?php if ($s['status'] == 'tertunda') : ?>
                        <a href="form_verifikasi.php?id=<?= $s['id']; ?>" class="btn-verif">Proses</a>
                    <?php else : ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>