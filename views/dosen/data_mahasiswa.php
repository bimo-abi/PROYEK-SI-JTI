<?php
require_once '../../autoload.php';
session_start();

use Config\Database;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: ../auth/login.php");
    exit();
}

$db = (new Database())->getConnection();
$current_page = 'data_mahasiswa';

// Ambil data golongan untuk filter
$golongan_list = $db->query("SELECT * FROM golongan")->fetchAll(PDO::FETCH_ASSOC);

// Ambil data pengajuan yang HANYA sudah terverifikasi/disetujui
$query = "SELECT p.*, u.nama, pr.nama_prodi, g.nama_golongan, p.tanggal_pengajuan as tgl_raw
          FROM pengajuan_surat p
          JOIN detail_pengguna d ON p.nim = d.nomor_induk
          JOIN pengguna u ON d.id_pengguna = u.id
          LEFT JOIN prodi pr ON d.id_prodi = pr.id
          LEFT JOIN golongan g ON d.id_golongan = g.id
          WHERE p.status IN ('disetujui', 'terverifikasi')
          ORDER BY p.tanggal_pengajuan DESC";

$stmt = $db->query($query);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa - Dosen SI-JTI</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .filter-section {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-size: 0.8rem;
            font-weight: bold;
            color: #666;
        }

        .filter-group select,
        .filter-group input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .btn-today {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            background: #00a2ed;
            color: white;
            cursor: pointer;
            font-size: 0.9rem;
            align-self: flex-end;
            transition: 0.3s;
        }

        .btn-today.active {
            background: #007bb5;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-detail {
            background: #e3f2fd;
            color: #00a2ed;
            border: 1px solid #00a2ed;
        }

        .btn-detail:hover {
            background: #00a2ed;
            color: white;
        }

        .hidden-row {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../layouts/sidebar_dosen.php'; ?>
        <div class="main-container">
            <?php include '../layouts/topbar_dosen.php'; ?>

            <div class="content">
                <div class="card-container" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h4><i class="fas fa-users" style="color: #00a2ed;"></i> Data Mahasiswa Terverifikasi</h4>
                    </div>

                    <!-- Filter & Search Section -->
                    <div class="filter-section">
                        <div class="filter-group" style="flex: 1; min-width: 200px;">
                            <label>Pencarian</label>
                            <input type="text" id="searchInput" placeholder="Cari nama atau NIM..."
                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        </div>
                        <div class="filter-group">
                            <label>Filter Golongan</label>
                            <select id="golonganFilter">
                                <option value="">Semua Golongan</option>
                                <?php foreach ($golongan_list as $g): ?>
                                    <option value="<?= htmlspecialchars($g['nama_golongan']) ?>"><?= htmlspecialchars($g['nama_golongan']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Filter Tanggal</label>
                            <input type="date" id="dateFilter" style="padding: 7px 12px;">
                        </div>
                    </div>

                    <table style="width: 100%; border-collapse: collapse;" id="studentTable">
                        <thead>
                            <tr style="background: #f8f9fa; text-align: left; border-bottom: 2px solid #eee;">
                                <th style="padding: 12px;">No</th>
                                <th style="padding: 12px;">Nama</th>
                                <th style="padding: 12px;">NIM</th>
                                <th style="padding: 12px;">Gol</th>
                                <th style="padding: 12px;">Prodi</th>
                                <th style="padding: 12px; text-align: center;">Tgl Pengajuan</th>
                                <th style="padding: 12px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($riwayat) > 0): ?>
                                <?php $no = 1;
                                foreach ($riwayat as $row):
                                    $tgl_formatted = date('Y-m-d', strtotime($row['tgl_raw']));
                                ?>
                                    <tr class="student-row"
                                        data-nama="<?= strtolower(htmlspecialchars($row['nama'])) ?>"
                                        data-nim="<?= htmlspecialchars($row['nim']) ?>"
                                        data-golongan="<?= htmlspecialchars($row['nama_golongan']) ?>"
                                        data-tanggal="<?= $tgl_formatted ?>"
                                        style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 12px;"><?= $no++ ?></td>
                                        <td style="padding: 12px; font-weight: 500;"><?= htmlspecialchars($row['nama'] ?? '') ?></td>
                                        <td style="padding: 12px;"><?= htmlspecialchars($row['nim'] ?? '') ?></td>
                                        <td style="padding: 12px;"><?= htmlspecialchars($row['nama_golongan'] ?? '-') ?></td>
                                        <td style="padding: 12px;"><?= htmlspecialchars($row['nama_prodi'] ?? '-') ?></td>
                                        <td style="padding: 12px; text-align: center;"><?= date('d/m/Y', strtotime($row['tgl_raw'])) ?></td>
                                        <td style="padding: 12px; text-align: center; display: flex; gap: 5px; justify-content: center;">
                                            <a href="detail_mahasiswa.php?id=<?= $row['id_pengajuan'] ?>" class="action-btn btn-detail">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            <?php if (!empty($row['file_path'])): ?>
                                                <a href="../../assets/uploads/pdf/<?= $row['file_path'] ?>" target="_blank" class="action-btn" style="background: #fff0f0; color: #ff4757; border: 1px solid #ff4757;">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="no-data">
                                    <td colspan="7" style="text-align: center; padding: 20px; color: #999;">Belum ada data mahasiswa terverifikasi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const golonganFilter = document.getElementById('golonganFilter');
        const dateFilter = document.getElementById('dateFilter');
        const tableRows = document.querySelectorAll('.student-row');
        const noDataRow = document.querySelector('.no-data');

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedGolongan = golonganFilter.value;
            const selectedDate = dateFilter.value;
            let visibleCount = 0;

            tableRows.forEach(row => {
                const nama = row.getAttribute('data-nama');
                const nim = row.getAttribute('data-nim');
                const golongan = row.getAttribute('data-golongan');
                const tanggal = row.getAttribute('data-tanggal');

                const matchesSearch = nama.includes(searchTerm) || nim.includes(searchTerm);
                const matchesGolongan = selectedGolongan === "" || golongan === selectedGolongan;
                const matchesDate = selectedDate === "" || tanggal === selectedDate;

                if (matchesSearch && matchesGolongan && matchesDate) {
                    row.classList.remove('hidden-row');
                    visibleCount++;
                } else {
                    row.classList.add('hidden-row');
                }
            });

            if (noDataRow) {
                noDataRow.style.display = (visibleCount === 0) ? 'table-row' : 'none';
            }
        }

        searchInput.addEventListener('input', applyFilters);
        golonganFilter.addEventListener('change', applyFilters);
        dateFilter.addEventListener('change', applyFilters);
        if (searchInput.value !== "") {
            applyFilters();
        }
    </script>
</body>

</html>