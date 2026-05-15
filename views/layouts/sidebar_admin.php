<?php

/**
 * SIDEBAR ADMIN SI-JTI
 * Sinkronisasi otomatis dengan Session Foto Profil
 */

// 1. Tentukan Foto Profil (Prioritas: Session > Database > Default)
$foto_sidebar = "../../assets/img/profiles/avatar.jpg";

if (isset($_SESSION['foto_profil']) && !empty($_SESSION['foto_profil'])) {
    // Mengambil langsung dari session agar sinkron setelah edit profil
    $foto_sidebar = "../../assets/img/profiles/" . $_SESSION['foto_profil'];
} elseif (isset($_SESSION['user_id']) && isset($db)) {
    // Backup: Jika session foto kosong, ambil dari database
    try {
        $stmt_side = $db->prepare("SELECT foto_profil FROM detail_pengguna WHERE id_pengguna = ?");
        $stmt_side->execute([$_SESSION['user_id']]);
        $row_side = $stmt_side->fetch(PDO::FETCH_ASSOC);

        if (!empty($row_side['foto_profil'])) {
            $foto_sidebar = "../../assets/img/profiles/" . $row_side['foto_profil'];
            $_SESSION['foto_profil'] = $row_side['foto_profil'];
        }
    } catch (PDOException $e) {
        // Tetap gunakan default jika terjadi error
    }
}
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <h2>SI - JTI</h2>
    </div>

    <div class="sidebar-user-mini" style="text-align: center; padding: 20px 0;">
        <img src="<?= $foto_sidebar ?>?t=<?= time() ?>"
            style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #00a2ed;">
        <p style="margin-top: 10px; font-weight: 600; color: white; margin-bottom: 0;">
            <?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator') ?>
        </p>
        <!-- <small style="color: #00a2ed; font-weight: 500;">Administrator</small> -->
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?= (isset($current_page) && $current_page == 'dashboard') ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="surat_masuk.php" class="<?= (isset($current_page) && $current_page == 'surat_masuk') ? 'active' : '' ?>">
                <i class="fas fa-envelope-open-text"></i> Surat Masuk
            </a>
        </li>
        <li>
            <a href="riwayat.php" class="<?= (isset($current_page) && $current_page == 'riwayat') ? 'active' : '' ?>">
                <i class="fas fa-history"></i> Riwayat Surat
            </a>
        </li>
        <li>
            <a href="edit_profil.php" class="<?= (isset($current_page) && $current_page == 'profil') ? 'active' : '' ?>">
                <i class="fas fa-user-edit"></i> Profil Saya
            </a>
        </li>
    </ul>

    <div class="sidebar-logout">
        <a href="../../process/auth_process.php?action=logout" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>