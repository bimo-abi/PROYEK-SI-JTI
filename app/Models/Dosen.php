<?php
require_once __DIR__ . '/Pengguna.php';

class Dosen extends Pengguna {
    /**
     * Dosen hanya punya fungsi untuk melihat (View Only)
     * Tidak punya akses ke fungsi verifikasi()
     */
    public function lihatSemuaSurat() {
        $query = "SELECT s.*, p.nama, p.nomor_induk 
                  FROM surat s 
                  JOIN pengguna p ON s.id_pemohon = p.id 
                  WHERE s.status = 'disetujui'"; // Dosen mungkin hanya perlu lihat yang sudah fix
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}