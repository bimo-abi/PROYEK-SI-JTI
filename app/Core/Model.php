<?php
interface Authenticatable {
    public function login($email, $password);
}

abstract class Model {
    protected $db;
    protected $table;

    public function __construct($db) {
        $this->db = $db;
    }

    // Destructor untuk membersihkan koneksi jika diperlukan
    public function __destruct() {
        $this->db = null;
    }
}