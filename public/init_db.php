<?php
require_once '../config/Database.php';
require_once '../app/Models/Pengguna.php';

$db = (new Database())->getConnection();
$pengguna = new Pengguna($db);

try {
    // 1. Tambah Admin
    $pengguna->save([
        'nama' => 'Admin JTI',
        'email' => 'admin@jti.com',
        'password' => 'admin123',
        'peran' => 'admin',
        'nomor_induk' => '19800101',
        'id_prodi' => 1
    ]);

    // 2. Tambah Dosen
    $pengguna->save([
        'nama' => 'Dosen Informatika',
        'email' => 'dosen@jti.com',
        'password' => 'dosen123',
        'peran' => 'dosen',
        'nomor_induk' => '19850202',
        'id_prodi' => 1
    ]);

    // 3. Tambah Mahasiswa (Contoh)
    $pengguna->save([
        'nama' => 'Moch Bimo Abi',
        'email' => 'bimo@student.com',
        'password' => 'bimo123',
        'peran' => 'mahasiswa',
        'nomor_induk' => 'E41250909',
        'id_prodi' => 1
    ]);

    echo "Berhasil! Data Admin, Dosen, dan Mahasiswa telah dibuat.";
} catch (Exception $e) {
    echo "Gagal: " . $e->getMessage();
}