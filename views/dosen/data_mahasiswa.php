<?php include '../layout/header.php'; ?>
<div class="d-flex bg-light">
    <?php include '../layout/sidebar_dosen.php'; ?>
    
    <div class="flex-grow-1">
        <div class="bg-white p-3 d-flex justify-content-between align-items-center shadow-sm">
            <h5 class="mb-0"><i class="bi bi-mortarboard"></i> Data Mahasiswa</h5>
            <div class="fw-bold"><i class="bi bi-person-circle"></i> Dosen</div>
        </div>

        <div class="p-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="mb-3">
                    <input type="text" class="form-control bg-light border-0" placeholder="Search..." style="width: 100%;">
                </div>
                
                <div class="table-responsive">
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
                                <th>Status</th>
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
                                <td><span class="badge bg-danger px-3">Ditolak</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../layout/footer.php'; ?>