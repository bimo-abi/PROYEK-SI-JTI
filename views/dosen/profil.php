<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

// Proteksi Halaman
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

try {
    // Query disesuaikan dengan kolom yang ada di screenshot phpMyAdmin kamu
    $query = "SELECT u.nama, u.email, dp.nomor_induk, dp.foto_profil
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
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Dosen - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="background-color: #f4f7fa;">
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>

        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>

            <div class="content" style="padding: 30px;">
                <div class="profile-main-card" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px;">
                    <div class="profile-header" style="display: flex; align-items: center; gap: 25px; border-bottom: 1px solid #f0f0f0; padding-bottom: 30px;">

                        <div class="photo-section" style="text-align: center;">
                            <?php
                            $foto = !empty($user['foto_profil']) ? "../../assets/img/profiles/" . $user['foto_profil'] : "../../assets/img/profiles/avatar.png";
                            ?>
                            <div style="position: relative; display: inline-block;">
                                <img src="<?= $foto ?>?t=<?= time() ?>" alt="User Photo" style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 4px solid #4f46e5; display: block; box-shadow: 0 5px 15px rgba(79, 70, 229, 0.2);">
                            </div>

                            <form action="proses_foto.php" method="POST" enctype="multipart/form-data" style="margin-top: 15px;">
                                <label for="file-upload" style="cursor: pointer; background: #eeeffe; color: #4f46e5; border: 1px solid #c7d2fe; padding: 8px 15px; border-radius: 8px; font-size: 0.75rem; display: inline-block; font-weight: 600; transition: 0.3s;">
                                    <i class="fas fa-camera"></i> Ganti Foto
                                </label>
                                <input id="file-upload" type="file" name="foto_profil" accept="image/*" style="display: none;" onchange="this.form.submit()">
                                <input type="hidden" name="upload" value="1">
                            </form>
                        </div>

                        <div class="user-id" style="flex: 1;">
                            <h3 style="margin: 0; font-size: 1.8rem; color: #1e293b; font-weight: 700;"><?= htmlspecialchars($user['nama'] ?? '') ?></h3>
                            <p style="margin: 8px 0; color: #64748b; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-id-badge" style="color: #4f46e5;"></i> NIP/NIDN : <?= htmlspecialchars($user['nomor_induk'] ?? '-') ?>
                            </p>
                            <span style="background: #e0e7ff; color: #4338ca; padding: 5px 15px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Dosen Pengajar</span>
                        </div>

                        <div class="btn-actions">
                            <a href="edit_profile.php" class="btn-mini" style="background: #4f46e5; color: white; text-decoration: none; padding: 12px 25px; border-radius: 10px; font-size: 0.9rem; display: inline-block; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);">
                                <i class="fas fa-user-edit"></i> Edit Profil
                            </a>
                        </div>
                    </div>
                </div>

                <div class="detail-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
                    <div class="info-box" style="background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;">
                        <h5 style="margin-top: 0; color: #4f46e5; border-bottom: 2px solid #e0e7ff; display: block; padding-bottom: 10px; margin-bottom: 20px; font-size: 1.1rem;">
                            <i class="fas fa-user-circle"></i> Informasi Dasar
                        </h5>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; font-weight: 700; margin-bottom: 5px;">Nama Lengkap</label>
                            <p style="margin: 0; color: #1e293b; font-weight: 500;"><?= htmlspecialchars($user['nama'] ?? '') ?></p>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; font-weight: 700; margin-bottom: 5px;">NIP / NIDN</label>
                            <p style="margin: 0; color: #1e293b; font-weight: 500;"><?= htmlspecialchars($user['nomor_induk'] ?? '-') ?></p>
                        </div>
                        <div>
                            <label style="display: block; color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; font-weight: 700; margin-bottom: 5px;">Email Institusi</label>
                            <p style="margin: 0; color: #1e293b; font-weight: 500;"><?= htmlspecialchars($user['email'] ?? '-') ?></p>
                        </div>
                    </div>

                    <div class="info-box" style="background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;">
                        <h5 style="margin-top: 0; color: #4f46e5; border-bottom: 2px solid #e0e7ff; display: block; padding-bottom: 10px; margin-bottom: 20px; font-size: 1.1rem;">
                            <i class="fas fa-map-marker-alt"></i> Kontak & Lokasi
                        </h5>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; font-weight: 700; margin-bottom: 5px;">Nomor Telepon</label>
                            <p style="margin: 0; color: #1e293b; font-weight: 500;">-</p>
                        </div>
                        <div>
                            <label style="display: block; color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; font-weight: 700; margin-bottom: 5px;">Alamat Domisili</label>
                            <p style="margin: 0; color: #1e293b; font-weight: 500;">Data alamat belum dilengkapi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Profil Anda telah diperbarui.',
                icon: 'success',
                confirmButtonColor: '#4f46e5'
            });
        } else if (status === 'error') {
            Swal.fire({
                title: 'Gagal!',
                text: 'Terjadi kesalahan sistem.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        }
    </script>
</body>

</html>