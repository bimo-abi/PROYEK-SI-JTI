<?php
require_once '../../app/Core/Auth.php';
Auth::check();
Auth::role('mahasiswa');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Ajukan Surat Izin - SI-JTI</title>
    <style>
        body { font-family: sans-serif; margin: 40px; background: #f9f9f9; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 500px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, textarea, input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
        button:hover { background: #218838; }
        .back-link { display: inline-block; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h2>Form Pengajuan Surat Izin</h2>
    <p>Silakan isi data di bawah ini dengan jujur untuk keperluan absensi.</p>
    
    <form action="ajukan_surat_proses.php" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label>Jenis Surat</label>
            <select name="jenis_surat" required>
                <option value="Sakit">Surat Izin Sakit</option>
                <option value="Kegiatan Kampus">Surat Izin Kegiatan Kampus</option>
                <option value="Kegiatan Luar Kampus">Surat Izin Kegiatan Luar Kampus</option>
            </select>
        </div>

        <div class="form-group">
            <label>Keperluan / Alasan</label>
            <textarea name="keperluan" rows="4" placeholder="Contoh: Mengikuti lomba LKS Tingkat Nasional" required></textarea>
        </div>

        <div class="form-group">
            <label>Bukti Pendukung (Wajib PDF)</label>
            <input type="file" name="bukti" accept=".pdf" required>
            <small style="color: red;">*Maksimal ukuran file 2MB</small>
        </div>

        <button type="submit">Kirim Pengajuan</button>
    </form>

    <a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a>
</div>

</body>
</html>