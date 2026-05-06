<?php
// Pastikan variabel $db tersedia dari file yang meng-include sidebar ini
// Ambil foto dari session jika tersedia, jika tidak ambil dari database
$foto_sidebar = "../../assets/img/avatar.png"; // Default

if (!empty($_SESSION['foto_profil'])) {
    $foto_sidebar = "../../assets/img/profiles/" . $_SESSION['foto_profil'];
} else if (isset($db)) {
    // Fallback jika session foto kosong, ambil langsung ke DB
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
        <!-- Foto Profil Dinamis dengan cache-buster time() -->
        <img src="<?= $foto_sidebar ?>?t=<?= time() ?>" alt="User" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.2);">
        <p><?= isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'Mahasiswa' ?></p>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?= (isset($current_page) && $current_page == 'dashboard') ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>

        <!-- Menu Dropdown Surat -->
        <!-- Perbaikan: Sinkronisasi current_page agar menu tetap terbuka saat di sub-menu -->
        <li class="dropdown <?= (isset($current_page) && ($current_page == 'pengajuan' || $current_page == 'daftar_pengajuan')) ? 'active' : '' ?>">
            <a href="javascript:void(0)" class="dropbtn" onclick="toggleDropdown()">
                <i class="fas fa-envelope"></i> Surat <span class="arrow">▼</span>
            </a>
            <div class="dropdown-content" id="dropdownSurat" style="<?= (isset($current_page) && ($current_page == 'pengajuan' || $current_page == 'daftar_pengajuan')) ? 'display: block;' : 'display: none;' ?>">
                <a href="pengajuan_surat.php" class="<?= ($current_page == 'pengajuan') ? 'active-sub' : '' ?>">
                    Pengajuan Surat
                </a>
                <a href="daftar_pengajuan.php" class="<?= ($current_page == 'daftar_pengajuan') ? 'active-sub' : '' ?>">
                    Daftar Pengajuan Surat
                </a>
            </div>
        </li>

        <li>
            <a href="notifikasi.php" class="<?= (isset($current_page) && $current_page == 'notifikasi') ? 'active' : '' ?>">
                <i class="fas fa-bell"></i> Notifikasi
            </a>
        </li>

        <li>
            <a href="profil.php" class="<?= (isset($current_page) && $current_page == 'profil') ? 'active' : '' ?>">
                <i class="fas fa-user"></i> Profil
            </a>
        </li>
    </ul>

    <div class="sidebar-logout">
        <a href="../../process/auth_process.php?action=logout">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>

<script>
    /* Script untuk handle buka-tutup dropdown surat */
    function toggleDropdown() {
        const dropdown = document.getElementById("dropdownSurat");
        const arrow = document.querySelector(".arrow");

        if (dropdown.style.display === "block") {
            dropdown.style.display = "none";
            arrow.style.transform = "rotate(0deg)";
        } else {
            dropdown.style.display = "block";
            arrow.style.transform = "rotate(180deg)";
        }
    }

    /* Animasi rotasi panah jika menu aktif saat halaman dimuat */
    window.addEventListener('DOMContentLoaded', (event) => {
        const dropdown = document.getElementById("dropdownSurat");
        const arrow = document.querySelector(".arrow");
        if (dropdown && dropdown.style.display === "block") {
            arrow.style.transform = "rotate(180deg)";
        }
    });
</script>