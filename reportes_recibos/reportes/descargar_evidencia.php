<?php
include '../auth_check.php';
check_auth("../");
include '../conexion.php';

$file = $_GET['file'] ?? '';

if (!$file) {
    die("Archivo no especificado");
}

// Security: Prevent directory traversal
$file = basename($file);
$filePath = UPLOAD_DIR . $file;

if (!file_exists($filePath)) {
    die("El archivo no existe físicamente en el servidor: " . $filePath);
}

// Detect Mime Type with fallback
$mimeType = 'application/octet-stream';
if (function_exists('finfo_open')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath);
    finfo_close($finfo);
} else {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimes = [
        'pdf'  => 'application/pdf',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg'
    ];
    $mimeType = $mimes[$ext] ?? 'application/octet-stream';
}

header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . $file . '"');
header('Content-Length: ' . filesize($filePath));

readfile($filePath);
exit();
?>