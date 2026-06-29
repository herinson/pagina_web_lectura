<?php
session_start();
require_once __DIR__ . "/conexion.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

$id_usuario      = $_SESSION['id_usuario'];
$nombre_usuario  = $_SESSION['nombre'];
$rol_usuario     = $_SESSION['rol'];
$id_oficina      = $_SESSION['id_oficina'];
$nombre_oficina  = $_SESSION['nombre_oficina'] ?? 'No asignada';
$numero_empleado = $_SESSION['numero_empleado'] ?? 'No asignado';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_lectura'])) {
    if (isset($_POST['update'])) {
        $id_lectura      = $_POST['id_lectura'];
        $lectura_actual  = $_POST['lectura_actual'];
        $fecha_lectura   = $_POST['fecha_lectura'];
        $observacion     = $_POST['observacion'];
        $anomalia2       = $_POST['anomalia2'];
        $estado          = $_POST['estado'];

        $sqlUpdate = "UPDATE lecturas SET lectura_actual = ?, fecha_lectura = ?, observacion = ?, anomalia2 = ?, estado = ? WHERE id_lectura = ?";
        $paramsUp = array($lectura_actual, $fecha_lectura, $observacion, $anomalia2, $estado, $id_lectura);
        $stmtUp = sqlsrv_query($conn, $sqlUpdate, $paramsUp);

        if ($stmtUp === false) {
            $update_message = "<div class='alert alert-danger'>Error al actualizar la lectura.</div>";
        } else {
            $update_message = "<div class='alert alert-success'>Lectura actualizada correctamente.</div>";
        }
    } elseif (isset($_POST['delete'])) {
        $id_lectura = $_POST['id_lectura'];
        $sqlDelete = "DELETE FROM lecturas WHERE id_lectura = ?";
        $paramsDel = array($id_lectura);
        $stmtDel = sqlsrv_query($conn, $sqlDelete, $paramsDel);

        if ($stmtDel === false) {
            $update_message = "<div class='alert alert-danger'>Error al eliminar la lectura.</div>";
        } else {
            $update_message = "<div class='alert alert-success'>Lectura eliminada correctamente.</div>";
        }
    }
}

$sql = "SELECT
            l.id_lectura, l.nis, l.nic, l.numero_medidor, l.ruta, l.itinerario,
            l.lectura_actual, l.anomalia_actual, l.oficina, l.fecha_lectura, l.observacion,
            l.anomalia2, u.nombre_completo AS supervisor, l.estado
        FROM lecturas l
        INNER JOIN oficinas o ON l.oficina = o.codigo
        LEFT JOIN usuarios u ON l.supervisor_asignado = u.id_usuario";

$params = [];
$where_clauses = [];

if ($rol_usuario !== 'admin') {
    $where_clauses[] = "o.id_oficina = ?";
    $params[] = $id_oficina;
}

if (!empty($_GET['filtro_nis'])) {
    $where_clauses[] = "l.nis LIKE ?";
    $params[] = '%' . $_GET['filtro_nis'] . '%';
}

if (!empty($_GET['filtro_medidor'])) {
    $where_clauses[] = "l.numero_medidor LIKE ?";
    $params[] = '%' . $_GET['filtro_medidor'] . '%';
}

if (!empty($_GET['filtro_centro'])) {
    $where_clauses[] = "l.centro_lectura = ?";
    $params[] = $_GET['filtro_centro'];
}

if (!empty($_GET['filtro_ruta'])) {
    $where_clauses[] = "l.ruta LIKE ?";
    $params[] = '%' . $_GET['filtro_ruta'] . '%';
}

