<?php
session_start();
require_once __DIR__ . "/conexion.php";

if (!isset($_SESSION['id_usuario'])) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

$rol_usuario     = $_SESSION['rol'];
$id_oficina      = $_SESSION['id_oficina'];

$sql = "SELECT
            l.id_lectura, l.nis, l.nic, l.numero_medidor, l.ruta, l.itinerario,
            l.lectura_actual, l.anomalia_actual, l.oficina, l.fecha_lectura, l.observacion,
            l.anomalia2, u.nombre_completo AS supervisor, l.estado
        FROM lecturas l
        INNER JOIN oficinas o ON l.oficina = o.codigo
        LEFT JOIN usuarios u ON l.supervisor_asignado = u.id_usuario";

$params = [];
$where_clauses = [];

if ($rol_usuario !== 'admin') {
    $where_clauses[] = "o.id_oficina = ?";
    $params[] = $id_oficina;
}

if (!empty($_GET['filtro_nis'])) {
    $where_clauses[] = "l.nis LIKE ?";
    $params[] = '%' . $_GET['filtro_nis'] . '%';
}

if (!empty($_GET['filtro_medidor'])) {
    $where_clauses[] = "l.numero_medidor LIKE ?";
    $params[] = '%' . $_GET['filtro_medidor'] . '%';
}

if (!empty($_GET['filtro_centro'])) {
    $where_clauses[] = "l.centro_lectura = ?";
    $params[] = $_GET['filtro_centro'];
}

if (!empty($_GET['filtro_ruta'])) {
    $where_clauses[] = "l.ruta LIKE ?";
    $params[] = '%' . $_GET['filtro_ruta'] . '%';
}

if (!empty($_GET['filtro_estado'])) {
    if ($_GET['filtro_estado'] === 'Pendiente de envío') {
        $where_clauses[] = "(l.estado = 'Pendiente de envío' OR l.estado = 'pendiente')";
    } else {
        $where_clauses[] = "l.estado = ?";
        $params[] = $_GET['filtro_estado'];
    }
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    // Handle query error
    die("Error in query execution.");
}

$filename = "lecturas_" . date('Y-m-d') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

fputcsv($output, array('NIS', 'NIC', 'Medidor', 'Ruta', 'Itinerario', 'Lectura', 'Anomalía', 'Oficina', 'Fecha Lectura', 'Observación', 'Anomalía 2', 'Supervisor', 'Estado Factura'));

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    fputcsv($output, array(
        $row['nis'] ?? '',
        $row['nic'] ?? '',
        $row['numero_medidor'] ?? '',
        $row['ruta'] ?? '',
        $row['itinerario'] ?? '',
        $row['lectura_actual'] ?? '',
        $row['anomalia_actual'] ?? '',
        $row['oficina'] ?? '',
        $row['fecha_lectura'] ? $row['fecha_lectura']->format('Y-m-d') : '',
        $row['observacion'] ?? '',
        $row['anomalia2'] ?? '',
        $row['supervisor'] ?? '',
        $row['estado'] ?? ''
    ));
}

fclose($output);
exit();

?>
