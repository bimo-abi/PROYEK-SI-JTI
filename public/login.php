<?php
require_once '../config/Database.php';
require_once '../app/Services/AuthService.php';

$database = new Database();
$db = $database->getConnection();
$auth = new AuthService($db);

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    if ($auth->login($email, $pass)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Email atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - JTI Surat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center py-4 bg-light" style="height: 100vh;">
    <div class="container" style="max-width: 400px;">
        <div class="card shadow">
            <div class="card-body p-4 text-center">
                <h3 class="mb-3">Login JTI</h3>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com" required>
                        <label for="floatingInput">Alamat Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                        <label for="floatingPassword">Kata Sandi</label>
                    </div>
                    <button class="w-100 btn btn-lg btn-primary" type="submit">Masuk</button>
                </form>
                <p class="mt-3 text-muted small">&copy; 2026 Jurusan Teknologi Informasi</p>
            </div>
        </div>
    </div>
</body>
</html>