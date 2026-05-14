<?php $token = $_GET['token'] ?? ''; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <form action="../../process/auth_process.php?action=simpan_password_baru" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <h2>Password Baru</h2>
            <p>Silakan masukkan kata sandi baru Anda.</p>
            
            <div class="form-group">
                <label>Kata Sandi Baru</label>
                <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter">
            </div>
            
            <button type="submit" class="btn-login">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>