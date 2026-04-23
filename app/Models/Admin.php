<?php
require_once __DIR__ . '/../Core/UserAbstract.php';

class Admin extends UserAbstract {

    public function getDashboardData() {
        // Admin melihat semua antrean surat dari semua mahasiswa
        $query = "SELECT s.*, p.nama FROM surat s JOIN pengguna p ON s.id_pemohon = p.id WHERE s.status = 'tertunda'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verifikasiSurat($id_surat, $status, $catatan = null) {
        // Enkapsulasi: Hanya admin yang bisa memanggil fungsi ini
        $query = "UPDATE surat SET status = :status, catatan_penolakan = :catatan, 
                  diverifikasi_oleh = :admin_id, diverifikasi_pada = NOW() 
                  WHERE id = :id_surat";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'status' => $status,
            'catatan' => $catatan,
            'admin_id' => $this->id,
            'id_surat' => $id_surat
        ]);
    }
}