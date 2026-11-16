-- Creación de la base de datos con soporte para tildes y caracteres especiales
CREATE DATABASE IF NOT EXISTS agenda_personal_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE agenda_personal_db;

-- 1. Tabla: usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabla: contactos (Relacionada con usuarios mediante id_usuario)
CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(25) NOT NULL,
    correo VARCHAR(120) NOT NULL,
    ciudad VARCHAR(80) NOT NULL,
    CONSTRAINT fk_contactos_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;