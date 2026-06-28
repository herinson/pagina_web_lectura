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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        $handle = fopen($file, "r");
        if ($handle) {
            $conexion->query("TRUNCATE TABLE clientes_info");
            fgetcsv($handle, 0, "@"); // Header

            $columnas = ['NIS_RAD', 'COD_TIPO_CONEXION', 'TIPO_CONEXION', 'NIF', 'NIC', 'FECHA_ALTA', 'FECHA_BAJA', 'FIANZA', 'CO_AN_VIP', 'AN_VIP', 'ESTADO_SUMINISTRO', 'DESCRIPCION_ESTADO_SUMINISTRO', 'TARIFA', 'NOMBRE_CLIENTE', 'APE1_CLI', 'APE2_CLI', 'TFNO_CLI', 'COD_CLI', 'DESC_TIPOCLIENTE', 'COD_CALLE', 'CALLE', 'NUM_PUERTA', 'DUPLICADOR', 'CGV_SUM', 'COD_LOCAL', 'LOCALIDAD', 'SECCION', 'MUNICIPIO', 'PROVINCIA', 'REF_DIR', 'ACC_FINCA', 'NOM_FINCA', 'COD_UNICOM', 'COD_AREA', 'NUM_DEUDA', 'IMPORTE', 'NUM_DEUDA_VENC', 'IMPORTE_VENC', 'NUM_APA', 'CO_MARCA', 'MARCA', 'F_INST_MED', 'NUM_PADRON', 'RUTA', 'ITINERARIO', 'TIP_FIN', 'TIPO_FINCA', 'TIP_CLI', 'TIPO_CLIENTE', 'TIP_TENSION', 'TIPO_TENSION', 'TIP_SUMINISTRO', 'TIPO_SUMINISTRO', 'FECHA_VENC_UF', 'CSMO_FIJO', 'DOC_ID', 'Subestacion', 'Circuito', 'CT', 'PUNTO_MEDIDA', 'NATURALEZA', 'FECHA_UF', 'ZONA', 'CENT_LECT', 'COORDX', 'COORDY', 'TABLA', 'COD_UNICOM_CONT', 'NUM_FISCAL', 'TIPO_DOC'];
            $placeholders = implode(',', array_fill(0, count($columnas), '?'));
            $stmt = $conexion->prepare("INSERT INTO clientes_info (" . implode(',', $columnas) . ") VALUES ($placeholders)");

            $insertados = 0;
            $errores = 0;

            while (($data = fgetcsv($handle, 0, "@")) !== false) {
                if (count($data) < count($columnas)) { $data = array_pad($data, count($columnas), null); }
                else { $data = array_slice($data, 0, count($columnas)); }

                try {
                    if ($stmt->execute($data)) { $insertados++; }
                    else { $errores++; }
                } catch (Exception $e) {
                    $errores++;
                }
            }
            fclose($handle);
            $import_results = ["success" => true, "inserted" => $insertados, "errors" => $errores];
        } else {
            $import_results = ["success" => false, "message" => "No se pudo abrir el archivo temporal."];
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
                    <i class="fas fa-info-circle me-2"></i> Seleccione el archivo <strong>iniforme_semanal.txt</strong> para actualizar la base de datos de autocompletado.
                    <br><strong>Nota:</strong> Este proceso borrará la información anterior y la reemplazará por la nueva.
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
                    <div class="mb-4">
                        <label for="archivo_txt" class="form-label fw-bold">Archivo TXT (Separado por @)</label>
                        <input type="file" class="form-control" name="archivo_txt" id="archivo_txt" accept=".txt" required>
                        <div class="form-text">Asegúrese de que el archivo tenga los encabezados correctos.</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-play me-2"></i> Iniciar Importación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>