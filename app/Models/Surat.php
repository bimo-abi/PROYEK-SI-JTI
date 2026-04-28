<?php
require_once __DIR__ . '/../Core/Model.php';

class Surat extends Model {
    protected $table = "surat";

    /**
     * Implementasi save untuk pengajuan surat baru
     */
    public function save($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (id_pemohon, jenis_surat, keperluan, bukti_pendukung, status) 
                  VALUES (?, ?, ?, ?, 'tertunda')";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['id_pemohon'],
            $data['jenis_surat'],
            $data['keperluan'],
            $data['bukti_pendukung']
        ]);
    }

    /**
     * Mengambil daftar surat berdasarkan ID Mahasiswa (Reusability)
     */
    public function getSuratByMahasiswa($id_mhs) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_pemohon = ? ORDER BY dibuat_pada DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_mhs]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}