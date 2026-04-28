<?php
require_once '../../config/Database.php';
require_once '../../app/Models/Surat.php';
require_once '../../app/Core/Auth.php';

Auth::check();
Auth::role('mahasiswa');

$db = (new Database())->getConnection();
$suratModel = new Surat($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // 1. Validasi Input Teks
        if (empty($_POST['keperluan'])) {
            throw new Exception("Keperluan tidak boleh kosong!");
        }

        // 2. Logika Upload File (Encapsulation logic)
        $targetDir = "../../uploads/";
        $fileName  = time() . "_" . basename($_FILES["bukti"]["name"]);
        $targetFile = $targetDir . $fileName;
        $fileType  = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validasi format harus PDF
        if ($fileType != "pdf") {
            throw new Exception("Hanya file PDF yang diperbolehkan!");
        }

        // Validasi ukuran (misal maks 2MB)
        if ($_FILES["bukti"]["size"] > 2000000) {
            throw new Exception("Ukuran file terlalu besar! Maksimal 2MB.");
        }

        // Proses Pindah File
        if (move_uploaded_file($_FILES["bukti"]["tmp_name"], $targetFile)) {
            // 3. Simpan ke Database
            $data = [
                'id_pemohon' => $_SESSION['user_id'],
                'jenis_surat' => $_POST['jenis_surat'],
                'keperluan'   => $_POST['keperluan'],
                'bukti_pendukung' => $fileName
            ];

            if ($suratModel->save($data)) {
                echo "Surat berhasil diajukan! Admin akan segera memverifikasi.";
            }
        } else {
            throw new Exception("Gagal mengunggah file.");
        }

    } catch (Exception $e) {
        // Exception Handling: Menangkap semua error dan menampilkannya
        echo "Error: " . $e->getMessage();
    }
}