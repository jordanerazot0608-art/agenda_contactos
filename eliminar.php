<?php
require_once 'config/conexion.php';

// 1. Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_usuario  = $_SESSION['usuario_id'];
$id_contacto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_contacto) {
    header("Location: index.php");
    exit;
}

try {
    // 2. Eliminar asegurando que el contacto pertenezca al usuario autenticado
    $stmt = $pdo->prepare("DELETE FROM contactos WHERE id = :id AND id_usuario = :id_usuario");
    $stmt->execute([
        ':id'         => $id_contacto,
        ':id_usuario' => $id_usuario
    ]);

    // Verificar si realmente se eliminó un registro propio
    if ($stmt->rowCount() > 0) {
        header("Location: index.php?msg=eliminado");
    } else {
        // Si rowCount es 0, el ID no existe o pertenece a otro usuario
        header("Location: index.php?msg=no_autorizado");
    }
    exit;
} catch (PDOException $e) {
    error_log("Error al eliminar contacto: " . $e->getMessage());
    header("Location: index.php");
    exit;
}
?>