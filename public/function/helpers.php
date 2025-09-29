<?php
function uploadPicture(array $file, string $prefix = 'file_'): array
{

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return [
            'success' => false,
            'path' => null,
            'error' => 'Aucun fichier téléchargé ou fichier invalide.'
        ];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    
    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    
    if (!array_key_exists($mimeType, $allowedMimeTypes)) {
        return [
            'success' => false,
            'path' => null,
            'error' => 'Type de fichier non autorisé. Types acceptés : ' . implode(', ', array_unique($allowedMimeTypes))
        ];
    }

    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
        return [
            'success' => false,
            'path' => null,
            'error' => 'Impossible de créer le répertoire de destination.'
        ];
    }

    $extension = $allowedMimeTypes[$mimeType];
    $newFileName = $prefix . uniqid('', true) . '.' . $extension;
    $destination = $uploadDir . $newFileName;
    $relativePath = 'uploads/' . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        if (@getimagesize($destination) === false) {
            unlink($destination);
            return [
                'success' => false,
                'path' => null,
                'error' => 'Le fichier téléchargé n\'est pas une image valide.'
            ];
        }
        
        chmod($destination, 0644);
        
        return [
            'success' => true,
            'path' => $relativePath,
            'full_path' => $destination,
            'filename' => $newFileName,
            'error' => null
        ];
    }

    return [
        'success' => false,
        'path' => null,
        'error' => 'Une erreur est survenue lors du déplacement du fichier téléchargé.'
    ];
}
