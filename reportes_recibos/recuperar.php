<?php
session_start();
include 'conexion.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';

    if (!empty($usuario)) {
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($user = $res->fetch_assoc()) {
            $user_id = $user['id'];
            
            // Check if there's already a pending request
            $check = $conexion->prepare("SELECT id FROM solicitudes_password WHERE usuario_id = ? AND estado = 'PENDIENTE'");
            $check->bind_param("i", $user_id);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $error = "Ya existe una solicitud pendiente para este usuario.";
            } else {
                $ins = $conexion->prepare("INSERT INTO solicitudes_password (usuario_id) VALUES (?)");
                $ins->bind_param("i", $user_id);
                if ($ins->execute()) {
                    $success = "Solicitud enviada. Por favor, contacte a un administrador para el restablecimiento.";
                } else {
                    $error = "Error al procesar solicitud.";
                }
            }
        } else {
            $error = "Usuario no encontrado.";
        }
    } else {
        $error = "Ingrese su nombre de usuario.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - EDENORTE</title>
    <link href="css/vendor/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="assets/images/pestaña.ico">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/pestaña.ico">
    <style>
        body { background-color: #f8f9fa; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .recovery-card { max-width: 400px; width: 100%; padding: 2rem; border-radius: 1rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1); background: white; }
    </style>
</head>
<body>
    <div class="recovery-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">EDENORTE</h2>
            <h5>Recuperar Acceso</h5>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <div class="d-grid">
                <a href="login.php" class="btn btn-primary">Volver al Login</a>
            </div>
        <?php else: ?>
            <p class="text-muted small text-center">Ingrese su nombre de usuario y un administrador recibirá su solicitud de restablecimiento.</p>
            <form action="recuperar.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre de Usuario</label>
                    <input type="text" name="usuario" class="form-control" required autofocus placeholder="ej: hgomezh">
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                    <a href="login.php" class="btn btn-link text-decoration-none text-center">Cancelar</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>