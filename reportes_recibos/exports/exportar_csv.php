<?php
include '../auth_check.php';
check_auth("../");
include '../conexion.php';

$oficina = $_GET['oficina'] ?? '';
$nic = $_GET['nic'] ?? '';
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$estado_factura = $_GET['estado_factura'] ?? '';
$ruta = $_GET['ruta'] ?? '';
$itinerario = $_GET['itinerario'] ?? '';
$area = $_GET['area'] ?? '';
$localidad = $_GET['localidad'] ?? '';

$query = "SELECT r.id, r.nic, r.localidad, r.oficina, r.fecha, r.area, r.telefono, r.mes_reclamado, r.ruta, r.itinerario, r.observaciones, r.estado_factura, r.estado, u.nombre as creador 
          FROM reportes r 
          LEFT JOIN usuarios u ON r.usuario_creador = u.id 
          WHERE r.estado != 'ELIMINADO'";

$params = [];
$types = "";

if ($oficina) {
    $query .= " AND r.oficina = ?";
    $params[] = $oficina;
    $types .= "s";
}
if ($nic) {
    $query .= " AND r.nic LIKE ?";
    $params[] = "%$nic%";
    $types .= "s";
}
if ($desde) {
    $query .= " AND r.fecha >= ?";
    $params[] = $desde;
    $types .= "s";
}
if ($hasta) {
    $query .= " AND r.fecha <= ?";
    $params[] = $hasta;
    $types .= "s";
}
if ($estado_factura) {
    $query .= " AND r.estado_factura = ?";
    $params[] = $estado_factura;
    $types .= "s";
}
if ($ruta) {
    $query .= " AND r.ruta = ?";
    $params[] = $ruta;
    $types .= "s";
}
if ($itinerario) {
    $query .= " AND r.itinerario = ?";
    $params[] = $itinerario;
    $types .= "s";
}
if ($area) {
    $query .= " AND r.area = ?";
    $params[] = $area;
    $types .= "s";
}
if ($localidad) {
    $query .= " AND r.localidad LIKE ?";
    $params[] = "%$localidad%";
    $types .= "s";
}

$stmt = $conexion->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=reportes_' . date('Ymd_His') . '.csv');

$output = fopen('php://output', 'w');
// Add BOM for Excel UTF-8 compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Column headers
fputcsv($output, ['ID', 'NIC', 'Localidad', 'Oficina', 'Fecha', 'Area', 'Teléfono', 'Mes Reclamado', 'Ruta', 'Itinerario', 'Observaciones', 'Estado Factura', 'Audit', 'Creador']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}
fclose($output);
?>