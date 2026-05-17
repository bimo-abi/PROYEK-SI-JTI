<?php
require_once 'autoload.php';
use Config\Database;

try {
    $db = (new Database())->getConnection();
    $queryNotif = "SELECT p.id_pengajuan, p.jenis_surat, p.tanggal_pengajuan, u.nama as nama_mhs 
                   FROM pengajuan_surat p
                   JOIN detail_pengguna d ON p.nim = d.nomor_induk
                   JOIN pengguna u ON d.id_pengguna = u.id
                   WHERE p.status = 'menunggu' AND p.is_read_dosen = 0
                   ORDER BY p.tanggal_pengajuan DESC LIMIT 5";
    $notifs = $db->query($queryNotif)->fetchAll(PDO::FETCH_ASSOC);
    echo "Query successful. Count: " . count($notifs);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
