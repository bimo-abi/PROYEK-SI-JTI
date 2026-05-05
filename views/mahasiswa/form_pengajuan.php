<div class="row">
    <!-- Kolom Form -->
    <div class="col-md-7">
        <div class="card p-4 rounded-4 shadow-sm">
            <h6 class="fw-bold mb-4">FORM PENGAJUAN SURAT</h6>
            <form action="../../process/surat_handler.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label small fw-bold">Jenis Surat :</label>
                    <div class="col-sm-8">
                        <select name="jenis_surat" class="form-control bg-light">
                            <option>Surat Izin Sakit</option>
                            <option>Surat Izin Kegiatan di Luar Kampus</option>
                            <option>Surat Izin Kegiatan dalam Kampus</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label small fw-bold">Keterangan :</label>
                    <div class="col-sm-8">
                        <textarea name="keterangan" class="form-control bg-light" rows="3"></textarea>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label small fw-bold">Tanggal Mulai :</label>
                    <div class="col-sm-8">
                        <input type="date" name="tgl_mulai" class="form-control bg-light">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label small fw-bold">Upload Bukti :</label>
                    <div class="col-sm-8">
                        <input type="file" name="bukti" class="form-control bg-light shadow-sm">
                        <small class="text-muted" style="font-size: 10px;">format file dalam bentuk: Gif, Png, Jpg</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="pengajuan_surat.php" class="btn btn-secondary px-4 rounded-pill"> Kembali</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill">Ajukan Surat</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Kolom Preview Surat -->
    <div class="col-md-5">
        <div class="card p-4 rounded-4 shadow-sm bg-white" style="min-height: 400px;">
            <p class="text-center fw-bold small border-bottom pb-2">Preview Surat</p>
            <div id="preview-content" class="small p-3 border rounded bg-light" style="font-family: 'Times New Roman'; min-height: 300px;">
                <!-- Data dari form akan tampil di sini secara real-time via JS -->
                <p>Nama: <strong><?php echo $_SESSION['nama'] ?? 'Mahasiswa'; ?></strong></p>
                <p>Jenis: <span id="view-jenis">...</span></p>
                <p>Keterangan: <span id="view-ket">...</span></p>
                <p>Tanggal: <span id="view-tgl">...</span></p>
            </div>
        </div>
    </div>
</div>