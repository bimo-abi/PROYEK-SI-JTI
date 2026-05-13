<?php
require_once '../../autoload.php';

use Config\Database;

$db = (new Database())->getConnection();

// Ambil data prodi untuk dropdown
$dataProdi = $db->query("SELECT * FROM prodi")->fetchAll();

// Tambahkan pengambilan data GOLONGAN
$dataGolongan = $db->query("SELECT * FROM golongan")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daftar Akun - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <div class="auth-container register-wide">
        <form action="../../process/auth_process.php?action=register" method="POST">
            <h3>Buat Akun Anda!</h3>
            <p class="subtitle">Silahkan isi data untuk membuat akun baru</p>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'duplicate_nim'): ?>
                <div class="alert-danger">
                    Nim sudah terdaftar, silahkan melakukan login.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'password'): ?>
                <div class="alert-danger">
                    Konfirmasi password tidak sesuai.
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Program Studi:</label>
                <select name="id_prodi" required>
                    <option value="">-- Pilih Program Studi</option>
                    <?php foreach ($dataProdi as $prodi): ?>
                        <option value="<?= $prodi['id'] ?>"><?= $prodi['nama_prodi'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Email kampus:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>NIM / NIP:</label>
                <input type="text" name="nomor_induk" required>
            </div>

            <div class="form-group">
                <label>Nama Lengkap:</label>
                <input type="text" name="nama" required>
            </div>

            <!-- INPUT GOLONGAN YANG BARU DITAMBAHKAN -->
            <div class="form-group">
                <label>Golongan:</label>
                <select name="id_golongan" required>
                    <option value="">-- Pilih Golongan</option>
                    <?php foreach ($dataGolongan as $gol): ?>
                        <option value="<?= $gol['id'] ?>"><?= $gol['nama_golongan'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Login Sebagai:</label>
                <select name="role" required>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label>Password :</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password:</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn-primary">Registrasi</button>
            <p class="text-center" style="font-size: 0.8rem;">Sudah punya akun? <a href="login.php" style="color:#00a2ed;">Login disini</a></p>
        </form>
    </div>

    <script>
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert-danger');
            alerts.forEach(function(alert) {
                alert.style.transition = "opacity 0.5s";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>

</html>