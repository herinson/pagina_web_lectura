<?php
$pathToRoot = "../";
include '../auth_check.php';
check_auth($pathToRoot);
include '../conexion.php';
include '../header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Reportes</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalReporte" onclick="nuevoReporte()">
        <i class="fas fa-plus me-2"></i> Nuevo Reporte
    </button>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form id="formFiltros" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Oficina</label>
                <select id="filtroOficina" class="form-select select2-oficinas">
                    <option value="">Todas las Oficinas</option>
                    <option value="2542 - VALVERDE MAO" <?php echo ($_SESSION['user_oficina'] == '2542 - VALVERDE MAO') ? 'selected' : ''; ?>>2542 - VALVERDE MAO</option>
                    <option value="2543 - STGO.RODRIGUEZ" <?php echo ($_SESSION['user_oficina'] == '2543 - STGO.RODRIGUEZ') ? 'selected' : ''; ?>>2543 - STGO.RODRIGUEZ</option>
                    <option value="2544 - MONTECRISTI" <?php echo ($_SESSION['user_oficina'] == '2544 - MONTECRISTI') ? 'selected' : ''; ?>>2544 - MONTECRISTI</option>
                    <option value="2545 - DAJABON" <?php echo ($_SESSION['user_oficina'] == '2545 - DAJABON') ? 'selected' : ''; ?>>2545 - DAJABON</option>
                    <option value="2561 - L. CABRERA" <?php echo ($_SESSION['user_oficina'] == '2561 - L. CABRERA') ? 'selected' : ''; ?>>2561 - L. CABRERA</option>
                    <option value="2562 - VILLA VASQUEZ" <?php echo ($_SESSION['user_oficina'] == '2562 - VILLA VASQUEZ') ? 'selected' : ''; ?>>2562 - VILLA VASQUEZ</option>
                    <option value="2563 - ESPERANZA" <?php echo ($_SESSION['user_oficina'] == '2563 - ESPERANZA') ? 'selected' : ''; ?>>2563 - ESPERANZA</option>
                    <option value="2564 - PARTIDO" <?php echo ($_SESSION['user_oficina'] == '2564 - PARTIDO') ? 'selected' : ''; ?>>2564 - PARTIDO</option>
                    <option value="2565 - LAG SALADA" <?php echo ($_SESSION['user_oficina'] == '2565 - LAG SALADA') ? 'selected' : ''; ?>>2565 - LAG SALADA</option>
                    <option value="2566 - MONCION" <?php echo ($_SESSION['user_oficina'] == '2566 - MONCION') ? 'selected' : ''; ?>>2566 - MONCION</option>
                    <option value="2567 - CASTANUELA" <?php echo ($_SESSION['user_oficina'] == '2567 - CASTANUELA') ? 'selected' : ''; ?>>2567 - CASTANUELA</option>
                    <option value="2568 - V ALMACIGOS" <?php echo ($_SESSION['user_oficina'] == '2568 - V ALMACIGOS') ? 'selected' : ''; ?>>2568 - V ALMACIGOS</option>
                    <option value="2569 - GUAYUBIN" <?php echo ($_SESSION['user_oficina'] == '2569 - GUAYUBIN') ? 'selected' : ''; ?>>2569 - GUAYUBIN</option>
                    <option value="2570 - M D S.CRUZ" <?php echo ($_SESSION['user_oficina'] == '2570 - M D S.CRUZ') ? 'selected' : ''; ?>>2570 - M D S.CRUZ</option>
                    <option value="2571 - MAIZAL" <?php echo ($_SESSION['user_oficina'] == '2571 - MAIZAL') ? 'selected' : ''; ?>>2571 - MAIZAL</option>
                    <option value="2573 - MANZANILLO" <?php echo ($_SESSION['user_oficina'] == '2573 - MANZANILLO') ? 'selected' : ''; ?>>2573 - MANZANILLO</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">NIC</label>
                <input type="text" id="filtroNic" class="form-control" placeholder="Buscar NIC">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Desde</label>
                <input type="date" id="filtroDesde" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Hasta</label>
                <input type="date" id="filtroHasta" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Estado Factura</label>
                <select id="filtroEstadoFactura" class="form-select">
                    <option value="">Todos</option>
                    <option value="Pendiente de envío">Pendiente de envío</option>
                    <option value="Enviada">Enviada</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Ruta</label>
                <select id="filtroRuta" class="form-select">
                    <option value="">Todas</option>
                    <?php for($i=31; $i<=52; $i++): ?>
                    <option value="Ruta <?php echo $i; ?>">Ruta <?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label small fw-bold">Itin.</label>
                <select id="filtroItinerario" class="form-select">
                    <option value="">Todos</option>
                    <?php for($i=1; $i<=5; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Área</label>
                <select id="filtroArea" class="form-select">
                    <option value="">Todas</option>
                    <option value="Comercial">Comercial</option>
                    <option value="Caja">Caja</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Localidad</label>
                <input type="text" id="filtroLocalidad" class="form-control" placeholder="Buscar Localidad">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-secondary me-2" onclick="limpiarFiltros()">
                    <i class="fas fa-eraser me-1"></i> Limpiar
                </button>
                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Exportar
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportar('csv')">CSV</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportar('excel')">Excel</a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla -->
<div class="card">
    <div class="card-body">
        <table id="tablaReportes" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NIC</th>
                    <th>Localidad</th>
                    <th>Oficina</th>
                    <th>Fecha</th>
                    <th>Area</th>
                    <th>Mes Reclamado</th>
                    <th>Ruta</th>
                    <th>Itin.</th>
                    <th>Estado Factura</th>
                    <th>Evid.</th>
                    <th>Audit</th>
                    <th>Creador</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Reporte -->
<div class="modal fade" id="modalReporte" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formReporte">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="reporte_id">
                    <input type="hidden" name="accion" id="accion" value="guardar">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">NIC <span class="text-danger">*</span></label>
                            <input type="text" name="nic" id="nic" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Localidad <span class="text-danger">*</span></label>
                            <input type="text" name="localidad" id="localidad" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Oficina <span class="text-danger">*</span></label>
                            <select name="oficina" id="oficina" class="form-select select2-oficinas" required>
                                <option value="">Seleccione Oficina</option>
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
                        <div class="col-md-6">
                            <label class="form-label">Fecha Reporte <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" id="fecha" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Area <span class="text-danger">*</span></label>
                            <select name="area" id="area" class="form-select" required>
                                <option value="Comercial">Comercial</option>
                                <option value="Caja">Caja</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" id="telefono" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mes Reclamado <span class="text-danger">*</span></label>
                            <?php $currentYear = date('Y'); ?>
                            <select name="mes_reclamado[]" id="mes_reclamado" class="form-select select2-multiple" multiple="multiple" required>
                                <option value="Enero <?php echo $currentYear; ?>">Enero <?php echo $currentYear; ?></option>
                                <option value="Febrero <?php echo $currentYear; ?>">Febrero <?php echo $currentYear; ?></option>
                                <option value="Marzo <?php echo $currentYear; ?>">Marzo <?php echo $currentYear; ?></option>
                                <option value="Abril <?php echo $currentYear; ?>">Abril <?php echo $currentYear; ?></option>
                                <option value="Mayo <?php echo $currentYear; ?>">Mayo <?php echo $currentYear; ?></option>
                                <option value="Junio <?php echo $currentYear; ?>">Junio <?php echo $currentYear; ?></option>
                                <option value="Julio <?php echo $currentYear; ?>">Julio <?php echo $currentYear; ?></option>
                                <option value="Agosto <?php echo $currentYear; ?>">Agosto <?php echo $currentYear; ?></option>
                                <option value="Septiembre <?php echo $currentYear; ?>">Septiembre <?php echo $currentYear; ?></option>
                                <option value="Octubre <?php echo $currentYear; ?>">Octubre <?php echo $currentYear; ?></option>
                                <option value="Noviembre <?php echo $currentYear; ?>">Noviembre <?php echo $currentYear; ?></option>
                                <option value="Diciembre <?php echo $currentYear; ?>">Diciembre <?php echo $currentYear; ?></option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ruta <span class="text-danger">*</span></label>
                            <select name="ruta" id="ruta" class="form-select" required>
                                <option value="">Seleccione Ruta</option>
                                <?php for($i=31; $i<=52; $i++): ?>
                                <option value="Ruta <?php echo $i; ?>">Ruta <?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Itinerario <span class="text-danger">*</span></label>
                            <select name="itinerario" id="itinerario" class="form-select" required>
                                <option value="">Seleccione Itinerario</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado Factura <span class="text-danger">*</span></label>
                            <select name="estado_factura" id="estado_factura" class="form-select" required <?php echo !in_array($_SESSION['user_rol'], ['ADMINISTRADOR', 'ENCARGADO DE LECTURA']) ? 'disabled' : ''; ?>>
                                <option value="Pendiente de envío">Pendiente de envío</option>
                                <option value="Enviada">Enviada</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarReporte">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cambio Estado -->
<div class="modal fade" id="modalCambioEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formCambioEstado" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Actualizar Estado de Factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="status_reporte_id">
                    <input type="hidden" name="accion" value="cambiar_estado">
                    
                    <div class="mb-3">
                        <label class="form-label">Nuevo Estado</label>
                        <select name="nuevo_estado" id="nuevo_estado" class="form-select" required>
                            <option value="Pendiente de envío">Pendiente de envío</option>
                            <option value="Enviada">Enviada</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Comentario de Resolución</label>
                        <textarea name="comentario" class="form-control" rows="3" placeholder="Opcional"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Evidencia (JPG, PNG, PDF)</label>
                        <input type="file" name="evidencia" id="input_evidencia" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">Tamaño máximo 5MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Visor Evidencia -->
<div class="modal fade" id="modalEvidencia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Evidencia y Comentarios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="evidencia_body">
                <!-- Se carga vía AJAX -->
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script>
$(document).ready(function() {
    var table = $('#tablaReportes').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [[0, "desc"]],
        "ajax": {
            "url": "listar.php",
            "data": function(d) {
                d.oficina = $('#filtroOficina').val();
                d.nic = $('#filtroNic').val();
                d.desde = $('#filtroDesde').val();
                d.hasta = $('#filtroHasta').val();
                d.estado_factura = $('#filtroEstadoFactura').val();
                d.ruta = $('#filtroRuta').val();
                d.itinerario = $('#filtroItinerario').val();
                d.area = $('#filtroArea').val();
                d.localidad = $('#filtroLocalidad').val();
            }
        },
        "columns": [
            { "data": "id" },
            { "data": "nic" },
            { "data": "localidad" },
            { "data": "oficina" },
            { "data": "fecha" },
            { "data": "area" },
            { "data": "mes_reclamado" },
            { "data": "ruta" },
            { "data": "itinerario" },
            { 
                "data": "estado_factura",
                "render": function(data, type, row) {
                    var badge = data == 'Enviada' ? 'bg-success' : 'bg-warning text-dark';
                    <?php if (in_array($_SESSION['user_rol'], ['ADMINISTRADOR', 'ENCARGADO DE LECTURA'])): ?>
                    return '<span class="badge ' + badge + ' border-0 cursor-pointer" onclick="cambiarEstado('+row.id+', \''+data+'\')" style="cursor:pointer">' + data + ' <i class="fas fa-sync-alt ms-1"></i></span>';
                    <?php else: ?>
                    return '<span class="badge ' + badge + '">' + data + '</span>';
                    <?php endif; ?>
                }
            },
            {
                "data": "id",
                "render": function(data, type, row) {
                    if (row.total_historial > 0) {
                        return '<button class="btn btn-sm btn-outline-info" onclick="verEvidencia('+data+')"><i class="fas fa-paperclip"></i></button>';
                    }
                    return '-';
                }
            },
            { "data": "estado" },
            { "data": "creador" },
            { 
                "data": null,
                "render": function(data, type, row) {
                    var canEdit = true;
                    <?php if ($_SESSION['user_rol'] === 'USUARIO'): ?>
                    // Manual parsing for browser compatibility (yyyy-mm-dd hh:mm:ss)
                    var t = row.fecha_registro.split(/[- :]/);
                    var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                    var ahora = new Date();
                    var diffMs = ahora - d;
                    var diffMins = Math.floor(diffMs / 60000);
                    if (diffMins >= 5) canEdit = false;
                    <?php endif; ?>

                    var btnEdit = '';
                    if (canEdit) {
                        // We store the data in the DOM instead of a long onclick string to avoid escaping/quoting issues
                        btnEdit = '<button class="btn btn-sm btn-warning me-1 btn-edit-reporte" data-reporte=\''+JSON.stringify(row).replace(/'/g, "&apos;")+'\'><i class="fas fa-edit"></i></button>';
                    } else {
                        btnEdit = '<button class="btn btn-sm btn-secondary me-1" title="Tiempo de edición expirado" disabled><i class="fas fa-lock"></i></button>';
                    }

                    var btnDelete = '';
                    <?php if ($_SESSION['user_rol'] === 'ADMINISTRADOR'): ?>
                    btnDelete = '<button class="btn btn-sm btn-danger" onclick="eliminarReporte('+row.id+')"><i class="fas fa-trash"></i></button>';
                    <?php endif; ?>
                    return btnEdit + btnDelete;
                }
            }
        ],
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
            "zeroRecords": "No se encontraron coincidencias",
            "processing": "Procesando..."
        }
    });

    // Delegated event for edit button to handle DataTables paging/AJAX
    $('#tablaReportes').on('click', '.btn-edit-reporte', function() {
        var data = $(this).data('reporte');
        editarReporte(data);
    });

    // Initializing Select2 for filters
    $('#filtroOficina').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Handle Select2 initialization inside modals to avoid rendering bugs
    $('#modalReporte').on('shown.bs.modal', function () {
        // Destroy existing to avoid duplicates
        if ($('#oficina').hasClass("select2-hidden-accessible")) {
            $('#oficina').select2('destroy');
        }
        if ($('#mes_reclamado').hasClass("select2-hidden-accessible")) {
            $('#mes_reclamado').select2('destroy');
        }

        $('#oficina').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#modalReporte'),
            placeholder: 'Seleccione Oficina'
        });

        $('#mes_reclamado').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Seleccione meses',
            dropdownParent: $('#modalReporte'),
            allowClear: true,
            closeOnSelect: false
        });
    });

    $('#filtroOficina, #filtroNic, #filtroDesde, #filtroHasta, #filtroEstadoFactura, #filtroRuta, #filtroItinerario, #filtroArea, #filtroLocalidad').on('change keyup', function() {
        table.draw();
    });

    // Real-time validation
    function validarFormulario() {
        var form = document.getElementById('formReporte');
        var isValid = form.checkValidity();
        
        // Custom check for Select2 multiple (sometimes checkValidity doesn't catch it well)
        if ($('#mes_reclamado').val().length === 0) {
            isValid = false;
        }

        $('#btnGuardarReporte').prop('disabled', !isValid);
        return isValid;
    }

    $('#formReporte input, #formReporte select, #formReporte textarea').on('input change', function() {
        validarFormulario();
        if ($(this).prop('required') && !$(this).val()) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Specific listeners for Select2
    $('#oficina, #mes_reclamado').on('change', function() {
        validarFormulario();
    });

    $('#formReporte').on('submit', function(e) {
        e.preventDefault();
        
        var form = this;
        if (!form.checkValidity() || $('#mes_reclamado').val().length === 0) {
            $(form).addClass('was-validated');
            
            // Find missing fields for the message
            var missing = [];
            $(form).find('[required]').each(function() {
                if (!$(this).val() || ($(this).is('select[multiple]') && $(this).val().length === 0)) {
                    var label = $(this).closest('.col-md-6, .col-12').find('label').text().replace(' *', '');
                    missing.push(label);
                }
            });

            Swal.fire('Atención', 'Faltan campos obligatorios: ' + missing.join(', '), 'warning');
            return false;
        }

        var $form = $(this);
        $.ajax({
            url: 'acciones.php',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    Swal.fire('Éxito', res.message, 'success');
                    $('#modalReporte').modal('hide');
                    table.ajax.reload(null, false); // Reload without resetting pagination
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error, xhr.responseText);
                Swal.fire('Error de Conexión', 'No se pudo conectar con el servidor. Verifique su conexión.', 'error');
            }
        });
    });

    $('#formCambioEstado').on('submit', function(e) {
        e.preventDefault();
        
        var fileInput = $('#input_evidencia')[0];
        if (fileInput && fileInput.files.length > 0) {
            var file = fileInput.files[0];
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire('Error', 'El archivo seleccionado es demasiado grande (Máximo 5MB).', 'error');
                return;
            }
        }

        var formData = new FormData(this);
        $.ajax({
            url: 'acciones.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    Swal.fire('Éxito', res.message, 'success');
                    $('#modalCambioEstado').modal('hide');
                    $('#tablaReportes').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error, xhr.responseText);
                Swal.fire('Error de Conexión', 'No se pudo actualizar el estado. Verifique su conexión.', 'error');
            }
        });
    });
});

