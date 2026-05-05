<?php
namespace Classes;

class Surat {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Mengambil semua surat (untuk Admin)
    public function getAllSurat() {
        $query = "SELECT s.*, p.nama as nama_pemohon, j.nama_surat 
                  FROM surat s 
                  JOIN pengguna p ON s.id_pemohon = p.id 
                  JOIN jenis_surat j ON s.id_jenis_surat = j.id 
                  ORDER BY s.dibuat_pada DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Mengambil detail 1 surat (untuk modal/detail view)
    public function getDetailSurat($id) {
        $query = "SELECT s.*, p.nama, d.nomor_induk, j.nama_surat 
                  FROM surat s 
                  JOIN pengguna p ON s.id_pemohon = p.id 
                  JOIN detail_pengguna d ON p.id = d.id_pengguna
                  JOIN jenis_surat j ON s.id_jenis_surat = j.id 
                  WHERE s.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}