<?php
require_once __DIR__ . '/Pengguna.php';
require_once __DIR__ . '/../Interfaces/VerifikasiInterface.php';

class Admin extends Pengguna implements VerifikasiInterface {
    
    /**
     * Implementasi fungsi dari VerifikasiInterface
     * Polimorfisme: Admin memiliki cara khusus mengelola surat
     */
    public function verifikasi($id_surat, $status, $catatan) {
        $query = "UPDATE surat SET 
                  status = ?, 
                  catatan_penolakan = ?, 
                  id_admin = ? 
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        // $_SESSION['user_id'] diambil dari admin yang sedang login
        return $stmt->execute([$status, $catatan, $_SESSION['user_id'], $id_surat]);
    }

    /**
     * Mengambil semua pengajuan surat untuk dashboard admin
     */
    public function getAllSurat() {
        $query = "SELECT s.*, p.nama as nama_mahasiswa, p.nomor_induk as nim 
                  FROM surat s 
                  JOIN pengguna p ON s.id_pemohon = p.id 
                  ORDER BY s.dibuat_pada DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}