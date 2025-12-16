-- Crear base de datos
CREATE DATABASE tramite_iest;
USE tramite_iest;

----------------------------------------------------------
-- 1. TABLA DE USUARIOS (personal del IEST)
----------------------------------------------------------
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    cargo VARCHAR(100),
    rol ENUM('ADMIN','MESA_PARTES','DIRECTOR','SECRETARIA ACADEMICA','JUA','COORD INFORMATICA'
    ,'COORD TOPO','COORD ENFERMERIA') DEFAULT 'MESA_PARTES',
    estado ENUM('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

----------------------------------------------------------
-- 2. TABLA DE AREAS DEL INSTITUTO
----------------------------------------------------------
CREATE TABLE areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_area VARCHAR(150) NOT NULL,
    responsable VARCHAR(150),
    tipo ENUM('ADMINISTRATIVA','ACADEMICA','DIRECCION','OTRO') DEFAULT 'ADMINISTRATIVA',
    estado ENUM('ACTIVO','INACTIVO') DEFAULT 'ACTIVO'
);

-- ÁREAS QUE SUELE TENER UN INSTITUTO
INSERT INTO areas (nombre_area, tipo) VALUES
('Dirección General', 'DIRECCION'),
('JUA', 'ACADEMICA'),
('Secretaría Académica', 'ACADEMICA'),
('Mesa de Partes', 'ADMINISTRATIVA'),
('Coordinación de Computación e Informática', 'ACADEMICA'),
('Coordinación de Enfermería Técnica', 'ACADEMICA'),
('Coordinación de topografia', 'ACADEMICA');


----------------------------------------------------------
-- 3. TABLA DE ESTUDIANTES Y EGRESADOS (CIUDADANOS INTERNOS)
----------------------------------------------------------
CREATE TABLE estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dni CHAR(8) NOT NULL UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    programa_estudio VARCHAR(100),
    ciclo VARCHAR(20),
    telefono VARCHAR(20),
    correo VARCHAR(120),
    tipo ENUM('ESTUDIANTE','EGRESADO','EXTERNO') DEFAULT 'ESTUDIANTE'
);

----------------------------------------------------------
-- 4. TABLA DE DOCUMENTOS INGRESADOS
----------------------------------------------------------
CREATE TABLE documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    tipo_documento ENUM('SOLICITUD','CARTA','OFICIO','MEMORANDO','INFORME','CONSTANCIA','OTRO') DEFAULT 'SOLICITUD',
    asunto TEXT NOT NULL,
    descripcion TEXT,
    id_remitente_est INT NULL,
    remitente_externo VARCHAR(200) NULL,
    dni_externo CHAR(8) NULL,
    fecha_ingreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('REGISTRADO','DERIVADO','EN PROCESO','ATENDIDO','ARCHIVADO') DEFAULT 'REGISTRADO',
    FOREIGN KEY (id_remitente_est) REFERENCES estudiantes(id)
);

----------------------------------------------------------
-- 5. MOVIMIENTOS (derivaciones entre áreas)
----------------------------------------------------------
CREATE TABLE movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_documento INT NOT NULL,
    id_area_origen INT,
    id_area_destino INT NOT NULL,
    observacion TEXT,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_recepcion DATETIME NULL,
    estado ENUM('ENVIADO','RECEPCIONADO','FINALIZADO') DEFAULT 'ENVIADO',

    FOREIGN KEY(id_documento) REFERENCES documentos(id),
    FOREIGN KEY(id_area_origen) REFERENCES areas(id),
    FOREIGN KEY(id_area_destino) REFERENCES areas(id)
);

----------------------------------------------------------
-- 6. ARCHIVOS ADJUNTOS
----------------------------------------------------------
CREATE TABLE adjuntos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_documento INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta VARCHAR(255) NOT NULL,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(id_documento) REFERENCES documentos(id)
);
