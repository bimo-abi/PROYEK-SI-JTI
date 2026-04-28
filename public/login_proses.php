<?php
require_once '../config/Database.php';
require_once '../app/Models/Pengguna.php';
require_once '../app/Core/Auth.php';

$db = (new Database())->getConnection();
$auth = new Auth();
$userModel = new Pengguna($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $userModel->login($email, $password);

    if ($user) {
        // Simpan ke session
        $auth->setSession($user);

        // Redirect berdasarkan peran (Polimorfisme dalam alur)
        if ($user['peran'] == 'admin') {
            header("Location: admin/dashboard.php");
        } elseif ($user['peran'] == 'dosen') {
            header("Location: dosen/dashboard.php");
        } else {
            header("Location: mahasiswa/dashboard.php");
        }
    } else {
        echo "Login Gagal! Email atau Password salah.";
    }
}