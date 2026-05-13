<?php
$foto_sidebar = "../../assets/img/avatar.png"; // Default
if (!empty($_SESSION['foto_profil'])) {
    $foto_sidebar = "../../assets/img/profiles/" . $_SESSION['foto_profil'];
} else if (isset($db)) {
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
        <img src="<?= $foto_sidebar ?>?t=<?= time() ?>" alt="User">
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
    </ul>

    <div class="sidebar-logout">
        <a href="../../process/auth_process.php?action=logout">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>