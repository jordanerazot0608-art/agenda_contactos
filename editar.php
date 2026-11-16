<?php
require_once 'config/conexion.php';

// 1. Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_usuario  = $_SESSION['usuario_id'];
$id_contacto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Si no hay un ID válido en la URL, redirigir al panel
if (!$id_contacto) {
    header("Location: index.php");
    exit;
}

// 2. Consultar el contacto asegurando que pertenezca al usuario autenticado (Protección anti-URL)
try {
    $stmt = $pdo->prepare(
        "SELECT id, nombre, telefono, correo, ciudad 
         FROM contactos 
         WHERE id = :id AND id_usuario = :id_usuario 
         LIMIT 1"
    );
    $stmt->execute([
        ':id'         => $id_contacto,
        ':id_usuario' => $id_usuario
    ]);
    $contacto = $stmt->fetch();

    // Si el contacto no existe o pertenece a otro usuario, impedir acceso
    if (!$contacto) {
        header("Location: index.php?msg=no_autorizado");
        exit;
    }
} catch (PDOException $e) {
    error_log("Error al obtener contacto: " . $e->getMessage());
    header("Location: index.php");
    exit;
}

$error    = '';
$nombre   = $contacto['nombre'];
$telefono = $contacto['telefono'];
$correo   = $contacto['correo'];
$ciudad   = $contacto['ciudad'];

// 3. Procesar la actualización del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $ciudad   = trim($_POST['ciudad'] ?? '');

    // Validación en el servidor
    if (empty($nombre) || empty($telefono) || empty($correo) || empty($ciudad)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico no es válido.";
    } elseif (!preg_match('/^[0-9+\-\s()]{7,20}$/', $telefono)) {
        $error = "El número telefónico debe contener entre 7 y 20 dígitos válidos.";
    } else {
        try {
            // Actualizar únicamente si coincide tanto el id del contacto como el id_usuario propietario
            $update = $pdo->prepare(
                "UPDATE contactos 
                 SET nombre = :nombre, telefono = :telefono, correo = :correo, ciudad = :ciudad 
                 WHERE id = :id AND id_usuario = :id_usuario"
            );
            $update->execute([
                ':nombre'     => $nombre,
                ':telefono'   => $telefono,
                ':correo'     => $correo,
                ':ciudad'     => $ciudad,
                ':id'         => $id_contacto,
                ':id_usuario' => $id_usuario
            ]);

            header("Location: index.php?msg=editado");
            exit;
        } catch (PDOException $e) {
            error_log("Error al actualizar contacto: " . $e->getMessage());
            $error = "No fue posible actualizar la información del contacto.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Contacto | Agenda Personal</title>
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
            <h2>Editar Contacto</h2>
            <a href="index.php" class="btn btn-secondary">← Cancelar y Volver</a>
        </div>

        <div class="card">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="editar.php?id=<?php echo (int)$id_contacto; ?>" method="POST" novalidate>
                <div class="form-group">
                    <label for="nombre">Nombre del contacto</label>
                    <input type="text" id="nombre" name="nombre" required
                           value="<?php echo htmlspecialchars($nombre); ?>">
                </div>

                <div class="form-group">
                    <label for="telefono">Número telefónico</label>
                    <input type="text" id="telefono" name="telefono" required
                           value="<?php echo htmlspecialchars($telefono); ?>">
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required
                           value="<?php echo htmlspecialchars($correo); ?>">
                </div>

                <div class="form-group">
                    <label for="ciudad">Ciudad de residencia</label>
                    <input type="text" id="ciudad" name="ciudad" required
                           value="<?php echo htmlspecialchars($ciudad); ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Guardar Cambios</button>
            </form>
        </div>
    </main>
</body>
</html>