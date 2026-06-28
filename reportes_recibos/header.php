<?php
// We expect $pathToRoot to be defined in the including file (e.g., "" or "../")
if (!isset($pathToRoot)) {
    $pathToRoot = "";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de No Recepción de Facturas</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $pathToRoot; ?>assets/images/pestaña.ico">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $pathToRoot; ?>assets/images/pestaña.ico">
    <!-- Bootstrap 5 CSS -->
    <link href="<?php echo $pathToRoot; ?>css/vendor/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="<?php echo $pathToRoot; ?>css/vendor/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?php echo $pathToRoot; ?>css/vendor/responsive.bootstrap5.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="<?php echo $pathToRoot; ?>css/vendor/fontawesome.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="<?php echo $pathToRoot; ?>css/vendor/select2.min.css" rel="stylesheet" />
    <link href="<?php echo $pathToRoot; ?>css/vendor/select2-bootstrap-5.min.css" rel="stylesheet" />
    <!-- Local Fonts Fallback -->
    <link href="<?php echo $pathToRoot; ?>css/vendor/fonts.css" rel="stylesheet">
    
    <!-- Core JS (Local versions) -->
    <script src="<?php echo $pathToRoot; ?>js/vendor/jquery.min.js"></script>
    <script src="<?php echo $pathToRoot; ?>js/vendor/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $pathToRoot; ?>js/vendor/sweetalert2.all.min.js"></script>
    
    <!-- Script Availability Guard -->
    <script>
        // Ensure Swal and Swal.fire are defined even if CDN fails
        if (typeof Swal === 'undefined') {
            window.Swal = {
                fire: function(title, text, icon) {
                    console.warn('SweetAlert2 not loaded, using alert fallback');
                    alert((title || '') + '\n' + (text || ''));
                    return { then: function(cb) { if(cb) cb({isConfirmed: true}); } };
                }
            };
        }
        if (typeof jQuery === 'undefined') {
            console.error('CRITICAL: jQuery failed to load. The system will not function correctly.');
        }
    </script>

    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
        }
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: var(--primary-color);
            color: #fff;
            transition: all 0.3s;
            min-height: 100vh;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: var(--secondary-color);
        }
        #sidebar ul.components {
            padding: 20px 0;
        }
        #sidebar ul li a {
            padding: 10px 20px;
            font-size: 1.1em;
            display: block;
            color: #fff;
            text-decoration: none;
        }
        #sidebar ul li a:hover {
            background: var(--accent-color);
        }
        #sidebar ul li.active > a {
            background: var(--accent-color);
        }
        #content {
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        .navbar {
            padding: 15px 10px;
            background: #fff;
            border: none;
            border-radius: 0;
            margin-bottom: 40px;
            box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header text-center">
                <img src="<?php echo $pathToRoot; ?>assets/images/icono.png" alt="Logo" class="img-fluid mb-2" style="max-width: 80px; filter: drop-shadow(0 0 5px rgba(255,255,255,0.2));" onerror="this.style.display='none'">
                <h4 class="mb-0">EDENORTE</h4>
                <small>Lectura y Distribución</small>
            </div>
            <ul class="list-unstyled components">
                <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <a href="<?php echo $pathToRoot; ?>dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                </li>
                <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/reportes/') !== false ? 'active' : ''; ?>">
                    <a href="<?php echo $pathToRoot; ?>reportes/index.php"><i class="fas fa-file-alt me-2"></i> Reportes</a>
                </li>
                <?php if ($_SESSION['user_rol'] === 'ADMINISTRADOR'): ?>
                <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/usuarios/') !== false && strpos($_SERVER['PHP_SELF'], 'solicitudes.php') === false ? 'active' : ''; ?>">
                    <a href="<?php echo $pathToRoot; ?>usuarios/index.php"><i class="fas fa-users me-2"></i> Usuarios</a>
                </li>
                <li class="<?php echo strpos($_SERVER['PHP_SELF'], 'solicitudes.php') !== false ? 'active' : ''; ?>">
                    <a href="<?php echo $pathToRoot; ?>usuarios/solicitudes.php"><i class="fas fa-key me-2"></i> Solicitudes</a>
                </li>
                <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'importar_ui.php' ? 'active' : ''; ?>">
                    <a href="<?php echo $pathToRoot; ?>importar_ui.php"><i class="fas fa-upload me-2"></i> Importar Clientes</a>
                </li>
                <?php endif; ?>
                <li class="<?php echo strpos($_SERVER['PHP_SELF'], 'perfil.php') !== false ? 'active' : ''; ?>">
                    <a href="<?php echo $pathToRoot; ?>usuarios/perfil.php"><i class="fas fa-user-cog me-2"></i> Mi Perfil</a>
                </li>
                <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manual.php' ? 'active' : ''; ?>">
                    <a href="<?php echo $pathToRoot; ?>manual.php"><i class="fas fa-book me-2"></i> Manual</a>
                </li>
                <li>
                    <a href="<?php echo $pathToRoot; ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-align-left"></i>
                    </button>
                    <div class="ms-auto">
                        <span class="text-muted">Bienvenido, <strong><?php echo $_SESSION['user_nombre']; ?></strong> (<?php echo $_SESSION['user_rol']; ?>)</span>
                    </div>
                </div>
            </nav>
            <div class="container-fluid">
