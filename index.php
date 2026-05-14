<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'autoload.php';
session_start();

$action = $_GET['action'] ?? '';

// Jika ada action login, arahkan ke proses auth
if ($action === 'login') {
    require_once 'process/auth_process.php';
    exit();
}

// Cek Session
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    header("Location: views/$role/dashboard.php");
    exit();
} else {
    header("Location: views/auth/login.php");
    exit();
}