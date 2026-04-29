<?php
require_once '../config/Database.php';
require_once '../app/Models/Pengguna.php';
require_once '../app/Core/Auth.php';

session_start();

$db = (new Database())->getConnection();
$auth = new Auth();
$userModel = new Pengguna($db);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('/surat-digital/resources/assets/gedung.png');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .login-card {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            z-index: 2;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #1a9cff;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #1084df;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .btn-secondary {
            display: block;
            text-align: center;
            margin-top: 10px;
            background: #333;
            color: white;
            padding: 10px;
            border-radius: 8px;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="overlay"></div>

    <div class="login-card">
        <h2>Masuk ke Akun</h2>

        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <label>Email</label>
                <input type="text" name="email" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit">Masuk</button>
        </form>

        <a href="/surat-digital/resources/views/register.blade.php" class="btn-secondary">
            Daftar Akun
        </a>
    </div>

</body>

</html>