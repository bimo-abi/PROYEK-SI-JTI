<?php
require_once 'Pengguna.php';
require_once __DIR__ . '/../Interfaces/VerifikasiInterface.php';

class Admin extends Pengguna implements VerifikasiInterface {
    
    // Mengambil semua surat untuk dashboard admin
    public function getAllSurat() {
        $query = "SELECT s.*, p.nama as nama_mahasiswa, p.nomor_induk as nim 
                  FROM surat s 
                  JOIN pengguna p ON s.id_pemohon = p.id 
                  ORDER BY s.dibuat_pada DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Implementasi dari VerifikasiInterface
    public function verifikasi($id_surat, $status, $catatan = null) {
        $query = "UPDATE surat SET status = ?, catatan_penolakan = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$status, $catatan, $id_surat]);
    }
}