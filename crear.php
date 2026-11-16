<?php
require_once 'config/conexion.php';

// 1. Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$error    = '';
$nombre   = '';
$telefono = '';
$correo   = '';
$ciudad   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $ciudad   = trim($_POST['ciudad'] ?? '');

    // 2. Validación de campos en el servidor
    if (empty($nombre) || empty($telefono) || empty($correo) || empty($ciudad)) {
        $error = "Todos los campos del contacto son obligatorios.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor, ingresa un correo electrónico válido para el contacto.";
    } elseif (!preg_match('/^[0-9+\-\s()]{7,20}$/', $telefono)) {
        $error = "El número telefónico debe contener entre 7 y 20 dígitos válidos.";
    } else {
        try {
            // 3. Insertar contacto asociado al id_usuario de la sesión activa
            $stmt = $pdo->prepare(
                "INSERT INTO contactos (id_usuario, nombre, telefono, correo, ciudad) 
                 VALUES (:id_usuario, :nombre, :telefono, :correo, :ciudad)"
            );
            $stmt->execute([
                ':id_usuario' => $_SESSION['usuario_id'],
                ':nombre'     => $nombre,
                ':telefono'   => $telefono,
                ':correo'     => $correo,
                ':ciudad'     => $ciudad
            ]);

            header("Location: index.php?msg=creado");
            exit;
        } catch (PDOException $e) {
            error_log("Error al crear contacto: " . $e->getMessage());
            $error = "Ocurrió un error al guardar el contacto. Inténtalo de nuevo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Contacto | Agenda Personal</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Agenda Personal de Contactos</div>
        <div class="user-info">
            <div class="user-details">
                <span><strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></span>
                <span class="user-email"><?php echo htmlspecialchars($_SESSION['usuario_correo']); ?></span>
            </div>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </nav>

    <main class="container" style="max-width: 600px;">
        <div class="panel-header">
            <h2>Registrar Nuevo Contacto</h2>
            <a href="index.php" class="btn btn-secondary">← Volver al Panel</a>
        </div>

        <div class="card">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="crear.php" method="POST" novalidate>
                <div class="form-group">
                    <label for="nombre">Nombre del contacto</label>
                    <input type="text" id="nombre" name="nombre" required
                           value="<?php echo htmlspecialchars($nombre); ?>"
                           placeholder="Ej. Carlos Mendoza">
                </div>

                <div class="form-group">
                    <label for="telefono">Número telefónico</label>
                    <input type="text" id="telefono" name="telefono" required
                           value="<?php echo htmlspecialchars($telefono); ?>"
                           placeholder="Ej. 0991234567">
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required
                           value="<?php echo htmlspecialchars($correo); ?>"
                           placeholder="contacto@ejemplo.com">
                </div>

                <div class="form-group">
                    <label for="ciudad">Ciudad de residencia</label>
                    <input type="text" id="ciudad" name="ciudad" required
                           value="<?php echo htmlspecialchars($ciudad); ?>"
                           placeholder="Ej. Ibarra">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Guardar Contacto</button>
            </form>
        </div>
    </main>
</body>
</html>