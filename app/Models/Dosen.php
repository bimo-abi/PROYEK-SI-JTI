<?php
require_once __DIR__ . '/../Core/UserAbstract.php';

class Dosen extends UserAbstract {
    
    /**
     * Implementasi getDashboardData untuk Dosen.
     * Dosen hanya bisa melihat daftar surat yang sudah masuk ke sistem 
     * untuk memantau kehadiran mahasiswa di kelasnya.
     */
    public function getDashboardData() {
        try {
            $query = "SELECT s.id, p.nama as nama_mahasiswa, j.nama_surat, s.status, s.dibuat_pada 
                      FROM surat s 
                      JOIN pengguna p ON s.id_pemohon = p.id 
                      JOIN jenis_surat j ON s.id_jenis_surat = j.id 
                      ORDER BY s.dibuat_pada DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Gagal mengambil data untuk dosen: " . $e->getMessage());
        }
    }

    // Dosen mungkin butuh melihat detail surat tertentu
    public function lihatDetailSurat($id_surat) {
        $query = "SELECT s.*, p.nama, d.nomor_induk as nim 
                  FROM surat s 
                  JOIN pengguna p ON s.id_pemohon = p.id 
                  JOIN detail_pengguna d ON p.id = d.id_pengguna 
                  WHERE s.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id_surat]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}