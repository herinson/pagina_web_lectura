<?php
$pathToRoot = "../";
include '../auth_check.php';
check_auth($pathToRoot);
include '../conexion.php';
include '../header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0"><i class="fas fa-lock me-2 text-primary"></i> Cambiar Mi Contraseña</h4>
            </div>
            <div class="card-body p-4">
                <form id="formCambiarPass">
                    <input type="hidden" name="accion" value="cambiar_mi_pass">
                    
                    <div class="mb-3">
                        <label class="form-label">Contraseña Actual</label>
                        <input type="password" name="pass_actual" class="form-control" required>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" name="pass_nueva" id="pass_nueva" class="form-control" required minlength="6">
                        <small class="text-muted">Mínimo 6 caracteres.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" name="pass_confirmar" class="form-control" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>
$(document).ready(function() {
    $('#formCambiarPass').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'acciones_usuarios.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    Swal.fire('Éxito', res.message, 'success').then(function() {
                        $('#formCambiarPass')[0].reset();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error, xhr.responseText);
                Swal.fire('Error de Conexión', 'No se pudo cambiar la contraseña. Verifique su conexión.', 'error');
            }
        });
    });
});
</script>