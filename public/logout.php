<?php
require_once '../app/Core/Auth.php';

$auth = new Auth();
$auth->logout(); // Ini akan menjalankan session_destroy dan redirect ke login.php