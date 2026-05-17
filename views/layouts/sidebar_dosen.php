<?php
// Pastikan variabel $db sudah tersedia dari file yang meng-include sidebar ini
$foto_sidebar = "../../assets/img/profiles/avatar.png"; // Default path avatar kamu

if (isset($_SESSION['user_id']) && isset($db)) {
    // Ambil foto profil terbaru dari database
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

    <div class="sidebar-user-mini" style="text-align: center; padding: 20px 10px;">
        <img src="<?= $foto_sidebar ?>?t=<?= time() ?>" alt="User"
            style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 3px solid rgba(255,255,255,0.2);">
        <p style="font-weight: 600; margin-bottom: 0; color: white;">
            <?= isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'Dosen' ?>
        </p>
        <small style="color: #60a5fa; font-weight: 500; text-transform: uppercase; letter-spacing: 1px;">Dosen</small>
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
        <li>
            <a href="profil.php" class="<?= (isset($current_page) && ($current_page == 'profil' || $current_page == 'edit_profile')) ? 'active' : '' ?>">
                <i class="fas fa-user-circle"></i> Profil Saya
            </a>
        </li>
    </ul>

    <div class="sidebar-logout">
        <a href="../../process/logout.php" onclick="return confirm('Apakah anda yakin ingin keluar?')">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>