function nuevoReporte() {
    var form = $('#formReporte');
    form[0].reset();
    form.removeClass('was-validated');
    form.find('.is-invalid').removeClass('is-invalid');
    $('#reporte_id').val('');
    $('#accion').val('guardar');
    $('#modalTitle').text('Nuevo Reporte');
    
    // Reset Select2
    $('#oficina').val('').trigger('change');
    $('#mes_reclamado').val([]).trigger('change');
    
    // Initial validation check
    $('#btnGuardarReporte').prop('disabled', true);
}

function cambiarEstado(id, actual) {
    $('#status_reporte_id').val(id);
    $('#nuevo_estado').val(actual);
    $('#formCambioEstado')[0].reset();
    $('#status_reporte_id').val(id); // reset clears everything, re-set it
    $('#modalCambioEstado').modal('show');
}

function verEvidencia(id) {
    $('#evidencia_body').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
    $('#modalEvidencia').modal('show');
    $.get('acciones.php', { accion: 'get_historial', id: id }, function(res) {
        if(res.success && res.data && res.data.length > 0) {
            var html = '<ul class="list-group">';
            for (var i = 0; i < res.data.length; i++) {
                var h = res.data[i];
                html += '<li class="list-group-item mb-3 border rounded shadow-sm">';
                html += '<div class="d-flex justify-content-between"><strong>' + h.usuario + '</strong> <small>' + h.fecha + '</small></div>';
                html += '<div class="mt-2">Cambió de: <span class="badge bg-secondary">' + h.anterior + '</span> a: <span class="badge bg-primary">' + h.nuevo + '</span></div>';
                if(h.comentario) html += '<div class="mt-2 bg-light p-2 rounded"><em>"' + h.comentario + '"</em></div>';
                if(h.evidencia) {
                    var extParts = h.evidencia.split('.');
                    var ext = extParts[extParts.length - 1].toLowerCase();
                    if(ext === 'jpg' || ext === 'jpeg' || ext === 'png') {
                        html += '<div class="mt-2 text-center"><img src="../reportes/descargar_evidencia.php?file=' + encodeURIComponent(h.evidencia) + '" class="img-fluid rounded border shadow-sm" style="max-height: 400px;"></div>';
                    }
                    html += '<div class="mt-3"><a href="../reportes/descargar_evidencia.php?file=' + encodeURIComponent(h.evidencia) + '" class="btn btn-sm btn-info text-white w-100" target="_blank"><i class="fas fa-download me-2"></i> Descargar Soporte (' + ext.toUpperCase() + ')</a></div>';
                }
                html += '</li>';
            }
            html += '</ul>';
            $('#evidencia_body').html(html);
        } else {
            $('#evidencia_body').html('<div class="alert alert-info border-0 shadow-sm"><i class="fas fa-info-circle me-2"></i> No se encontraron registros de cambios para este reporte.</div>');
        }
    }).fail(function() {
        $('#evidencia_body').html('<div class="alert alert-danger border-0 shadow-sm"><i class="fas fa-exclamation-triangle me-2"></i> Error al conectar con el servidor.</div>');
    });
}

