<?php
require_once 'Pengguna.php';

class Mahasiswa extends Pengguna {
    private $nim;

    public function __construct($db, $nim = null) {
        parent::__construct($db);
        if ($nim !== null) {
            $this->setNim($nim);
        }
    }

    // Encapsulation: NIM harus diawali huruf (Aturan JTI)
    public function setNim($nim) {
        if (!preg_match("/^[a-zA-Z]/", $nim)) {
            throw new Exception("Format NIM tidak valid! Harus diawali dengan huruf (Contoh: E4122).");
        }
        $this->nim = $nim;
    }

    public function getNim() {
        return $this->nim;
    }
}