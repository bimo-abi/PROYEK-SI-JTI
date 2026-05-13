<?php
require_once '../../autoload.php';
session_start();
//KEAMANAN: Wajib Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

use Config\Database;

$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// 2. AMBIL DATA PROFIL ADMIN
$queryProfil = "SELECT p.nama, p.email, d.nomor_induk 
                FROM pengguna p
                LEFT JOIN detail_pengguna d ON p.id = d.id_pengguna
                WHERE p.id = ?";
$stmt = $db->prepare($queryProfil);
$stmt->execute([$user_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. QUERY STATISTIK (Menghitung Total Semua Mahasiswa)
try {
    // Total Pengajuan
    $totalSurat = $db->query("SELECT COUNT(*) FROM pengajuan_surat")->fetchColumn();
    // Total Diterima
    $totalApprove = $db->query("SELECT COUNT(*) FROM pengajuan_surat WHERE status = 'disetujui'")->fetchColumn();
    // Total Diproses/Menunggu
    $totalPending = $db->query("SELECT COUNT(*) FROM pengajuan_surat WHERE status = 'menunggu'")->fetchColumn();
    // Total Ditolak
    $totalReject = $db->query("SELECT COUNT(*) FROM pengajuan_surat WHERE status = 'ditolak'")->fetchColumn();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

$current_page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_admin.php'; ?>

        <div class="main-container">
            <?php include '../layouts/topbar_admin.php'; ?>

            <div class="content">
                <div class="main-grid">
                    <div class="left-column">
                        <div class="section-title">Dashboard Administrator</div>

                        <div class="stats-grid">
                            <div class="stat-card blue"> Semua Pengajuan <span><?= $totalSurat ?></span> </div>
                            <div class="stat-card green"> Diterima <span><?= $totalApprove ?></span> </div>
                            <div class="stat-card orange"> Diproses <span><?= $totalPending ?></span> </div>
                            <div class="stat-card red"> Ditolak <span><?= $totalReject ?></span> </div>
                        </div>

                        <div class="notif-box">
                            <h4>Manajemen Surat</h4>
                            <ul>
                                <li>Halo <?= htmlspecialchars($admin['nama']) ?>, Anda login sebagai Administrator.</li>
                                <li>Terdapat <strong><?= $totalPending ?></strong> pengajuan surat baru yang butuh verifikasi.</li>
                            </ul>
                            <br>
                            <a href="/PROYEK-SI-JTI/views/admin/surat_masuk.php" class="btn-ajukan" style="background-color: #f39c12;">Kelola Pengajuan</a>
                        </div>
                    </div>

                    <div class="right-column">
                        <div class="profile-card">
                            <div class="avatar-wrapper">
                                <img src="<?= $foto_sidebar ?? '../../assets/img/avatar.png' ?>?t=<?= time() ?>" alt="Admin Avatar">
                            </div>
                            <p class="profile-name"><?= htmlspecialchars($admin['nama']) ?></p>
                            <p class="profile-role">Administrator</p>
                        </div>

                        <div class="info-card">
                            <div class="info-header">
                                <h5>Info Profil</h5>
                            </div>
                            <div class="info-body">
                                <p><strong>NIP/ID :</strong> <?= htmlspecialchars($admin['nomor_induk'] ?? '-') ?></p>
                                <p><strong>Email :</strong> <?= htmlspecialchars($admin['email'] ?? '-') ?></p>
                                <p><strong>Akses :</strong> Full Access</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>