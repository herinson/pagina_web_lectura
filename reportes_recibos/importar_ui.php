<?php
$pathToRoot = "";
include 'auth_check.php';
check_auth($pathToRoot);
include 'conexion.php';

// Restricted to ADMIN
if ($_SESSION['user_rol'] !== 'ADMINISTRADOR') {
    header("Location: dashboard.php");
    exit();
}

$import_results = null;

// Aumentar límites para archivos grandes
set_time_limit(0);
ini_set('memory_limit', '1024M');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $modo = $_POST['modo'] ?? 'upload';
    $file = null;

    if ($modo == 'upload') {
        if (!isset($_FILES["archivo_txt"])) {
            $import_results = ["success" => false, "message" => "No se recibieron datos. Es posible que el archivo sea demasiado grande para la configuración del servidor (post_max_size)."];
        } else if ($_FILES["archivo_txt"]["error"] !== UPLOAD_ERR_OK) {
            $error_codes = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP.',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario.',
                UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente.',
                UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo.',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal en el servidor.',
                UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en el disco.',
                UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida.'
            ];
            $msg = $error_codes[$_FILES["archivo_txt"]["error"]] ?? 'Error desconocido en la subida.';
            $import_results = ["success" => false, "message" => $msg];
        } else {
            $file = $_FILES["archivo_txt"]["tmp_name"];
        }
    } else {
        // Modo local
        $local_path = __DIR__ . DIRECTORY_SEPARATOR . 'iniforme_semanal.txt';
        if (file_exists($local_path)) {
            $file = $local_path;
        } else {
            $import_results = ["success" => false, "message" => "El archivo iniforme_semanal.txt no se encontró en la carpeta del servidor."];
        }
    }

    if ($file && !$import_results) {
        $handle = fopen($file, "r");
        if ($handle) {
            // Desactivar autocommit para mayor velocidad
            $conexion->autocommit(FALSE);

            try {
                $conexion->query("TRUNCATE TABLE clientes_info");
                fgetcsv($handle, 0, "@"); // Header

                $columnas = ['NIS_RAD', 'COD_TIPO_CONEXION', 'TIPO_CONEXION', 'NIF', 'NIC', 'FECHA_ALTA', 'FECHA_BAJA', 'FIANZA', 'CO_AN_VIP', 'AN_VIP', 'ESTADO_SUMINISTRO', 'DESCRIPCION_ESTADO_SUMINISTRO', 'TARIFA', 'NOMBRE_CLIENTE', 'APE1_CLI', 'APE2_CLI', 'TFNO_CLI', 'COD_CLI', 'DESC_TIPOCLIENTE', 'COD_CALLE', 'CALLE', 'NUM_PUERTA', 'DUPLICADOR', 'CGV_SUM', 'COD_LOCAL', 'LOCALIDAD', 'SECCION', 'MUNICIPIO', 'PROVINCIA', 'REF_DIR', 'ACC_FINCA', 'NOM_FINCA', 'COD_UNICOM', 'COD_AREA', 'NUM_DEUDA', 'IMPORTE', 'NUM_DEUDA_VENC', 'IMPORTE_VENC', 'NUM_APA', 'CO_MARCA', 'MARCA', 'F_INST_MED', 'NUM_PADRON', 'RUTA', 'ITINERARIO', 'TIP_FIN', 'TIPO_FINCA', 'TIP_CLI', 'TIPO_CLIENTE', 'TIP_TENSION', 'TIPO_TENSION', 'TIP_SUMINISTRO', 'TIPO_SUMINISTRO', 'FECHA_VENC_UF', 'CSMO_FIJO', 'DOC_ID', 'Subestacion', 'Circuito', 'CT', 'PUNTO_MEDIDA', 'NATURALEZA', 'FECHA_UF', 'ZONA', 'CENT_LECT', 'COORDX', 'COORDY', 'TABLA', 'COD_UNICOM_CONT', 'NUM_FISCAL', 'TIPO_DOC'];
                $placeholders = implode(',', array_fill(0, count($columnas), '?'));
                $stmt = $conexion->prepare("INSERT INTO clientes_info (" . implode(',', $columnas) . ") VALUES ($placeholders)");

                $insertados = 0;
                $errores = 0;
                $batch_size = 1000;
                $count = 0;

                while (($data = fgetcsv($handle, 0, "@")) !== false) {
                    if (count($data) < count($columnas)) { $data = array_pad($data, count($columnas), null); }
                    else { $data = array_slice($data, 0, count($columnas)); }

                    if ($stmt->execute($data)) {
                        $insertados++;
                    } else {
                        $errores++;
                    }

                    $count++;
                    if ($count % $batch_size == 0) {
                        $conexion->commit();
                    }
                }
                $conexion->commit();
                fclose($handle);
                $import_results = ["success" => true, "inserted" => $insertados, "errors" => $errores];
            } catch (Exception $e) {
                $conexion->rollback();
                $import_results = ["success" => false, "message" => "Error durante la importación: " . $e->getMessage()];
            }
            $conexion->autocommit(TRUE);
        } else {
            $import_results = ["success" => false, "message" => "No se pudo abrir el archivo para lectura."];
        }
    }
}

