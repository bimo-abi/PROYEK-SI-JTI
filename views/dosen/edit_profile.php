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
$stmt = $db->prepare("SELECT u.nama, u.email, d.nomor_induk, d.nomor_telepon, d.alamat, d.foto_profil
                      FROM pengguna u 
                      JOIN detail_pengguna d ON u.id = d.id_pengguna 
                      WHERE u.id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
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
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <h4><i class="fas fa-user-edit"></i> Edit Profil Saya</h4>
                    <hr>

                    <form action="../../process/admin_process.php?action=update_profile" method="POST" enctype="multipart/form-data">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <!-- Data Akun -->
                            <div>
                                <label style="display:block; margin-bottom: 8px;">Nama Lengkap</label>
                                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama'] ?? '') ?>" required style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                            </div>
                            <div>
                                <label style="display:block; margin-bottom: 8px;">Email</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                            </div>
                            <div>
                                <label style="display:block; margin-bottom: 8px;">NIP / NIDN</label>
                                <input type="text" name="nip" value="<?= htmlspecialchars($user['nomor_induk'] ?? '') ?>" readonly style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; background: #eee; cursor: not-allowed; color: #666;">
                                <small style="color: #888; margin-top: 5px; display: block;">*NIP tidak dapat diubah.</small>
                            </div>
                            <div>
                                <label style="display:block; margin-bottom: 8px;">Nomor Telepon</label>
                                <input type="text" name="nomor_telepon" value="<?= htmlspecialchars($user['nomor_telepon'] ?? '') ?>" style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                            </div>
                            <div style="grid-column: span 2;">
                                <label style="display:block; margin-bottom: 8px;">Alamat Lengkap</label>
                                <textarea name="alamat" rows="3" style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; font-family: inherit;"><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
                            </div>

                            <!-- Foto Profil -->
                            <div style="grid-column: span 2; background: #f9f9f9; padding: 20px; border-radius: 12px; border: 1px dashed #ddd;">
                                <label style="display:block; margin-bottom: 10px; font-weight: bold;">Foto Profil</label>
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <?php 
                                        $foto_path = "../../assets/img/profiles/avatar.jpg";
                                        if(!empty($user['foto_profil'])) {
                                            $foto_path = "../../assets/img/profiles/" . $user['foto_profil'];
                                        }
                                    ?>
                                    <img src="<?= $foto_path ?>?t=<?= time() ?>" id="previewImg" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                    <div style="flex: 1;">
                                        <input type="file" name="foto_profil" accept="image/*" onchange="previewFile(this)" style="margin-bottom: 5px;">
                                        <p style="margin: 0; color: #888; font-size: 0.75rem;">Format: JPG, PNG. Maksimal 2MB.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 30px; display: flex; gap: 10px;">
                            <button type="submit" style="background: #00a2ed; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: bold;">Simpan Perubahan</button>
                            <a href="profil.php" style="background: #666; color: white; text-decoration: none; padding: 12px 30px; border-radius: 8px;">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewFile(input) {
            var file = $("input[type=file]").get(0).files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function() {
                    $("#previewImg").attr("src", reader.result);
                }
                reader.readAsDataURL(file);
            }
        }
        
        // Simple preview without jQuery (since it might not be loaded)
        function previewFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').setAttribute('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Show SweetAlert if status is success
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'success') {
            // Check if Swal is loaded
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Profil Diperbarui',
                    text: 'Data profil Anda telah berhasil disimpan.',
                    confirmButtonColor: '#00a2ed'
                });
            } else {
                alert('Profil berhasil diperbarui!');
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>