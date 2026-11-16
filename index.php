<?php
require_once 'config/conexion.php';

// 1. Verificación de sesión activa (Restricción de acceso a página protegida)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_usuario     = $_SESSION['usuario_id'];
$nombre_usuario = $_SESSION['usuario_nombre'];
$correo_usuario = $_SESSION['usuario_correo'];

// 2. Consultar exclusivamente los contactos pertenecientes al usuario autenticado
try {
    $stmt = $pdo->prepare(
        "SELECT id, nombre, telefono, correo, ciudad 
         FROM contactos 
         WHERE id_usuario = :id_usuario 
         ORDER BY nombre ASC"
    );
    $stmt->execute([':id_usuario' => $id_usuario]);
    $contactos = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error al listar contactos: " . $e->getMessage());
    $contactos = [];
    $error_carga = "No se pudo cargar la lista de contactos en este momento.";
}

// Mensajes de retroalimentación según la operación realizada
$mensaje = '';
$tipo_alerta = 'alert-success';

if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'creado':
            $mensaje = "Contacto registrado exitosamente en tu agenda.";
            break;
        case 'editado':
            $mensaje = "La información del contacto fue actualizada correctamente.";
            break;
        case 'eliminado':
            $mensaje = "El contacto ha sido eliminado de tu agenda.";
            break;
        case 'no_autorizado':
            $mensaje = "Acción denegada: El contacto solicitado no existe o no pertenece a tu cuenta.";
            $tipo_alerta = 'alert-error';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Agenda de Contactos | Panel Principal</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <!-- Barra superior con datos del usuario autenticado -->
    <nav class="navbar">
        <div class="navbar-brand">Agenda Personal de Contactos</div>
        <div class="user-info">
            <div class="user-details">
                <span><strong><?php echo htmlspecialchars($nombre_usuario); ?></strong></span>
                <span class="user-email"><?php echo htmlspecialchars($correo_usuario); ?></span>
            </div>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="container">
        <div class="panel-header">
            <div>
                <h2>Mis Contactos Registrados</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem;">
                    Total de contactos en tu cuenta: <strong><?php echo count($contactos); ?></strong>
                </p>
            </div>
            <a href="crear.php" class="btn btn-primary">+ Nuevo Contacto</a>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class="alert <?php echo $tipo_alerta; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_carga)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_carga); ?></div>
        <?php endif; ?>

        <div class="card">
            <?php if (count($contactos) > 0): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Correo Electrónico</th>
                                <th>Ciudad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contactos as $contacto): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($contacto['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($contacto['telefono']); ?></td>
                                    <td><?php echo htmlspecialchars($contacto['correo']); ?></td>
                                    <td><?php echo htmlspecialchars($contacto['ciudad']); ?></td>
                                    <td class="acciones">
                                        <a href="editar.php?id=<?php echo (int)$contacto['id']; ?>" 
                                           class="btn btn-warning">Editar</a>
                                        <a href="eliminar.php?id=<?php echo (int)$contacto['id']; ?>" 
                                           class="btn btn-danger"
                                           onclick="return confirm('¿Estás seguro de que deseas eliminar a <?php echo htmlspecialchars(addslashes($contacto['nombre'])); ?> de tus contactos?');">
                                           Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>Aún no tienes contactos registrados en tu agenda personal.</p>
                    <br>
                    <a href="crear.php" class="btn btn-primary">Registrar mi primer contacto</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>