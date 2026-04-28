<?php
class Controller {
    // Membantu untuk memanggil view (nanti di frontend)
    public function view($view, $data = []) {
        if (file_exists("../app/Views/" . $view . ".php")) {
            require_once "../app/Views/" . $view . ".php";
        } else {
            die("View tidak ditemukan");
        }
    }
}