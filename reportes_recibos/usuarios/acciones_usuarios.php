<?php
include '../auth_check.php';
// Initial auth check, but specific actions will check roles later
check_auth("../");
include '../conexion.php';

$accion = $_REQUEST['accion'] ?? '';
$response = ['success' => false, 'message' => 'Acción no válida'];

if ($accion == 'guardar' || $accion == 'editar') {
    check_role('ADMINISTRADOR', '../');
    $nombre = $_POST['nombre'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'USUARIO';
    $oficina_usuario = $_POST['oficina_usuario'] ?? null;
    $id = $_POST['id'] ?? null;

    if ($accion == 'guardar') {
        // Check if username exists
        $check = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $check->bind_param("s", $usuario);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            die(json_encode(['success' => false, 'message' => "El nombre de usuario '$usuario' ya está en uso."]));
        }

        if (empty($password)) {
            die(json_encode(['success' => false, 'message' => 'La contraseña es requerida']));
        }
        $passHash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (nombre, usuario, password, rol, oficina_usuario) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            die(json_encode(['success' => false, 'message' => 'Error de preparación: ' . $conexion->error]));
        }
        $stmt->bind_param("sssss", $nombre, $usuario, $passHash, $rol, $oficina_usuario);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Usuario creado correctamente'];
        } else {
            $response = ['success' => false, 'message' => 'Error al crear usuario.'];
        }
    } else if ($accion == 'editar' && $id) {
        // Check if username exists (excluding current user)
        $check = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ? AND id != ?");
        $check->bind_param("si", $usuario, $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            die(json_encode(['success' => false, 'message' => "El nombre de usuario '$usuario' ya está en uso por otra cuenta."]));
        }

        if (!empty($password)) {
            $passHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nombre=?, usuario=?, password=?, rol=?, oficina_usuario=? WHERE id=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssssi", $nombre, $usuario, $passHash, $rol, $oficina_usuario, $id);
        } else {
            $sql = "UPDATE usuarios SET nombre=?, usuario=?, rol=?, oficina_usuario=? WHERE id=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssssi", $nombre, $usuario, $rol, $oficina_usuario, $id);
        }
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Usuario actualizado correctamente'];
        } else {
            $response = ['success' => false, 'message' => 'Error al actualizar usuario.'];
        }
    }
} else if ($accion == 'cambiar_mi_pass') {
    $pass_actual = $_POST['pass_actual'] ?? '';
    $pass_nueva = $_POST['pass_nueva'] ?? '';
    $pass_confirmar = $_POST['pass_confirmar'] ?? '';
    $user_id = $_SESSION['user_id'];

    if (strlen($pass_nueva) < 6) {
        die(json_encode(['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres']));
    }

    if ($pass_nueva !== $pass_confirmar) {
        die(json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']));
    }

    // Get current hash
    $stmt = $conexion->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $hash = $stmt->get_result()->fetch_assoc()['password'];

    if (password_verify($pass_actual, $hash)) {
        $newHash = password_hash($pass_nueva, PASSWORD_DEFAULT);
        $update = $conexion->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $update->bind_param("si", $newHash, $user_id);
        if ($update->execute()) {
            $response = ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
        } else {
            $response = ['success' => false, 'message' => 'Error al actualizar'];
        }
    } else {
        $response = ['success' => false, 'message' => 'La contraseña actual es incorrecta'];
    }

} else if ($accion == 'restablecer_pass' && isset($_POST['id'])) {
    check_role('ADMINISTRADOR', '../');
    $solicitud_id = $_POST['id'];
    $admin_id = $_SESSION['user_id'];
    $newPass = 'Edenorte123';
    $newHash = password_hash($newPass, PASSWORD_DEFAULT);

    // Get user id from request
    $stmt = $conexion->prepare("SELECT usuario_id FROM solicitudes_password WHERE id = ?");
    $stmt->bind_param("i", $solicitud_id);
    $stmt->execute();
    $uid = $stmt->get_result()->fetch_assoc()['usuario_id'];

    if ($uid) {
        $conexion->begin_transaction();
        try {
            // Update User
            $updUser = $conexion->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $updUser->bind_param("si", $newHash, $uid);
            $updUser->execute();

            // Mark Request as completed
            $updReq = $conexion->prepare("UPDATE solicitudes_password SET estado = 'COMPLETADA', admin_id = ?, fecha_completada = CURRENT_TIMESTAMP WHERE id = ?");
            $updReq->bind_param("ii", $admin_id, $solicitud_id);
            $updReq->execute();

            $conexion->commit();
            $response = ['success' => true, 'message' => 'Contraseña restablecida a: Edenorte123'];
        } catch (Exception $e) {
            $conexion->rollback();
            $response = ['success' => false, 'message' => 'Error al procesar'];
        }
    }

} else if ($accion == 'toggle_estado' && isset($_POST['id'])) {
    check_role('ADMINISTRADOR', '../');
    $id = $_POST['id'];
    $nuevo_estado = $_POST['estado'] ?? 'ACTIVO';
    
    $sql = "UPDATE usuarios SET estado_usuario = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $nuevo_estado, $id);
    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Estado de usuario actualizado'];
    } else {
        $response = ['success' => false, 'message' => 'Error al actualizar estado'];
    }

} else if ($accion == 'eliminar' && isset($_POST['id'])) {
    check_role('ADMINISTRADOR', '../');
    $id = $_POST['id'];
    
    // Prevent self-deletion
    if ($id == $_SESSION['user_id']) {
        die(json_encode(['success' => false, 'message' => 'No puedes eliminar tu propio usuario']));
    }

    // Server-side check for related records
    $sqlCheck = "SELECT 
                (SELECT COUNT(*) FROM reportes WHERE usuario_creador = ? OR usuario_modificacion = ?) +
                (SELECT COUNT(*) FROM historial_estados WHERE usuario_id = ?) +
                (SELECT COUNT(*) FROM solicitudes_password WHERE usuario_id = ? OR admin_id = ?) as total";
    $stmtCheck = $conexion->prepare($sqlCheck);
    $stmtCheck->bind_param("iiiii", $id, $id, $id, $id, $id);
    $stmtCheck->execute();
    $total = $stmtCheck->get_result()->fetch_assoc()['total'];

    if ($total > 0) {
        $response = ['success' => false, 'message' => 'El usuario posee información relacionada y solo puede ser desactivado.'];
    } else {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Usuario eliminado correctamente'];
        } else {
            $response = ['success' => false, 'message' => 'Error al eliminar usuario.'];
        }
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
?>