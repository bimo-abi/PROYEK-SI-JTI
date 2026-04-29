CREATE DATABASE IF NOT EXISTS si_jti;
USE si_jti;

-- Tabel Referensi
CREATE TABLE prodi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_prodi VARCHAR(100) NOT NULL
);

CREATE TABLE golongan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_golongan VARCHAR(10) NOT NULL
);

-- Tabel Pengguna (Simpan Mahasiswa, Dosen, Admin di sini)
CREATE TABLE pengguna (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    kata_sandi VARCHAR(255) NOT NULL,
    peran ENUM('admin', 'dosen', 'mahasiswa') NOT NULL,
    nomor_induk VARCHAR(50) UNIQUE, -- NIM atau NIP
    id_prodi INT,
    id_golongan INT,
    FOREIGN KEY (id_prodi) REFERENCES prodi(id),
    FOREIGN KEY (id_golongan) REFERENCES golongan(id)
);

-- Tabel Surat
CREATE TABLE surat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_pemohon INT NOT NULL,
    jenis_surat ENUM('Sakit', 'Kegiatan Kampus', 'Kegiatan Luar Kampus') NOT NULL,
    keperluan TEXT NOT NULL,
    bukti_pendukung VARCHAR(255),
    status ENUM('tertunda', 'diproses', 'disetujui', 'ditolak') DEFAULT 'tertunda',
    catatan_penolakan TEXT,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pemohon) REFERENCES pengguna(id)
);

-- Isi Data Awal
INSERT INTO prodi (nama_prodi) VALUES 
('Teknik Informatika'), ('Teknik Komputer'), ('Manajemen Informatika'), 
('Teknologi Rekayasa Komputer'), ('Teknologi Rekayasa Perangkat Lunak');

INSERT INTO golongan (nama_golongan) VALUES ('A'), ('B'), ('C'), ('D'), ('E'), ('F'), ('G');