<?php
$peran = $_SESSION['peran'] ?? '';
?>
<nav class="col-md-2 d-none d-md-block sidebar p-3">
    <h4 class="text-center mb-4">JTI SURAT</h4>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="dashboard.php">Dashboard</a>
        </li>
        
        <?php if ($peran === 'mahasiswa'): ?>
            <li class="nav-item">
                <a class="nav-link" href="ajukan_surat.php">Ajukan Surat</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="riwayat_surat.php">Riwayat Saya</a>
            </li>
        <?php endif; ?>

        <?php if ($peran === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link" href="verifikasi_surat.php">Verifikasi Surat</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="kelola_pengguna.php">Kelola Pengguna</a>
            </li>
        <?php endif; ?>

        <?php if ($peran === 'dosen' || $peran === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link" href="daftar_absensi.php">Daftar Absensi</a>
            </li>
        <?php endif; ?>

        <li class="nav-item mt-4">
            <a class="nav-link text-danger" href="logout.php">Keluar</a>
        </li>
    </ul>
</nav>
<main class="col-md-10 ms-sm-auto px-md-4 py-4">