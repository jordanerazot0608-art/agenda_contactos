# Agenda Personal de Contactos - PHP y MySQL

## Descripción del proyecto
Aplicación web desarrollada en PHP y MySQL que implementa mecanismos seguros de autenticación de usuarios, manejo de sesiones y gestión de información (CRUD) para una agenda personal de contactos. El sistema aplica buenas prácticas de seguridad en el servidor para garantizar que únicamente los usuarios autenticados accedan al panel principal y que cada cuenta visualice, edite y elimine exclusivamente sus propios registros, impidiendo el acceso cruzado incluso ante modificaciones manuales en la URL.

## Tecnologías utilizadas
* **Lenguaje de servidor (Backend):** PHP 8.x
* **Motor de Base de Datos:** MySQL / MariaDB (con extensión PDO y sentencias preparadas nativas)
* **Frontend y Maquetación:** HTML5, CSS3 (diseño responsivo moderno) y JavaScript (confirmación de borrado)
* **Entorno de servidor local:** XAMPP (Apache + MySQL)

## Requisitos para su ejecución
* Servidor web local como **XAMPP**, **Laragon** o **WAMP** con:
  * **PHP** versión 7.4 o superior (compatible con PHP 8.x).
  * **Servidor MySQL / MariaDB** activo en el puerto `3306`.
  * Extensión `pdo_mysql` habilitada en `php.ini` (activa por defecto en XAMPP).
* Navegador web moderno (Chrome, Edge, Firefox).

## Pasos para instalar la aplicación
1. **Clonar o descargar el proyecto:**
   Descargue o clone este repositorio dentro del directorio público de su servidor web local:
   * En XAMPP: `C:\xampp\htdocs\agenda_contactos`
   * En Laragon: `C:\laragon\www\agenda_contactos`

2. **Iniciar los servicios:**
   Abra el panel de control de **XAMPP** e inicie los módulos **Apache** y **MySQL**.

3. **Configurar la Base de Datos:**
   * Puede importar manualmente el archivo `database/script_agenda.sql` desde **phpMyAdmin** (`http://localhost/phpmyadmin`), o bien dejar que el sistema cree automáticamente la base de datos `agenda_personal_db` y las tablas `usuarios` y `contactos` al abrir la aplicación por primera vez.
   * Verifique en `config/conexion.php` que las credenciales correspondan a su entorno local (`DB_HOST = 'localhost'`, `DB_USER = 'root'`, `DB_PASS = ''`).

4. **Ejecutar la aplicación en el navegador:**
   Ingrese a la siguiente URL en su navegador:
   ```text
   http://localhost/agenda_contactos/