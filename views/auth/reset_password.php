<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/lupa_reset_password.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="icon-circle icon-shield">
                <i class="fas fa-shield-check"></i>
            </div>
            <h2>Password Baru</h2>
            <p class="subtitle">Silakan buat kata sandi baru yang kuat untuk akun Anda.</p>
        </div>

        <?php $token = $_GET['token'] ?? ''; ?>

        <form action="../../process/auth_process.php?action=simpan_password_baru" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>
                        <?php
                        if ($_GET['error'] == 'password') echo "Konfirmasi password tidak sesuai.";
                        else if ($_GET['error'] == 'password_length') echo "Kata sandi harus 8-12 karakter.";
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Kata Sandi Baru</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="••••••••" minlength="8" maxlength="12" required>
                </div>
            </div>

            <div class="form-group">
                <label>Konfirmasi Kata Sandi</label>
                <div class="input-wrapper">
                    <i class="fas fa-shield-halved"></i>
                    <input type="password" name="confirm_password" placeholder="••••••••" minlength="8" maxlength="12" required>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                Simpan Password Baru <i class="fas fa-check-circle"></i>
            </button>
        </form>
    </div>
</body>
</html>