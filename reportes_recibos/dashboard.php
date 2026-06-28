<?php
$pathToRoot = "";
include 'auth_check.php';
check_auth($pathToRoot);
include 'conexion.php';

// Statistics
    $res_total = $conexion->query("SELECT COUNT(*) FROM reportes WHERE estado != 'ELIMINADO'");
    $total_reportes = $res_total ? $res_total->fetch_row()[0] : 0;
$reportes_hoy = $conexion->query("SELECT COUNT(*) FROM reportes WHERE DATE(fecha_registro) = CURDATE() AND estado != 'ELIMINADO'")->fetch_row()[0];
$pendientes = $conexion->query("SELECT COUNT(*) FROM reportes WHERE estado_factura = 'Pendiente de envío' AND estado != 'ELIMINADO'")->fetch_row()[0];
$enviadas = $conexion->query("SELECT COUNT(*) FROM reportes WHERE estado_factura = 'Enviada' AND estado != 'ELIMINADO'")->fetch_row()[0];

// Filter for Office Table
$mes_filtro = $_GET['mes'] ?? 'total';
$where_oficina = "WHERE estado != 'ELIMINADO'";
$params_oficina = [];
$types_oficina = "";

if ($mes_filtro !== 'total') {
    $where_oficina .= " AND MONTH(fecha_registro) = ? AND YEAR(fecha_registro) = YEAR(CURDATE())";
    $params_oficina[] = (int)$mes_filtro;
    $types_oficina = "i";
}

$sql_oficina = "SELECT oficina, 
                COUNT(*) as total,
                SUM(CASE WHEN estado_factura = 'Pendiente de envío' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado_factura = 'Enviada' THEN 1 ELSE 0 END) as enviadas
                FROM reportes 
                $where_oficina 
                GROUP BY oficina";

$stmt_oficina = $conexion->prepare($sql_oficina);
if ($params_oficina) {
    $stmt_oficina->bind_param($types_oficina, ...$params_oficina);
}
$stmt_oficina->execute();
$reportes_por_oficina = $stmt_oficina->get_result();

include 'header.php';
?>

<div class="text-center mb-4">
    <h1 class="display-5 fw-bold text-primary">EDENORTE</h1>
    <h3 class="text-secondary">Sistema de No Recepción de Facturas</h3>
    <h5 class="text-muted">Lectura y Distribución de Facturas</h5>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Total Reportes</h6>
                        <h2 class="mb-0"><?php echo $total_reportes; ?></h2>
                    </div>
                    <i class="fas fa-file-alt fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Pendientes</h6>
                        <h2 class="mb-0"><?php echo $pendientes; ?></h2>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Enviadas</h6>
                        <h2 class="mb-0"><?php echo $enviadas; ?></h2>
                    </div>
                    <i class="fas fa-paper-plane fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Reportes de Hoy</h6>
                        <h2 class="mb-0"><?php echo $reportes_hoy; ?></h2>
                    </div>
                    <i class="fas fa-calendar-day fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Reportes por Oficina</span>
                <form id="filtroMesForm" method="GET" class="d-flex align-items-center">
                    <select name="mes" class="form-select form-select-sm" onchange="document.getElementById('filtroMesForm').submit()">
                        <option value="total" <?php echo $mes_filtro == 'total' ? 'selected' : ''; ?>>Total</option>
                        <option value="1" <?php echo $mes_filtro == '1' ? 'selected' : ''; ?>>Enero</option>
                        <option value="2" <?php echo $mes_filtro == '2' ? 'selected' : ''; ?>>Febrero</option>
                        <option value="3" <?php echo $mes_filtro == '3' ? 'selected' : ''; ?>>Marzo</option>
                        <option value="4" <?php echo $mes_filtro == '4' ? 'selected' : ''; ?>>Abril</option>
                        <option value="5" <?php echo $mes_filtro == '5' ? 'selected' : ''; ?>>Mayo</option>
                        <option value="6" <?php echo $mes_filtro == '6' ? 'selected' : ''; ?>>Junio</option>
                        <option value="7" <?php echo $mes_filtro == '7' ? 'selected' : ''; ?>>Julio</option>
                        <option value="8" <?php echo $mes_filtro == '8' ? 'selected' : ''; ?>>Agosto</option>
                        <option value="9" <?php echo $mes_filtro == '9' ? 'selected' : ''; ?>>Septiembre</option>
                        <option value="10" <?php echo $mes_filtro == '10' ? 'selected' : ''; ?>>Octubre</option>
                        <option value="11" <?php echo $mes_filtro == '11' ? 'selected' : ''; ?>>Noviembre</option>
                        <option value="12" <?php echo $mes_filtro == '12' ? 'selected' : ''; ?>>Diciembre</option>
                    </select>
                </form>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Oficina</th>
                            <th class="text-center">Pend.</th>
                            <th class="text-center">Env.</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $reportes_por_oficina->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['oficina'] ?: 'Sin Oficina'); ?></td>
                            <td class="text-center"><span class="badge bg-warning text-dark"><?php echo $row['pendientes']; ?></span></td>
                            <td class="text-center"><span class="badge bg-success"><?php echo $row['enviadas']; ?></span></td>
                            <td class="text-center fw-bold"><?php echo $row['total']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white font-weight-bold">
                Últimos Reportes
            </div>
            <div class="card-body">
                <?php
                $ultimos = $conexion->query("SELECT r.*, u.nombre as creador FROM reportes r LEFT JOIN usuarios u ON r.usuario_creador = u.id WHERE r.estado != 'ELIMINADO' ORDER BY r.fecha_registro DESC LIMIT 5");
                ?>
                <ul class="list-group list-group-flush">
                    <?php while($u = $ultimos->fetch_assoc()): ?>
                    <li class="list-group-item px-0">
                        <div class="d-flex justify-content-between">
                            <strong>NIC: <?php echo htmlspecialchars($u['nic'] ?? ''); ?></strong>
                            <small class="text-muted"><?php echo $u['fecha_registro']; ?></small>
                        </div>
                        <div class="text-muted small">Por: <?php echo htmlspecialchars($u['creador'] ?? ''); ?></div>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>