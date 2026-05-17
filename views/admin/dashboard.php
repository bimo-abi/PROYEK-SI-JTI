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
    <link rel="stylesheet" href="../../assets/css/mahasiswa_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_admin.php'; ?>

        <div class="main-container">
            <?php include '../layouts/topbar_admin.php'; ?>

            <div class="content mahasiswa-dashboard-content">
                
                <div class="section-title" style="margin-bottom: 24px;">Dashboard Administrator</div>

                <!-- Stats Section -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 32px;">
                    <div class="stat-card blue">
                        <i class="fas fa-file-alt"></i>
                        <span class="label">Semua Pengajuan</span>
                        <span class="value"><?= $totalSurat ?></span>
                    </div>
                    <div class="stat-card green">
                        <i class="fas fa-check-circle"></i>
                        <span class="label">Diterima</span>
                        <span class="value"><?= $totalApprove ?></span>
                    </div>
                    <div class="stat-card orange">
                        <i class="fas fa-clock"></i>
                        <span class="label">Diproses</span>
                        <span class="value"><?= $totalPending ?></span>
                    </div>
                    <div class="stat-card red">
                        <i class="fas fa-times-circle"></i>
                        <span class="label">Ditolak</span>
                        <span class="value"><?= $totalReject ?></span>
                    </div>
                </div>

                <div class="main-grid">
                    <!-- Left Column: Notifications & Quick Actions -->
                    <div class="left-column">
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

                        <div class="section-card" style="margin-bottom: 24px;">
                            <div class="section-title">
                                <i class="fas fa-envelope-open-text text-primary"></i> Pengajuan Butuh Verifikasi
                            </div>
                            <div class="notif-list">
                                <?php if (!empty($incoming_surats)): ?>
                                    <?php foreach ($incoming_surats as $n): ?>
                                        <div class="notif-item" style="display: flex; justify-content: space-between; align-items: center;">
                                            <div style="display: flex; gap: 16px; align-items: flex-start; flex: 1;">
                                                <div style="width: 40px; height: 40px; border-radius: 12px; background: var(--background); display: flex; align-items: center; justify-content: center; color: var(--warning);">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                </div>
                                                <div style="flex: 1;">
                                                    <a href="lihat_detail.php?id=<?= $n['id_pengajuan'] ?>" style="text-decoration: none; color: inherit;">
                                                        <p style="margin: 0; font-weight: 600; font-size: 0.9375rem;">
                                                            <?= htmlspecialchars($n['nama_mhs'] ?? '') ?>
                                                        </p>
                                                    </a>
                                                    <small style="color: var(--text-muted);">Jenis: <?= htmlspecialchars($n['jenis_surat'] ?? '') ?></small>
                                                </div>
                                            </div>
                                            <div style="text-align: right; min-width: 100px;">
                                                <span style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;"><?= date('d M, H:i', strtotime($n['tanggal_pengajuan'])) ?></span>
                                                <span style="font-size: 0.7rem; background: #fff3e0; color: #e67e22; padding: 4px 8px; border-radius: 10px; font-weight: bold;">MENUNGGU</span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div style="text-align: center; color: var(--text-muted); padding: 20px;">
                                        <i class="fas fa-check-circle" style="color: var(--success); font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                        <p>Semua surat telah diproses.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($incoming_surats)): ?>
                            <div style="margin-top: 24px; text-align: center;">
                                <a href="surat_masuk.php" class="btn-primary" style="display: inline-flex; width: auto; padding: 12px 32px;">
                                    Kelola Semua Surat <i class="fas fa-arrow-right" style="margin-left: 8px; margin-right: 0;"></i>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right Column: Profile Summary -->
                    <div class="right-column">
                        <div class="profile-card-premium">
                            <div class="avatar-wrapper" style="position: relative; display: inline-block;">
                                <img src="<?= $foto_sidebar ?? '../../assets/img/profiles/avatar.jpg' ?>?t=<?= time() ?>" alt="Admin Avatar" class="avatar">
                                <div style="position: absolute; bottom: 15px; right: 5px; width: 24px; height: 24px; background: var(--success); border: 4px solid var(--surface); border-radius: 50%;"></div>
                            </div>
                            <h3 style="margin-bottom: 4px;"><?= htmlspecialchars($admin['nama']) ?></h3>
                            <p style="color: var(--primary); font-weight: 700; font-size: 0.875rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 24px;">Administrator</p>
                            
                            <div style="text-align: left; background: var(--background); padding: 20px; border-radius: 20px;">
                                <div style="margin-bottom: 12px;">
                                    <small style="color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 0.7rem;">NIP/ID</small>
                                    <p style="margin: 0; font-weight: 600; font-size: 0.9rem;"><?= htmlspecialchars($admin['nomor_induk'] ?? '-') ?></p>
                                </div>
                                <div>
                                    <small style="color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 0.7rem;">Email</small>
                                    <p style="margin: 0; font-weight: 600; font-size: 0.9rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($admin['email'] ?? '-') ?></p>
                                </div>
                            </div>
                            
                            <a href="edit_profil.php" class="btn-secondary" style="margin-top: 24px; width: 100%;">
                                <i class="fas fa-user-edit" style="margin-right: 8px;"></i> Edit Profil
                            </a>
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