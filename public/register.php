<!DOCTYPE html>
<html lang="id">
<head>
    <title>Daftar Akun Mahasiswa - SI-JTI</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f4f4; margin: 0; }
        .reg-box { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 350px; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; font-weight: bold; }
        .login-link { display: block; text-align: center; margin-top: 15px; font-size: 0.9em; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="reg-box">
        <h3>Daftar Akun Mahasiswa</h3>
        <form action="register_proses.php" method="POST">
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
            <input type="email" name="email" placeholder="Email (Contoh: bimo@student.com)" required>
            <input type="text" name="nomor_induk" placeholder="NIM (Contoh: E4125...)" required>
            <input type="password" name="password" placeholder="Password Baru" required>
            
            <label style="font-size: 0.8em; color: #666;">Pilih Program Studi:</label>
            <select name="id_prodi" required>
                <option value="1">Teknik Informatika</option>
                <option value="2">Teknik Komputer</option>
                <option value="3">Manajemen Informatika</option>
                <option value="4">Teknologi Rekayasa Komputer</option>
                <option value="5">Teknologi Rekayasa Perangkat Lunak</option>
            </select>

            <button type="submit">Daftar Sekarang</button>
        </form>
        <a href="login.php" class="login-link">Sudah punya akun? Login di sini</a>
    </div>
</body>
</html>