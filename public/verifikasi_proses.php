<?php
require_once '../config/Database.php';
require_once '../app/Services/StatusHandler.php';

session_start();

if ($_SESSION['peran'] !== 'admin') {
    die("Hanya admin yang boleh memverifikasi!");
}

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['id']) && isset($_GET['aksi'])) {
    $suratId = $_GET['id'];
    $aksi = $_GET['aksi'];
    $adminId = $_SESSION['user_id'];

    try {
        // Implementasi Polimorfisme
        if ($aksi === 'setuju') {
            $proses = new ApproveSurat($db);
        } elseif ($aksi === 'tolak') {
            $proses = new RejectSurat($db);
        } else {
            throw new Exception("Aksi tidak valid.");
        }

        if ($proses->handle($suratId, $adminId)) {
            header("Location: dashboard_admin.php?info=berhasil");
        }
    } catch (Exception $e) {
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
}