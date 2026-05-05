<?php
namespace Classes;

class Admin extends User {
    
    public function __construct($db, $id = null) {
        parent::__construct($db);
        if ($id) {
            $this->loadAdminData($id);
        }
    }

    private function loadAdminData($id) {
        $query = "SELECT * FROM pengguna WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            $this->id = $data['id'];
            $this->nama = $data['nama'];
        }
    }

    // Fungsi Utama: Verifikasi Surat
    public function verifikasiSurat($id_surat, $status, $catatan) {
        $query = "UPDATE surat SET 
                    status = ?, 
                    catatan_penolakan = ?, 
                    diverifikasi_oleh = ?, 
                    diverifikasi_pada = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$status, $catatan, $this->id, $id_surat]);
    }

    public function dashboard() {
        return "Dashboard Administrator JTI";
    }
}