<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .auth-page {
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        .auth-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            width: 100%;
            max-width: 450px;
        }
        .icon-circle {
            background: #ecfdf5;
            color: #10b981;
            width: 60px; height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
        }
    </style>
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="auth-header" style="text-align: center; margin-bottom: 30px;">
            <div class="icon-circle">
                <i class="fas fa-shield-check"></i>
            </div>
            <h2>Password Baru</h2>
            <p class="subtitle">Silakan buat kata sandi baru yang kuat untuk akun Anda.</p>
        </div>

        <?php 
        $token = $_GET['token'] ?? ''; 
        ?>

        <form action="../../process/auth_process.php?action=simpan_password_baru" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-danger" style="margin-bottom: 20px; padding: 12px; border-radius: 8px; background: #fff5f5; color: #e03131; border-left: 4px solid #e03131; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span style="font-size: 0.9rem;">
                        <?php
                        if ($_GET['error'] == 'password') echo "Konfirmasi password tidak sesuai.";
                        else if ($_GET['error'] == 'password_length') echo "Kata sandi harus 8-12 karakter.";
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Kata Sandi Baru (8-12 Karakter)</label>
                <div class="input-wrapper" style="position: relative;">
                    <i class="fas fa-lock" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="password" name="password" 
                           placeholder="••••••••" 
                           style="width: 100%; padding: 12px 12px 12px 45px; border: 1px solid #e2e8f0; border-radius: 8px;"
                           minlength="8" maxlength="12" required>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Konfirmasi Kata Sandi Baru</label>
                <div class="input-wrapper" style="position: relative;">
                    <i class="fas fa-shield-halved" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="password" name="confirm_password" 
                           placeholder="••••••••" 
                           style="width: 100%; padding: 12px 12px 12px 45px; border: 1px solid #e2e8f0; border-radius: 8px;"
                           minlength="8" maxlength="12" required>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; background: #4f46e5; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s;">
                Simpan Password Baru <i class="fas fa-check-circle" style="margin-left: 8px;"></i>
            </button>
        </form>
    </div>
</body>
</html>