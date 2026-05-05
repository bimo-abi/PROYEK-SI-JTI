<?php
namespace Classes;

trait UploadTrait {
    public function uploadImage($file, $destination) {
        $fileName = time() . '_' . $file['name'];
        if (move_uploaded_file($file['tmp_name'], $destination . $fileName)) {
            return $fileName;
        }
        throw new \Exception("Gagal mengunggah gambar.");
    }
}