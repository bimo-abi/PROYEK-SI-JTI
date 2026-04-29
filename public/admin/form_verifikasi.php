<?php
require_once '../../app/Core/Auth.php';
Auth::check();
Auth::role('admin');

$id_surat = $_GET['id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Surat</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 50px; }
        .card { background: white; padding: 20px; border-radius: 8px; max-width: 400px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        select, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; }
        button { width: 100%; padding: 10px; border: none; cursor: pointer; border-radius: 5px; }
        .btn-save { background: #28a745; color: white; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Keputusan Verifikasi</h3>
        <form action="verifikasi_proses.php" method="POST">
            <input type="hidden" name="id_surat" value="<?= $id_surat; ?>">
            
            <label>Status:</label>
            <select name="status" id="status" onchange="toggleCatatan()">
                <option value="disetujui">Setujui</option>
                <option value="ditolak">Tolak</option>
            </select>

            <div id="box-catatan" style="display:none;">
                <label>Alasan Penolakan:</label>
                <textarea name="catatan" placeholder="Contoh: Bukti surat tidak valid/palsu"></textarea>
            </div>

            <button type="submit" class="btn-save">Simpan Keputusan</button>
        </form>
        <br>
        <a href="dashboard.php">Batal</a>
    </div>

    <script>
        function toggleCatatan() {
            var status = document.getElementById("status").value;
            var box = document.getElementById("box-catatan");
            box.style.display = (status === 'ditolak') ? "block" : "none";
        }
    </script>
</body>
</html>