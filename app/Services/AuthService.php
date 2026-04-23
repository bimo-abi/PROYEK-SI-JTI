<?php

class AuthService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($email, $password) {
        try {
            $query = "SELECT id, nama, kata_sandi, peran FROM pengguna WHERE email = :email AND is_aktif = 1 LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifikasi Password (asumsi password di database sudah di-hash)
            if ($user && password_verify($password, $user['kata_sandi'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama']   = $user['nama'];
                $_SESSION['peran']  = $user['peran'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Login error: " . $e->getMessage());
        }
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public static function cekAkses($peranYangDibutuhkan) {
        if (!isset($_SESSION['peran']) || $_SESSION['peran'] !== $peranYangDibutuhkan) {
            header("Location: login.php?error=unauthorized");
            exit();
        }
    }
}