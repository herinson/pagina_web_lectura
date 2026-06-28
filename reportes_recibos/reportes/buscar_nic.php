<?php
header('Content-Type: application/json; charset=utf-8');
include '../auth_check.php';
check_auth("../");
include '../conexion.php';

$nic = $_GET['nic'] ?? '';
$response = ['success' => false, 'data' => null];

if (!empty($nic)) {
    $sql = "SELECT NIC, LOCALIDAD, COD_UNICOM, TFNO_CLI, RUTA, ITINERARIO
            FROM clientes_info
            WHERE NIC = ?
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $nic);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $response = [
                'success' => true,
                'data' => $row
            ];
        }
    }
}

echo json_encode($response);
?>