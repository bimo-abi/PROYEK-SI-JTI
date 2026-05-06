<div class="sidebar">
    <div class="sidebar-brand">
        <h2>SI - JTI</h2>
    </div>
    
    <div class="sidebar-user-mini">
        <!-- Gambar avatar bisa disesuaikan jalurnya -->
        <img src="../../assets/img/avatar.png" alt="User">
        <!-- Mengambil nama dari session yang sudah login -->
        <p><?= isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Mahasiswa' ?></p>
    </div>

    <ul class="sidebar-menu">
        <li>
            <!-- Perbaikan: href harusnya ke dashboard.php, bukan profil.php -->
            <a href="dashboard.php" class="<?= (isset($current_page) && $current_page == 'dashboard') ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        
        <!-- Menu Dropdown Surat -->
        <li class="dropdown <?= (isset($current_page) && ($current_page == 'pengajuan' || $current_page == 'daftar_surat')) ? 'active' : '' ?>">
            <a href="javascript:void(0)" class="dropbtn" onclick="toggleDropdown()">
                <i class="fas fa-envelope"></i> Surat <span class="arrow">▼</span>
            </a>
            <div class="dropdown-content" id="dropdownSurat">
                <a href="pengajuan_surat.php" class="<?= (isset($current_page) && $current_page == 'pengajuan') ? 'active-sub' : '' ?>">
                    Pengajuan Surat
                </a>
                <a href="daftar_pengajuan.php" class="<?= (isset($current_page) && $current_page == 'daftar_surat') ? 'active-sub' : '' ?>">
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
        <!-- Pastikan file auth_process.php memiliki case 'logout' -->
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

/* Otomatis buka jika halaman aktif adalah bagian dari surat */
<?php if (isset($current_page) && ($current_page == 'pengajuan' || $current_page == 'daftar_surat')): ?>
    document.getElementById("dropdownSurat").style.display = "block";
    document.querySelector(".arrow").style.transform = "rotate(180deg)";
<?php endif; ?>
</script>