include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-upload me-2"></i> Importar Clientes (Informe Semanal)</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Actualice la base de datos de autocompletado desde el archivo <strong>iniforme_semanal.txt</strong>.
                </div>

                <?php if ($import_results): ?>
                    <?php if ($import_results['success']): ?>
                        <div class="alert alert-<?php echo $import_results['inserted'] > 0 ? 'success' : 'warning'; ?> alert-dismissible fade show" role="alert">
                            <strong>Proceso finalizado!</strong>
                            <ul class="mb-0 mt-2">
                                <li>Registros importados: <?php echo $import_results['inserted']; ?></li>
                                <li>Errores encontrados: <?php echo $import_results['errors']; ?></li>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error:</strong> <?php echo $import_results['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="mt-4">
                    <div class="mb-4 p-3 border rounded bg-light">
                        <label class="form-label fw-bold d-block mb-2">Método de Importación</label>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="modo" id="modo_upload" value="upload" checked onclick="toggleModo('upload')">
                            <label class="form-check-label" for="modo_upload">
                                <strong>Subir archivo desde mi PC</strong> (Archivos pequeños < 20MB)
                            </label>
                        </div>

                        <div id="div_upload" class="ms-4 mb-4">
                            <input type="file" class="form-control" name="archivo_txt" id="archivo_txt" accept=".txt">
                            <div class="form-text">Límite de PHP: <?php echo ini_get('upload_max_filesize'); ?></div>
                        </div>

                        <hr>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="modo" id="modo_local" value="local" onclick="toggleModo('local')">
                            <label class="form-check-label" for="modo_local">
                                <strong>Procesar archivo local en el servidor</strong> (Para archivos GRANDES como 150MB)
                            </label>
                        </div>

                        <div id="div_local" class="ms-4 mb-2 d-none">
                            <div class="alert alert-warning py-2 small">
                                <i class="fas fa-exclamation-triangle"></i> Paso 1: Copie el archivo <strong>iniforme_semanal.txt</strong> a la carpeta <code>reportes_recibos/</code> mediante FTP.
                                <br>Paso 2: Haga clic en el botón de abajo para procesarlo.
                            </div>
                            <p class="text-muted small mb-0">Ruta esperada: <code><?php echo __DIR__ . DIRECTORY_SEPARATOR; ?>iniforme_semanal.txt</code></p>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-play me-2"></i> Iniciar Importación
                        </button>
                    </div>
                </form>

                <script>
                function toggleModo(modo) {
                    if (modo === 'upload') {
                        document.getElementById('div_upload').classList.remove('d-none');
                        document.getElementById('div_local').classList.add('d-none');
                        document.getElementById('archivo_txt').required = true;
                    } else {
                        document.getElementById('div_upload').classList.add('d-none');
                        document.getElementById('div_local').classList.remove('d-none');
                        document.getElementById('archivo_txt').required = false;
                    }
                }
                </script>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>