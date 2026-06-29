# Manual de Usuario Extendido - Sistema de No Recepción de Facturas
## EDENORTE - Departamento de Lectura y Distribución de Facturas

### 1. Introducción
Este sistema ha sido desarrollado para centralizar el registro y seguimiento de los reportes de "No Recepción de Facturas". Su objetivo es permitir que el **Departamento de Lectura y Distribución** tenga un control exacto de qué facturas han sido reportadas, en qué oficinas y cuál es su estado actual de gestión.

---

### 2. Guía Paso a Paso: Registro de un Nuevo Reporte

Para registrar una incidencia, siga estos pasos:

1.  **Acceder al módulo**: En el menú lateral, haga clic en **"Reportes"**.
2.  **Abrir el formulario**: Haga clic en el botón azul **"+ Nuevo Reporte"**.
3.  **Llenado de Campos**:
    *   **NIC (Obligatorio)**: Ingrese el Número de Identificación de Contrato del cliente (ej. 1234567). Al ingresar el NIC, el sistema buscará automáticamente la información del cliente.
    *   **Autocompletado**: Al terminar de escribir el NIC o presionar el botón de búsqueda, los campos **Localidad, Oficina, Teléfono, Ruta e Itinerario** se llenarán automáticamente si el cliente existe en la base de datos.
    *   **Fecha Reporte**: Seleccione la fecha en la que se recibió la reclamación.
    *   **Area**: Seleccione si la incidencia proviene del área **Comercial** o de **Caja**.
    *   **Mes Reclamado**: Seleccione los meses que el cliente está reclamando.
    *   **Observaciones**: Detalle cualquier información adicional relevante para el caso.
4.  **Guardar**: Presione el botón **"Guardar"**. El sistema mostrará una alerta de éxito y el reporte aparecerá inmediatamente en la tabla.

---

### 3. Estados de la Factura y Flujo de Trabajo

El sistema utiliza estados para indicar la etapa en la que se encuentra la gestión:

#### 3.1 Pendiente de envío (Color Amarillo/Naranja) 🟡
*   **Significado**: El reporte ha sido recibido y registrado en el sistema, pero la factura aún no ha sido procesada o enviada para su distribución final.

#### 3.2 Enviada (Color Verde) 🟢
*   **Significado**: La gestión ha sido completada y la factura ha sido enviada o el problema ha sido resuelto.
*   **Quién lo cambia**: Solo el **Administrador** o el **Encargado de Lectura** tienen permisos para cambiar un reporte a este estado.

---

### 4. Consulta y Seguimiento (Búsqueda y Filtros)

La tabla de reportes es dinámica y permite encontrar información rápidamente:

*   **Buscador Instantáneo**: En la parte superior derecha de la tabla, puede escribir cualquier dato (NIC, Oficina, Localidad) y la tabla se filtrará en tiempo real.
*   **Filtros Avanzados**: Puede filtrar por Oficina, Rango de Fechas, Estado, Ruta, Itinerario y Área.
*   **Ordenamiento**: Haga clic en los encabezados de las columnas para ordenar los datos.

---

### 5. Auditoría e Historial de Cambios

Para garantizar la transparencia, el sistema registra cada acción:
1.  **Creador**: Muestra quién registró originalmente el reporte.
2.  **Historial de Estados**: Si hace clic en el icono de clip (evidencia), podrá ver quién cambió el estado, en qué fecha y las observaciones adjuntas.

---

### 6. Exportación de Reportes

Si necesita presentar informes:
1.  Aplique los filtros deseados.
2.  Haga clic en el botón verde **"Exportar"**.
3.  Elija entre **CSV** o **Excel**.

---

### 7. Gestión de Usuarios (Solo Administradores)

Los administradores pueden gestionar quién tiene acceso al sistema:
*   **Usuarios**: Panel para crear, editar o desactivar cuentas.
*   **Solicitudes**: Para gestionar peticiones de restablecimiento de contraseña.

---

### 8. Solución de Problemas Comunes

*   **No puedo cambiar el estado de la factura**: Verifique su rol. Solo los Encargados y Administradores pueden cambiar estados.
*   **El NIC no autocompleta**: Asegúrese de que el NIC sea correcto. Si los datos no aparecen automáticamente, puede ingresarlos manualmente.
*   **Sesión Cerrada**: Por seguridad, el sistema cerrará su sesión automáticamente después de 30 minutos de inactividad.

---
*Manual actualizado para EDENORTE - Versión 3.0*
