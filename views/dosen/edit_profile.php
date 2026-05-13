<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();
$current_page = 'edit_profile';

// Ambil data dosen terbaru dari database untuk mengisi form
$stmt = $db->prepare("SELECT u.nama, d.nomor_induk 
                      FROM pengguna u 
                      JOIN detail_pengguna d ON u.id = d.id_pengguna 
                      WHERE u.id = ?");
$stmt->execute([$_SESSION['user_id']]);
$data_dosen = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil - Dosen SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>
            
            <div class="content">
                <div class="card-profile" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto;">
                    <h3 style="margin-bottom: 20px;"><i class="fas fa-user-edit" style="color: #00a2ed;"></i> Edit Profil Dosen</h3>
                    <hr style="margin-bottom: 25px; border: 0; border-top: 1px solid #eee;">
                    
                    <form action="../../process/admin_process.php?action=update_profile" method="POST" enctype="multipart/form-data">
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Nama Lengkap :</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($data_dosen['nama']) ?>" 
                                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;" required>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">NIP / NIDN :</label>
                            <input type="text" name="nip" value="<?= htmlspecialchars($data_dosen['nomor_induk']) ?>" 
                                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;" required>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Foto Profil Baru :</label>
                            <input type="file" name="foto_profil" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 8px;">
                            <small style="color: #666;">*Kosongkan jika tidak ingin mengubah foto profil.</small>
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <button type="submit" style="background: #00a2ed; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: bold; flex: 1;">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="dashboard.php" style="background: #ccc; color: #333; text-decoration: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; text-align: center;">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>