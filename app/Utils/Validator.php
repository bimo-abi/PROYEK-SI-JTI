<?php

class Validator {
    // Polimorfisme sederhana: Validasi berdasarkan peran
    public static function validasiNomorInduk($nomor, $peran) {
        if ($peran === 'mahasiswa') {
            // Regex: Huruf di depan, diikuti angka (Contoh: E41250909)
            return preg_match('/^[A-Z][0-9]+$/', $nomor);
        } elseif ($peran === 'dosen') {
            // Regex: Hanya angka dan titik (Contoh: 19800101.200501.1.001)
            return preg_match('/^[0-9.]+$/', $nomor);
        }
        return false;
    }

    public static function bersihkanInput($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}