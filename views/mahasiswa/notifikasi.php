<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();
$notifs = [];

try {
    $nim = $_SESSION['nim'] ?? null;

    if ($nim) {
        // 1. Ambil data pengajuan surat langsung dari tabel utama tanpa kolom is_read
        $querySurat = "SELECT id_pengajuan, jenis_surat, tanggal_pengajuan, status 
                       FROM pengajuan_surat 
                       WHERE nim = ? 
                       ORDER BY tanggal_pengajuan DESC LIMIT 15";
        $stmtSurat = $db->prepare($querySurat);
        $stmtSurat->execute([$nim]);
        $daftar_surat = $stmtSurat->fetchAll(PDO::FETCH_ASSOC);

        // 2. Olah data surat menjadi teks notifikasi secara dinamis
        foreach ($daftar_surat as $s) {
            $statusText = strtolower($s['status']);
            $judul = "Pembaruan Pengajuan Surat";

            if ($statusText === 'menunggu') {
                $pesan = "Surat pengajuan '" . htmlspecialchars($s['jenis_surat']) . "' Anda telah dikirim dan saat ini sedang menunggu verifikasi.";
                $icon = "fa-clock";
                $iconColor = "#eab308"; // Kuning
            } else if ($statusText === 'terverifikasi' || $statusText === 'disetujui') {
                $pesan = "Selamat! Surat pengajuan '" . htmlspecialchars($s['jenis_surat']) . "' Anda telah disetujui dan terverifikasi.";
                $icon = "fa-check-circle";
                $iconColor = "#16a34a"; // Hijau
            } else if ($statusText === 'ditolak') {
                $pesan = "Maaf, surat pengajuan '" . htmlspecialchars($s['jenis_surat']) . "' Anda ditolak oleh Admin/Dosen.";
                $icon = "fa-times-circle";
                $iconColor = "#dc2626"; // Merah
            } else {
                $pesan = "Surat pengajuan '" . htmlspecialchars($s['jenis_surat']) . "' Anda saat ini berstatus: " . htmlspecialchars($s['status']) . ".";
                $icon = "fa-info-circle";
                $iconColor = "#2563eb"; // Biru
            }

            // Simpan ke array $notifs agar dibaca oleh HTML struktur bawah
            $notifs[] = [
                'id_pengajuan' => $s['id_pengajuan'],
                'judul'        => $judul,
                'pesan'        => $pesan,
                'created_at'   => $s['tanggal_pengajuan'],
                'status'       => $s['status'],
                'icon'         => $icon,
                'icon_color'   => $iconColor
            ];
        }
    }
} catch (PDOException $e) {
    die("Gagal mengambil data notifikasi: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi - SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .notif-item {
            transition: transform 0.2s;
            cursor: pointer;
        }

        .notif-item:hover {
            transform: translateX(5px);
            background-color: #f8f9fa !important;
        }

        .unread {
            border-left: 4px solid #00a2ed !important;
            background-color: #f0faff !important;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar.php'; ?>

            <div class="content">
                <div class="notif-header" style="margin-bottom: 25px;">
                    <h4 style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-bell" style="color: #00a2ed;"></i> Notifikasi
                    </h4>
                    <p style="color: #666; font-size: 0.9rem;">Informasi terbaru mengenai status pengajuan surat Anda</p>
                </div>

                <div class="notif-main-container" style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <?php if (count($notifs) > 0): ?>
                        <?php foreach ($notifs as $n): ?>
                            <div class="notif-item <?= strtolower($n['status']) === 'menunggu' ? 'unread' : '' ?>"
                                onclick="window.location.href='detail_pengajuan.php?id=<?= $n['id_pengajuan'] ?>'"
                                style="background: white; border: 1px solid #eee; padding: 15px; border-radius: 10px; margin-bottom: 12px; display: flex; align-items: center; gap: 15px;">

                                <div style="background: #f4f7fe; width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: <?= $n['icon_color'] ?? '#2563eb' ?>; font-size: 1.2rem;">
                                    <i class="fas <?= $n['icon'] ?? 'fa-bell' ?>"></i>
                                </div>

                                <div class="notif-body" style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <h5 style="margin: 0; color: #333; font-weight: <?= strtolower($n['status']) === 'menunggu' ? 'bold' : 'normal' ?>;">
                                            <?= htmlspecialchars($n['judul']) ?>
                                        </h5>
                                        <small style="color: #999; font-size: 0.75rem;">
                                            <i class="far fa-clock"></i> <?= date('d M, H:i', strtotime($n['created_at'])) ?>
                                        </small>
                                    </div>
                                    <p style="margin: 5px 0 0; color: #555; font-size: 0.85rem; line-height: 1.4;">
                                        <?= htmlspecialchars($n['pesan']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 60px 20px;">
                            <div style="font-size: 4rem; color: #eee; margin-bottom: 20px;">
                                <i class="fas fa-bell-slash"></i>
                            </div>
                            <h5 style="color: #888;">Belum Ada Notifikasi</h5>
                            <p style="color: #bbb; font-size: 0.9rem;">Kami akan memberi tahu Anda di sini saat ada pembaruan pada surat Anda.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>