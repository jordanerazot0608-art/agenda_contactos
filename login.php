<?php
require_once 'config/conexion.php';

// Si ya inició sesión, enviarlo directo al panel principal
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo   = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1. Validación en el servidor
    if (empty($correo) || empty($password)) {
        $error = "Por favor, ingresa tu correo electrónico y contraseña.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico no es válido.";
    } else {
        try {
            // 2. Buscar usuario por correo
            $stmt = $pdo->prepare("SELECT id, nombre, correo, password FROM usuarios WHERE correo = :correo LIMIT 1");
            $stmt->execute([':correo' => $correo]);
            $usuario = $stmt->fetch();

            // 3. Comprobar contraseña con password_verify()
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Regenerar ID de sesión por seguridad
                session_regenerate_id(true);

                // 4. Crear variables de sesión del usuario autenticado
                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_correo'] = $usuario['correo'];

                header("Location: index.php");
                exit;
            } else {
                // Mensaje claro sin revelar cuál de los dos datos falló
                $error = "Credenciales incorrectas. Verifica tu correo y contraseña.";
            }
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            $error = "Ocurrió un error al procesar la solicitud. Intenta nuevamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Agenda Personal</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h1>Iniciar Sesión</h1>
            <p class="subtitle">Ingresa tus credenciales para acceder a tus contactos</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST" novalidate>
                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required
                           value="<?php echo htmlspecialchars($correo); ?>"
                           placeholder="usuario@correo.com">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required
                           placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Ingresar al Panel</button>
            </form>

            <div class="auth-footer">
                ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
            </div>
        </div>
    </div>
</body>
</html>