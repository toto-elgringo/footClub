<?php

namespace Helper;

class UploadPicture {
    private const UPLOAD_DIR = __DIR__ . '/../../public/uploads/';

    public static function upload(array $file, string $prefix): array {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Fichier invalide ou upload échoué.'];
        }
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg');
        $name = $prefix . bin2hex(random_bytes(8)) . '.' . $ext; // genere 8 octets aleatoire pour donner un nom unique a l'image

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0775, true);
        }
        $dest = self::UPLOAD_DIR . $name;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'error' => 'Impossible de déplacer le fichier.'];
        }
        return ['success' => true, 'filename' => $name];
    }

    public static function delete(string $filename): bool {
        $filePath = self::UPLOAD_DIR . $filename;
        
        // Vérifie si le fichier existe et est bien dans le dossier uploads pour des raisons de sécurité
        if (file_exists($filePath) && str_starts_with(realpath($filePath), realpath(self::UPLOAD_DIR))) {
            return unlink($filePath);
        }
        return false;
    }
}
