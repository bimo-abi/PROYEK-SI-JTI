<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/auth-layout.css">
    <link rel="stylesheet" href="../../assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="auth-page">
    <div class="auth-split-layout">
        <!-- Visual Side -->
        <div class="auth-visual-side">
            <div class="auth-visual-content">
                <div style="margin-bottom: 40px;">
                    <i class="fas fa-graduation-cap" style="font-size: 3rem;"></i>
                </div>
                <h1>Kelola Persuratan Lebih Mudah.</h1>
                <p>Platform terintegrasi untuk mahasiswa dan dosen Jurusan Teknologi Informasi dalam mengelola administrasi surat menyurat secara efisien.</p>
            </div>
        </div>

        <!-- Form Side -->
        <div class="auth-form-side">
            <div class="auth-container">
                <div style="margin-bottom: 40px;">
                    <h2>Selamat Datang</h2>
                    <p class="subtitle">Masuk untuk melanjutkan ke dashboard Anda</p>
                </div>

                <form action="../../index.php?action=login" method="POST">
                    <?php if (isset($_GET['status']) && $_GET['status'] == 'failed'): ?>
                        <div class="alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            Email atau Password salah!
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>NIM / Email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" name="identifier" placeholder="Masukkan NIM atau Email" required autofocus>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <div style="margin-bottom: 20px;">
                            <div style="display: grid; grid-template-columns: 1fr auto; align-items: center; margin-bottom: 5px;">
                                <label style="font-size: 0.9rem; font-weight: 500; color: #333;">Kata Sandi</label>
                                <a href="lupa_password.php" style="font-size: 0.85rem; color: #4f46e5; text-decoration: none; font-weight: 500;">
                                    Lupa kata sandi?
                                </a>
                            </div>

                            <div class="password-wrapper" style="position: relative; width: 100%;">
                                <i class="fa-solid fa-lock" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; pointer-events: none;"></i>

                                <input type="password" name="password" id="password" required
                                    placeholder="Masukkan kata sandi anda"
                                    class="form-input"
                                    style="padding: 12px 15px 12px 42px; /* Padding kiri: 42px, Padding kanan: 42px agar tidak tumpang tindih */ 
                      width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; box-sizing: border-box; outline: none; transition: 0.2s;">

                                <i class="fa-solid fa-eye-slash toggle-password"
                                    onclick="togglePasswordVisibility()"
                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; font-size: 1rem; transition: color 0.2s;">
                                </i>
                            </div>
                        </div>

                        <button type="submit" style="width: 100%; background: #4f46e5; color: white; border: none; padding: 13px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.95rem; margin-top: 5px; transition: background 0.2s;">
                            Masuk ke Akun <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
                        </button>
                        <div class="text-divider">Atau</div>

                        <a href="register.php" class="btn-secondary">
                            Belum punya akun? Daftar Sekarang
                        </a>
                </form>

                <div style="margin-top: 60px; text-align: center;">
                    <small style="color: var(--text-muted);">© <?= date('Y') ?> Jurusan Teknologi Informasi</small>
                </div>
            </div>
        </div>
    </div>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const iconElement = document.querySelector('.toggle-password');

            if (passwordInput && iconElement) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    iconElement.classList.remove('fa-eye');
                    iconElement.classList.add('fa-eye-slash'); // Ganti jadi ikon mata tercoret
                } else {
                    passwordInput.type = 'password';
                    iconElement.classList.remove('fa-eye-slash');
                    iconElement.classList.add('fa-eye'); // Kembali ke ikon mata terbuka
                }
            }
        }
    </script>
</body>

</html>