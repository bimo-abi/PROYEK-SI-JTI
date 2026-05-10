<?php
require_once '../../autoload.php';
session_start();

// Proteksi halaman: Wajib Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

use Config\Database;
$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// Ambil data admin saat ini
$query = "SELECT p.nama, p.email, d.nomor_induk 
          FROM pengguna p 
          LEFT JOIN detail_pengguna d ON p.id = d.id_pengguna 
          WHERE p.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$current_page = 'profil';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil Admin - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_admin.php'; ?>
        
        <div class="main-container">
            <?php include '../layouts/topbar_admin.php'; ?>

            <div class="content">
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; max-width: 600px; margin: auto;">
                    <h4 style="margin-bottom: 20px; color: #333;"><i class="fas fa-user-edit"></i> Edit Profil Administrator</h4>
                    
                    <form action="proses_edit_profil.php" method="POST">
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">Nama Lengkap</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($admin['nama']) ?>" required 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">NIP / ID Admin (Read Only)</label>
                            <input type="text" value="<?= htmlspecialchars($admin['nomor_induk']) ?>" readonly 
                                   style="width: 100%; padding: 10px; border: 1px solid #eee; background: #f9f9f9; border-radius: 8px; color: #888;">
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <button type="submit" style="background: #00a2ed; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                Simpan Perubahan
                            </button>
                            <a href="dashboard.php" style="background: #eee; color: #333; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 0.9rem; text-align: center;">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'success') {
            Swal.fire('Berhasil!', 'Profil Anda telah diperbarui.', 'success');
        }
    </script>
</body>
</html>