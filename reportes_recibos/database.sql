-- Updated Database schema for Reportes system
-- =============================================================================
-- PROYECTO: Sistema de No Recepción de Facturas - EDENORTE
-- ARCHIVO: database.sql
-- 
-- IMPORTANTE PARA FUTURAS MODIFICACIONES:
-- De ahora en adelante, cada vez que sea necesario modificar la estructura 
-- de la base de datos MySQL, no se debe eliminar ni recrear la base de datos 
-- completa, ya que el sistema contiene información en producción.
-- 
-- Las modificaciones deben realizarse mediante scripts SQL incrementales 
-- agregados AL FINAL de este archivo, utilizando instrucciones ALTER TABLE, 
-- CREATE TABLE (para tablas nuevas), etc., sin afectar los datos existentes.
-- =============================================================================

CREATE DATABASE IF NOT EXISTS reportes;
USE reportes;

-- Table for users
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('ADMINISTRADOR', 'USUARIO', 'ENCARGADO DE LECTURA') NOT NULL DEFAULT 'USUARIO',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table for reports
CREATE TABLE IF NOT EXISTS reportes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nic VARCHAR(50) NOT NULL,
    localidad VARCHAR(100),
    oficina VARCHAR(100),
    fecha DATE,
    area VARCHAR(100),
    telefono VARCHAR(20),
    mes_reclamado TEXT,
    ruta VARCHAR(50),
    itinerario VARCHAR(50),
    observaciones TEXT,
    estado_factura ENUM('Pendiente de envío', 'Enviada') NOT NULL DEFAULT 'Pendiente de envío',
    usuario_creador INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    usuario_modificacion INT,
    estado ENUM('ACTIVO', 'MODIFICADO', 'ELIMINADO') NOT NULL DEFAULT 'ACTIVO',
    FOREIGN KEY (usuario_creador) REFERENCES usuarios(id),
    FOREIGN KEY (usuario_modificacion) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- History of status changes
