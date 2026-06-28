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
            <span class="badge bg-secondary">Versión 2.0</span>
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
                        <p>Para crear un reporte, haga clic en el botón <strong>"+ Nuevo Reporte"</strong>. A continuación, se detallan los campos:</p>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item"><strong>NIC:</strong> Número de contrato del cliente. Campo obligatorio.</li>
                            <li class="list-group-item"><strong>Localidad:</strong> Sector o zona de la incidencia.</li>
                            <li class="list-group-item"><strong>Oficina:</strong> Selección de una de las 16 oficinas predefinidas.</li>
                            <li class="list-group-item"><strong>Area:</strong> Indica si el reporte viene de <em>Comercial</em> o <em>Caja</em>.</li>
                            <li class="list-group-item"><strong>Teléfono:</strong> Contacto para seguimiento.</li>
                            <li class="list-group-item"><strong>Observaciones:</strong> Detalles adicionales del caso.</li>
                        </ul>
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6><i class="fas fa-check-circle text-success me-2"></i> Recomendación:</h6>
                                <p class="mb-0 small">Siempre verifique que el NIC sea correcto antes de guardar, ya que es el identificador principal para las búsquedas.</p>
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
                                        <p class="card-text">Es el estado inicial. Indica que la incidencia ha sido registrada pero la factura aún no ha salido a distribución o no ha sido gestionada.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-success text-white fw-bold">Enviada</div>
                                    <div class="card-body">
                                        <p class="card-text">Indica que el proceso ha concluido exitosamente. Solo puede ser asignado por el <strong>Administrador</strong> o el <strong>Encargado de Lectura</strong>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Búsqueda y Filtros -->
                    <div class="tab-pane fade" id="content-search" role="tabpanel">
                        <h3 class="text-primary border-bottom pb-2 mb-3">4. Búsqueda y Filtros</h3>
                        <p>El sistema está optimizado para encontrar reportes entre miles de registros:</p>
                        <ul>
                            <li><strong>Buscador Global:</strong> Filtra por NIC, Localidad, Oficina o Área en tiempo real.</li>
                            <li><strong>Filtros por Fecha:</strong> Permite analizar periodos específicos (ej. reportes del último mes).</li>
                            <li><strong>Paginación:</strong> Use los controles al pie de la tabla para navegar entre páginas de resultados.</li>
                        </ul>
                    </div>

                    <!-- Auditoría -->
                    <div class="tab-pane fade" id="content-audit" role="tabpanel">
                        <h3 class="text-primary border-bottom pb-2 mb-3">5. Auditoría e Historial</h3>
                        <p>Cada reporte cuenta con una trazabilidad completa:</p>
                        <div class="list-group mb-4">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Registro de Creación
                                <span class="badge bg-primary rounded-pill">Automático</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Historial de Estados
                                <span class="badge bg-primary rounded-pill">Auditado</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Última Modificación
                                <span class="badge bg-primary rounded-pill">Usuario + Fecha</span>
                            </div>
                        </div>
                        <p class="text-muted small"><em>* El sistema guarda el historial detallado de quién cambió un estado de "Pendiente" a "Enviada".</em></p>
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
                                    <tr><td>Eliminar Reportes</td><td class="text-center">✅</td><td class="text-center">❌</td><td class="text-center">❌</td></tr>
                                    <tr><td>Gestionar Usuarios</td><td class="text-center">✅</td><td class="text-center">❌</td><td class="text-center">❌</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Exportación -->
                    <div class="tab-pane fade" id="content-export" role="tabpanel">
                        <h3 class="text-primary border-bottom pb-2 mb-3">7. Exportación de Datos</h3>
                        <p>Puede descargar la información en dos formatos desde el botón <strong>"Exportar"</strong>:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded text-center">
                                    <i class="fas fa-file-csv fa-3x text-info mb-3"></i>
                                    <h5>CSV</h5>
                                    <p class="small">Ideal para importar en otros sistemas.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded text-center">
                                    <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                                    <h5>Excel</h5>
                                    <p class="small">Formato legible y listo para informes.</p>
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