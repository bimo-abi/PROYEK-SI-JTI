<?php
require_once __DIR__ . '/Pengguna.php';

class Dosen extends Pengguna {
    private $nip;

    // Polimorfisme: Validasi NIP (Hanya angka dan titik)
    public function setNip($nip) {
        if (!preg_match("/^[0-9.]+$/", $nip)) {
            throw new Exception("Format NIP Salah! Hanya boleh angka dan titik.");
        }
        $this->nip = $nip;
    }

    public function getNip() {
        return $this->nip;
    }
}