<?php
require_once '../config/Database.php';
require_once '../app/Models/Pengguna.php';
require_once '../app/Core/Auth.php';

$db = (new Database())->getConnection();
$auth = new Auth();
$userModel = new Pengguna($db);

$error = ""; // Variabel untuk menampung pesan error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $userModel->login($email, $password);

    if ($user) {
        $auth->setSession($user);

        if ($user['peran'] == 'admin') {
            header("Location: admin/dashboard.php");
        } elseif ($user['peran'] == 'dosen') {
            header("Location: dosen/dashboard.php");
        } else {
            header("Location: mahasiswa/dashboard.php");
        }
        exit;
    } else {
        $error = "Login Gagal! Email atau Password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cek Login - SI-JTI</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; padding-top: 50px; background: #eee; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 300px; }
        .error-msg { color: red; font-size: 0.8em; margin-bottom: 10px; }
        input { width: 100%; padding: 8px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="card">
    <h2>Login SI-JTI</h2>
    
    <?php if($error): ?>
        <div class="error-msg"><?= $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Email:</label>
        <input type="email" name="email" placeholder="bimo@student.com" required>
        
        <label>Password:</label>
        <input type="password" name="password" placeholder="bimo123" required>
        
        <button type="submit">Cek Login</button>
    </form>
    
    <p style="font-size: 0.7em; margin-top: 15px; color: #666;">
        *Pastikan sudah jalankan init_db.php sebelumnya.
    </p>
</div>

</body>
</html>