<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();
$nim = $_SESSION['nim'];

// Ambil data profil lengkap dengan JOIN antara pengguna dan detail_pengguna
try {
    $query = "SELECT u.email, d.* 
              FROM pengguna u 
              JOIN detail_pengguna d ON u.id_pengguna = d.id_pengguna 
              WHERE d.nomor_induk = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$nim]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}

$current_page = 'profil';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>
            
            <div class="content">
                <div class="profile-main-card" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <!-- Header Profil -->
                    <div class="profile-header" style="display: flex; align-items: center; gap: 25px; margin-bottom: 40px; border-bottom: 1px solid #f0f0f0; padding-bottom: 30px;">
                        <img src="../../assets/img/avatar.png" alt="User Photo" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #00a2ed;">
                        <div class="user-id" style="flex: 1;">
                            <h3 style="margin: 0; font-size: 1.5rem; color: #333;"><?= htmlspecialchars($user['nama_lengkap']) ?></h3>
                            <p style="margin: 5px 0; color: #666;">NIM : <?= htmlspecialchars($user['nomor_induk']) ?></p>
                            <p style="margin: 0; color: #888; font-size: 0.9rem;">Jurusan Teknologi Informasi - <?= htmlspecialchars($user['program_studi']) ?></p>
                        </div>
                        <div class="btn-actions" style="display: flex; flex-direction: column; gap: 10px;">
                            <button class="btn-mini" style="background: #00a2ed; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 0.8rem;">
                                <i class="fas fa-edit"></i> Edit Profil
                            </button>
                            <button class="btn-mini" style="background: #f8f9fa; color: #333; border: 1px solid #ddd; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 0.8rem;">
                                <i class="fas fa-camera"></i> Ganti foto
                            </button>
                        </div>
                    </div>

                    <!-- Detail Data -->
                    <div class="detail-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                        <!-- Box Data Diri -->
                        <div class="info-box" style="background: #fafafa; padding: 20px; border-radius: 12px; border: 1px solid #eee;">
                            <h5 style="margin-top: 0; color: #00a2ed; border-bottom: 2px solid #00a2ed; display: inline-block; padding-bottom: 5px; margin-bottom: 15px;">
                                <i class="fas fa-user"></i> Data Diri
                            </h5>
                            <p><strong>Nama Lengkap :</strong> <?= htmlspecialchars($user['nama_lengkap']) ?></p>
                            <p><strong>NIM :</strong> <?= htmlspecialchars($user['nomor_induk']) ?></p>
                            <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
                            <p><strong>Jenis Kelamin :</strong> <?= htmlspecialchars($user['jenis_kelamin'] ?? '-') ?></p>
                            <p><strong>Nomor HP :</strong> <?= htmlspecialchars($user['nomor_hp'] ?? '-') ?></p>
                        </div>

                        <!-- Box Data Akademik -->
                        <div class="info-box" style="background: #fafafa; padding: 20px; border-radius: 12px; border: 1px solid #eee;">
                            <h5 style="margin-top: 0; color: #00a2ed; border-bottom: 2px solid #00a2ed; display: inline-block; padding-bottom: 5px; margin-bottom: 15px;">
                                <i class="fas fa-graduation-cap"></i> Data Akademik
                            </h5>
                            <p><strong>Jurusan :</strong> Teknologi Informasi</p>
                            <p><strong>Program Studi :</strong> <?= htmlspecialchars($user['program_studi']) ?></p>
                            <p><strong>Semester :</strong> <?= htmlspecialchars($user['semester'] ?? '-') ?></p>
                            <p><strong>Dosen Wali :</strong> <?= htmlspecialchars($user['dosen_wali'] ?? '-') ?></p>
                            <p><strong>Kelas :</strong> <?= htmlspecialchars($user['kelas'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>