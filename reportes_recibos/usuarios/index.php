<?php
$pathToRoot = "../";
include '../auth_check.php';
check_role('ADMINISTRADOR', $pathToRoot);
include '../conexion.php';
include '../header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Usuarios</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="nuevoUsuario()">
        <i class="fas fa-user-plus me-2"></i> Nuevo Usuario
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table id="tablaUsuarios" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Oficina</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT u.*, 
                        (SELECT COUNT(*) FROM reportes r WHERE r.usuario_creador = u.id OR r.usuario_modificacion = u.id) as total_reportes,
                        (SELECT COUNT(*) FROM historial_estados h WHERE h.usuario_id = u.id) as total_historial,
                        (SELECT COUNT(*) FROM solicitudes_password s WHERE s.usuario_id = u.id OR s.admin_id = u.id) as total_solicitudes
                        FROM usuarios u 
                        ORDER BY u.id DESC";
                $res = $conexion->query($sql);
                while($u = $res->fetch_assoc()):
                    $hasRelated = ($u['total_reportes'] + $u['total_historial'] + $u['total_solicitudes']) > 0;
                    $isInactive = $u['estado_usuario'] === 'INACTIVO';
                ?>
                <tr class="<?php echo $isInactive ? 'text-muted italic' : ''; ?>">
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($u['usuario']); ?></td>
                    <td><?php echo htmlspecialchars($u['oficina_usuario'] ?: '-'); ?></td>
                    <td><?php echo $u['rol']; ?></td>
                    <td>
                        <span class="badge <?php echo $isInactive ? 'bg-danger' : 'bg-success'; ?>">
                            <?php echo $u['estado_usuario']; ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" title="Editar" onclick='editarUsuario(<?php echo json_encode($u); ?>)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        
                        <?php if($u['usuario'] !== $_SESSION['user_usuario']): ?>
                            <!-- Botón Toggle Activo/Inactivo -->
                            <button class="btn btn-sm <?php echo $isInactive ? 'btn-success' : 'btn-secondary'; ?>" 
                                    title="<?php echo $isInactive ? 'Activar' : 'Desactivar'; ?>"
                                    onclick="toggleEstado(<?php echo $u['id']; ?>, '<?php echo $u['estado_usuario']; ?>')">
                                <i class="fas <?php echo $isInactive ? 'fa-user-check' : 'fa-user-slash'; ?>"></i>
                            </button>

                            <!-- Botón Eliminar Físico (Solo si no tiene registros) -->
                            <?php if(!$hasRelated): ?>
                            <button class="btn btn-sm btn-danger" title="Eliminar Permanentemente" onclick="eliminarUsuario(<?php echo $u['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formUsuario">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="usuario_id">
                    <input type="hidden" name="accion" id="accion" value="guardar">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usuario (Login)</label>
                        <input type="text" name="usuario" id="usuario_login" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                        <small class="text-muted" id="passHelp">Requerido para nuevos usuarios.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Oficina del Usuario</label>
                        <select name="oficina_usuario" id="oficina_usuario_select" class="form-select">
                            <option value="">Ninguna</option>
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
                        <label class="form-label">Rol</label>
                        <select name="rol" id="rol" class="form-select" required>
                            <option value="USUARIO">USUARIO</option>
                            <option value="ENCARGADO DE LECTURA">ENCARGADO DE LECTURA</option>
                            <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>
$(document).ready(function() {
    $('#tablaUsuarios').DataTable({
        "language": {
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "emptyTable": "No hay datos disponibles en la tabla",
            "zeroRecords": "No se encontraron coincidencias"
        }
    });

    $('#formUsuario').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'acciones_usuarios.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    Swal.fire('Éxito', res.message, 'success').then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error, xhr.responseText);
                Swal.fire('Error de Conexión', 'No se pudo guardar el usuario. Verifique su conexión.', 'error');
            }
        });
    });
});

function toggleEstado(id, actual) {
    var nuevo = (actual === 'ACTIVO') ? 'INACTIVO' : 'ACTIVO';
    var texto = (nuevo === 'INACTIVO') ? 'El usuario no podrá iniciar sesión.' : 'El usuario recuperará el acceso.';
    
    Swal.fire({
        title: '¿' + (nuevo === 'INACTIVO' ? 'Desactivar' : 'Activar') + ' usuario?',
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, confirmar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: 'acciones_usuarios.php',
                type: 'POST',
                data: { accion: 'toggle_estado', id: id, estado: nuevo },
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        Swal.fire('Éxito', res.message, 'success').then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error, xhr.responseText);
                    Swal.fire('Error de Conexión', 'No se pudo cambiar el estado del usuario.', 'error');
                }
            });
        }
    });
}

function nuevoUsuario() {
    $('#formUsuario')[0].reset();
    $('#usuario_id').val('');
    $('#accion').val('guardar');
    $('#modalTitle').text('Nuevo Usuario');
    $('#password').attr('required', true);
    $('#passHelp').text('Requerido para nuevos usuarios.');
}

function editarUsuario(data) {
    $('#usuario_id').val(data.id);
    $('#nombre').val(data.nombre);
    $('#usuario_login').val(data.usuario);
    $('#oficina_usuario_select').val(data.oficina_usuario);
    $('#rol').val(data.rol);
    $('#password').val('');
    $('#password').attr('required', false);
    $('#accion').val('editar');
    $('#modalTitle').text('Editar Usuario');
    $('#passHelp').text('Dejar en blanco para no cambiar la contraseña.');
    $('#modalUsuario').modal('show');
}

function eliminarUsuario(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "El usuario será eliminado permanentemente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: 'acciones_usuarios.php',
                type: 'POST',
                data: { accion: 'eliminar', id: id },
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        Swal.fire('Eliminado', res.message, 'success').then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        }
    });
}
</script>