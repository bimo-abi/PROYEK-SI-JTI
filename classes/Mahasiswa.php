<?php
namespace Classes;

class Mahasiswa extends User {
    private $nim;
    private $prodi;

    public function __construct($db, $id = null) {
        parent::__construct($db);
        if ($id) {
            $this->loadProfile($id);
        }
    }

    private function loadProfile($id) {
        $query = "SELECT p.*, d.*, pr.nama_prodi 
                  FROM pengguna p 
                  LEFT JOIN detail_pengguna d ON p.id = d.id_pengguna 
                  LEFT JOIN prodi pr ON d.id_prodi = pr.id
                  WHERE p.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data) {
            $this->id = $data['id'];
            $this->nama = $data['nama'];
            $this->email = $data['email'];
            $this->hashed_password = $data['kata_sandi'];
            $this->nim = $data['nomor_induk'];
            $this->prodi = $data['nama_prodi'];
        }
    }

    public function getStatusSurat() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'tertunda' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as sukses,
                    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as gagal
                  FROM surat WHERE id_pemohon = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$this->id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getRiwayatSurat() {
        $query = "SELECT s.*, j.nama_surat 
                  FROM surat s 
                  JOIN jenis_surat j ON s.id_jenis_surat = j.id 
                  WHERE s.id_pemohon = ? 
                  ORDER BY s.dibuat_pada DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function dashboard() { return "Dashboard Mahasiswa"; }
}