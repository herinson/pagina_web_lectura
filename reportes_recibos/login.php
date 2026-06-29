<?php
session_start();
include 'conexion.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($usuario) && !empty($password)) {
        $stmt = $conexion->prepare("SELECT id, nombre, password, rol, oficina_usuario, estado_usuario FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if ($user['estado_usuario'] === 'INACTIVO') {
                $error = "Su cuenta ha sido desactivada. Por favor, contacte a un administrador.";
            } else if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nombre'] = $user['nombre'];
                $_SESSION['user_usuario'] = $usuario;
                $_SESSION['user_rol'] = $user['rol'];
                $_SESSION['user_oficina'] = $user['oficina_usuario'];
                $_SESSION['last_activity'] = time(); // Initialize activity tracker

                header("Location: dashboard.php");
                exit();
            } else {
                // If it fails, check if the DB has plain text (common mistake during manual setup)
                if ($password === $user['password']) {
                    $error = "Error de seguridad: La contraseña en la base de datos no está encriptada. Por favor use password_hash() en PHP.";
                } else {
                    $error = "Contraseña incorrecta";
                }
            }
        } else {
            $error = "Usuario no encontrado";
        }
    } else {
        $error = "Por favor complete todos los campos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Reportes</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/pestaña.ico">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/pestaña.ico">
    <!-- Bootstrap 5 CSS -->
    <link href="css/vendor/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            background: white;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <img src="assets/images/icono.png" alt="Logo" class="mb-3" style="max-width: 120px;" onerror="this.style.display='none'">
            <h2 class="fw-bold text-primary">EDENORTE</h2>
            <h6 class="text-secondary">Lectura y Distribución de Facturas</h6>
            <hr>
            <h4 class="fw-bold">Sistema de No Recepción de Facturas</h4>
            <h3>Iniciar Sesión</h3>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['timeout'])): ?>
            <div class="alert alert-warning" role="alert">
                Su sesión ha expirado por inactividad. Por favor, inicie sesión de nuevo.
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                <div class="text-center">
                    <a href="registro.php" class="btn btn-link text-decoration-none small">¿No tienes cuenta? Regístrate</a>
                    <br>
                    <a href="recuperar.php" class="btn btn-link text-decoration-none small text-muted">Olvidé mi contraseña</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>