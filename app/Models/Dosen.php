<?php
require_once 'Pengguna.php';

class Dosen extends Pengguna {
    // Dosen hanya melihat mahasiswa yang suratnya sudah di-ACC
    public function lihatSemuaSurat() {
        $query = "SELECT s.*, p.nama, p.nomor_induk 
                  FROM surat s 
                  JOIN pengguna p ON s.id_pemohon = p.id 
                  WHERE s.status = 'disetujui'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}