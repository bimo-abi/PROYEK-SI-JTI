<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/lupa_reset_password.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="icon-circle icon-key">
                <i class="fas fa-key"></i>
            </div>
            <h2>Lupa Kata Sandi?</h2>
            <p class="subtitle">Masukkan email kampus Anda untuk menerima tautan pemulihan.</p>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'not_found'): ?>
            <div class="alert-danger">
                <i class="fas fa-exclamation-circle"></i> Email tidak terdaftar di sistem.
            </div>
        <?php endif; ?>

        <form action="../../process/auth_process.php?action=proses_lupa_password" method="POST">
            <div class="form-group">
                <label>Email Kampus</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Contoh: E4125xxxx@student.polije.ac.id" required>
                </div>
            </div>
            <button type="submit" class="btn-primary">
                Kirim Link Reset <i class="fas fa-paper-plane"></i>
            </button>
        </form>

        <div class="auth-footer" style="text-align: center; margin-top: 25px;">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
        </div>
    </div>
</body>
</html>