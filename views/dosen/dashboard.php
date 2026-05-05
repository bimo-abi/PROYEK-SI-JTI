<?php include '../layout/header.php'; ?>
<div class="d-flex bg-light">
    <?php include '../layout/sidebar_dosen.php'; ?>
    
    <div class="flex-grow-1">
        <!-- Top Navbar -->
        <div class="bg-white p-3 d-flex justify-content-between align-items-center shadow-sm">
            <h5 class="mb-0"><i class="bi bi-house-door"></i> Dashboard</h5>
            <div class="fw-bold"><i class="bi bi-person-circle"></i> Dosen</div>
        </div>

        <div class="p-4">
            <!-- Stat Cards (Sesuai Gambar) -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center">
                        <img src="../../assets/img/icon-sakit.png" width="50" class="me-3">
                        <div>
                            <h2 class="mb-0 fw-bold">0</h2>
                            <p class="small text-muted mb-0">Surat Izin Sakit</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center">
                        <img src="../../assets/img/icon-kampus.png" width="50" class="me-3">
                        <div>
                            <h2 class="mb-0 fw-bold">0</h2>
                            <p class="small text-muted mb-0">Surat Izin Kegiatan Kampus</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center">
                        <img src="../../assets/img/icon-luar.png" width="50" class="me-3">
                        <div>
                            <h2 class="mb-0 fw-bold">0</h2>
                            <p class="small text-muted mb-0">Surat Izin Kegiatan Luar</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Chart / Placeholder Area -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4" style="height: 400px;">
                        <!-- Tempat Grafik atau Pesan Selamat Datang -->
                    </div>
                </div>
                <!-- Notifikasi -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-3">
                        <h6><i class="bi bi-bell"></i> Notifikasi Terbaru</h6>
                        <hr>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><strong>1. IZIN MASUK</strong> - Rayhan..</li>
                            <li class="mb-2"><strong>1. IZIN MASUK</strong> - Rayhan..</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../layout/footer.php'; ?>