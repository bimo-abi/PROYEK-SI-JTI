<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/register.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-container" style="max-width: 450px; margin: 50px auto;">
        <div class="auth-card">
            <div class="auth-header" style="text-align: center; margin-bottom: 30px;">
                <div class="icon-circle" style="background: #eef2ff; color: #4f46e5; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 24px;">
                    <i class="fas fa-key"></i>
                </div>
                <h2>Lupa Kata Sandi?</h2>
                <p class="subtitle">Masukkan email kampus Anda untuk menerima tautan pemulihan.</p>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'not_found'): ?>
                <div class="alert-danger" style="margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i> Email tidak terdaftar di sistem.
                </div>
            <?php endif; ?>

            <form action="../../process/auth_process.php?action=proses_lupa_password" method="POST">
                <div class="form-group">
                    <label>Email Kampus</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" 
                               placeholder="Contoh: E4125xxxx@student.polije.ac.id" 
                               required>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 20px;">
                    Kirim Link Reset <i class="fas fa-paper-plane" style="margin-left: 8px;"></i>
                </button>
            </form>

            <div class="auth-footer" style="text-align: center; margin-top: 25px;">
                <a href="login.php" style="text-decoration: none; color: #4f46e5; font-weight: 500;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>