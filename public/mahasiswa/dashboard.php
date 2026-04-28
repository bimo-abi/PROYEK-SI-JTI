<?php
require_once '../../app/Core/Auth.php';
Auth::check();
Auth::role('mahasiswa');

echo "Halo Mahasiswa, " . $_SESSION['nama'];