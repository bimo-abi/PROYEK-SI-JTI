<?php
class CustomException extends Exception {
    
    // Kita bisa menambahkan method khusus di sini jika perlu
    // Untuk saat ini, kita warisi fungsionalitas utama dari Exception PHP
    
    public function errorMessage() {
        // Mengembalikan pesan error dengan format yang lebih rapi
        return "<b>[SI-JTI Error]:</b> " . $this->getMessage();
    }
}