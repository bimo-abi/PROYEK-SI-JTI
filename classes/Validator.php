<?php
namespace Classes;

class Validator {
    // Validasi NIM: Harus diawali 1 huruf besar dan diikuti 8 angka (Contoh: E41250904)
    public static function validateNIM($nim) {
        return preg_match("/^[A-Z][0-9]{8}$/", $nim);
    }

    // Validasi NIP: Hanya boleh angka dan titik
    public static function validateNIP($nip) {
        return preg_match("/^[0-9.]+$/", $nip);
    }

    // Sanitasi input agar aman dari serangan XSS (Keamanan Dasar)
    public static function sanitize($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}