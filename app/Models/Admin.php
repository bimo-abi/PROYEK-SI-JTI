<?php
require_once '../../app/Core/Auth.php';
Auth::check(); // Tendang jika belum login
Auth::role('admin'); // Tendang jika bukan admin

echo "Halo Admin, " . $_SESSION['nama'];

class Admin extends Pengguna implements VerifikasiInterface {
    
    public function verifikasi($id_surat, $status, $catatan) {
        $query = "UPDATE surat SET status = ?, catatan_penolakan = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$status, $catatan, $id_surat]);
    }
}