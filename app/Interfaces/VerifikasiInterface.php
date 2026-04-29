<?php
interface VerifikasiInterface {
    /**
     * Method untuk memproses status surat
     * * @param int $id_surat - ID dari surat yang akan diverifikasi
     * @param string $status - Status baru (disetujui/ditolak)
     * @param string|null $catatan - Alasan jika surat ditolak
     * @return bool - Mengembalikan true jika berhasil
     */
    public function verifikasi($id_surat, $status, $catatan = null);
}