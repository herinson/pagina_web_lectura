<?php
$pathToRoot = "../";
include '../auth_check.php';
check_role('ADMINISTRADOR', $pathToRoot);
include '../conexion.php';
include '../header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Solicitudes de Restablecimiento</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table id="tablaSolicitudes" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario Solicitante</th>
                    <th>Fecha Solicitud</th>
                    <th>Estado</th>
                    <th>Atendido por</th>
                    <th>Fecha Resolución</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT s.*, u.nombre as solicitante, u.usuario as solicitante_user, a.nombre as administrador 
                        FROM solicitudes_password s 
                        JOIN usuarios u ON s.usuario_id = u.id 
                        LEFT JOIN usuarios a ON s.admin_id = a.id 
                        ORDER BY s.estado DESC, s.fecha_solicitud DESC";
                $res = $conexion->query($sql);
                while($s = $res->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $s['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($s['solicitante']); ?></strong><br>
                        <small class="text-muted"><?php echo htmlspecialchars($s['solicitante_user']); ?></small>
                    </td>
                    <td><?php echo $s['fecha_solicitud']; ?></td>
                    <td>
                        <span class="badge <?php echo $s['estado'] == 'PENDIENTE' ? 'bg-warning text-dark' : 'bg-success'; ?>">
                            <?php echo $s['estado']; ?>
                        </span>
                    </td>
                    <td><?php echo $s['administrador'] ?: '-'; ?></td>
                    <td><?php echo $s['fecha_completada'] ?: '-'; ?></td>
                    <td>
                        <?php if($s['estado'] == 'PENDIENTE'): ?>
                        <button class="btn btn-sm btn-primary" onclick="restablecer(<?php echo $s['id']; ?>, '<?php echo htmlspecialchars($s['solicitante_user']); ?>')">
                            <i class="fas fa-undo me-1"></i> Restablecer
                        </button>
                        <?php else: ?>
                        <span class="text-success"><i class="fas fa-check-circle"></i></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>
$(document).ready(function() {
    $('#tablaSolicitudes').DataTable({
        "order": [[3, "desc"], [2, "desc"]],
        "language": {
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });
});

function restablecer(id, user) {
    Swal.fire({
        title: '¿Restablecer contraseña?',
        text: "La contraseña de " + user + " será: Edenorte123",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, restablecer',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: 'acciones_usuarios.php',
                type: 'POST',
                data: { accion: 'restablecer_pass', id: id },
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        Swal.fire('Completado', res.message, 'success').then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error, xhr.responseText);
                    Swal.fire('Error de Conexión', 'No se pudo restablecer la contraseña.', 'error');
                }
            });
        }
    });
}
</script>