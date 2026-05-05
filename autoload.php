<?php
spl_autoload_register(function ($class) {
    // Mengubah namespace menjadi path file (Contoh: Classes\User -> classes/User.php)
    $path = str_replace('\\', '/', $class) . '.php';
    if (file_exists(__DIR__ . '/' . $path)) {
        require_once __DIR__ . '/' . $path;
    }
});