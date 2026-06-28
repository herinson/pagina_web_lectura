<?php
include '../auth_check.php';
check_auth("../");
include '../conexion.php';

$accion = $_REQUEST['accion'] ?? '';
$response = ['success' => false, 'message' => 'Acción no válida'];

if ($accion == 'guardar' || $accion == 'editar') {
    $nic = $_POST['nic'] ?? '';
    $localidad = $_POST['localidad'] ?? '';
    $oficina = $_POST['oficina'] ?? '';
    $fecha = !empty($_POST['fecha']) ? $_POST['fecha'] : null;
    $area = $_POST['area'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $mes_reclamado = isset($_POST['mes_reclamado']) ? implode(', ', $_POST['mes_reclamado']) : '';
    $ruta = $_POST['ruta'] ?? '';
    $itinerario = $_POST['itinerario'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';
    $id = $_POST['id'] ?? null;

    if ($accion == 'guardar') {
        $sql = "INSERT INTO reportes (nic, localidad, oficina, fecha, area, telefono, mes_reclamado, ruta, itinerario, observaciones, usuario_creador, estado, estado_factura) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ACTIVO', 'Pendiente de envío')";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            die(json_encode(['success' => false, 'message' => 'Error de preparación: ' . $conexion->error]));
        }
        $stmt->bind_param("ssssssssssi", $nic, $localidad, $oficina, $fecha, $area, $telefono, $mes_reclamado, $ruta, $itinerario, $observaciones, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Reporte guardado correctamente'];
        } else {
            $response = ['success' => false, 'message' => 'Error al guardar: ' . $stmt->error];
        }
    } else if ($accion == 'editar' && $id) {
        // Security check: User can only edit their own reports unless Admin or Encargado
        $check = $conexion->prepare("SELECT usuario_creador, estado_factura, fecha_registro FROM reportes WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $reportData = $check->get_result()->fetch_assoc();
        $creator = $reportData['usuario_creador'];
        $oldEstadoFactura = $reportData['estado_factura'];

        if (!in_array($_SESSION['user_rol'], ['ADMINISTRADOR', 'ENCARGADO DE LECTURA'])) {
            // Check time restriction (5 minutes)
            $fechaReg = strtotime($reportData['fecha_registro']);
            $ahora = time();
            $diffMins = ($ahora - $fechaReg) / 60;
            
            if ($diffMins >= 5) {
                die(json_encode(['success' => false, 'message' => 'El tiempo de edición (5 minutos) ha expirado']));
            }

            if ($creator != $_SESSION['user_id']) {
                die(json_encode(['success' => false, 'message' => 'No tienes permiso para editar este reporte']));
            }
            // Normal users cannot change status
            $newEstadoFactura = $oldEstadoFactura;
        } else {
            $newEstadoFactura = $_POST['estado_factura'] ?? $oldEstadoFactura;
            
            // Audit status change
            if ($newEstadoFactura !== $oldEstadoFactura) {
                $audit = $conexion->prepare("INSERT INTO historial_estados (reporte_id, usuario_id, estado_anterior, estado_nuevo) VALUES (?, ?, ?, ?)");
                $audit->bind_param("iiss", $id, $_SESSION['user_id'], $oldEstadoFactura, $newEstadoFactura);
                $audit->execute();
            }
        }

        $sql = "UPDATE reportes SET nic=?, localidad=?, oficina=?, fecha=?, area=?, telefono=?, mes_reclamado=?, ruta=?, itinerario=?, observaciones=?, usuario_modificacion=?, estado='MODIFICADO', estado_factura=? 
                WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssssssssisi", $nic, $localidad, $oficina, $fecha, $area, $telefono, $mes_reclamado, $ruta, $itinerario, $observaciones, $_SESSION['user_id'], $newEstadoFactura, $id);
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Reporte actualizado correctamente'];
        } else {
            $response = ['success' => false, 'message' => 'Error al actualizar: ' . $conexion->error];
        }
    }
} else if ($accion == 'cambiar_estado' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nuevo_estado = $_POST['nuevo_estado'] ?? '';
    $comentario = $_POST['comentario'] ?? '';
    $evidencia_path = null;

    if (!in_array($_SESSION['user_rol'], ['ADMINISTRADOR', 'ENCARGADO DE LECTURA'])) {
        die(json_encode(['success' => false, 'message' => 'No tienes permiso para cambiar el estado']));
    }

    // Get current state
    $stmt = $conexion->prepare("SELECT estado_factura FROM reportes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $oldEstadoFactura = $stmt->get_result()->fetch_assoc()['estado_factura'];

    if ($nuevo_estado == $oldEstadoFactura && empty($_FILES['evidencia']['name'])) {
        die(json_encode(['success' => false, 'message' => 'El estado es el mismo y no se adjuntó evidencia']));
    }

    // Handle File Upload
    if (!empty($_FILES['evidencia']['name'])) {
        $file = $_FILES['evidencia'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errMsgs = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede el límite (upload_max_filesize) de PHP.',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede el límite del formulario.',
                UPLOAD_ERR_PARTIAL => 'El archivo solo se subió parcialmente.',
                UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo.',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal.',
                UPLOAD_ERR_CANT_WRITE => 'Error al escribir en disco.',
                UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida.'
            ];
            die(json_encode(['success' => false, 'message' => 'Error de subida: ' . ($errMsgs[$file['error']] ?? 'Código ' . $file['error'])]));
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($ext, $allowed)) {
            die(json_encode(['success' => false, 'message' => 'Formato de archivo no permitido (JPG, PNG, PDF solamente)']));
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            die(json_encode(['success' => false, 'message' => 'El archivo excede el tamaño máximo permitido (5MB)']));
        }

        if (!is_dir(UPLOAD_DIR)) {
            if (!mkdir(UPLOAD_DIR, 0755, true)) {
                die(json_encode(['success' => false, 'message' => 'El directorio de destino no existe y no pudo ser creado: ' . UPLOAD_DIR]));
            }
        }

        if (!is_writable(UPLOAD_DIR)) {
            die(json_encode(['success' => false, 'message' => 'El directorio de destino no tiene permisos de escritura: ' . UPLOAD_DIR]));
        }

        $filename = uniqid('evid_', true) . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
            $evidencia_path = $filename;
        } else {
            die(json_encode(['success' => false, 'message' => 'Error crítico: move_uploaded_file falló. Verifique permisos de la carpeta ' . UPLOAD_DIR]));
        }
    }

    // Update Report
    $update = $conexion->prepare("UPDATE reportes SET estado_factura = ?, usuario_modificacion = ? WHERE id = ?");
    $update->bind_param("sii", $nuevo_estado, $_SESSION['user_id'], $id);
    
    if ($update->execute()) {
        // Log in history
        $log = $conexion->prepare("INSERT INTO historial_estados (reporte_id, usuario_id, estado_anterior, estado_nuevo, comentario, evidencia) VALUES (?, ?, ?, ?, ?, ?)");
        $log->bind_param("iissss", $id, $_SESSION['user_id'], $oldEstadoFactura, $nuevo_estado, $comentario, $evidencia_path);
        $log->execute();
        
        $response = ['success' => true, 'message' => 'Estado actualizado correctamente'];
    } else {
        $response = ['success' => false, 'message' => 'Error al actualizar estado'];
    }

} else if ($accion == 'get_historial' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT h.*, u.nombre as usuario 
            FROM historial_estados h 
            JOIN usuarios u ON h.usuario_id = u.id 
            WHERE h.reporte_id = ? 
            ORDER BY h.fecha_cambio DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = [
            'usuario' => $row['usuario'],
            'fecha' => $row['fecha_cambio'],
            'anterior' => $row['estado_anterior'],
            'nuevo' => $row['estado_nuevo'],
            'comentario' => $row['comentario'],
            'evidencia' => $row['evidencia']
        ];
    }
    $response = ['success' => true, 'data' => $data];

} else if ($accion == 'eliminar' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Only admins can delete (per requirements)
    if ($_SESSION['user_rol'] !== 'ADMINISTRADOR') {
        $response = ['success' => false, 'message' => 'No tienes permiso para eliminar reportes'];
    } else {
        $sql = "UPDATE reportes SET estado = 'ELIMINADO', usuario_modificacion = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $_SESSION['user_id'], $id);
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Reporte eliminado correctamente'];
        } else {
            $response = ['success' => false, 'message' => 'Error al eliminar: ' . $conexion->error];
        }
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
?>