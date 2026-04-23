<?php

abstract class UserAbstract {
    protected $db;
    protected $id;
    protected $nama;
    protected $peran;

    public function __construct($db) {
        $this->db = $db;
    }

    // Abstract Method: Harus diimplementasikan oleh kelas turunan
    abstract public function getDashboardData();

    // Getter untuk enkapsulasi
    public function getNama() {
        return $this->nama;
    }
}