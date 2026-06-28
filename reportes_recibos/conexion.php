<?php
// Database configuration
$host = "localhost";
$user = "root";
$pass = "Edenorte2299";
$db   = "reportes";

// Create connection
$conexion = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

// Set charset to utf8mb4 for better international character support
$conexion->set_charset("utf8mb4");

// Upload configuration
// User requested: Q:\datos_sistema_lectura\images
// We use a relative path for the placeholder, but the user can change this to an absolute path in their environment.
define('UPLOAD_DIR', __DIR__ . '/assets/evidencias/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

/**
 * Helper function to execute prepared statements for SELECT queries
 */
function query($sql, $params = [], $types = "") {
    global $conexion;
    $stmt = $conexion->prepare($sql);
    if ($params) {
        if ($types == "") {
            $types = str_repeat("s", count($params));
        }
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Helper function to execute prepared statements for INSERT, UPDATE, DELETE
 */
function execute($sql, $params = [], $types = "") {
    global $conexion;
    $stmt = $conexion->prepare($sql);
    if ($params) {
        if ($types == "") {
            $types = str_repeat("s", count($params));
        }
        $stmt->bind_param($types, ...$params);
    }
    return $stmt->execute();
}
?>