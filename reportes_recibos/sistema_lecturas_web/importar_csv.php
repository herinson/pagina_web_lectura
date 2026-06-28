<?php
session_start();
require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/header.php";

if (!isset($_SESSION['id_usuario'])) {
    echo "<div class='alert alert-danger'>Acceso denegado. Debes iniciar sesión.</div>";
    require_once __DIR__ . "/footer.php";
    exit();
}

$import_results = [];

function limpiar($str) {
    if ($str === null) return null;
    $str = str_replace("�", "", $str);
    return iconv("UTF-8", "UTF-8//IGNORE", $str);
}

function convertirFechaExcel($valor) {
    if ($valor === "" || $valor === null) return null;
    if (is_numeric($valor)) {
        $timestamp = ($valor - 25569) * 86400;
        return date("Y-m-d", $timestamp);
    }
    if (strtotime($valor)) {
        return date("Y-m-d", strtotime(str_replace("/", "-", $valor)));
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_FILES["archivo"]) || $_FILES["archivo"]["error"] !== 0) {
        $import_results[] = ['type' => 'danger', 'message' => 'Error: No se recibió ningún archivo.'];
    } else {
        $file = $_FILES["archivo"]["tmp_name"];
        $handle = fopen($file, "r");

        if (!$handle) {
            $import_results[] = ['type' => 'danger', 'message' => 'Error: No se pudo abrir el archivo.'];
        } else {
            $primeraLinea = fgets($handle);
            $delim = (substr_count($primeraLinea, "\t") > 5) ? "\t" : ((substr_count($primeraLinea, ";") > 5) ? ";" : ",");
            rewind($handle);

            $fila = 0;
            $insertados = 0;
            $errores = 0;

            while (($data = fgetcsv($handle, 99999, $delim)) !== false) {
                $fila++;
                if ($fila == 1) continue;

                if (count($data) < 29) {
                    $import_results[] = ['type' => 'warning', 'message' => "Fila $fila ignorada: solo tiene " . count($data) . " columnas."];
                    continue;
                }

                foreach ($data as $i => $v) {
                    $data[$i] = limpiar(trim($v));
                }

                $nis                   = $data[0];
                $numero_medidor        = $data[1];
                $centro_lectura        = $data[2];
                $lectura_actual        = $data[3];
                $lectura_anterior      = $data[4];
                $anomalia_actual       = $data[5];
                $desc_anomalia_actual  = $data[6];
                $anomalia_anterior     = $data[7];
                $desc_anomalia_anterior= $data[8];
                $ruta                  = $data[9];
                $ruta_anterior         = $data[10];
                $sector                = $data[11];
                $itinerario            = $data[12];
                $fecha_raw             = $data[13];
                $ciclo                 = $data[14];
                $oficina               = (int)$centro_lectura;
                $itinerario_anterior   = $data[16];
                $tipo_conexion         = $data[17];
                $nic                   = $data[18];
                $estado_suministro     = $data[19];
                $tarifa                = $data[20];
                $latitud               = $data[21];
                $longitud              = $data[22];
                $nombre_lector         = $data[23];
                $responsable           = $data[24];
                $tecnologia            = $data[25];
                $cantidad_pendientes   = $data[26];
                $hora                  = $data[27];
                $nif                   = $data[28];
                $fecha = convertirFechaExcel($fecha_raw);

                $observacion         = "";
                $anomalia2           = "";
                $estado              = "Pendiente de envío";
                $fecha_lectura       = null;
                $supervisor_asignado = 1;

                $sql = "INSERT INTO lecturas (
                    nis, numero_medidor, centro_lectura, lectura_actual, lectura_anterior,
                    anomalia_actual, desc_anomalia_actual, anomalia_anterior, desc_anomalia_anterior,
                    ruta, ruta_anterior, sector, itinerario, fecha, ciclo, oficina,
                    itinerario_anterior, tipo_conexion, nic, estado_suministro, tarifa,
                    latitud, longitud, nombre_lector, responsable, tecnologia,
                    cantidad_facturas_pendientes, hora, nif, observacion, anomalia2,
                    estado, fecha_lectura, supervisor_asignado
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                $params = array(
                    $nis, $numero_medidor, $centro_lectura, $lectura_actual, $lectura_anterior,
                    $anomalia_actual, $desc_anomalia_actual, $anomalia_anterior, $desc_anomalia_anterior,
                    $ruta, $ruta_anterior, $sector, $itinerario, $fecha, $ciclo, $oficina,
                    $itinerario_anterior, $tipo_conexion, $nic, $estado_suministro, $tarifa,
                    $latitud, $longitud, $nombre_lector, $responsable, $tecnologia,
                    $cantidad_pendientes, $hora, $nif, $observacion, $anomalia2,
                    $estado, $fecha_lectura, $supervisor_asignado
                );

                $stmt = sqlsrv_query($conn, $sql, $params);

                if ($stmt === false) {
                    $import_results[] = ['type' => 'danger', 'message' => "<b>Error SQL en la fila $fila:</b> " . print_r(sqlsrv_errors(), true)];
                    $errores++;
                } else {
                    $insertados++;
                }
            }
            fclose($handle);

            if ($insertados > 0) {
                $import_results[] = ['type' => 'success', 'message' => "Importación completada. Filas insertadas: $insertados"];
            }
            if ($errores > 0) {
                $import_results[] = ['type' => 'danger', 'message' => "Se encontraron $errores errores durante la importación."];
            }
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Importar Archivo TXT / CSV</h2>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="archivo" class="form-label">Seleccionar Archivo:</label>
                <input type="file" class="form-control" name="archivo" id="archivo" required>
            </div>
            <button type="submit" class="btn btn-primary">Importar</button>
        </form>
    </div>
</div>

<?php if (!empty($import_results)): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h3>Resultados de la Importación</h3>
        </div>
        <div class="card-body">
            <?php foreach ($import_results as $result): ?>
                <div class="alert alert-<?= $result['type'] ?>" role="alert">
                    <?= $result['message'] ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php
require_once __DIR__ . "/footer.php";
?>
