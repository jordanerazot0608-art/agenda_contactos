<?php
// Iniciar sesión de forma segura si aún no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Parámetros de conexión (Si tu MySQL en XAMPP usa otro puerto como 3307, cámbialo en DB_PORT)
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'agenda_personal_db');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ];

    // 1. Conectar al servidor MySQL y crear la base de datos si no existe
    $dsn_servidor = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
    $pdo = new PDO($dsn_servidor, DB_USER, DB_PASS, $opciones);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");

    // 2. Crear tabla 'usuarios' automáticamente si no existe
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        correo VARCHAR(120) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // 3. Crear tabla 'contactos' automáticamente si no existe
    $pdo->exec("CREATE TABLE IF NOT EXISTS contactos (
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
    ) ENGINE=InnoDB");

} catch (PDOException $e) {
    error_log("Error de conexión a la BD: " . $e->getMessage());
    die("Error del sistema: No fue posible conectar con el servicio de datos. Verifica que MySQL esté iniciado (en verde) en XAMPP.");
}
?>