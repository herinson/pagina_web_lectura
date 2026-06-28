<?php
/**
 * Script de importación diaria de clientes desde iniforme_semanal.txt
 */
if (php_sapi_name() !== 'cli' && !isset($_GET['access_token'])) {
    die("Acceso denegado. Este script solo puede ejecutarse desde CLI o con un token de acceso.");
}

include 'conexion.php';

$archivo = __DIR__ . '/iniforme_semanal.txt';

if (!file_exists($archivo)) {
    die("Error: El archivo $archivo no existe.\n");
}

$handle = fopen($archivo, "r");
if (!$handle) {
    die("Error: No se pudo abrir el archivo $archivo.\n");
}

// 1. Vaciar la tabla
$conexion->query("TRUNCATE TABLE clientes_info");

// 2. Importar datos
$fila = 0;
$insertados = 0;
$errores = 0;

// Leer encabezados
$encabezados = fgetcsv($handle, 0, "@");

$columnas = [
    'NIS_RAD', 'COD_TIPO_CONEXION', 'TIPO_CONEXION', 'NIF', 'NIC', 'FECHA_ALTA', 'FECHA_BAJA', 'FIANZA', 'CO_AN_VIP', 'AN_VIP',
    'ESTADO_SUMINISTRO', 'DESCRIPCION_ESTADO_SUMINISTRO', 'TARIFA', 'NOMBRE_CLIENTE', 'APE1_CLI', 'APE2_CLI', 'TFNO_CLI', 'COD_CLI', 'DESC_TIPOCLIENTE', 'COD_CALLE',
    'CALLE', 'NUM_PUERTA', 'DUPLICADOR', 'CGV_SUM', 'COD_LOCAL', 'LOCALIDAD', 'SECCION', 'MUNICIPIO', 'PROVINCIA', 'REF_DIR',
    'ACC_FINCA', 'NOM_FINCA', 'COD_UNICOM', 'COD_AREA', 'NUM_DEUDA', 'IMPORTE', 'NUM_DEUDA_VENC', 'IMPORTE_VENC', 'NUM_APA', 'CO_MARCA',
    'MARCA', 'F_INST_MED', 'NUM_PADRON', 'RUTA', 'ITINERARIO', 'TIP_FIN', 'TIPO_FINCA', 'TIP_CLI', 'TIPO_CLIENTE', 'TIP_TENSION',
    'TIPO_TENSION', 'TIP_SUMINISTRO', 'TIPO_SUMINISTRO', 'FECHA_VENC_UF', 'CSMO_FIJO', 'DOC_ID', 'Subestacion', 'Circuito', 'CT', 'PUNTO_MEDIDA',
    'NATURALEZA', 'FECHA_UF', 'ZONA', 'CENT_LECT', 'COORDX', 'COORDY', 'TABLA', 'COD_UNICOM_CONT', 'NUM_FISCAL', 'TIPO_DOC'
];

$placeholders = implode(',', array_fill(0, count($columnas), '?'));
$sql = "INSERT INTO clientes_info (" . implode(',', $columnas) . ") VALUES ($placeholders)";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en preparación de SQL: " . $conexion->error . "\n");
}

while (($data = fgetcsv($handle, 0, "@")) !== false) {
    $fila++;

    // Asegurar que tenemos el número correcto de columnas (70)
    if (count($data) < count($columnas)) {
        // Rellenar con nulos si faltan columnas al final
        $data = array_pad($data, count($columnas), null);
    } else if (count($data) > count($columnas)) {
        // Recortar si sobran
        $data = array_slice($data, 0, count($columnas));
    }

    $types = str_repeat("s", count($columnas));
    $stmt->bind_param($types, ...$data);

    if ($stmt->execute()) {
        $insertados++;
    } else {
        $errores++;
        echo "Error en fila $fila: " . $stmt->error . "\n";
    }
}

fclose($handle);

echo "Proceso finalizado.\n";
echo "Filas insertadas: $insertados\n";
echo "Errores: $errores\n";
?>