<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard <?= ucfirst($_SESSION['peran']) ?></h1>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card p-4">
            <h4>Selamat Datang, <?= $_SESSION['nama'] ?>!</h4>
            <p>Sistem ini membantu Anda mengelola surat izin mahasiswa JTI dengan transparan.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>