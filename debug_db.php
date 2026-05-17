<?php
require_once 'autoload.php';
use Config\Database;

try {
    $db = (new Database())->getConnection();
    $stmt = $db->query("SHOW COLUMNS FROM pengajuan_surat");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($columns, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}
