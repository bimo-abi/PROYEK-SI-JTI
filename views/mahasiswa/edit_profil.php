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

// Ambil data lama untuk mengisi form
$query = "SELECT u.nama, u.email, dp.nomor_induk, dp.id_prodi, dp.id_golongan 
          FROM pengguna u 
          JOIN detail_pengguna dp ON u.id = dp.id_pengguna 
          WHERE u.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil data master untuk pilihan (dropdown)
$prodi_list = $db->query("SELECT * FROM prodi")->fetchAll();
$golongan_list = $db->query("SELECT * FROM golongan")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Profil - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>

            <div class="content">
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <h4><i class="fas fa-user-edit"></i> Edit Profil Saya</h4>
                    <hr>

                    <form action="proses_edit_profil.php" method="POST">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <!-- Data Akun -->
                            <div>
                                <label style="display:block; margin-bottom: 8px;">Nama Lengkap</label>
                                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                            </div>
                            <div>
                                <label style="display:block; margin-bottom: 8px;">Email</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                            </div>
                            <div>
                                <label style="display:block; margin-bottom: 8px;">NIM</label>
                                <input type="text" name="nomor_induk" value="<?= htmlspecialchars($user['nomor_induk']) ?>" readonly style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; background: #eee; cursor: not-allowed; color: #666;">
                                <small style="color: #888; margin-top: 5px; display: block;">*NIM tidak dapat diubah.</small>
                            </div>

                            <!-- Data Akademik -->
                            <div>
                                <label style="display:block; margin-bottom: 8px;">Program Studi</label>
                                <select name="id_prodi" style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                                    <?php foreach ($prodi_list as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= $user['id_prodi'] == $p['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['nama_prodi']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label style="display:block; margin-bottom: 8px;">Golongan</label>
                                <select name="id_golongan" style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                                    <option value="">-- Pilih Golongan --</option>
                                    <?php foreach ($golongan_list as $g): ?>
                                        <option value="<?= $g['id'] ?>" <?= $user['id_golongan'] == $g['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($g['nama_golongan']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div style="margin-top: 30px; display: flex; gap: 10px;">
                            <button type="submit" style="background: #00a2ed; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer;">Simpan Perubahan</button>
                            <a href="profil.php" style="background: #666; color: white; text-decoration: none; padding: 12px 30px; border-radius: 8px;">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>