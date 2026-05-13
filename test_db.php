<?php
require_once 'autoload.php';
use Config\Database;
$db = (new Database())->getConnection();

// Cek kolom di tabel pengajuan_surat
$stmt = $db->query("DESCRIBE pengajuan_surat");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "pengajuan_surat:\n";
foreach($cols as $c) {
    echo $c['Field'] . " | " . $c['Type'] . "\n";
}

echo "\nnotifikasi:\n";
$stmt2 = $db->query("DESCRIBE notifikasi");
$cols2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
foreach($cols2 as $c) {
    echo $c['Field'] . " | " . $c['Type'] . "\n";
}
