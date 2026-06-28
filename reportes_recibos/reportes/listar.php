<?php
include '../auth_check.php';
check_auth("../");
include '../conexion.php';

// Parameters for ServerSide DataTables
$draw = $_GET['draw'] ?? 1;
$start = $_GET['start'] ?? 0;
$length = $_GET['length'] ?? 10;
$searchValue = $_GET['search']['value'] ?? '';

// Filters
$oficina = $_GET['oficina'] ?? '';
$nic = $_GET['nic'] ?? '';
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$estado_factura = $_GET['estado_factura'] ?? '';
$ruta = $_GET['ruta'] ?? '';
$itinerario = $_GET['itinerario'] ?? '';
$area = $_GET['area'] ?? '';
$localidad = $_GET['localidad'] ?? '';

// Base query
$query = "SELECT r.*, u.nombre as creador,
          (SELECT COUNT(*) FROM historial_estados h WHERE h.reporte_id = r.id) as total_historial
          FROM reportes r 
          LEFT JOIN usuarios u ON r.usuario_creador = u.id 
          WHERE r.estado != 'ELIMINADO'";

$params = [];
$types = "";

// Apply filters
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

// Global search
if ($searchValue) {
    $query .= " AND (r.nic LIKE ? OR r.localidad LIKE ? OR r.oficina LIKE ? OR r.area LIKE ?)";
    $params[] = "%$searchValue%";
    $params[] = "%$searchValue%";
    $params[] = "%$searchValue%";
    $params[] = "%$searchValue%";
    $types .= "ssss";
}

// Count total (without limit)
$stmtCount = $conexion->prepare($query);
if ($params) {
    $stmtCount->bind_param($types, ...$params);
}
$stmtCount->execute();
$totalRecords = $stmtCount->get_result()->num_rows;

// Ordering
$columns = ['r.id', 'r.nic', 'r.localidad', 'r.oficina', 'r.fecha', 'r.area', 'r.mes_reclamado', 'r.ruta', 'r.itinerario', 'r.estado_factura', 'r.id', 'r.estado', 'u.nombre'];
$orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
$orderDir = $_GET['order'][0]['dir'] ?? 'DESC';
$orderColumn = $columns[$orderColumnIndex] ?? 'r.id';

$query .= " ORDER BY $orderColumn $orderDir LIMIT ?, ?";
$params[] = (int)$start;
$params[] = (int)$length;
$types .= "ii";

// Fetch data
$stmt = $conexion->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$response = [
    "draw" => intval($draw),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
?>