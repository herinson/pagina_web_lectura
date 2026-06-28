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
    *   **NIC (Obligatorio)**: Ingrese el Número de Identificación de Contrato del cliente (ej. 1234567).
    *   **Localidad**: Escriba la zona o sector específico (ej. Barrio Lindo, Sector Centro).
    *   **Oficina (Obligatorio)**: Seleccione la oficina correspondiente de la lista desplegable. Puede escribir el nombre para buscarla rápidamente.
    *   **Fecha Reporte**: Seleccione la fecha en la que se recibió la reclamación.
    *   **Area**: Seleccione si la incidencia proviene del área **Comercial** o de **Caja**.
    *   **Teléfono**: Ingrese el número de contacto del cliente para seguimiento.
    *   **Observaciones**: Detalle cualquier información adicional relevante para el caso.
4.  **Guardar**: Presione el botón **"Guardar"**. El sistema mostrará una alerta de éxito y el reporte aparecerá inmediatamente en la tabla.

*Nota: Por defecto, todo reporte nuevo se crea con el estado "**Pendiente de envío**".*

---

### 3. Estados de la Factura y Flujo de Trabajo

El sistema utiliza estados para indicar la etapa en la que se encuentra la gestión:

#### 3.1 Pendiente de envío (Color Amarillo/Naranja) 🟡
*   **Significado**: El reporte ha sido recibido y registrado en el sistema, pero la factura aún no ha sido procesada o enviada para su distribución final.
*   **Quién lo ve**: Todos los usuarios.
*   **Acción**: El Encargado de Lectura debe revisar estos reportes periódicamente.

#### 3.2 Enviada (Color Verde) 🟢
*   **Significado**: La gestión ha sido completada y la factura ha sido enviada o el problema ha sido resuelto.
*   **Quién lo cambia**: Solo el **Administrador** o el **Encargado de Lectura** tienen permisos para cambiar un reporte a este estado.
*   **Restricción**: Un usuario normal no puede marcar una factura como enviada.

---

### 4. Consulta y Seguimiento (Búsqueda y Filtros)

La tabla de reportes es dinámica y permite encontrar información rápidamente:

*   **Buscador Instantáneo**: En la parte superior derecha de la tabla, puede escribir cualquier dato (NIC, Oficina, Localidad) y la tabla se filtrará en tiempo real.
*   **Filtros Avanzados**:
    *   **Oficina**: Filtre todos los registros de una oficina específica.
    *   **Rango de Fechas**: Use los campos "Desde" y "Hasta" para ver reportes de un periodo determinado.
*   **Ordenamiento**: Haga clic en los encabezados de las columnas (ID, NIC, Fecha, etc.) para ordenar de forma ascendente o descendente.

---

### 5. Auditoría e Historial de Cambios

Para garantizar la transparencia, el sistema registra cada acción:

1.  **Creador**: En la columna "Creador" verá quién registró originalmente el reporte.
2.  **Audit (Estado)**: La columna "Audit" muestra un contador de cambios. Si hace clic en editar, los roles autorizados pueden ver quién modificó el registro por última vez.
3.  **Historial de Estados**: Cada vez que una factura cambia de "Pendiente" a "Enviada", el sistema guarda internamente:
    *   Nombre del usuario que hizo el cambio.
    *   Fecha y hora exacta.
    *   El valor anterior y el valor nuevo.

---

### 6. Exportación de Reportes

Si necesita presentar informes en reuniones o enviarlos por correo:
1.  Aplique los filtros deseados (ej. Oficina Valverde Mao, Mes de Mayo).
2.  Haga clic en el botón verde **"Exportar"**.
3.  Elija entre **CSV** (ideal para sistemas externos) o **Excel** (formato profesional para lectura humana).

---

### 7. Gestión de Usuarios (Solo Administradores)

Los administradores pueden gestionar quién tiene acceso al sistema:
*   **Nuevo Usuario**: Defina nombre, usuario (login), contraseña y rol.
*   **Roles**:
    *   **ADMINISTRADOR**: Control total.
    *   **ENCARGADO DE LECTURA**: Gestión operativa de todos los reportes.
    *   **USUARIO**: Registro básico de sus propios casos.

---

### 8. Solución de Problemas Comunes

*   **No puedo cambiar el estado de la factura**: Verifique su rol. Solo los Encargados y Administradores pueden cambiar estados.
*   **No veo reportes de otros compañeros**: Si su rol es "USUARIO", por diseño solo podrá ver y editar lo que usted mismo ha registrado.
*   **Contraseña Incorrecta**: Si un administrador le creó la cuenta manualmente en la base de datos, asegúrese de que se haya usado la función de encriptación adecuada. Se recomienda siempre crear usuarios desde el panel de Gestión de Usuarios.

---
*Manual actualizado para EDENORTE - Versión 2.0*
