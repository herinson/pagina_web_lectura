<?php

include 'conexion.php';

$nic = $_POST['nic'];
$localidad = $_POST['localidad'];
$oficina = $_POST['oficina'];
$fecha = $_POST['fecha'];
$area = $_POST['area'];
$observaciones = $_POST['observaciones'];

$sql = "INSERT INTO reportes
(
    nic,
    localidad,
    oficina,
    fecha,
    area,
    observaciones
)

VALUES

(
    '$nic',
    '$localidad',
    '$oficina',
    '$fecha',
    '$area',
    '$observaciones'
)";

if($conexion->query($sql) === TRUE){

    echo "
    <script>
        alert('Reporte guardado correctamente');
        window.location='index.php';
    </script>
    ";

}else{

    echo "Error: " . $conexion->error;

}

?>