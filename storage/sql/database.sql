CREATE DATABASE bot_padel;
USE bot_padel;

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    fecha_reserva DATETIME NULL,
    dia VARCHAR(20),
    hora VARCHAR(10),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_fecha_reserva (fecha_reserva)
);

CREATE TABLE historial_reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NULL,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    fecha_reserva DATETIME NULL,
    dia VARCHAR(20),
    hora VARCHAR(10),
    fecha_creacion_reserva DATETIME NULL,
    fecha_historial TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_hist_reserva_id (reserva_id)
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- guardaremos hash
    rol VARCHAR(20) DEFAULT 'trabajador'
);
