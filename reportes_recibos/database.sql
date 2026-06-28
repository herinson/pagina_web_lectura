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
    NIS_RAD VARCHAR(50),
    COD_TIPO_CONEXION VARCHAR(50),
    TIPO_CONEXION VARCHAR(100),
    NIF VARCHAR(50),
    NIC VARCHAR(50),
    FECHA_ALTA VARCHAR(50),
    FECHA_BAJA VARCHAR(50),
    FIANZA VARCHAR(50),
    CO_AN_VIP VARCHAR(50),
    AN_VIP VARCHAR(50),
    ESTADO_SUMINISTRO VARCHAR(50),
    DESCRIPCION_ESTADO_SUMINISTRO VARCHAR(100),
    TARIFA VARCHAR(50),
    NOMBRE_CLIENTE VARCHAR(150),
    APE1_CLI VARCHAR(100),
    APE2_CLI VARCHAR(100),
    TFNO_CLI VARCHAR(50),
    COD_CLI VARCHAR(50),
    DESC_TIPOCLIENTE VARCHAR(100),
    COD_CALLE VARCHAR(50),
    CALLE VARCHAR(200),
    NUM_PUERTA VARCHAR(50),
    DUPLICADOR VARCHAR(50),
    CGV_SUM VARCHAR(50),
    COD_LOCAL VARCHAR(50),
    LOCALIDAD VARCHAR(150),
    SECCION VARCHAR(50),
    MUNICIPIO VARCHAR(150),
    PROVINCIA VARCHAR(150),
    REF_DIR VARCHAR(255),
    ACC_FINCA VARCHAR(255),
    NOM_FINCA VARCHAR(150),
    COD_UNICOM VARCHAR(50),
    COD_AREA VARCHAR(50),
    NUM_DEUDA VARCHAR(50),
    IMPORTE VARCHAR(50),
    NUM_DEUDA_VENC VARCHAR(50),
    IMPORTE_VENC VARCHAR(50),
    NUM_APA VARCHAR(50),
    CO_MARCA VARCHAR(50),
    MARCA VARCHAR(100),
    F_INST_MED VARCHAR(50),
    NUM_PADRON VARCHAR(50),
    RUTA VARCHAR(50),
    ITINERARIO VARCHAR(50),
    TIP_FIN VARCHAR(50),
    TIPO_FINCA VARCHAR(100),
    TIP_CLI VARCHAR(50),
    TIPO_CLIENTE VARCHAR(100),
    TIP_TENSION VARCHAR(50),
    TIPO_TENSION VARCHAR(100),
    TIP_SUMINISTRO VARCHAR(50),
    TIPO_SUMINISTRO VARCHAR(100),
    FECHA_VENC_UF VARCHAR(50),
    CSMO_FIJO VARCHAR(50),
    DOC_ID VARCHAR(50),
    Subestacion VARCHAR(150),
    Circuito VARCHAR(150),
    CT VARCHAR(150),
    PUNTO_MEDIDA VARCHAR(150),
    NATURALEZA VARCHAR(150),
    FECHA_UF VARCHAR(50),
    ZONA VARCHAR(150),
    CENT_LECT VARCHAR(150),
    COORDX VARCHAR(50),
    COORDY VARCHAR(50),
    TABLA VARCHAR(50),
    COD_UNICOM_CONT VARCHAR(50),
    NUM_FISCAL VARCHAR(50),
    TIPO_DOC VARCHAR(50),
    INDEX (NIC)
) ENGINE=InnoDB;
