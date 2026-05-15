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

try {
    // Perbaikan Query: Hapus nomor_telepon dan alamat karena tidak ada di DB
    $stmt = $db->prepare("SELECT u.nama, u.email, d.nomor_induk, d.foto_profil
                          FROM pengguna u 
                          JOIN detail_pengguna d ON u.id = d.id_pengguna 
                          WHERE u.id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Data profil tidak ditemukan.");
    }
} catch (\PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Profil - Dosen SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="background: #f4f7fa;">
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>

            <div class="content" style="padding: 30px;">
                <div class="card-container" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <h4 style="margin-top: 0; color: #1e293b; font-weight: 700;">
                        <i class="fas fa-user-edit" style="color: #4f46e5;"></i> Edit Profil Saya
                    </h4>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

                    <form action="../../process/auth_process.php?action=update_profile" method="POST" enctype="multipart/form-data">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

                            <div>
                                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #444;">Nama Lengkap</label>
                                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama'] ?? '') ?>" required style="width:100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; outline: none;">
                            </div>

                            <div>
                                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #444;">Email Institusi</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required style="width:100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; outline: none;">
                            </div>

                            <div>
                                <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #444;">NIP / NIDN</label>
                                <input type="text" value="<?= htmlspecialchars($user['nomor_induk'] ?? '') ?>" readonly style="width:100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; background: #f8fafc; cursor: not-allowed; color: #64748b;">
                                <small style="color: #94a3b8; margin-top: 5px; display: block;">* Identitas utama tidak dapat diubah secara mandiri.</small>
                            </div>

                            <div style="grid-column: span 2; background: #f8fafc; padding: 20px; border-radius: 15px; border: 1px dashed #cbd5e1; margin-top: 10px;">
                                <label style="display:block; margin-bottom: 15px; font-weight: 700; color: #1e293b;">Foto Profil</label>
                                <div style="display: flex; align-items: center; gap: 25px;">
                                    <?php
                                    $foto_path = !empty($user['foto_profil']) ? "../../assets/img/profiles/" . $user['foto_profil'] : "../../assets/img/profiles/avatar.png";
                                    ?>
                                    <img src="<?= $foto_path ?>?t=<?= time() ?>" id="previewImg" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">

                                    <div style="flex: 1;">
                                        <input type="file" name="foto_profil" id="fileInput" accept="image/*" onchange="previewFile(this)" style="margin-bottom: 8px; font-size: 0.9rem;">
                                        <p style="margin: 0; color: #64748b; font-size: 0.8rem;">Gunakan format JPG atau PNG. Maksimal ukuran file 2MB.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 40px; display: flex; gap: 15px;">
                            <button type="submit" style="background: #4f46e5; color: white; border: none; padding: 14px 35px; border-radius: 12px; cursor: pointer; font-weight: 700; transition: 0.3s; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="profil.php" style="background: #f1f5f9; color: #475569; text-decoration: none; padding: 14px 35px; border-radius: 12px; font-weight: 600; transition: 0.3s;">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preview Foto Profil secara Real-time
        function previewFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').setAttribute('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // SweetAlert Notifikasi
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Profil Anda telah diperbarui.',
                confirmButtonColor: '#4f46e5'
            });
        } else if (urlParams.get('status') === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat menyimpan data.',
                confirmButtonColor: '#ef4444'
            });
        }
    </script>
</body>

</html>