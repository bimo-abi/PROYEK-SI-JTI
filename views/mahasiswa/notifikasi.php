<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();

// Pastikan kita menggunakan variabel $notifs (sesuai yang dipanggil di HTML kamu)
$notifs = [];

try {
    // Gunakan NIM dari session yang sudah kita set di proses login tadi
    $nim = $_SESSION['nim'] ?? null;

    if ($nim) {
        $query = "SELECT * FROM notifikasi WHERE nim = ? ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute([$nim]);
        $notifs_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil surat terverifikasi atau ditolak
        $queryUnread = "SELECT id_pengajuan, jenis_surat, tanggal_pengajuan, status, is_read FROM pengajuan_surat WHERE nim = ? AND status IN ('disetujui', 'ditolak') ORDER BY tanggal_pengajuan DESC LIMIT 10";
        $stmtUnread = $db->prepare($queryUnread);
        $stmtUnread->execute([$nim]);
        $unread_surats = $stmtUnread->fetchAll(PDO::FETCH_ASSOC);

        $notifs = [];
        foreach ($unread_surats as $s) {
            $is_disetujui = $s['status'] == 'disetujui';
            $notifs[] = [
                'judul' => $is_disetujui ? 'Surat Disetujui' : 'Surat Ditolak',
                'pesan' => "Surat pengajuan " . $s['jenis_surat'] . " Anda telah " . ($is_disetujui ? 'disetujui' : 'ditolak') . ". Klik untuk melihat detail.",
                'created_at' => $s['tanggal_pengajuan'],
                'is_read' => $s['is_read'],
                'is_unread_surat' => true,
                'id_pengajuan' => $s['id_pengajuan']
            ];
        }
        foreach ($notifs_db as $n) {
            $n['is_unread_surat'] = false;
            $notifs[] = $n;
        }
    }
} catch (Exception $e) {
    // Opsional: log error jika gagal
}

$current_page = 'notifikasi';
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
                            <div class="notif-item <?= $n['is_read'] == 0 ? 'unread' : '' ?>"
                                <?= (isset($n['is_unread_surat']) && $n['is_unread_surat']) ? 'onclick="window.location.href=\'detail_pengajuan.php?id='.$n['id_pengajuan'].'\'"' : '' ?>
                                style="background: white; border: 1px solid #eee; padding: 15px; border-radius: 10px; margin-bottom: 12px; display: flex; align-items: center; gap: 15px;">

                                <div class="notif-icon" style="background: <?= $n['is_read'] == 0 ? '#e3f2fd' : '#f5f5f5' ?>; color: <?= $n['is_read'] == 0 ? '#00a2ed' : '#9e9e9e' ?>; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 1.2rem;">
                                    <i class="fas <?= $n['is_read'] == 0 ? 'fa-envelope' : 'fa-envelope-open' ?>"></i>
                                </div>

                                <div class="notif-body" style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <h5 style="margin: 0; color: #333; font-weight: <?= $n['is_read'] == 0 ? 'bold' : 'normal' ?>;">
                                            <?php if (isset($n['is_unread_surat']) && $n['is_unread_surat']): ?>
                                                <a href="detail_pengajuan.php?id=<?= $n['id_pengajuan'] ?>" style="text-decoration: none; color: inherit;">
                                                    <?= htmlspecialchars($n['judul']) ?>
                                                </a>
                                            <?php else: ?>
                                                <?= htmlspecialchars($n['judul']) ?>
                                            <?php endif; ?>
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