<?php
require_once __DIR__ . '/../Core/UserAbstract.php';

class Mahasiswa extends UserAbstract {
    
    public function getDashboardData() {
        // Logika mengambil riwayat surat milik mahasiswa tersebut
        $query = "SELECT * FROM surat WHERE id_pemohon = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function kirimSurat($id_jenis, $keperluan, $file) {
        // Logika upload dan simpan ke database
        try {
            $query = "INSERT INTO surat (id_pemohon, id_jenis_surat, keperluan, berkas_pdf) 
                      VALUES (:id, :jenis, :keperluan, :file)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                'id' => $this->id,
                'jenis' => $id_jenis,
                'keperluan' => $keperluan,
                'file' => $file
            ]);
        } catch (PDOException $e) {
            throw new Exception("Gagal mengirim surat: " . $e->getMessage());
        }
    }
}