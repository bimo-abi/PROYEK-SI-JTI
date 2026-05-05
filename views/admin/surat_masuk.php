<?php include '../layout/header.php'; ?>
<div class="d-flex bg-light">
    <?php include '../layout/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1" style="margin-left: 250px;">
        <div class="bg-white p-3 d-flex justify-content-between align-items-center shadow-sm">
            <h5 class="mb-0"><i class="bi bi-envelope-paper"></i> Surat Masuk</h5>
            <div class="fw-bold"><i class="bi bi-person-circle"></i> Admin</div>
        </div>

        <div class="p-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <table class="table table-bordered text-center align-middle small">
                    <thead class="table-secondary">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Prodi</th>
                            <th>Gol</th>
                            <th>NIM</th>
                            <th>Semester</th>
                            <th>Surat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Rayhan Riyadhul Jinan</td>
                            <td>Teknik Informatika</td>
                            <td>C</td>
                            <td>E41250835</td>
                            <td>2</td>
                            <td><a href="#" class="text-danger"><i class="bi bi-file-pdf"></i> pdf</a></td>
                            <td>
                                <button class="btn btn-info btn-sm text-white px-3" data-bs-toggle="modal" data-bs-target="#modalDetail">Lihat Detail</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL LIHAT DETAIL (Sesuai Desain Frame "Lihat Detail") -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title w-100 text-center fw-bold">Verifikasi Keaslian Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <div class="row">
                    <!-- Sisi Kiri: Data Mahasiswa -->
                    <div class="col-md-6 border-end">
                        <p class="mb-1 fw-bold small">NIM :</p>
                        <p class="text-muted mb-3 small">E41250835</p>

                        <p class="mb-1 fw-bold small">Nama :</p>
                        <p class="text-muted mb-3 small">Rayhan Riyadhul Jinan</p>

                        <p class="mb-1 fw-bold small">Prodi :</p>
                        <p class="text-muted mb-3 small">Teknik Informatika</p>

                        <p class="mb-1 fw-bold small">Golongan :</p>
                        <p class="text-muted mb-3 small">C</p>

                        <p class="mb-1 fw-bold small">Email Kampus :</p>
                        <p class="text-muted mb-3 small">rayhan@jti.polije.ac.id</p>

                        <p class="mb-1 fw-bold small">Semester :</p>
                        <p class="text-muted mb-3 small">2</p>
                    </div>

                    <!-- Sisi Kanan: Detail Izin & Lampiran -->
                    <div class="col-md-6 ps-4">
                        <p class="mb-1 fw-bold small">Keterangan Izin :</p>
                        <p class="text-muted mb-3 small">Sakit</p>

                        <p class="mb-1 fw-bold small text-primary">Lampiran (Foto Bukti) :</p>
                        <!-- Menampilkan lampiran foto sesuai permintaan (JPG/JPEG) -->
                        <div class="border rounded-3 p-2 bg-light text-center">
                            <img src="../../assets/img/contoh-surat.jpg" class="img-fluid rounded-2 mb-2" alt="Bukti Surat">
                            <br>
                            <a href="../../assets/img/contoh-surat.jpg" target="_blank" class="small text-decoration-none">Buka Gambar Penuh</a>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 gap-2">
                    <button class="btn btn-danger px-4 rounded-3" onclick="confirmAction('tolak')">Tolak</button>
                    <button class="btn btn-primary px-4 rounded-3" onclick="confirmAction('terima')">Terima</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>