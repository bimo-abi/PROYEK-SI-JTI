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
<div class="sidebar" style="background: linear-gradient(180deg, #1a1a1a 0%, #2c3e50 100%); color: white; min-height: 100vh; width: 250px;">
    <div class="sidebar-brand" style="padding: 30px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <h2 style="font-weight: 800; letter-spacing: 2px;">SI - JTI</h2>
    </div>

    <div class="sidebar-user" style="text-align: center; padding: 20px;">
        <img src="<?= $foto_sidebar ?>?t=<?= time() ?>" style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid #00a2ed; padding: 3px; object-fit: cover;">
        <p style="margin-top: 10px; font-weight: 600;"><?= htmlspecialchars($_SESSION['nama']) ?></p>
        <small style="color: #00a2ed; font-size: 0.75rem;">Dosen</small>
    </div>

    <ul class="sidebar-menu" style="list-style: none; padding: 10px;">
        <li style="margin-bottom: 10px;">
            <a href="dashboard.php" class="<?= ($current_page == 'dashboard') ? 'active' : '' ?>" 
               style="display: flex; align-items: center; gap: 15px; color: white; text-decoration: none; padding: 12px 20px; border-radius: 10px;">
                <i class="fas fa-th-large"></i> <span>Dashboard</span>
            </a>
        </li>
        <li style="margin-bottom: 10px;">
            <a href="surat_masuk.php" class="<?= ($current_page == 'surat_masuk') ? 'active' : '' ?>"
               style="display: flex; align-items: center; gap: 15px; color: white; text-decoration: none; padding: 12px 20px; border-radius: 10px;">
                <i class="fas fa-envelope-open-text"></i> <span>Data Mahasiswa</span>
            </a>
        </li>
        <li style="margin-top: 50px;">
            <a href="../../auth/logout.php" style="display: flex; align-items: center; gap: 15px; color: #ff4757; text-decoration: none; padding: 12px 20px; font-weight: bold;">
                <i class="fas fa-sign-out-alt"></i> <span>Keluar</span>
            </a>
        </li>
    </ul>
</div>