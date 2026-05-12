<?php
// Logika pengambilan foto profil yang sinkron dengan session
$foto_topbar = "../../assets/img/avatar.png";
?>

<div class="topbar" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <div class="topbar-title" style="font-weight: 600; color: #333; font-size: 1.1rem;">
        <!-- Menampilkan Dashboard/Profil sesuai halaman aktif -->
        <?= isset($current_page) ? ucwords(str_replace('_', ' ', $current_page)) : 'Mahasiswa' ?>
    </div>

    <div class="topbar-user" style="display: flex; align-items: center; gap: 12px;">
        <div class="user-info" style="display: flex; align-items: center; gap: 10px;">
            <!-- Nama Mahasiswa -->
            <span style="font-weight: 500; color: #555; font-size: 0.9rem;">
                <?= htmlspecialchars($_SESSION['nama'] ?? 'Mahasiswa') ?>
            </span>

            <!-- Foto Profil Dinamis -->
            <img src="<?= $foto_topbar ?>?t=<?= time() ?>"
                alt="Profile"
                style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #00a2ed;">
        </div>
    </div>
</div>