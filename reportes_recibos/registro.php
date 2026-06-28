<?php
session_start();
include 'conexion.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!empty($nombre) && !empty($usuario) && !empty($password)) {
        if ($password === $password_confirm) {
            // Check if username exists
            $check = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
            $check->bind_param("s", $usuario);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $error = "El nombre de usuario '$usuario' ya está en uso. Por favor elija otro.";
            } else {
                $passHash = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, usuario, password, rol, oficina_usuario) VALUES (?, ?, ?, 'USUARIO', ?)");
                $oficina_registro = $_POST['oficina_usuario'] ?? null;
                $stmt->bind_param("ssss", $nombre, $usuario, $passHash, $oficina_registro);
                
                if ($stmt->execute()) {
                    $success = "Usuario registrado correctamente. Ya puede iniciar sesión.";
                } else {
                    $error = "Error al registrar: " . $conexion->error;
                }
            }
        } else {
            $error = "Las contraseñas no coinciden.";
        }
    } else {
        $error = "Por favor complete todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - EDENORTE</title>
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
        .register-card {
            max-width: 500px;
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
    <div class="register-card">
        <div class="text-center mb-4">
            <img src="assets/images/icono.png" alt="Logo" class="mb-3" style="max-width: 120px;" onerror="this.style.display='none'">
            <h2 class="fw-bold text-primary">EDENORTE</h2>
            <h6 class="text-secondary">Lectura y Distribución</h6>
            <h5 class="fw-bold">Registro de Usuario</h5>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <div class="d-grid mt-3">
                <a href="login.php" class="btn btn-outline-primary">Ir al Login</a>
            </div>
        <?php else: ?>
            <form action="registro.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Oficina</label>
                    <select name="oficina_usuario" class="form-select" required>
                        <option value="">Seleccione su Oficina</option>
                        <option value="2542 - VALVERDE MAO">2542 - VALVERDE MAO</option>
                        <option value="2543 - STGO.RODRIGUEZ">2543 - STGO.RODRIGUEZ</option>
                        <option value="2544 - MONTECRISTI">2544 - MONTECRISTI</option>
                        <option value="2545 - DAJABON">2545 - DAJABON</option>
                        <option value="2561 - L. CABRERA">2561 - L. CABRERA</option>
                        <option value="2562 - VILLA VASQUEZ">2562 - VILLA VASQUEZ</option>
                        <option value="2563 - ESPERANZA">2563 - ESPERANZA</option>
                        <option value="2564 - PARTIDO">2564 - PARTIDO</option>
                        <option value="2565 - LAG SALADA">2565 - LAG SALADA</option>
                        <option value="2566 - MONCION">2566 - MONCION</option>
                        <option value="2567 - CASTANUELA">2567 - CASTANUELA</option>
                        <option value="2568 - V ALMACIGOS">2568 - V ALMACIGOS</option>
                        <option value="2569 - GUAYUBIN">2569 - GUAYUBIN</option>
                        <option value="2570 - M D S.CRUZ">2570 - M D S.CRUZ</option>
                        <option value="2571 - MAIZAL">2571 - MAIZAL</option>
                        <option value="2573 - MANZANILLO">2573 - MANZANILLO</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="nombre" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="usuario" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmar Contraseña</label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Registrarse</button>
                    <a href="login.php" class="btn btn-link text-decoration-none">¿Ya tienes cuenta? Inicia sesión</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>