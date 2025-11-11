<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $correo_electronico = trim($_POST['correo_electronico'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

    if (empty($nombre_usuario) || empty($correo_electronico) || empty($contrasena) || empty($confirmar_contrasena)) {
        $error = 'Todos los campos son obligatorios.';
    } elseif (strlen($nombre_usuario) < 3) {
        $error = 'El nombre de usuario debe tener al menos 3 caracteres.';
    } elseif (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido.';
    } elseif ($contrasena !== $confirmar_contrasena) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($contrasena) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ? OR correo_electronico = ?");
        $stmt->bind_param("ss", $nombre_usuario, $correo_electronico);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'El nombre de usuario o correo electrónico ya están registrados.';
        } else {
            $hash_contrasena = password_hash($contrasena, PASSWORD_BCRYPT);
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre_usuario, correo_electronico, contrasena) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre_usuario, $correo_electronico, $hash_contrasena);

            if ($stmt->execute()) {
                $success = 'Registro exitoso. Ahora puedes iniciar sesión.';
                $_POST = array();
            } else {
                $error = 'Error al registrar: ' . $stmt->error;
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Guía Cuphead</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .contenedor {
            background: #2d2d2d;
            border: 2px solid #ff6b35;
            border-radius: 10px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(255, 107, 53, 0.2);
        }

        h1 {
            color: #ff6b35;
            margin-bottom: 30px;
            text-align: center;
            font-size: 28px;
        }

        .grupo-formulario {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #fff;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            background: #1a1a1a;
            border: 1px solid #ff6b35;
            color: #fff;
            border-radius: 5px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            background: #ff6b35;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #e55a2b;
        }

        .mensaje {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
        }

        .error {
            background: #cc0000;
            color: #fff;
        }

        .success {
            background: #00cc00;
            color: #fff;
        }

        .enlace {
            text-align: center;
            margin-top: 20px;
            color: #ccc;
        }

        .enlace a {
            color: #ff6b35;
            text-decoration: none;
            font-weight: bold;
        }

        .enlace a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Registrarse</h1>

        <?php if ($error): ?>
            <div class="mensaje error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="mensaje success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="grupo-formulario">
                <label for="nombre_usuario">Nombre de usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($_POST['nombre_usuario'] ?? ''); ?>" required>
            </div>

            <div class="grupo-formulario">
                <label for="correo_electronico">Correo electrónico:</label>
                <input type="email" id="correo_electronico" name="correo_electronico" value="<?php echo htmlspecialchars($_POST['correo_electronico'] ?? ''); ?>" required>
            </div>

            <div class="grupo-formulario">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>

            <div class="grupo-formulario">
                <label for="confirmar_contrasena">Confirmar contraseña:</label>
                <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>
            </div>

            <button type="submit">Registrarse</button>
        </form>

        <div class="enlace">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>
</body>
</html>
