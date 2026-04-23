<?php

class Surat {
    private $db;
    
    // Properti surat
    public $id;
    public $id_pemohon;
    public $id_jenis_surat;
    public $nomor_surat;
    public $keperluan;
    public $status;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Mengambil daftar semua jenis surat (Sakit, Izin Kampus, dll)
     * Digunakan untuk mengisi dropdown di form mahasiswa
     */
    public function getJenisSurat() {
        $query = "SELECT * FROM jenis_surat";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Static Method untuk memformat status agar lebih rapi saat ditampilkan di Frontend
     */
    public static function formatLabelStatus($status) {
        switch ($status) {
            case 'disetujui': return "<span class='badge-success'>Disetujui</span>";
            case 'ditolak': return "<span class='badge-danger'>Ditolak</span>";
            case 'tertunda': return "<span class='badge-warning'>Menunggu</span>";
            default: return "<span class='badge-secondary'>$status</span>";
        }
    }
}