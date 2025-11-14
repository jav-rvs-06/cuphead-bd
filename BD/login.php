<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if (empty($nombre_usuario) || empty($contrasena)) {
        $error = 'El nombre de usuario y contraseña son obligatorios.';
    } else {
        $stmt = $conexion->prepare("SELECT id, nombre_usuario, contrasena, rol FROM usuarios WHERE nombre_usuario = ?");
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            if (password_verify($contrasena, $usuario['contrasena'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
                $_SESSION['rol'] = $usuario['rol']; // Esto es crítico
                
                if ($usuario['rol'] === 'superadmin') {
                    header('Location: superadmin.html');
                } elseif ($usuario['rol'] === 'admin') {
                    header('Location: admin.html');
                } else {
                    header('Location: index.html');
                }
                exit();
            } else {
                $error = 'Contraseña incorrecta.';
            }
        } else {
            $error = 'Nombre de usuario no encontrado.';
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
    <title>Iniciar Sesión - Guía Cuphead</title>
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
            background: #cc0000;
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
        <h1>Iniciar Sesión</h1>

        <?php if ($error): ?>
            <div class="mensaje"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="grupo-formulario">
                <label for="nombre_usuario">Nombre de usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required>
            </div>

            <div class="grupo-formulario">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>

            <button type="submit">Iniciar Sesión</button>
        </form>

        <div class="enlace">
            ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
        </div>
    </div>
</body>
</html>
