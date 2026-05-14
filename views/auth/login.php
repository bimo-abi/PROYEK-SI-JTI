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

                    <div class="form-group">
                        <div class="flex-label">
                            <label>Kata Sandi</label>
                            <a href="lupa_password.php" class="forgot-link">Lupa kata sandi?</a>
                        </div>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        Masuk ke Akun <i class="fas fa-arrow-right"></i>
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
</body>
</html>