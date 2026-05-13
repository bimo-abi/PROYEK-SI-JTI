<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

try {
    $query = "SELECT u.nama, u.email, dp.nomor_induk, dp.nomor_telepon, dp.alamat, dp.foto_profil
              FROM pengguna u 
              JOIN detail_pengguna dp ON u.id = dp.id_pengguna 
              WHERE u.id = ?";

    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Data profil tidak ditemukan.");
    }
} catch (\PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}

$current_page = 'profil';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil Dosen - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>
            
            <div class="content">
                <div class="profile-main-card" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px;">
                    <div class="profile-header" style="display: flex; align-items: center; gap: 25px; border-bottom: 1px solid #f0f0f0; padding-bottom: 30px;">
                        <!-- Bagian Foto & Form Upload -->
                        <div class="photo-section" style="text-align: center;">
                            <?php
                            $foto = !empty($user['foto_profil']) ? "../../assets/img/profiles/" . $user['foto_profil'] : "../../assets/img/avatar.png";
                            ?>
                            <div style="position: relative; display: inline-block;">
                                <img src="<?= $foto ?>?t=<?= time() ?>" alt="User Photo" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #00a2ed; display: block;">
                            </div>
                            
                            <!-- Form Upload Cepat -->
                            <form action="proses_foto.php" method="POST" enctype="multipart/form-data" style="margin-top: 15px;">
                                <label for="file-upload" style="cursor: pointer; background: #f8f9fa; border: 1px solid #ddd; padding: 5px 10px; border-radius: 5px; font-size: 0.75rem; display: inline-block;">
                                    <i class="fas fa-camera"></i> Ganti Foto
                                </label>
                                <input id="file-upload" type="file" name="foto_profil" accept="image/*" style="display: none;" onchange="this.form.submit()">
                                <input type="hidden" name="upload" value="1">
                            </form>
                        </div>
                        <div class="user-id" style="flex: 1;">
                            <h3 style="margin: 0; font-size: 1.5rem; color: #333;"><?= htmlspecialchars($user['nama'] ?? '') ?></h3>
                            <p style="margin: 5px 0; color: #666;">NIP/NIDN : <?= htmlspecialchars($user['nomor_induk'] ?? '') ?></p>
                            <p style="margin: 0; color: #888; font-size: 0.9rem;">Dosen - Jurusan Teknologi Informasi</p>
                        </div>
                        <div class="btn-actions">
                            <a href="edit_profile.php" class="btn-mini" style="background: #00a2ed; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 0.85rem; display: inline-block; font-weight: 600;">
                                <i class="fas fa-edit"></i> Edit Profil
                            </a>
                        </div>
                    </div>
                </div>

                <div class="detail-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div class="info-box" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #eee;">
                        <h5 style="margin-top: 0; color: #00a2ed; border-bottom: 2px solid #00a2ed; display: inline-block; padding-bottom: 5px; margin-bottom: 15px;">
                            <i class="fas fa-user"></i> Data Diri
                        </h5>
                        <p style="margin-bottom: 12px;"><strong>Nama Lengkap :</strong> <br><?= htmlspecialchars($user['nama'] ?? '') ?></p>
                        <p style="margin-bottom: 12px;"><strong>NIP/NIDN :</strong> <br><?= htmlspecialchars($user['nomor_induk'] ?? '') ?></p>
                        <p style="margin-bottom: 0;"><strong>Email :</strong> <br><?= htmlspecialchars($user['email'] ?? '-') ?></p>
                    </div>

                    <div class="info-box" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #eee;">
                        <h5 style="margin-top: 0; color: #00a2ed; border-bottom: 2px solid #00a2ed; display: inline-block; padding-bottom: 5px; margin-bottom: 15px;">
                            <i class="fas fa-address-book"></i> Kontak & Alamat
                        </h5>
                        <p style="margin-bottom: 12px;"><strong>No. Telepon :</strong> <br><?= htmlspecialchars($user['nomor_telepon'] ?? '-') ?></p>
                        <p style="margin-bottom: 0;"><strong>Alamat :</strong> <br><?= nl2br(htmlspecialchars($user['alamat'] ?? '-')) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Script SweetAlert2 untuk Notifikasi -->
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Foto profil Anda telah diperbarui.',
                icon: 'success',
                confirmButtonColor: '#00a2ed'
            });
        } else if (status === 'error') {
            Swal.fire({
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat mengunggah foto.',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    </script>
</body>
</html>
