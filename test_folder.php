<?php
$folder = 'assets/uploads/';
if (is_writable($folder)) {
    echo "Sip! Folder sudah bisa digunakan untuk simpan file.";
} else {
    echo "Waduh! Folder belum punya izin tulis (Permission Denied).";
}
?>