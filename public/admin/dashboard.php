<?php
require_once '../../app/Core/Auth.php';
Auth::check(); // Tendang jika belum login
Auth::role('admin'); // Tendang jika bukan admin

echo "Halo Admin, " . $_SESSION['nama'];