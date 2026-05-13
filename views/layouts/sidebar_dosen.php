<?php
// Pastikan variabel $db sudah tersedia dari file yang meng-include sidebar ini
$foto_sidebar = "../../assets/img/avatar.png"; // Default jika tidak ada foto

if (!empty($_SESSION['foto_profil'])) {
    $foto_sidebar = "../../assets/img/profiles/" . $_SESSION['foto_profil'];
} else if (isset($db)) {
    // Ambil foto dari database jika session foto masih kosong
    $stmt_side = $db->prepare("SELECT foto_profil FROM detail_pengguna WHERE id_pengguna = ?");
    $stmt_side->execute([$_SESSION['user_id']]);
    $row_side = $stmt_side->fetch(PDO::FETCH_ASSOC);
    if (!empty($row_side['foto_profil'])) {
        $foto_sidebar = "../../assets/img/profiles/" . $row_side['foto_profil'];
        $_SESSION['foto_profil'] = $row_side['foto_profil'];
    }
}
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <h2>SI - JTI</h2>
    </div>

    <div class="sidebar-user-mini">
        <!-- Tambahkan parameter ?t=time() agar browser selalu refresh foto saat diganti -->
        <img src="<?= $foto_sidebar ?>?t=<?= time() ?>" alt="User" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;">
        <p><?= isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'Dosen' ?></p>
        <small>Dosen</small>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?= (isset($current_page) && $current_page == 'dashboard') ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="data_mahasiswa.php" class="<?= (isset($current_page) && $current_page == 'data_mahasiswa') ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Data Mahasiswa
            </a>
        </li>
        <!-- Tambahan Menu Edit Profil Sesuai Permintaan -->
        <li>
            <a href="edit_profile.php" class="<?= (isset($current_page) && $current_page == 'edit_profile') ? 'active' : '' ?>">
                <i class="fas fa-user-cog"></i> Edit Profil
            </a>
        </li>
    </ul>

    <div class="sidebar-logout">
        <!-- Sesuaikan dengan action logout yang kamu gunakan -->
        <a href="../../process/auth_process.php?action=logout">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>