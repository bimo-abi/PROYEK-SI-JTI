<?php
require_once '../config/Database.php';
require_once '../app/Models/Mahasiswa.php';
require_once '../app/Utils/Validator.php';

session_start();

// Cek apakah yang akses benar mahasiswa
if ($_SESSION['peran'] !== 'mahasiswa') {
    die("Akses ditolak!");
}

$database = new Database();
$db = $database->getConnection();
$mhs = new Mahasiswa($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_jenis = Validator::bersihkanInput($_POST['id_jenis_surat']);
        $keperluan = Validator::bersihkanInput($_POST['keperluan']);

        // Logika Upload File
        $targetDir = "uploads/";
        $fileName = time() . '_' . basename($_FILES["berkas_pdf"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Bagian pengecekan file di surat_proses.php
        if (isset($_FILES['berkas_pdf'])) {
            $fileSize = $_FILES['berkas_pdf']['size'];
            $maxSize = 2 * 1024 * 1024; // Limit 2MB

            // Cek ukuran
            if ($fileSize > $maxSize) {
                throw new Exception("Ukuran file terlalu besar. Maksimal 2MB.");
            }

            // Cek mime type untuk memastikan benar-benar PDF (bukan sekadar ganti ekstensi)
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['berkas_pdf']['tmp_name']);
            if ($mime !== 'application/pdf') {
                throw new Exception("File yang diunggah bukan PDF asli.");
            }
        }

        if ($fileType != "pdf") {
            throw new Exception("Hanya file PDF yang diperbolehkan.");
        }

        if (move_uploaded_file($_FILES["berkas_pdf"]["tmp_name"], $targetFilePath)) {
            // Memanggil method dari Model Mahasiswa
            $data = [
                'id' => $_SESSION['user_id'],
                'jenis' => $id_jenis,
                'keperluan' => $keperluan,
                'file' => $fileName
            ];

            if ($mhs->kirimSurat($data['jenis'], $data['keperluan'], $data['file'])) {
                header("Location: dashboard_mhs.php?status=sukses");
            }
        } else {
            throw new Exception("Gagal mengunggah file.");
        }
    } catch (Exception $e) {
        header("Location: dashboard_mhs.php?status=error&msg=" . $e->getMessage());
    }
}
