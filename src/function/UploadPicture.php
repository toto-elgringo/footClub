<?php

namespace src\function;

class UploadPicture {
    public static function upload(array $file, string $prefix = 'player_'): array {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Fichier invalide ou upload échoué.'];
        }
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg');
        $name = $prefix . bin2hex(random_bytes(8)) . '.' . $ext;

        $destDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($destDir)) {
            @mkdir($destDir, 0775, true);
        }
        $dest = $destDir . $name;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'error' => 'Impossible de déplacer le fichier.'];
        }
        return ['success' => true, 'filename' => $name];
    }
}
