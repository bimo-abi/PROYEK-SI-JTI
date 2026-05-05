<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <form action="../../index.php?action=login" method="POST">
            <h2>Masuk ke Akun</h2>
            
            <div class="form-group">
                <label>NIM / Email</label>
                <input type="text" name="identifier" placeholder="Masukkan NIM atau Email" required>
            </div>

            <div class="form-group">
                <div class="flex-label">
                    <label>Password</label>
                    <a href="#" class="forgot-link">Lupa password?</a>
                </div>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-primary">Masuk</button>
            
            <div class="text-center">Atau</div>
            
            <a href="register.php" class="btn-secondary">Daftar Akun</a>
        </form>
    </div>
</body>
</html>