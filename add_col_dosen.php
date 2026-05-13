<?php
require_once 'autoload.php';
use Config\Database;
$db = (new Database())->getConnection();

try {
    $db->exec("ALTER TABLE pengajuan_surat ADD COLUMN is_read_dosen TINYINT(1) DEFAULT 0");
    echo "Column is_read_dosen added";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
