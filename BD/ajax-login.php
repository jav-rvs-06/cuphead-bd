<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once 'config.php';

header('Content-Type: application/json');

if (!isset($conexion)) {
    echo json_encode(['success' => false, 'error' => 'No se pudo establecer conexión con la base de datos']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit();
}

$email = trim($_POST['correo_electronico'] ?? '');
$password = $_POST['contrasena'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Email y contraseña son obligatorios']);
    exit();
}

$stmt = $conexion->prepare("SELECT id, nombre_usuario, contrasena, rol, activo FROM usuarios WHERE correo_electronico = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error al preparar consulta: ' . $conexion->error]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
    
    if ($usuario['activo'] == 0) {
        echo json_encode(['success' => false, 'error' => 'Tu cuenta ha sido deshabilitada. Contacta al administrador.']);
        exit();
    }
    
    if (password_verify($password, $usuario['contrasena'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
        $_SESSION['rol'] = $usuario['rol'] ?? 'usuario';
        
        echo json_encode([
            'success' => true, 
            'mensaje' => 'Inicio de sesión exitoso',
            'rol' => $_SESSION['rol']
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Contraseña incorrecta']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado. Por favor regístrate.']);
}

$stmt->close();
?>
