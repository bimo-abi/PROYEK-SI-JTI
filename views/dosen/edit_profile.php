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
    <style>
        .edit-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #00a2ed;
            box-shadow: 0 0 0 3px rgba(0, 162, 237, 0.1);
            outline: none;
        }
        .form-control[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
            color: #777;
        }
        .btn-save {
            background: #00a2ed;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }
        .btn-save:hover {
            background: #0081bd;
            transform: translateY(-2px);
        }
        .btn-cancel {
            background: #f1f2f6;
            color: #555;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-cancel:hover {
            background: #dfe4ea;
        }
        .photo-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #eee;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>
            
            <div class="content">
                <div class="card-container" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); max-width: 900px; margin: 20px auto;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                        <div style="background: #e3f2fd; color: #00a2ed; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; color: #333;">Pengaturan Profil</h3>
                            <p style="margin: 5px 0 0; color: #777; font-size: 0.9rem;">Kelola informasi pribadi dan pengaturan akun Anda.</p>
                        </div>
                    </div>

                    <form action="../../process/admin_process.php?action=update_profile" method="POST" enctype="multipart/form-data">
                        <div class="edit-form-grid">
                            <!-- Data Personal -->
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama'] ?? '') ?>" required>
                            </div>

                            <div class="form-group">
                                <label>NIP / NIDN</label>
                                <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($user['nomor_induk'] ?? '') ?>" readonly>
                                <small style="color: #999; margin-top: 5px; display: block;">*NIP/NIDN tidak dapat diubah secara mandiri.</small>
                            </div>

                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required placeholder="contoh@polinema.ac.id">
                            </div>

                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <input type="text" name="nomor_telepon" class="form-control" value="<?= htmlspecialchars($user['nomor_telepon'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                            </div>

                            <div class="form-group" style="grid-column: span 2;">
                                <label>Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap Anda..."><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
                            </div>

                            <div class="form-group" style="grid-column: span 2; background: #fcfcfc; padding: 20px; border-radius: 15px; border: 1px dashed #ddd;">
                                <label>Foto Profil</label>
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <?php 
                                        $foto_path = "../../assets/img/avatar.png";
                                        if(!empty($user['foto_profil'])) {
                                            $foto_path = "../../assets/img/profiles/" . $user['foto_profil'];
                                        }
                                    ?>
                                    <img src="<?= $foto_path ?>" class="photo-preview" id="previewImg">
                                    <div style="flex: 1;">
                                        <input type="file" name="foto_profil" class="form-control" accept="image/*" onchange="previewFile(this)">
                                        <p style="margin: 8px 0 0; color: #888; font-size: 0.8rem;">Format: JPG, PNG, atau GIF. Maksimal 2MB.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 15px; border-top: 1px solid #eee; padding-top: 30px;">
                            <a href="profil.php" class="btn-cancel">Batal</a>
                            <button type="submit" class="btn-save">
                                <i class="fas fa-check-circle"></i> Simpan Perubahan
                            </button>
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