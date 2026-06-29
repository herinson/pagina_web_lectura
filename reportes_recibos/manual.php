<?php
$pathToRoot = "";
include 'auth_check.php';
check_auth($pathToRoot);
include 'header.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-5">
            <h1 class="display-4 text-primary fw-bold">Manual de Usuario</h1>
            <p class="lead">Sistema de No Recepción de Facturas - <strong>EDENORTE</strong></p>
            <span class="badge bg-secondary">Versión 3.0</span>
        </div>

        <div class="row">
            <div class="col-lg-3">
                <div class="nav flex-column nav-pills me-3 mb-4 sticky-top" id="v-pills-tab" role="tablist" aria-orientation="vertical" style="top: 20px;">
                    <button class="nav-link active text-start" id="tab-intro" data-bs-toggle="pill" data-bs-target="#content-intro" type="button" role="tab"><i class="fas fa-home me-2"></i> Introducción</button>
                    <button class="nav-link text-start" id="tab-form" data-bs-toggle="pill" data-bs-target="#content-form" type="button" role="tab"><i class="fas fa-edit me-2"></i> Registro de Reportes</button>
                    <button class="nav-link text-start" id="tab-status" data-bs-toggle="pill" data-bs-target="#content-status" type="button" role="tab"><i class="fas fa-tasks me-2"></i> Flujo de Estados</button>
                    <button class="nav-link text-start" id="tab-search" data-bs-toggle="pill" data-bs-target="#content-search" type="button" role="tab"><i class="fas fa-search me-2"></i> Búsqueda y Filtros</button>
                    <button class="nav-link text-start" id="tab-audit" data-bs-toggle="pill" data-bs-target="#content-audit" type="button" role="tab"><i class="fas fa-user-shield me-2"></i> Auditoría</button>
                    <button class="nav-link text-start" id="tab-roles" data-bs-toggle="pill" data-bs-target="#content-roles" type="button" role="tab"><i class="fas fa-users me-2"></i> Roles y Permisos</button>
                    <button class="nav-link text-start" id="tab-export" data-bs-toggle="pill" data-bs-target="#content-export" type="button" role="tab"><i class="fas fa-file-export me-2"></i> Exportación</button>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="tab-content" id="v-pills-tabContent">
                    <!-- Introducción -->
                    <div class="tab-pane fade show active" id="content-intro" role="tabpanel">
                        <h3 class="text-primary border-bottom pb-2 mb-3">1. Introducción</h3>
                        <p>El <strong>Sistema de No Recepción de Facturas</strong> es una herramienta esencial para el Departamento de Lectura y Distribución. Permite centralizar las incidencias reportadas por los clientes, asignarlas a las oficinas correspondientes y realizar un seguimiento hasta su resolución final.</p>
                        <div class="alert alert-info">
                            <i class="fas fa-lightbulb me-2"></i> El sistema garantiza que cada cambio sea registrado, permitiendo una auditoría completa del proceso de gestión de facturas.
                        </div>
                    </div>

                    <!-- Registro de Reportes -->
                    <div class="tab-pane fade" id="content-form" role="tabpanel">
                        <h3 class="text-primary border-bottom pb-2 mb-3">2. Registro de un Nuevo Reporte</h3>
                        <p>Para crear un reporte, haga clic en el botón <strong>"+ Nuevo Reporte"</strong>. El sistema cuenta con una funcionalidad de **Autocompletado por NIC**:</p>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item"><strong>NIC:</strong> Ingrese el número de contrato. Al finalizar o presionar la lupa, el sistema buscará los datos del cliente.</li>
                            <li class="list-group-item"><strong>Localidad:</strong> Se completa automáticamente desde el NIC.</li>
                            <li class="list-group-item"><strong>Oficina:</strong> Se selecciona automáticamente según el registro del cliente.</li>
                            <li class="list-group-item"><strong>Fecha Reporte:</strong> Fecha de la reclamación.</li>
                            <li class="list-group-item"><strong>Teléfono, Ruta e Itinerario:</strong> Se completan automáticamente.</li>
                            <li class="list-group-item"><strong>Mes Reclamado:</strong> Selección múltiple de los meses en reclamación.</li>
                        </ul>
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6><i class="fas fa-info-circle text-info me-2"></i> Nota:</h6>
                                <p class="mb-0 small">Si el NIC no existe en la base de datos de clientes, puede ingresar los datos de forma manual.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Flujo de Estados -->
                    <div class="tab-pane fade" id="content-status" role="tabpanel">
                        <h3 class="text-primary border-bottom pb-2 mb-3">3. Flujo de Estados</h3>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-warning">
                                    <div class="card-header bg-warning text-dark fw-bold">Pendiente de envío</div>
                                    <div class="card-body">
                                        <p class="card-text">Estado inicial. Indica que el reporte ha sido registrado.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-success text-white fw-bold">Enviada</div>
                                    <div class="card-body">
                                        <p class="card-text">Indica finalización. Solo modificable por <strong>Administradores</strong> o <strong>Encargados</strong>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Búsqueda y Filtros -->
                    <div class="tab-pane fade" id="content-search" role="tabpanel">
                        <h3 class="text-primary border-bottom pb-2 mb-3">4. Búsqueda y Filtros</h3>
                        <p>Puede filtrar por múltiples criterios: NIC, Oficina, Rango de Fechas, Estado, Ruta, Itinerario y Localidad.</p>
                    </div>

                    <!-- Auditoría -->
                    <div class="tab-pane fade" id="content-audit" role="tabpanel">
                        <h3 class="text-primary border-bottom pb-2 mb-3">5. Auditoría e Historial</h3>
                        <p>Haciendo clic en el icono de evidencia (clip) en la tabla, puede ver el historial detallado de cambios de estado y las evidencias adjuntas.</p>
                    </div>

                    <!-- Roles -->
                    <div class="tab-pane fade" id="content-roles" role="tabpanel">
                        <h3 class="text-primary border-bottom pb-2 mb-3">6. Roles y Permisos</h3>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Acción</th>
                                        <th class="text-center">Admin</th>
                                        <th class="text-center">Encargado</th>
                                        <th class="text-center">Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>Crear Reportes</td><td class="text-center">✅</td><td class="text-center">✅</td><td class="text-center">✅</td></tr>
                                    <tr><td>Ver todos los reportes</td><td class="text-center">✅</td><td class="text-center">✅</td><td class="text-center">❌</td></tr>
                                    <tr><td>Cambiar Estado Factura</td><td class="text-center">✅</td><td class="text-center">✅</td><td class="text-center">❌</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Exportación -->
                    <div class="tab-pane fade" id="content-export" role="tabpanel">
                        <h3 class="text-primary border-bottom pb-2 mb-3">7. Exportación de Datos</h3>
                        <p>Formatos disponibles:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded text-center">
                                    <i class="fas fa-file-csv fa-3x text-info mb-3"></i>
                                    <h5>CSV</h5>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded text-center">
                                    <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                                    <h5>Excel</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>