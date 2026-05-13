<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

try {
    $query = "SELECT 
                u.nama, 
                u.email, 
                dp.nomor_induk, 
                dp.foto_profil,
                p.nama_prodi, 
                g.nama_golongan
              FROM pengguna u 
              JOIN detail_pengguna dp ON u.id = dp.id_pengguna 
              LEFT JOIN prodi p ON dp.id_prodi = p.id
              LEFT JOIN golongan g ON dp.id_golongan = g.id
              WHERE u.id = ?";

    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Data profil tidak ditemukan di database.");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Step 1: Tambahkan SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>

        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>

            <div class="content">
                <div class="profile-main-card" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px;">

                    <!-- Header Profil -->
                    <div class="profile-header" style="display: flex; align-items: center; gap: 25px; border-bottom: 1px solid #f0f0f0; padding-bottom: 30px;">

                        <!-- Bagian Foto & Form Upload -->
                        <div class="photo-section" style="text-align: center;">
                            <?php
                            $foto = !empty($user['foto_profil']) ? "../../assets/img/profiles/" . $user['foto_profil'] : "../../assets/img/profiles/avatar.jpg";
                            ?>

                            <div style="position: relative; display: inline-block;">
                                <img src="<?= $foto ?>?t=<?= time() ?>" alt="User Photo" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #00a2ed; display: block;">
                            </div>

                            <!-- Form Upload -->
                            <form action="proses_foto.php" method="POST" enctype="multipart/form-data" style="margin-top: 15px;">
                                <label for="file-upload" style="cursor: pointer; background: #f8f9fa; border: 1px solid #ddd; padding: 5px 10px; border-radius: 5px; font-size: 0.75rem; display: inline-block;">
                                    <i class="fas fa-camera"></i> Ganti Foto
                                </label>
                                <input id="file-upload" type="file" name="foto_profil" accept="image/*" style="display: none;" onchange="this.form.submit()">
                                <input type="hidden" name="upload" value="1">
                            </form>
                        </div>

                        <!-- Informasi User -->
                        <div class="user-id" style="flex: 1;">
                            <h3 style="margin: 0; font-size: 1.5rem; color: #333;"><?= htmlspecialchars($user['nama']) ?></h3>
                            <p style="margin: 5px 0; color: #666;">NIM : <?= htmlspecialchars($user['nomor_induk']) ?></p>
                            <p style="margin: 0; color: #888; font-size: 0.9rem;">Jurusan Teknologi Informasi - <?= htmlspecialchars($user['nama_prodi']) ?></p>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="btn-actions">
                            <a href="edit_profil.php" class="btn-mini" style="background: #00a2ed; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-size: 0.8rem; display: inline-block;">
                                <i class="fas fa-edit"></i> Edit Profil
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Detail Data Grid -->
                <div class="detail-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <!-- Box Data Diri -->
                    <div class="info-box" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #eee;">
                        <h5 style="margin-top: 0; color: #00a2ed; border-bottom: 2px solid #00a2ed; display: inline-block; padding-bottom: 5px; margin-bottom: 15px;">
                            <i class="fas fa-user"></i> Data Diri
                        </h5>
                        <p style="margin-bottom: 10px;"><strong>Nama Lengkap :</strong> <br><?= htmlspecialchars($user['nama']) ?></p>
                        <p style="margin-bottom: 10px;"><strong>NIM :</strong> <br><?= htmlspecialchars($user['nomor_induk']) ?></p>
                        <p style="margin-bottom: 0;"><strong>Email :</strong> <br><?= htmlspecialchars($user['email']) ?></p>
                    </div>

                    <!-- Box Data Akademik -->
                    <div class="info-box" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #eee;">
                        <h5 style="margin-top: 0; color: #00a2ed; border-bottom: 2px solid #00a2ed; display: inline-block; padding-bottom: 5px; margin-bottom: 15px;">
                            <i class="fas fa-graduation-cap"></i> Data Akademik
                        </h5>
                        <p style="margin-bottom: 10px;"><strong>Jurusan :</strong> <br>Teknologi Informasi</p>
                        <p style="margin-bottom: 10px;"><strong>Program Studi :</strong> <br><?= htmlspecialchars($user['nama_prodi']) ?></p>
                        <p style="margin-bottom: 0;"><strong>Golongan :</strong> <br><?= htmlspecialchars($user['nama_golongan'] ?? '-') ?></p>
                    </div>
                </div>
            </div> <!-- End Content -->
        </div> <!-- End Main Container -->
    </div> <!-- End Wrapper -->

    <!-- Script SweetAlert2 untuk Notifikasi -->
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Profil Anda berhasil diperbarui.',
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