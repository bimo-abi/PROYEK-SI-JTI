<?php
require_once 'autoload.php';

use Config\Database;

try {
    $db = (new Database())->getConnection();
    
    // Add is_read to pengajuan_surat
    try {
        $db->exec("ALTER TABLE pengajuan_surat ADD COLUMN is_read TINYINT(1) DEFAULT 0");
        echo "Successfully added 'is_read' to 'pengajuan_surat'.\n";
    } catch (PDOException $e) {
        if ($e->getCode() == '42S21') { // 42S21 is Duplicate column name
            echo "'is_read' already exists in 'pengajuan_surat'.\n";
        } else {
            echo "Error adding 'is_read' to 'pengajuan_surat': " . $e->getMessage() . "\n";
        }
    }

    // Add is_read_dosen to pengajuan_surat
    try {
        $db->exec("ALTER TABLE pengajuan_surat ADD COLUMN is_read_dosen TINYINT(1) DEFAULT 0");
        echo "Successfully added 'is_read_dosen' to 'pengajuan_surat'.\n";
    } catch (PDOException $e) {
        if ($e->getCode() == '42S21') {
            echo "'is_read_dosen' already exists in 'pengajuan_surat'.\n";
        } else {
            echo "Error adding 'is_read_dosen' to 'pengajuan_surat': " . $e->getMessage() . "\n";
        }
    }

    // Add is_read to notifikasi
    try {
        $db->exec("ALTER TABLE notifikasi ADD COLUMN is_read TINYINT(1) DEFAULT 0");
        echo "Successfully added 'is_read' to 'notifikasi'.\n";
    } catch (PDOException $e) {
        if ($e->getCode() == '42S21') {
            echo "'is_read' already exists in 'notifikasi'.\n";
        } else {
            echo "Error adding 'is_read' to 'notifikasi': " . $e->getMessage() . "\n";
        }
    }

} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
