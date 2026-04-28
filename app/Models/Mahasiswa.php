<?php
require_once '../../app/Core/Auth.php';
Auth::check();
Auth::role('mahasiswa');

echo "Halo Mahasiswa, " . $_SESSION['nama'];

class Mahasiswa extends Pengguna {
    private $nim;

    // Constructor khusus Mahasiswa
    public function __construct($db, $nim) {
        parent::__construct($db);
        $this->setNim($nim);
    }

    public function setNim($nim) {
        // Validasi NIM: Harus ada huruf di depan (E41...)
        if (!preg_match("/^[A-Z]/", $nim)) {
            throw new Exception("Format NIM Salah! Harus diawali huruf.");
        }
        $this->nim = $nim;
    }

    public function getNim() {
        return $this->nim;
    }
}