<?php
require_once '../config/Database.php';
require_once '../app/Models/Mahasiswa.php';

$db = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Inisialisasi Mahasiswa (Ini memicu validasi NIM di constructor/setter)
        $mhs = new Mahasiswa($db, $_POST['nomor_induk']);

        // Data untuk disimpan
        // Di file register_proses.php
        $data = [
            'nama' => $_POST['nama'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'peran' => 'mahasiswa',
            'nomor_induk' => $_POST['nomor_induk'],
            'id_prodi' => $_POST['id_prodi'] // Pastikan ini ada!
        ];

        // Memanggil fungsi save yang ada di Pengguna.php (Inheritance)
        if ($mhs->save($data)) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='login.php';</script>";
        } else {
            throw new Exception("Gagal menyimpan data ke database.");
        }
    } catch (Exception $e) {
        // Menangkap error dari validasi NIM di class Mahasiswa
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
