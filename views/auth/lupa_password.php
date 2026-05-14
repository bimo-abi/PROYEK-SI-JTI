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
            <div class="form-group">
                <label>Email Kampus</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email"
                        placeholder="E41xxxxx@student.polije.ac.id"
                        required>
                </div>
            </div>
            <button type="submit" class="btn-primary">Kirim Link Reset</button>
        </form>
    </div>
</body>

</html>