function editarReporte(data) {
    $('#reporte_id').val(data.id);
    $('#nic').val(data.nic);
    $('#localidad').val(data.localidad);
    $('#oficina').val(data.oficina);
    $('#fecha').val(data.fecha);
    $('#area').val(data.area);
    $('#telefono').val(data.telefono);
    if(data.mes_reclamado) {
        $('#mes_reclamado').val(data.mes_reclamado.split(', ')).trigger('change');
    } else {
        $('#mes_reclamado').val(null).trigger('change');
    }
    $('#ruta').val(data.ruta);
    $('#itinerario').val(data.itinerario);
    $('#observaciones').val(data.observaciones);
    $('#estado_factura').val(data.estado_factura);
    $('#accion').val('editar');
    $('#modalTitle').text('Editar Reporte');
    $('#modalReporte').modal('show');
}

function eliminarReporte(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "El reporte será eliminado lógicamente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: 'acciones.php',
                type: 'POST',
                data: { accion: 'eliminar', id: id },
                dataType: 'json',
                success: function(res) {
                    if(res.success) {
                        Swal.fire('Eliminado', res.message, 'success');
                        $('#tablaReportes').DataTable().ajax.reload();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        }
    });
}

function limpiarFiltros() {
    $('#formFiltros')[0].reset();
    $('#tablaReportes').DataTable().draw();
}

function exportar(tipo) {
    var params = $.param({
        oficina: $('#filtroOficina').val(),
        nic: $('#filtroNic').val(),
        desde: $('#filtroDesde').val(),
        hasta: $('#filtroHasta').val(),
        estado_factura: $('#filtroEstadoFactura').val(),
        ruta: $('#filtroRuta').val(),
        itinerario: $('#filtroItinerario').val(),
        area: $('#filtroArea').val(),
        localidad: $('#filtroLocalidad').val()
    });
    window.location.href = '../exports/exportar_' + tipo + '.php?' + params;
}
</script>