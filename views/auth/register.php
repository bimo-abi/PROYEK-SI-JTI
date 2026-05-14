<?php
require_once '../../autoload.php';
session_start();

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/auth-layout.css">
    <link rel="stylesheet" href="../../assets/css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="auth-page">
    <div class="auth-split-layout">
        <!-- Visual Side -->
        <div class="auth-visual-side">
            <div class="auth-visual-content">
                <div style="margin-bottom: 40px;">
                    <i class="fas fa-user-plus" style="font-size: 3rem;"></i>
                </div>
                <h1>Bergabung dengan Kami.</h1>
                <p>Buat akun Anda untuk mulai mengelola pengajuan surat dan administrasi akademik lainnya dengan lebih praktis.</p>
            </div>
        </div>

        <!-- Form Side -->
        <div class="auth-form-side">
            <div class="auth-container" style="max-width: 500px;">
                <div style="margin-bottom: 32px;">
                    <h2>Pendaftaran Akun</h2>
                    <p class="subtitle">Lengkapi data diri Anda untuk bergabung</p>
                </div>

                <form action="../../process/auth_process.php?action=register" method="POST">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <<?php
                                if ($_GET['error'] == 'duplicate_nim') echo "NIM sudah terdaftar, silakan login.";
                                else if ($_GET['error'] == 'password') echo "Konfirmasi password tidak sesuai.";
                                else if ($_GET['error'] == 'password_length') echo "Kata sandi harus berjumlah 8-12 karakter."; // PESAN BARU
                                else if ($_GET['error'] == 'email') echo "Email sudah terdaftar, gunakan email lain.";
                                else if ($_GET['error'] == 'email_format') echo "Gagal! Mahasiswa wajib menggunakan email @student.polije.ac.id";
                                ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-id-card"></i>
                                        <input type="text" name="nama" placeholder="" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Mendaftar Sebagai</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-users"></i>
                                        <select name="role" id="input-role" required style="padding-left: 48px;">
                                            <option value="mahasiswa">Mahasiswa</option>
                                            <option value="dosen">Dosen</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="mhs-fields">
                                <div class="form-group">
                                    <label>NIM (Nomor Induk Mahasiswa)</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-hashtag"></i>
                                        <input type="text" name="nim" placeholder="" id="input-nim">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Email Kampus</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email"
                                            name="email"
                                            id="input-email-mhs"
                                            placeholder="E41xxxxx@student.polije.ac.id"
                                            pattern="[a-zA-Z0-9._%+-]+@student\.polije\.ac\.id$"
                                            title="Gunakan email resmi: (NIM)@student.polije.ac.id">
                                    </div>
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Program Studi</label>
                                        <div class="input-wrapper">
                                            <i class="fas fa-graduation-cap"></i>
                                            <select name="id_prodi" id="input-prodi" style="padding-left: 48px;">
                                                <option value="">Pilih Prodi</option>
                                                <?php foreach ($dataProdi as $prodi): ?>
                                                    <option value="<?= $prodi['id'] ?>"><?= $prodi['nama_prodi'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Golongan</label>
                                        <div class="input-wrapper">
                                            <i class="fas fa-layer-group"></i>
                                            <select name="id_golongan" id="input-golongan" style="padding-left: 48px;">
                                                <option value="">Pilih Golongan</option>
                                                <?php foreach ($dataGolongan as $gol): ?>
                                                    <option value="<?= $gol['id'] ?>"><?= $gol['nama_golongan'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="dosen-fields" style="display: none;">
                                <div class="form-group">
                                    <label>Email Dosen</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email" name="email_dosen" placeholder="nama@polije.ac.id" id="input-email-dosen">
                                    </div>
                                </div>
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Kata Sandi (8-12 Karakter)</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-lock"></i>
                                        <input type="password" name="password"
                                            placeholder="8-12 karakter"
                                            minlength="8"
                                            maxlength="12"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Konfirmasi Sandi</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-shield-halved"></i>
                                        <input type="password" name="confirm_password"
                                            placeholder="8-12 karakter"
                                            minlength="8"
                                            maxlength="12"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary" style="margin-top: 16px;">
                                Daftar Sekarang <i class="fas fa-check-circle"></i>
                            </button>

                            <div class="text-divider"><span>Sudah punya akun?</span></div>

                            <a href="login.php" class="btn-secondary">Masuk ke Akun</a>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('input-role').addEventListener('change', function() {
            const role = this.value;
            const mhsFields = document.getElementById('mhs-fields');
            const dosenFields = document.getElementById('dosen-fields');

            // Input Mahasiswa
            const nimInput = document.getElementById('input-nim');
            const emailMhs = document.getElementById('input-email-mhs');
            const prodiInput = document.getElementById('input-prodi');
            const golInput = document.getElementById('input-golongan');

            // Input Dosen
            const emailDosen = document.getElementById('input-email-dosen');

            if (role === 'mahasiswa') {
                mhsFields.style.display = 'block';
                dosenFields.style.display = 'none';

                // Set required untuk mahasiswa
                nimInput.required = true;
                emailMhs.required = true;
                prodiInput.required = true;
                golInput.required = true;

                // Lepas required dosen
                emailDosen.required = false;
            } else {
                mhsFields.style.display = 'none';
                dosenFields.style.display = 'block';

                // Lepas required mahasiswa
                nimInput.required = false;
                emailMhs.required = false;
                prodiInput.required = false;
                golInput.required = false;

                // Set required untuk dosen
                emailDosen.required = true;
            }
        });

        // Inisialisasi awal saat halaman dimuat
        window.onload = function() {
            document.getElementById('input-role').dispatchEvent(new Event('change'));
        };
    </script>
</body>

</html>