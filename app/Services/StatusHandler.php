<?php
interface StatusHandler {
    public function handle($suratId, $adminId);
}

class ApproveSurat implements StatusHandler {
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function handle($suratId, $adminId) {
        $query = "UPDATE surat SET status = 'disetujui', diverifikasi_oleh = :admin WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['admin' => $adminId, 'id' => $suratId]);
    }
}

class RejectSurat implements StatusHandler {
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function handle($suratId, $adminId) {
        // Logika untuk menolak surat
        $query = "UPDATE surat SET status = 'ditolak', diverifikasi_oleh = :admin WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['admin' => $adminId, 'id' => $suratId]);
    }
}