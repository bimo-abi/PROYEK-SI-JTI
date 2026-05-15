<?php
require_once '../../autoload.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

use Config\Database;

$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

$query = "SELECT p.nama, p.email, d.nomor_induk, d.foto_profil 
          FROM pengguna p 
          LEFT JOIN detail_pengguna d ON p.id = d.id_pengguna 
          WHERE p.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$foto_sekarang = !empty($admin['foto_profil'])
    ? "../../assets/img/profiles/" . $admin['foto_profil']
    : "../../assets/img/profiles/avatar.jpg";

$current_page = 'profil';
$page_title = 'Edit Profil Admin';
?>

<!DOCTYPE html>
<html lang="id">

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
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; max-width: 600px; margin: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <h4 style="margin-bottom: 25px; color: #333; text-align: center;"><i class="fas fa-user-edit"></i> Edit Profil Administrator</h4>

                    <form action="../../process/proses_edit_profil.php" method="POST" enctype="multipart/form-data">

                        <div style="text-align: center; margin-bottom: 30px;">
                            <div style="position: relative; display: inline-block;">
                                <img id="previewFoto" src="<?= $foto_sekarang ?>?t=<?= time() ?>"
                                    style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 4px solid #00a2ed; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <label for="fotoInput" style="position: absolute; bottom: 5px; right: 5px; background: #00a2ed; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid white;">
                                    <i class="fas fa-camera"></i>
                                </label>
                            </div>
                            <input type="file" id="fotoInput" name="foto_profil" accept="image/*" style="display: none;">
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; font-weight: 600;">Nama Lengkap</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($admin['nama']) ?>" required
                                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 10px; box-sizing: border-box;">
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; font-weight: 600;">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required
                                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 10px; box-sizing: border-box;">
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; font-weight: 600;">NIP / ID Admin</label>
                            <input type="text" value="<?= htmlspecialchars($admin['nomor_induk']) ?>" readonly
                                style="width: 100%; padding: 12px; border: 1px solid #eee; background: #f9f9f9; border-radius: 10px; color: #888; cursor: not-allowed; box-sizing: border-box;">
                        </div>

                        <div style="margin-top: 30px; display: flex; gap: 10px;">
                            <button type="submit" style="background: #00a2ed; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: 600;">Simpan Perubahan</button>
                            <a href="dashboard.php" style="background: #666; color: white; text-decoration: none; padding: 12px 30px; border-radius: 8px; font-size: 0.9rem;">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const fotoInput = document.getElementById('fotoInput');
        const previewFoto = document.getElementById('previewFoto');
        fotoInput.onchange = evt => {
            const [file] = fotoInput.files;
            if (file) previewFoto.src = URL.createObjectURL(file);
        }
    </script>
</body>

</html>