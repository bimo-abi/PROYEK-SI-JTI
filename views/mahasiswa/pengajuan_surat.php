<?php
session_start();
$current_page = 'pengajuan';
include '../layouts/header.php'; // Berisi CSS & Metadata
?>

<div class="wrapper">
    <?php include '../layouts/sidebar.php'; ?>
    <div class="main-container">
        <?php include '../layouts/topbar.php'; ?>
        
        <div class="content">
            <div class="card-container">
                <div class="header-content">
                    <i class="fas fa-envelope"></i> <span>Pengajuan Surat</span>
                </div>
                
                <div class="selection-area text-center">
                    <h3>Pilih Jenis Surat</h3>
                    <p>Silahkan pilih jenis surat yang ingin anda ajukan</p>
                    
                    <form action="form_pengajuan.php" method="GET" class="surat-grid">
                        <label class="card-radio">
                            <input type="radio" name="jenis" value="sakit" required>
                            <div class="card-body">
                                <img src="../../assets/img/icon-sakit.png" alt="">
                                <p>Surat izin sakit</p>
                            </div>
                        </label>

                        <label class="card-radio">
                            <input type="radio" name="jenis" value="kampus">
                            <div class="card-body">
                                <img src="../../assets/img/icon-kampus.png" alt="">
                                <p>Surat Izin Kegiatan Kampus</p>
                            </div>
                        </label>

                        <label class="card-radio">
                            <input type="radio" name="jenis" value="luar_kampus">
                            <div class="card-body">
                                <img src="../../assets/img/icon-luar.png" alt="">
                                <p>Surat Izin Kegiatan</p>
                            </div>
                        </label>
                        
                        <div class="btn-footer">
                            <button type="submit" class="btn-lanjut">Lanjutkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>