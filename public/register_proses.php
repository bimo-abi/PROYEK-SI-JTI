<?php
require_once '../config/Database.php';
require_once '../app/Models/Mahasiswa.php';

$db = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // validasi sederhana
        if ($_POST['password'] !== $_POST['confirm_password']) {
            throw new Exception("Konfirmasi password tidak cocok!");
        }

        // trigger validasi di class Mahasiswa
        $mhs = new Mahasiswa($db, $_POST['nomor_induk']);

        $data = [
            'nama' => $_POST['nama'],
            'email' => $_POST['email'],
            'password' => $_POST['password'], // nanti bisa lo hash
            'peran' => 'mahasiswa',
            'nomor_induk' => $_POST['nomor_induk'],
            'id_prodi' => $_POST['id_prodi']
        ];

        if ($mhs->save($data)) {
            echo "<script>alert('Registrasi Berhasil!'); window.location='login.php';</script>";
        } else {
            throw new Exception("Gagal menyimpan data.");
        }

    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi SI-JTI</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma;
        }

        body {
            background: url('/surat-digital/resources/assets/gedung.png') center/cover no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            color: #fff;
        }

        .overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        .container {
            display: flex;
            max-width: 1100px;
            margin: auto;
            gap: 50px;
            padding: 30px;
        }

        .left-content h1 {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .right-content {
            width: 420px;
        }

        .form-card {
            background: #fff;
            color: #333;
            padding: 30px;
            border-radius: 16px;
        }

        form input,
        form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        form button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #1a9cff;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }

        form button:hover {
            background: #1084df;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: #fff;
            color: #333;
            padding: 25px;
            border-radius: 10px;
            width: 350px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <div class="overlay"></div>

    <div class="container">
        <div class="left-content">
            <h1>SI-JTI</h1>
            <p>Buat akun dan mulai petualangan digital lo.</p>
        </div>

        <div class="right-content">
            <div class="form-card">
                <h2>Registrasi</h2>

                <form method="POST" id="formRegister">

                    <label>Program Studi</label>
                    <select name="id_prodi" required>
                        <option value="1">Teknik Informatika</option>
                        <option value="2">Manajemen Informatika</option>
                        <option value="3">Teknik Komputer</option>
                    </select>

                    <label>Email</label>
                    <input type="email" name="email" required>

                    <label>NIM</label>
                    <input type="text" name="nomor_induk" required>

                    <label>Nama</label>
                    <input type="text" name="nama" required>

                    <label>Password</label>
                    <input type="password" name="password" required>

                    <label>Konfirmasi Password</label>
                    <input type="password" name="confirm_password" required>

                    <button type="submit">Registrasi</button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL -->
    <div id="modalKonfirmasi" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Data</h3>
            <div id="previewData"></div>

            <div class="modal-actions">
                <button id="btnBatal">Batal</button>
                <button id="btnLanjut">Konfirmasi</button>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('formRegister');
        const modal = document.getElementById('modalKonfirmasi');
        const preview = document.getElementById('previewData');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const data = new FormData(form);

            preview.innerHTML = `
        <p>Email: ${data.get('email')}</p>
        <p>NIM: ${data.get('nomor_induk')}</p>
        <p>Nama: ${data.get('nama')}</p>
    `;

            modal.style.display = 'flex';
        });

        document.getElementById('btnBatal').onclick = () => modal.style.display = 'none';

        document.getElementById('btnLanjut').onclick = () => {
            modal.style.display = 'none';
            form.submit();
        };
    </script>

</body>

</html>