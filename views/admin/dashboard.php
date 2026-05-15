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

                        <?php
                        // 4. AMBIL DAFTAR SURAT MASUK (MENUNGGU VERIFIKASI)
                        $queryIncoming = "SELECT p.id_pengajuan, p.jenis_surat, p.tanggal_pengajuan, u.nama as nama_mhs 
                  FROM pengajuan_surat p
                  JOIN detail_pengguna d ON p.nim = d.nomor_induk
                  JOIN pengguna u ON d.id_pengguna = u.id
                  WHERE p.status = 'menunggu'
                  ORDER BY p.tanggal_pengajuan DESC LIMIT 5";
                        $incoming_surats = $db->query($queryIncoming)->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <div class="notif-box" style="margin-top: 30px; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <h4 style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                                <i class="fas fa-envelope-open-text" style="color: #f39c12;"></i> Pengajuan Butuh Verifikasi
                            </h4>
                            <ul style="list-style: none; padding: 0;">
                                <?php if (!empty($incoming_surats)): ?>
                                    <?php foreach ($incoming_surats as $n): ?>
                                        <li style="padding: 12px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <a href="lihat_detail.php?id=<?= $n['id_pengajuan'] ?>" style="text-decoration: none; color: #333; font-weight: bold; display: block;">
                                                    <?= htmlspecialchars($n['nama_mhs'] ?? '') ?>
                                                </a>
                                                <small style="color: #666;">Jenis: <?= htmlspecialchars($n['jenis_surat'] ?? '') ?></small>
                                            </div>
                                            <div style="text-align: right;">
                                                <span style="display: block; font-size: 0.75rem; color: #999;"><?= date('d M, H:i', strtotime($n['tanggal_pengajuan'])) ?></span>
                                                <span style="font-size: 0.7rem; background: #fff3e0; color: #e67e22; padding: 2px 8px; border-radius: 10px; font-weight: bold;">MENUNGGU</span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                    <li style="text-align: center; margin-top: 15px;">
                                        <a href="surat_masuk.php" style="color: #f39c12; text-decoration: none; font-size: 0.85rem; font-weight: bold;">Kelola Semua Surat <i class="fas fa-arrow-right"></i></a>
                                    </li>
                                <?php else: ?>
                                    <li style="color: #888; text-align: center; padding: 20px;">
                                        <i class="fas fa-check-circle" style="color: #2ecc71; font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                        Semua surat telah diproses.
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="right-column">
                        <div class="profile-card">
                            <div class="avatar-wrapper">
                                <img src="<?= $foto_sidebar ?? '../../assets/img/profiles/avatar.jpg' ?>?t=<?= time() ?>" alt="Admin Avatar">
                            </div>
                            <p class="profile-name"><?= htmlspecialchars($admin['nama']) ?></p>
                            <!-- <p style="color: #00a2ed; font-weight: bold; font-size: 0.8rem; margin-top: -10px;">ADMINISTRATOR</p> -->
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Profil Diperbarui!',
                text: 'Data profil Anda telah berhasil disimpan.',
                confirmButtonColor: '#00a2ed'
            });
        }
    </script>
</body>

</html>