<?php
require_once '../../autoload.php';
session_start();

// Keamanan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

// Penanda halaman aktif
$current_page = 'pengajuan';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Pengajuan Surat - SI-JTI</title>

    <!-- SWEET ALERT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">

    <!-- FONT AWESOME CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <div class="wrapper">

        <!-- SIDEBAR -->
        <?php include '../layouts/sidebar.php'; ?>

        <div class="main-container">

            <!-- TOPBAR -->
            <?php include '../layouts/topbar.php'; ?>

            <div class="content">

                <div class="section-title">
                    <i class="fa-solid fa-envelope"></i>
                    Pengajuan Surat
                </div>

                <!-- BOX -->
                <div class="selection-box text-center">

                    <h3>Pilih Jenis Surat</h3>

                    <p>
                        Silakan pilih jenis surat yang ingin Anda ajukan
                    </p>

                    <!-- FORM -->
                    <form action="form_pengajuan.php" method="GET" class="surat-grid-wrapper">

                        <!-- CARD CONTAINER -->
                        <div class="options-container">

                            <!-- SAKIT -->
                            <label class="surat-option">

                                <input type="radio" name="jenis" value="sakit" required>

                                <div class="option-card">

                                    <i class="fa-solid fa-notes-medical"></i>

                                    <p>
                                        Surat Izin Sakit
                                    </p>

                                </div>

                            </label>

                            <!-- KAMPUS -->
                            <label class="surat-option">

                                <input type="radio" name="jenis" value="kampus">

                                <div class="option-card">

                                    <i class="fa-solid fa-building-columns"></i>

                                    <p>
                                        Izin Kegiatan Kampus
                                    </p>

                                </div>

                            </label>

                            <!-- LUAR -->
                            <label class="surat-option">

                                <input type="radio" name="jenis" value="luar_kampus">

                                <div class="option-card">

                                    <i class="fa-solid fa-earth-asia"></i>

                                    <p>
                                        Surat Izin Kegiatan
                                    </p>

                                </div>

                            </label>

                        </div>

                        <!-- BUTTON -->
                        <div class="btn-group">

                            <button type="submit" class="btn-lanjutkan">

                                Lanjutkan

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</body>

</html>