<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <form action="../../process/auth_process.php?action=proses_lupa_password" method="POST">
            <h2>Lupa Password</h2>
            <p>Masukkan email akun Anda untuk mereset kata sandi.</p>
            
            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" required placeholder="contoh@student.polije.ac.id">
            </div>
            
            <button type="submit" class="btn-login">Minta Token Reset</button>
            
            <div class="auth-footer" style="margin-top: 15px; text-align: center;">
                <a href="login.php" style="text-decoration: none; color: #00a2ed;">Kembali ke Login</a>
            </div>
        </form>
    </div>
</body>
</html>