if (!empty($_GET['filtro_estado'])) {
    if ($_GET['filtro_estado'] === 'Pendiente de envío') {
        $where_clauses[] = "(l.estado = 'Pendiente de envío' OR l.estado = 'pendiente')";
    } else {
        $where_clauses[] = "l.estado = ?";
        $params[] = $_GET['filtro_estado'];
    }
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$stmt = sqlsrv_query($conn, $sql, $params);

$centros_sql = "SELECT DISTINCT centro_lectura FROM lecturas WHERE centro_lectura IS NOT NULL ORDER BY centro_lectura ASC";
$centros_stmt = sqlsrv_query($conn, $centros_sql);
$centros = [];
while ($row = sqlsrv_fetch_array($centros_stmt, SQLSRV_FETCH_ASSOC)) {
    $centros[] = $row;
}

require_once __DIR__ . "/header.php";
?>

<?php
if (isset($update_message)) {
    echo $update_message;
}

if (isset($_GET['success']) && $_GET['success'] === 'delete_all') {
    echo "<div class='alert alert-success'>Todos los registros han sido eliminados correctamente.</div>";
} elseif (isset($_GET['error']) && $_GET['error'] === 'delete_all_failed') {
    echo "<div class='alert alert-danger'>Error al eliminar los registros.</div>";
} elseif (isset($_GET['error']) && $_GET['error'] === 'unauthorized') {
    echo "<div class='alert alert-danger'>No tiene permisos para realizar esta acción.</div>";
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Lecturas – Oficina: <?php echo htmlspecialchars($nombre_oficina); ?></h3>
    <div>
        <strong>Usuario:</strong> <?php echo htmlspecialchars($nombre_usuario); ?><br>
        <strong>No. Empleado:</strong> <?php echo htmlspecialchars($numero_empleado); ?>
    </div>
</div>

<?php if (isset($rol_usuario) && $rol_usuario === 'admin'): ?>
<div class="mb-3">
    <a href="#" id="exportar_excel" class="btn btn-info">Exportar a Excel</a>
    <a href="eliminar_todo.php" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar TODOS los registros? Esta acción no se puede deshacer.');">Eliminar Todo</a>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        Filtros de Búsqueda
    </div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="row align-items-end">
                <div class="col-md-2">
                    <label for="filtro_nis" class="form-label">NIS</label>
                    <input type="text" class="form-control" name="filtro_nis" id="filtro_nis" value="<?= htmlspecialchars($_GET['filtro_nis'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label for="filtro_medidor" class="form-label">Número de Medidor</label>
                    <input type="text" class="form-control" name="filtro_medidor" id="filtro_medidor" value="<?= htmlspecialchars($_GET['filtro_medidor'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label for="filtro_centro" class="form-label">Centro de Lectura</label>
                    <select class="form-select" name="filtro_centro" id="filtro_centro">
                        <option value="">Todos</option>
                        <?php foreach ($centros as $centro): ?>
                            <option value="<?= htmlspecialchars($centro['centro_lectura']) ?>" <?= ($_GET['filtro_centro'] ?? '') == $centro['centro_lectura'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($centro['centro_lectura']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filtro_ruta" class="form-label">Ruta</label>
                    <input type="text" class="form-control" name="filtro_ruta" id="filtro_ruta" value="<?= htmlspecialchars($_GET['filtro_ruta'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label for="filtro_estado" class="form-label">Estado Factura</label>
                    <select class="form-select" name="filtro_estado" id="filtro_estado">
                        <option value="">Todos</option>
                        <option value="Pendiente de envío" <?= ($_GET['filtro_estado'] ?? '') === 'Pendiente de envío' ? 'selected' : '' ?>>Pendiente de envío</option>
                        <option value="visitado" <?= ($_GET['filtro_estado'] ?? '') === 'visitado' ? 'selected' : '' ?>>Visitado</option>
                        <option value="resuelto" <?= ($_GET['filtro_estado'] ?? '') === 'resuelto' ? 'selected' : '' ?>>Resuelto</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    <a href="lecturas_oficina.php" class="btn btn-secondary w-100 mt-2">Limpiar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>NIS</th>
                <th>NIC</th>
                <th>Medidor</th>
                <th>Ruta</th>
                <th>Itinerario</th>
                <th>Lectura</th>
                <th>Anomalía</th>
                <th>Oficina</th>
                <th>Fecha Lectura</th>
                <th>Observación</th>
                <th>Anomalía 2</th>
                <th>Supervisor</th>
                <th>Estado Factura</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($stmt && sqlsrv_has_rows($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['nis'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['nic'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['numero_medidor'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['ruta'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['itinerario'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['lectura_actual'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['anomalia_actual'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['oficina'] ?? '') ?></td>
                    <td><?= $row['fecha_lectura'] ? $row['fecha_lectura']->format('Y-m-d') : '' ?></td>
                    <td><?= htmlspecialchars($row['observacion'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['anomalia2'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['supervisor'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['estado'] ?? '') ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal"
                                data-id="<?= $row['id_lectura'] ?>"
                                data-lectura="<?= htmlspecialchars($row['lectura_actual'] ?? '') ?>"
                                data-fecha="<?= $row['fecha_lectura'] ? $row['fecha_lectura']->format('Y-m-d') : '' ?>"
                                data-observacion="<?= htmlspecialchars($row['observacion'] ?? '') ?>"
                                data-anomalia2="<?= htmlspecialchars($row['anomalia2'] ?? '') ?>"
                                data-estado="<?= htmlspecialchars($row['estado'] ?? '') ?>">
                            Editar
                        </button>
                    </td>
                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='13' class='text-center'>No hay lecturas para esta oficina.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal de Edición -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Lectura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="id_lectura" id="edit-id_lectura">
                    <div class="mb-3">
                        <label for="edit-lectura_actual" class="form-label">Lectura Actual:</label>
                        <input type="number" class="form-control" name="lectura_actual" id="edit-lectura_actual" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-fecha_lectura" class="form-label">Fecha Lectura:</label>
                        <input type="date" class="form-control" name="fecha_lectura" id="edit-fecha_lectura" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-observacion" class="form-label">Observación:</label>
                        <textarea class="form-control" name="observacion" id="edit-observacion"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-anomalia2" class="form-label">Anomalía 2:</label>
                        <input type="text" class="form-control" name="anomalia2" id="edit-anomalia2">
                    </div>
                    <div class="mb-3">
                        <label for="edit-estado" class="form-label">Estado Factura:</label>
                        <input type="text" class="form-control" name="estado" id="edit-estado" value="Pendiente de envío" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este registro?');">Eliminar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" name="update" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;

        document.getElementById('edit-id_lectura').value = button.getAttribute('data-id');
        document.getElementById('edit-lectura_actual').value = button.getAttribute('data-lectura');
        document.getElementById('edit-fecha_lectura').value = button.getAttribute('data-fecha');
        document.getElementById('edit-observacion').value = button.getAttribute('data-observacion');
        document.getElementById('edit-anomalia2').value = button.getAttribute('data-anomalia2');
        document.getElementById('edit-estado').value = button.getAttribute('data-estado');
    });

    document.getElementById('exportar_excel').addEventListener('click', function(e) {
        e.preventDefault();

        const filtro_nis = document.getElementById('filtro_nis').value;
        const filtro_medidor = document.getElementById('filtro_medidor').value;
        const filtro_centro = document.getElementById('filtro_centro').value;
        const filtro_ruta = document.getElementById('filtro_ruta').value;
        const filtro_estado = document.getElementById('filtro_estado').value;

        const params = new URLSearchParams({
            filtro_nis,
            filtro_medidor,
            filtro_centro,
            filtro_ruta,
            filtro_estado
        });

        window.location.href = 'exportar_excel.php?' + params.toString();
    });
});
</script>

<?php
require_once __DIR__ . "/footer.php";
?>
