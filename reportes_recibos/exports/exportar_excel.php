<?php
include '../auth_check.php';
check_auth("../");
include '../conexion.php';

// For Excel, we can use a simple HTML table with Excel headers for basic functionality without external libraries
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

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename=reportes_' . date('Ymd_His') . '.xls');

// Fix for character encoding in Excel (BOM)
echo "\xEF\xBB\xBF"; 

echo "<html><head><meta http-equiv=\"Content-type\" content=\"text/html;charset=utf-8\" /></head><body>";
echo "<table border='1'>";
echo "<tr>
        <th>ID</th>
        <th>NIC</th>
        <th>Localidad</th>
        <th>Oficina</th>
        <th>Fecha</th>
        <th>Area</th>
        <th>Teléfono</th>
        <th>Mes Reclamado</th>
        <th>Ruta</th>
        <th>Itin.</th>
        <th>Observaciones</th>
        <th>Estado Factura</th>
        <th>Audit</th>
        <th>Creador</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $cell) {
        echo "<td>" . htmlspecialchars($cell ?? '') . "</td>";
    }
    echo "</tr>";
}
echo "</table></body></html>";
?>