CREATE TABLE IF NOT EXISTS historial_estados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporte_id INT NOT NULL,
    usuario_id INT NOT NULL,
    estado_anterior VARCHAR(50),
    estado_nuevo VARCHAR(50),
    comentario TEXT,
    evidencia VARCHAR(255),
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporte_id) REFERENCES reportes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Table for password reset requests
CREATE TABLE IF NOT EXISTS solicitudes_password (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    estado ENUM('PENDIENTE', 'COMPLETADA') NOT NULL DEFAULT 'PENDIENTE',
    admin_id INT NULL,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_completada TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (admin_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Initial admin user (password: edenorte)
INSERT INTO usuarios (nombre, usuario, password, rol) 
VALUES ('Administrador Sistema', 'admin', '$2y$10$eMrkzKDdMddPO8/sagDt..XNCgzoxN25soq6IoSkM4BsY.1FNpC9K', 'ADMINISTRADOR')
ON DUPLICATE KEY UPDATE rol='ADMINISTRADOR';

-- =====================================================
-- MODIFICACIÓN: AGREGAR CAMPO oficina_usuario
-- FECHA: 2026-05-26
-- DESCRIPCIÓN: Se agrega campo para identificar la oficina del usuario
-- =====================================================
ALTER TABLE usuarios ADD COLUMN oficina_usuario VARCHAR(100) NULL;


-- =====================================================
-- MODIFICACIÓN: AGREGAR CAMPO estado_usuario
-- FECHA: 2026-05-26
-- DESCRIPCIÓN: Se agrega control de activación/desactivación de usuarios
-- =====================================================
ALTER TABLE usuarios ADD COLUMN estado_usuario ENUM('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO';

-- =====================================================
-- MODIFICACIÓN: CREAR TABLA clientes_info
-- FECHA: 2024-05-27
-- DESCRIPCIÓN: Nueva tabla para autocompletado desde informe semanal
-- =====================================================
CREATE TABLE IF NOT EXISTS clientes_info (
    NIS_RAD TEXT,
    COD_TIPO_CONEXION TEXT,
    TIPO_CONEXION TEXT,
    NIF TEXT,
    NIC VARCHAR(50),
    FECHA_ALTA TEXT,
    FECHA_BAJA TEXT,
    FIANZA TEXT,
    CO_AN_VIP TEXT,
    AN_VIP TEXT,
    ESTADO_SUMINISTRO TEXT,
    DESCRIPCION_ESTADO_SUMINISTRO TEXT,
    TARIFA TEXT,
    NOMBRE_CLIENTE TEXT,
    APE1_CLI TEXT,
    APE2_CLI TEXT,
    TFNO_CLI TEXT,
    COD_CLI TEXT,
    DESC_TIPOCLIENTE TEXT,
    COD_CALLE TEXT,
    CALLE TEXT,
    NUM_PUERTA TEXT,
    DUPLICADOR TEXT,
    CGV_SUM TEXT,
    COD_LOCAL TEXT,
    LOCALIDAD TEXT,
    SECCION TEXT,
    MUNICIPIO TEXT,
    PROVINCIA TEXT,
    REF_DIR TEXT,
    ACC_FINCA TEXT,
    NOM_FINCA TEXT,
    COD_UNICOM TEXT,
    COD_AREA TEXT,
    NUM_DEUDA TEXT,
    IMPORTE TEXT,
    NUM_DEUDA_VENC TEXT,
    IMPORTE_VENC TEXT,
    NUM_APA TEXT,
    CO_MARCA TEXT,
    MARCA TEXT,
    F_INST_MED TEXT,
    NUM_PADRON TEXT,
    RUTA TEXT,
    ITINERARIO TEXT,
    TIP_FIN TEXT,
    TIPO_FINCA TEXT,
    TIP_CLI TEXT,
    TIPO_CLIENTE TEXT,
    TIP_TENSION TEXT,
    TIPO_TENSION TEXT,
    TIP_SUMINISTRO TEXT,
    TIPO_SUMINISTRO TEXT,
    FECHA_VENC_UF TEXT,
    CSMO_FIJO TEXT,
    DOC_ID TEXT,
    Subestacion TEXT,
    Circuito TEXT,
    CT TEXT,
    PUNTO_MEDIDA TEXT,
    NATURALEZA TEXT,
    FECHA_UF TEXT,
    ZONA TEXT,
    CENT_LECT TEXT,
    COORDX TEXT,
    COORDY TEXT,
    TABLA TEXT,
    COD_UNICOM_CONT TEXT,
    NUM_FISCAL TEXT,
    TIPO_DOC TEXT,
    PRIMARY KEY (NIC)
) ENGINE=InnoDB ROW_FORMAT=DYNAMIC;

-- =====================================================
-- INSTRUCCIONES PARA IMPORTACIÓN MANUAL (ARCHIVOS GRANDES)
-- Si el script PHP falla por el tamaño del archivo (150MB+),
-- ejecute el siguiente comando directamente en su consola de MySQL
-- o en la pestaña SQL de phpMyAdmin:
-- =====================================================

/*
TRUNCATE TABLE clientes_info;

LOAD DATA LOCAL INFILE 'C:/ruta/a/su/archivo/iniforme_semanal.txt'
INTO TABLE clientes_info
FIELDS TERMINATED BY '@'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(NIS_RAD, COD_TIPO_CONEXION, TIPO_CONEXION, NIF, NIC, FECHA_ALTA, FECHA_BAJA, FIANZA, CO_AN_VIP, AN_VIP, ESTADO_SUMINISTRO, DESCRIPCION_ESTADO_SUMINISTRO, TARIFA, NOMBRE_CLIENTE, APE1_CLI, APE2_CLI, TFNO_CLI, COD_CLI, DESC_TIPOCLIENTE, COD_CALLE, CALLE, NUM_PUERTA, DUPLICADOR, CGV_SUM, COD_LOCAL, LOCALIDAD, SECCION, MUNICIPIO, PROVINCIA, REF_DIR, ACC_FINCA, NOM_FINCA, COD_UNICOM, COD_AREA, NUM_DEUDA, IMPORTE, NUM_DEUDA_VENC, IMPORTE_VENC, NUM_APA, CO_MARCA, MARCA, F_INST_MED, NUM_PADRON, RUTA, ITINERARIO, TIP_FIN, TIPO_FINCA, TIP_CLI, TIPO_CLIENTE, TIP_TENSION, TIPO_TENSION, TIP_SUMINISTRO, TIPO_SUMINISTRO, FECHA_VENC_UF, CSMO_FIJO, DOC_ID, Subestacion, Circuito, CT, PUNTO_MEDIDA, NATURALEZA, FECHA_UF, ZONA, CENT_LECT, COORDX, COORDY, TABLA, COD_UNICOM_CONT, NUM_FISCAL, TIPO_DOC);
*/
