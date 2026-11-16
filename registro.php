<?php
require_once 'config/conexion.php';

// Si el usuario ya tiene sesión activa, redirigir al panel principal
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$exito = '';
$nombre = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpieza y captura de datos del formulario
    $nombre   = trim($_POST['nombre'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // 1. Validación de formulario en el servidor
    if (empty($nombre) || empty($correo) || empty($password) || empty($confirm)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico no es válido.";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } elseif ($password !== $confirm) {
        $error = "Las contraseñas ingresadas no coinciden.";
    } else {
        try {
            // 2. Validar que el correo electrónico no exista previamente
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo LIMIT 1");
            $stmt->execute([':correo' => $correo]);

            if ($stmt->fetch()) {
                $error = "Este correo electrónico ya se encuentra registrado.";
            } else {
                // 3. Cifrar la contraseña de forma segura con password_hash()
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // 4. Insertar el nuevo usuario en la base de datos
                $insert = $pdo->prepare(
                    "INSERT INTO usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)"
                );
                $insert->execute([
                    ':nombre'   => $nombre,
                    ':correo'   => $correo,
                    ':password' => $password_hash
                ]);

                $exito = "Cuenta creada correctamente. Ahora puedes iniciar sesión.";
                // Limpiar variables del formulario tras registro exitoso
                $nombre = '';
                $correo = '';
            }
        } catch (PDOException $e) {
            error_log("Error en registro: " . $e->getMessage());
            $error = "Ocurrió un problema al procesar el registro. Inténtalo más tarde.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario | Agenda Personal</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h1>Crear Cuenta</h1>
            <p class="subtitle">Regístrate para gestionar tu agenda personal de contactos</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($exito)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($exito); ?>
                    <br><a href="login.php" style="color: inherit; font-weight: bold;">Ir a Iniciar Sesión</a>
                </div>
            <?php endif; ?>

            <form action="registro.php" method="POST" novalidate>
                <div class="form-group">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" required
                           value="<?php echo htmlspecialchars($nombre); ?>"
                           placeholder="Ej. Jordan Erazo">
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required
                           value="<?php echo htmlspecialchars($correo); ?>"
                           placeholder="usuario@correo.com">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña (mínimo 6 caracteres)</label>
                    <input type="password" id="password" name="password" required
                           placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                           placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Registrar Usuario</button>
            </form>

            <div class="auth-footer">
                ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
            </div>
        </div>
    </div>
</body>
</html>